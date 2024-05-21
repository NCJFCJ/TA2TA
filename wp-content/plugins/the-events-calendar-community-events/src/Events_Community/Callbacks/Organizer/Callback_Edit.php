<?php

namespace TEC\Events_Community\Callbacks\Organizer;

use TEC\Events_Community\Callbacks\Abstract_Callback;
use Tribe__Events__API;
use Tribe__Events__Community__Main;
use Tribe__Events__Community__Organizer_Submission_Scrubber;
use Tribe__Events__Main;
use Tribe__Utils__Array;

/**
 * Class Callback_Edit
 *
 * @since 4.10.14
 *
 * @package TEC\Events_Community\Callbacks\Organizer
 */
class Callback_Edit extends Abstract_Callback {

	/**
	 * @inheritdoc
	 */
	protected static string $slug = 'organizer';

	/**
	 * @inheritdoc
	 */
	protected string $logout_page_tagline = 'Please log in to edit this organizer.';

	/**
	 * Check access to the organizer editing page.
	 *
	 * @since 4.10.14
	 *
	 * @return string|null Error message if access is denied.
	 */
	public function get_access_message(): ?string {
		$organizer_id = intval( (int) $this->get_page_args( 'organizer_id' ) );

		// Some preliminary checks to ensure editing the organizer is allowed.

		if ( ! is_user_logged_in() ) {
			return $this->display_login_form();
		}

		if ( ! $organizer_id ) {
			return '<p>' . esc_html__( 'Organizer not found.', 'tribe-events-community' ) . '</p>';
		}

		if ( Tribe__Events__Main::ORGANIZER_POST_TYPE !== get_post_type( $organizer_id ) ) {
			return '<p>' . esc_html__( 'Only an Organizer can be edited on this page.', 'tribe-events-community' ) . '</p>';
		}

		if ( ! current_user_can( 'edit_post', $organizer_id ) ) {
			return '<p>' . esc_html__( 'You do not have permission to edit this organizer.', 'tribe-events-community' ) . '</p>';
		}

		return null;
	}

	/**
	 * Handle the submission of the organizer editing form.
	 *
	 * @since 4.10.14
	 *
	 * @return void
	 */
	public function handle_form_submission() {
		$main = tribe( 'community.main' );

		/**
		 * Filter Community Events Required Organizer Fields.
		 *
		 * @param array $required_organizer_fields Fields to validate - Organizer, Phone, Website, Email.
		 */
		$required_organizer_fields = apply_filters( 'tribe_events_community_required_organizer_fields', [] );

		if ( Tribe__Utils__Array::get( $_POST, 'community-event', false ) ) {

			if ( ! check_admin_referer( 'ecp_organizer_submission' ) ) {
				$main->enqueueOutputMessage( esc_html__( 'There was a problem updating this organizer, please try again.', 'tribe-events-community' ), 'error' );
			}

			if ( ! isset( $_POST['post_title'] ) ) {
				$main->enqueueOutputMessage( esc_html__( 'Organizer name cannot be blank.', 'tribe-events-community' ), 'error' );
			}

			$_POST['ID']             = $this->get_page_args( 'organizer_id' );
			$scrubber                = new Tribe__Events__Community__Organizer_Submission_Scrubber( $_POST );
			$_POST                   = $scrubber->scrub();
			$has_all_required_fields = true;

			foreach ( $required_organizer_fields as $field ) {

				// This array of required fields is shared with the submission form, on which the existence of an Organizer is not a given.
				// Here on the edit-organizer form, though, it *is* a given, so we can skip checking the parent 'Organizer' field to prevent unnecessary messages about it.
				if ( 'Organizer' === $field ) {
					continue;
				}

				$required_field = Tribe__Utils__Array::get( $_POST, [ 'organizer', $field ], '' );

				if ( empty( $required_field ) ) {
					/* translators : %1$s the field that is required. */
					$main->enqueueOutputMessage( sprintf( esc_html__( '%1$s required', 'tribe-events-community' ), $field ), 'error' );
					$has_all_required_fields = false;
				}
			}

			remove_action(
				'save_post_' . Tribe__Events__Main::ORGANIZER_POST_TYPE,
				[ Tribe__Events__Main::instance(), 'save_organizer_data' ],
				16,
				2
			);

			if ( $has_all_required_fields ) {

				wp_update_post( [
					'post_title'   => $_POST['post_title'],
					'ID'           => $this->get_page_args( 'organizer_id' ),
					'post_content' => $_POST['post_content'],
				] );

				Tribe__Events__API::updateOrganizer( $this->get_page_args( 'organizer_id' ), $_POST['organizer'] );
				$main->enqueueOutputMessage( esc_html__( 'Organizer updated.', 'tribe-events-community' ) );
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function callback(): string {

		add_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

		$this->default_template_compatibility();

		$access_message = $this->get_access_message();

		if ( $access_message ) {
			return $access_message;
		}

		$this->handle_form_submission();

		global $post;
		$post = get_post( $this->get_page_args( 'organizer_id' ) );

		$args   = [
			'organizer_id' => $this->get_page_args( 'organizer_id' ),
		];
		$output = $this->display_template( 'community/edit-organizer', $args );

		remove_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

		return $output;
	}

}
