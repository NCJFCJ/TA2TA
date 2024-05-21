<?php
/**
 * The Virtual View Breadcrumbs Class
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */

namespace Tribe\Events\Virtual\Views\V2;

use Tribe\Events\Views\V2\View;
use Tribe__Events__Main as TEC;

/**
 * Class Breadcrumbs
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */
class Breadcrumbs {

	/**
	 * Filter breadcrumbs for virtual archives.
	 *
	 * @since 1.0.1
	 *
	 * @param array $breadcrumbs An array of breadcrumbs.
	 * @param View  $this        The current View instance being rendered.
	 *
	 * @return array An array of breadcrumb data the View will display on the front-end.
	 */
	public function filter_views_v2_breadcrumbs( $breadcrumbs, $view ) {

		$context     = $view->get_context();
		$taxonomy    = TEC::TAXONOMY;
		$context_tax = $context->get( $taxonomy, false );

		if ( tribe_is_truthy( $context->get( 'virtual', false ) ) ) {
			$non_virtual_link = tribe_events_get_url( [ 'virtual' => 0 ] );

			if ( empty( $context_tax ) ) {
				$breadcrumbs[] = [
					'link'  => $non_virtual_link,
					'label' => tribe_get_event_label_plural(),
				];
			}

			$breadcrumbs[] = [
				'link'  => '',
				'label' => esc_html( tribe_get_virtual_label() ),
			];
		}

		return $breadcrumbs;
	}
}
