<?php
/**
 * Handles the maintenance mode template overrides during migration to prevent WRITE operations on event
 * related information.
 *
 * @since    4.10.0
 *
 * @package  TEC\Events_Community\Custom_Tables\V1\Migration\Maintenance_Mode;
 */

namespace TEC\Events_Community\Custom_Tables\V1\Migration\Maintenance_Mode;

use WP_Post;

/**
 * Class Maintenance_Mode.
 *
 * @since    4.10.0
 *
 * @package  TEC\Events_Community\Custom_Tables\V1\Migration\Maintenance_Mode;
 */
class Maintenance_Mode {

	/**
	 * Returns the absolute file path to the community events form in-progress message.
	 *
	 * @since 4.10.0
	 *
	 * @return string The absolute file path to the community events form in-progress message.
	 */
	public function filter_migration_in_progress_community_events_message_file_path(): string {
		return TEC_EC_CUSTOM_TABLES_V1_ROOT . '/admin-views/migration/maintenance-mode/community-events-form.php';
	}

	/**
	 * Disable the delete button while in maintenance mode.
	 *
	 * @since 4.10.1
	 *
	 * @param bool    $can_delete Whether the user should be able to delete a submission or not.
	 * @param WP_Post $post A reference to the post object to allow or disallow the deletion of.
	 *
	 * @return false Whether the user should be allowed to delete the post or not.
	 */
	public function allow_users_to_delete_submissions( bool $can_delete, WP_Post $post ): bool {
		return false;
	}
}