<?php

namespace TEC\Events_Community\Callbacks\Event;

use stdClass;
use TEC\Events_Community\Callbacks\Abstract_Callback;
use Tribe__Events__Community__Event_Form;
use Tribe__Events__Community__Main;
use Tribe__Events__Community__Submission_Handler;
use Tribe__Events__Main;

/**
 * Class Callback_Add_Edit
 *
 * @since 4.10.14
 *
 * @package TEC\Events_Community\Callbacks\Event
 */
class Callback_Add_Edit extends Abstract_Callback {

	/**
	 * The callback slug.
	 *
	 * @var string
	 */
	protected static string $slug = 'event_form';

	/**
	 * The logout page tagline.
	 *
	 * @var string
	 */
	protected string $logout_page_tagline = 'Please log in to create events.';

	/**
	 * Flag to indicate if it's an edit operation.
	 *
	 * @var bool
	 */
	protected bool $is_edit = false;

	/**
	 * The event ID.
	 *
	 * @var int
	 */
	protected int $event_id = 0;

	/**
	 * The event Object.
	 *
	 * @var object
	 */
	protected object $event;

	/**
	 * Check if the user has access to the event form.
	 *
	 * @since 4.10.14
	 *
	 * @return string|null The access message if user doesn't have access, null otherwise.
	 */
	public function get_access_message(): ?string {
		$main = tribe( 'community.main' );

		$events_label_singular           = tribe_get_event_label_singular();
		$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();

		if ( ! is_user_logged_in() && ( ! $main->allowAnonymousSubmissions || ( $this->is_edit && $this->event_id ) ) ) {
			return $this->display_login_form();
		}

		/**
		 * Can the user `allowUsersToEditSubmissions`, and is it an edit page?
		 * Does the user have access to `edit_post`?
		 * Can the user edit their submission?
		 */
		if ( ( ! $main->allowUsersToEditSubmissions && $this->is_edit ) || (
				$this->event_id
				&& ! current_user_can( 'edit_post', $this->event_id )
				&& ! $main->user_can_edit_their_submissions( $this->event_id ) ) ) {

			/* translators: %s: Event label in singular lowercase */
			return '<p>' . sprintf( esc_html__( 'You do not have permission to edit this %s.', 'tribe-events-community' ), $events_label_singular_lowercase ) . '</p>';
		}

		// If for whatever reason we are in "edit" mode and no event ID is passed, fall through here.
		if ( $this->is_edit && ( 0 === $this->event_id ) ) {
			/* translators: %s: Event label in singular form */
			return '<p>' . sprintf( esc_html__( '%s not found.', 'tribe-events-community' ), $events_label_singular ) . '</p>';
		}

		return null;
	}

	/**
	 * Check if there are any warnings, if yes then add to message queue.
	 *
	 * @since 4.10.14
	 *
	 * @return void
	 */
	public function check_warnings(): void {
		$main = tribe( 'community.main' );

		if ( $this->event_id && class_exists( 'Tribe__Events__Pro__Main' ) && tribe_is_recurring_event( $this->event_id ) ) {
			$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();
			/* translators : %1$s event label lowercase */
			$main->enqueueOutputMessage( sprintf( __( 'Warning: You are editing a recurring %1$s. Changes will be applied to all occurrences of this %1$s.', 'tribe-events-community' ), $events_label_singular_lowercase ), 'error' );
		}

		if ( $main->max_file_size_exceeded() ) {
			/* translators : %1$s the maximum file size allowed for uploading. */
			$main->enqueueOutputMessage( sprintf( __( 'The file you attempted to upload exceeded the maximum file size of %1$s.', 'tribe-events-community' ), size_format( $main->max_file_size_allowed() ) ), 'error' );
		}
	}

