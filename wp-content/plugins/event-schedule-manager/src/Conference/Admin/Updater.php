<?php
/**
 * Run schema updates on plugin activation or updates.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

/**
 * Class Updater
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin
 */
class Updater {

	/**
	 * Option name for storing schema version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $version_option = 'tec-event-schedule-manager-schema-version';

	/**
	 * Version to revert to when reset() is called.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $reset_version = '1.0.0';

	/**
	 * Current schema version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $current_version;

	/**
	 * Updater constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $current_version Current schema version.
	 */
	public function __construct( string $current_version ) {
		$this->current_version = $current_version;
	}

	/**
	 * Clear option caches.
	 *
	 * @since 1.0.0
	 */
	protected function clear_option_caches() {
		wp_cache_delete( 'notoptions', 'options' );
		wp_cache_delete( 'alloptions', 'options' );
	}

	/**
	 * Execute updates.
	 *
	 * @since 1.0.0
	 */
	public function do_updates() {
		$this->clear_option_caches();
		$updates = $this->get_updates();
		uksort( $updates, 'version_compare' );

		try {
			foreach ( $updates as $version => $callback ) {
				if (
					version_compare( $version, $this->current_version, '<=' )
					&& $this->is_version_in_db_less_than( $version )
				) {
					call_user_func( $callback );
				}
			}

			foreach ( $this->get_constant_update_callbacks() as $callback ) {
				call_user_func( $callback );
			}

			$this->update_version_option( $this->current_version );
		} catch ( \Exception $e ) {
			// fail silently, but it should try again next time
		}
	}

	/**
	 * Update schema version option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $new_version New schema version.
	 */
	public function update_version_option( string $new_version ) {
		update_option( $this->version_option, $new_version );
	}

	/**
	 * Get updates array.
	 *
	 * @since      1.0.0
	 *
	 * @return array Callbacks for each version.
	 */
	protected function get_updates(): array {
		return $this->get_update_callbacks();
	}

	/**
	 * Get reset version.
	 *
	 * @since 1.0.0
	 *
	 * @return string The reset version number.
	 */
	public function get_reset_version(): string {
		return $this->reset_version;
	}

	/**
	 * Get update callbacks.
	 *
	 * @since 1.0.0
	 *
	 * @return array Callbacks for each version.
	 */
	public function get_update_callbacks(): array {
		return [
			'0.9.0a' => [ $this, 'migrate_wcps_to_tec' ],
			'0.9.0b' => [ $this, 'migrate_wcpsp_to_tec' ],
		];
	}

	/**
	 * Get constant update callbacks.
	 *
	 * @since 1.0.0
	 *
	 * @return array Callbacks that are always executed.
	 */
	public function get_constant_update_callbacks(): array {
		return [
			[ $this, 'flush_rewrites' ],
		];
	}

	/**
	 * Get version from database.
	 *
	 * @since 1.0.0
	 *
	 * @return string Version in database.
	 */
	public function get_version_from_db(): string {
		return get_option( $this->version_option );
	}

	/**
	 * Check if version in database is less than the given version.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version Version to compare.
	 *
	 * @return bool Result of comparison.
	 */
	public function is_version_in_db_less_than( string $version ): bool {
		return version_compare( $version, $this->get_version_from_db() ) > 0;
	}

	/**
	 * Check if update is required.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if update required, false otherwise.
	 */
	public function update_required(): bool {
		return $this->is_version_in_db_less_than( $this->current_version );
	}

	/**
	 * Flush rewrite rules.
	 *
	 * @since 1.0.0
	 */
	public function flush_rewrites() {
		// Run after updates to clear permalinks and rebuild.
		add_action( 'admin_init', 'flush_rewrite_rules', 100 );
	}

	/**
	 * Reset update flags. All updates past $this->reset_version will
	 * run again on the next page load.
	 *
	 * @since 1.0.0
	 */
	public function reset() {
		$this->update_version_option( $this->reset_version );
	}

	/**
	 * Run Updates on Plugin Upgrades.
	 *
	 * @since 1.0.0
	 */
	public function run_updates() {
		if ( ! $this->update_required() ) {
			return;
		}

		$this->do_updates();
	}

	/**
	 * Migrate data from old tec schema to new tec schema.
	 *
	 * @since 1.0.0
	 */
	public function migrate_wcps_to_tec() {
		global $wpdb;

		// Update Post Content and Post Meta fields.
		$wpdb->query( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, '[wpcs_schedule', '[tec_schedule')" );
		$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, '[wpcs_schedule', '[tec_schedule')" );

