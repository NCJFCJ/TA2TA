<?php

namespace TEC\Events_Community\Integrations\Themes\Divi;

use TEC\Common\Integrations\Traits\Theme_Integration;
use TEC\Events_Community\Integrations\Integration_Abstract;
use TEC\Events_Community\Routes\Provider as Route_Provider;

class Provider extends Integration_Abstract {

	use Theme_Integration;

	/**
	 * @inheritDoc
	 *
	 * @return string The slug of the integration.
	 */
	public static function get_slug(): string {
		return 'divi';
	}

	/**
	 * @inheritDoc
	 *
	 * @return bool Whether or not integrations should load.
	 */
	public function load_conditionals(): bool {
		$theme             = wp_get_theme();
		$theme_name        = strtolower( $theme->get( 'Name' ) );
		$parent_theme_name = strtolower( $theme->get( 'Parent Theme' ) );

		return $theme_name === 'divi' || $parent_theme_name === 'divi';
	}

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
	protected function load(): void {
		add_action( 'wp', [ $this, 'disable_static_css_generation' ] );
	}

	/**
	 * Disable dynamic assets for the Divi theme.
	 *
	 * @return void
	 */
	public function disable_dynamic_assets(): void {
		// Disable Feature: Dynamic Assets.
		add_filter( 'et_disable_js_on_demand', '__return_true' );
		add_filter( 'et_use_dynamic_css', '__return_false' );
		add_filter( 'et_should_generate_dynamic_assets', '__return_false' );

		// Disable Feature: Critical CSS.
		add_filter( 'et_builder_critical_css_enabled', '__return_false' );
	}

	/**
	 * Disable static CSS generation when on a Community Events Router page.
	 *
	 * @return void
	 */
	public function disable_static_css_generation(): void {
		// Get the current post object
		global $post;

		$post_type = get_post_type( $post );

		if ( 'wp_router_page' !== $post_type ) {
			return;
		}

		$this->disable_dynamic_assets();
	}
}