	/**
	 * Validate the event submission.
	 *
	 * @since 4.10.14
	 *
	 * @return bool True if the submission is valid, false otherwise.
	 */
	public function validate_submission(): bool {
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'ecp_event_submission' ) ) {
			return false;
		}

		if ( empty( $_POST['community-event'] ) ) {
			return false;
		}

		if ( ! check_admin_referer( 'ecp_event_submission' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Process the submission and save the event.
	 *
	 * @since 4.10.14
	 *
	 * @return array Messages generated during submit of event.
	 */
	public function process_submission(): array {
		$validate_submission = $this->validate_submission();

		// Check to confirm the form validates.
		if ( ! $validate_submission ) {
			return [];
		}

		$submission = $_POST;

		$main    = tribe( 'community.main' );
		$handler = new Tribe__Events__Community__Submission_Handler( $submission, $this->event_id );

		if ( ! $handler->validate() ) {
			// If the event is invalid, reset the event object to the data that was returned from the $handler to make fixing it easier.
			$this->event = (object) $handler->get_submission();
			return $handler->get_messages();
		}

		add_filter( 'tribe-post-origin', [ $main, 'filterPostOrigin' ] );

		if ( ! empty( $submission['community-shortcode-type'] ) && 'submission_form' === $submission['community-shortcode-type'] ) {
			add_filter(
				'tribe_events_community_submission_url', [
				tribe( 'community.shortcodes' ),
				'custom_nav_link'
			] );
		}

		$this->event_id = $handler->save();

		remove_filter( 'tribe-post-origin', [ $main, 'filterPostOrigin' ] );

		delete_transient( 'tribe_community_events_today_page' );

		if ( $main->emailAlertsEnabled ) {
			$main->sendEmailAlerts( $this->event_id );
		}

		/**
		 * Runs after the Community Event Form is properly validated. Allows for additional logic.
		 *
		 * @since 4.10.5
		 */
		do_action( 'tribe_events_community_after_form_validation' );

		return $handler->get_messages();
	}

	/**
	 * Process the errors.
	 *
	 * @since 4.10.14
	 *
	 * @param array $messages The errors to process.
	 *
	 * @return void
	 */
	public function process_messages( array $messages ): void {
		$main       = tribe( 'community.main' );
		$has_errors = in_array( 'error', wp_list_pluck( $messages, 'type' ) );

		foreach ( $messages as $m ) {
			if ( $has_errors && 'error' !== $m->type ) {
				continue;
			}
			$main->enqueueOutputMessage( $m->message, $m->type );
		}
	}

	/**
	 * Get the event post object.
	 *
	 * @since 4.10.14
	 *
	 * @return object The event post object.
	 */
	protected function get_event(): object {
		$check_event = get_post( $this->event_id );

		// Add an additional check to make sure that the user is getting an event they are allowed to be getting.
		if (
			$check_event instanceof \WP_Post
			&& Tribe__Events__Main::POSTTYPE === $check_event->post_type
			&& get_current_user_id() === (int) $check_event->post_author
		) {
			// Because the event exists, we are editing it.
			$this->is_edit = true;
			return $check_event;
		}

		return new stdClass();
	}

	/**
	 * Generate the output HTML.
	 *
	 * @since 4.10.14
	 *
	 * @return string The generated output HTML.
	 */
	protected function generate_output(): string {
		$main       = tribe( 'community.main' );
		$event_form = new Tribe__Events__Community__Event_Form( $this->event );
		$event_form->set_event( $this->event );
		$event_form->set_required_fields( $main->required_fields_for_submission() );

		$message_type = $main->messageType;

		$render_form = ( 'error' === $message_type || empty( $message_type ) );

		/**
		 * Allow the user to override the default "show form" logic.
		 *
		 * @since 1.0
		 *
		 * @param bool   $render_form  Whether to render the form.
		 * @param string $message_type The type of message Example `error`, `update`.
		 */
		$render_form = apply_filters( 'tribe_community_events_show_form', $render_form, $message_type );

		$output  = '<div id="tribe-community-events" class="tribe-community-events form">';
		$output .= $this->custom_above_content();
		$output .= $main->outputMessage( null, false );
		if ( $render_form ) {
			$output .= $event_form->render();
		}
		$output .= $this->custom_below_content();
		$output .= '</div>';
		return $output;
	}

	/**
	 * @inheritdoc
	 */
	public function callback(): string {

		$this->pre_filters();
		$this->default_template_compatibility();
		/**
		 * Retrieves the event ID for the community event form through a filter hook.
		 *
		 * This filter allows modification of the event ID used in the community event form.
		 *
		 * @since 4.10.14
		 *
		 * @param int $event_id The event ID.
		 *
		 * @return int Modified event ID.
		 */
		$event_id       = apply_filters( 'tec_events_community_event_form_post_id', (int) $this->get_page_args( 'event_id' ) );
		$this->event_id = intval( $event_id );

		do_action( 'tribe_community_before_event_page', $event_id );

		$this->event = $this->get_event();

		// Make sure the user has access to the page.
		$access_message = $this->get_access_message();

		if ( $access_message ) {
			return $access_message;
		}

		// Make sure there are no warnings to display.
		$this->check_warnings();

		/**
		 * Allow the user to add content or functions right before the submission template is loaded.
		 *
		 * @since 1.0
		 */
		do_action( 'tribe_events_community_before_event_submission_page' );

		// Process the submission now if applicable.
		$check_processed_submission = $this->process_submission();

		// Process the messages if any come back.
		$this->process_messages( $check_processed_submission );

		$GLOBALS['post'] = $this->event;

		/**
		 * Allow the user to add content or functions right before the submission template is loaded.
		 *
		 * @since 1.0
		 */
		do_action( 'tribe_events_community_before_event_submission_page_template' );

		$output = $this->generate_output();

		wp_reset_query();

		return $output;
	}

}
