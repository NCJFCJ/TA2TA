<?php
/**
 * Handles the maintenance mode set during migration to prevent WRITE operations on event
 * related information.
 *
 * @since   4.10.0
 *
 * @package TEC\Events_Community\Custom_Tables\V1\Migration\Maintenance_Mode;
 */

namespace TEC\Events_Community\Custom_Tables\V1\Migration\Maintenance_Mode;

use TEC\Common\Contracts\Service_Provider;
use WP_Post;

/**
 * Class Provider.
 *
 * @since   4.10.0
 *
 * @package TEC\Events_Community\Custom_Tables\V1\Migration\Maintenance_Mode;
 */
class Provider extends Service_Provider {
	/**
	 * @var bool
	 */
	protected $has_registered = false;

	/**
	 * Activates the migration mode, disabling a number of UI elements
	 * across EC.
	 *
	 * @since 4.10.0
	 *
	 * @return bool Whether the Event-wide maintenance mode was activated or not.
	 */
	public function register() {
		if ( $this->has_registered ) {

			return false;
		}
		$this->has_registered = true;
		add_action( 'tec_events_custom_tables_v1_migration_maintenance_mode', [ $this, 'add_filters' ] );

		return true;
	}

	/**
	 * Hooks into filters and actions disabling a number of UI across plugins to make sure
	 * no Event-related data will be modified during the migration.
	 *
	 * @since 4.10.0
	 *
	 * @return void
	 */
	public function add_filters() {
		// Disable the Community Events edit form.
		add_filter( 'tribe_events_template_community/edit-event.php', [
			$this,
			'filter_migration_in_progress_community_events_message_file_path'
		] );

		add_filter( 'tec_events_community_allow_users_to_delete_event', [
			$this,
			'allow_users_to_delete_submissions'
		], 10, 2 );

		remove_action(
			'tribe_events_community_form',
			[ tribe( 'community.main' )->event_form(), 'print_form' ],
			10,
			3
		);
	}

	/**
	 * Returns the absolute file path to the community events form in-progress message.
	 *
	 * @since 4.10.0
	 *
	 * @return string The absolute file path to the community events form in-progress message.
	 */
	public function filter_migration_in_progress_community_events_message_file_path(): string {
		return $this->container->make( Maintenance_Mode::class )->filter_migration_in_progress_community_events_message_file_path();
	}

	/**
	 * Disable the delete button while in maintenance mode.
	 *
	 * @since 4.10.1
	 *
	 * @param bool    $can_delete Whether the user should be able to delete a submission or not.
	 * @param WP_Post $event A reference to the post object to allow or disallow the deletion of.
	 *
	 * @return bool Whether the user should be allowed to delete the post or not.
	 */
	public function allow_users_to_delete_submissions( bool $can_delete, WP_Post $event ): bool {
		return $this->container->make( Maintenance_Mode::class )->allow_users_to_delete_submissions( $can_delete, $event );
	}
}
