<?php

namespace TEC\Events_Community\Callbacks\Venue;

use TEC\Events_Community\Callbacks\Abstract_Callback;
use Tribe__Events__API;
use Tribe__Events__Community__Main;
use Tribe__Events__Community__Venue_Submission_Scrubber;
use Tribe__Events__Main;
use Tribe__Utils__Array;

/**
 * Class Callback_Edit
 *
 * Handles the callback for editing a venue in the events community.
 *
 * @package TEC\Events_Community\Callbacks\Venue
 */
class Callback_Edit extends Abstract_Callback {

	/**
	 * @inheritdoc
	 */
	protected static string $slug = 'venue';

	/**
	 * @inheritdoc
	 */
	protected string $logout_page_tagline = 'Please log in to edit this venue.';

	/**
	 * Check access to the venue editing page.
	 *
	 * @since 4.10.14
	 *
	 * @return string|null Error message if access is denied.
	 */
	public function get_access_message(): ?string {

		$tribe_venue_id = intval( $this->get_page_args( 'venue_id' ) );

		if ( ! is_user_logged_in() ) {
			return $this->display_login_form();
		}

		if ( empty( $tribe_venue_id ) ) {
			return '<p>' . esc_html__( 'Venue not found.', 'tribe-events-community' ) . '</p>';

		}

		if ( ! current_user_can( 'edit_post', $tribe_venue_id ) ) {
			return '<p>' . esc_html__( 'You do not have permission to edit this venue.', 'tribe-events-community' ) . '</p>';

		}

		return null;

	}

	/**
	 * Handle the submission of the venue editing form.
	 *
	 * @since 4.10.14
	 *
	 * @return void
	 */
	public function handle_form_submission() {
		$main = tribe( 'community.main' );

		if ( ! isset( $_POST['community-event'] ) || ! $_POST['community-event'] || ! check_admin_referer( 'ecp_venue_submission' ) ) {
			if ( isset( $_POST['community-event'] ) ) {
				$main->enqueueOutputMessage( esc_html__( 'There was a problem updating your venue, please try again.', 'tribe-events-community' ), 'error' );
			}
			return;
		}

		if ( ! isset( $_POST['post_title'] ) || empty( $_POST['post_title'] ) ) {
			$main->enqueueOutputMessage( esc_html__( 'Venue name cannot be blank.', 'tribe-events-community' ), 'error' );
			return;
		}

		$_POST['ID'] = $this->get_page_args( 'venue_id' );
		$scrubber    = new Tribe__Events__Community__Venue_Submission_Scrubber( $_POST );
		$_POST       = $scrubber->scrub();

		remove_action(
			'save_post_' . Tribe__Events__Main::VENUE_POST_TYPE,
			[ Tribe__Events__Main::instance(), 'save_venue_data' ],
			16,
			2
		);

		wp_update_post( [
			'post_title'   => $_POST['post_title'],
			'ID'           => $this->get_page_args( 'venue_id' ),
			'post_content' => $_POST['post_content'],
		] );

		Tribe__Events__API::updateVenue( $this->get_page_args( 'venue_id' ), $_POST['venue'] );

		$main->enqueueOutputMessage( esc_html__( 'Venue updated.', 'tribe-events-community' ) );
	}

	/**
	 * @inheritDoc
	 */
	public function callback(): string {

		$this->default_template_compatibility();

		$tribe_venue_id = intval( (int) $this->get_page_args( 'venue_id' ) );

		add_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

		$access_message = $this->get_access_message();

		if ( $access_message ) {
			return $access_message;
		}

		$this->handle_form_submission();

		global $post;
		$post = get_post( $tribe_venue_id );

		$args   = [
			'venue_id' => $this->get_page_args( 'venue_id' ),
		];
		$output = $this->display_template( 'community/edit-venue', $args );

		remove_filter( 'tribe-post-origin', [ $this, 'filterPostOrigin' ] );

		return $output;
	}
}