		// Migrate Gutenberg blocks.
        $wpdb->query("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, '<!-- wp:wpcs/schedule-block', '<!-- wp:tec/schedule-block')");

		// Update Post Types.
		$wpdb->query( "UPDATE {$wpdb->posts} SET post_type = 'tec_session' WHERE post_type = 'wpcs_session'" );

		// Update Taxonomies.
		$wpdb->query( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'tec_track' WHERE taxonomy = 'wpcs_track'" );
		$wpdb->query( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'tec_location' WHERE taxonomy = 'wpcs_location'" );

		// Update Post Meta Keys.
		$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key = '_tec_session_speaker_names' WHERE meta_key = '_wpcs_session_speakers'" );
		$update_meta_keys = [
			'_conference_session_speakers'  => '_tec_conference_session_speakers',
			'_wpcs_session_time'            => '_tec_session_time',
			'_wpcs_session_end_time'        => '_tec_session_end_time',
			'_wpcs_session_type'            => '_tec_session_type',
			'wpcsp_session_speaker_display' => 'tec_session_speaker_display',
			'wpcsp_session_speakers'        => 'tec_session_speakers',
			'wpcsp_session_sponsors'        => 'tec_session_sponsors'
		];
		foreach ( $update_meta_keys as $old_key => $new_key ) {
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key = '$new_key' WHERE meta_key = '$old_key'" );
		}

		// Update Option Names.
		$update_option_names = [
			'wpcs_field_schedule_page_url'         => 'esm_schedule_page_url',
			'wpcsp_field_speakers_page_url'        => 'esm_speakers_page_url',
			'wpcsp_field_sponsor_page_url'         => 'tec_field_sponsor_page_url',
			'wpcsp_conference_sponsor_level_order' => 'tec_conference_sponsor_level_order',
			'wpcsp_speaker_level_order'            => 'tec_speaker_level_order'
		];
		foreach ( $update_option_names as $old_option => $new_option ) {
			$option_value = get_option( $old_option );
			if ( $option_value !== false ) {
				update_option( $new_option, $option_value );
				delete_option( $old_option );
			}
		}

		wp_cache_flush();
	}

	/**
	 * Migrate data from old wpcsp pro schema to new tec schema.
	 *
	 * @since 1.0.0
	 */
	public function migrate_wcpsp_to_tec() {
		global $wpdb;

		// Update Post Content and Post Meta fields.
		$wpdb->query( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, '[wpcs_sponsors', '[tec_sponsors')" );
		$wpdb->query( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, '[wpcs_speakers', '[tec_speakers')" );
		$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, '[wpcs_sponsors', '[tec_sponsors')" );
		$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, '[wpcs_speakers', '[tec_speakers')" );

		// Update Post Types.
		$wpdb->query( "UPDATE {$wpdb->posts} SET post_type = 'tec_speaker' WHERE post_type = 'wpcsp_speaker'" );
		$wpdb->query( "UPDATE {$wpdb->posts} SET post_type = 'tec_sponsor' WHERE post_type = 'wpcsp_sponsor'" );

		// Update Taxonomies.
		$wpdb->query( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'tec_session_tag' WHERE taxonomy = 'wpcs_session_tag'" );
		$wpdb->query( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'tec_sponsor_level' WHERE taxonomy = 'wpcsp_sponsor_level'" );
		$wpdb->query( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'tec_group' WHERE taxonomy = 'wpcsp_group'" );
		$wpdb->query( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'tec_group' WHERE taxonomy = 'wpcsp_speaker_level'" );

		// Update Taxonomy Meta.
		$wpdb->query( "UPDATE {$wpdb->termmeta} SET meta_key = 'tec_logo_height' WHERE meta_key = 'wpcsp_logo_height'" );

		// Update Post Meta Fields for Speaker and Sponsor.
		$update_meta_keys = [
			'wpcsp_first_name'    => 'tec_first_name',
			'wpcsp_last_name'     => 'tec_last_name',
			'wpcsp_title'         => 'tec_title',
			'wpcsp_organization'  => 'tec_organization',
			'wpcsp_facebook_url'  => 'tec_facebook_url',
			'wpcsp_twitter_url'   => 'tec_twitter_url',
			'wpcsp_instagram_url' => 'tec_instagram_url',
			'wpcsp_linkedin_url'  => 'tec_linkedin_url',
			'wpcsp_youtube_url'   => 'tec_youtube_url',
			'wpcsp_website_url'   => 'tec_website_url'
		];

		foreach ( $update_meta_keys as $old_key => $new_key ) {
			$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key = '$new_key' WHERE meta_key = '$old_key'" );
		}

		wp_cache_flush();
	}
}
