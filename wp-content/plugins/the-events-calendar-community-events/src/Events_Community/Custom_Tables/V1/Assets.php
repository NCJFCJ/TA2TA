<?php
/**
 * Handles the looking around and following enqueueing of ECP CT1 assets on the
 * Events-Community screens that require it.
 *
 * @since   4.10.0
 *
 * @package TEC\Events_Community\Custom_Tables\V1;
 */

namespace TEC\Events_Community\Custom_Tables\V1;


/**
 * Class Assets.
 *
 * @since   4.10.0
 *
 * @package TEC\Events_Community\Custom_Tables\V1;
 */
class Assets {
	/**
	 * Detects, reading the request URI directly, whether the request is to edit or add an Event using
	 * the plugin pages.
	 *
	 * This method uses knowledge of the system to get the job done: the plugin using WP Router to set
	 * up its own routes, we know the slugs used to register those routes and can, following this, hard-code
	 * the values we're looking for.
	 * When this method runs, the global `$wp` variable has not been set up yet, so we have to make do and work
	 * with what we have to identify the request route very early.
	 *
	 * @since 4.10.0
	 *
	 * @return bool Whether the request is to edit or add an Event.
	 */
	public function is_edit_route(): bool {
		$request_uri = filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL );

		if ( empty( $request_uri ) ) {
			return false;
		}

		$request_uri = trim( wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
		$rewrite_rules = (array) get_option( 'rewrite_rules', [] );
		$to_check = 2;
		foreach ( $rewrite_rules as $pattern => $rule ) {
			if ( strpos( $rule, 'WP_Route=ce-add-route' ) !== false || strpos( $rule, 'WP_Route=ce-edit-route' ) !== false ) {
				$to_check --;
				// Because it's possible that the $_SERVER['REQUEST_URI'] returns with an
				// additional folder we strip out the beginning ^ so that it will look anywhere.
				$pattern = ltrim( $pattern, '^' );
				if ( preg_match( '#' . $pattern . '#', $request_uri ) ) {
					return true;
				}
			}
			if ( $to_check === 0 ) {
				break;
			}
		}

		return false;
	}

	/**
	 * Hooks on the actions and filters required to enqueue ECP Custom Tables v1 scripts
	 * and styles in the editing screens.
	 *
	 * @since 4.10.0
	 *
	 * @return bool Whether the assets were enqueued or not.
	 */
	public function enqueue_ecp_assets(): bool {
		add_filter( 'tec_events_pro_custom_tables_v1_editor_asset_context', static function ( array $context ): array {
			$context['is_series_post_screen'] = false;
			$context['is_series_edit_screen'] = false;
			$context['is_classic_event_post_screen'] = true;
			$context['is_blocks_event_post_screen'] = false;

			return $context;
		} );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_asset_group' ] );

		return true;
	}

	/**
	 * Returns the name of assets to enqueue.
	 *
	 * @since 4.10.5
	 *
	 * @return string
	 */
	public function enqueue_asset_group() {
		$group_name = \TEC\Events_Pro\Custom_Tables\V1\Editors\Provider::$classic_event_min_group_key;
		tribe_asset_enqueue_group( $group_name );
	}
}
