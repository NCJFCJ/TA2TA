<?php
namespace Tribe\Events\Virtual\Editor;

use Tribe\Events\Virtual\Plugin;
use TEC\Common\Contracts\Service_Provider;

/**
 * Events Virtual Gutenberg Assets.
 *
 * @since 1.7.1
 */
class Assets extends Service_Provider {
	/**
	 * Registers and Enqueues the assets.
	 *
	 * @since 1.7.1
	 */
	public function register() {
		$this->container->singleton( static::class, $this );

		$plugin = tribe( Plugin::class );

		tribe_asset(
			$plugin,
			'tribe-virtual-gutenberg-main',
			'app/main.js',
			[ 'tribe-common-gutenberg-main' ],
			'enqueue_block_editor_assets',
			[
				'in_footer' => false,
				'localize'  => [],
				'priority'  => 200,
				'conditionals' => tribe_callback(  'events.editor', 'is_events_post_type' ),
				'translations' => [
					'domain' => 'events-virtual',
					'path'   => $plugin->plugin_path . 'lang',
				],
			]
		);
	}
}
