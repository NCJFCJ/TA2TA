<?php
/**
 * Widget
 *
 * @since   1.1.2
 *
 * @package Tribe\Events\Virtual\Views\V2\Widgets
 */

namespace Tribe\Events\Virtual\Views\V2\Widgets;

use Tribe\Events\Views\V2\Assets as TEC_Assets;

/**
 * Generic Widget Class.
 *
 * @since   1.1.2
 *
 * @package Tribe\Events\Virtual\Views\V2\Widgets
 */
class Widget {
	/**
	 * Enqueue assets for events virtual for events list widget.
	 *
	 * @since 1.1.2
	 *
	 * @param boolean         $should_enqueue Whether assets are enqueued or not.
	 * @param \Tribe__Context $context        Context we are using to build the view.
	 * @param View_Interface  $view           Which view we are using the template on.
	 */
	public function action_enqueue_assets( $should_enqueue, $context, $view ) {
		/**
		 * Allows filtering of Whether assets (virtual icon styles) should be enqueued or not.
		 *
		 * @since 1.1.5
		 *
		 * @param boolean         $should_enqueue Whether assets are enqueued or not.
		 * @param \Tribe__Context $context        Context we are using to build the view.
		 * @param View_Interface  $view           Which view we are using the template on.
		 */
		$should_enqueue = apply_filters( 'tribe-events-virtual-widgets-v2-should-enqueue-assets', $should_enqueue, $context, $view );

		$view_slug = $view::get_view_slug();

		/**
		 * Allows filtering of Whether assets (virtual icon styles) should be enqueued or not, per widget slug.
		 *
		 * @since 1.1.5
		 *
		 * @param boolean         $should_enqueue Whether assets are enqueued or not.
		 * @param \Tribe__Context $context        Context we are using to build the view.
		 * @param View_Interface  $view           Which view we are using the template on.
		 */
		$should_enqueue = apply_filters( "tribe-events-virtual-widgets-v2-{$view_slug}-should-enqueue-assets", $should_enqueue, $context, $view );

		if ( ! $should_enqueue ) {
			return;
		}

		tribe_asset_enqueue( 'tribe-events-virtual-widgets-v2-common-skeleton' );


		if ( tribe( TEC_Assets::class )->should_enqueue_full_styles() ) {
			tribe_asset_enqueue( 'tribe-events-virtual-widgets-v2-common-full' );
		}
	}
}
