<?php
/**
 * Handles the filtering of the Views title to support Virtual events.
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */

namespace Tribe\Events\Virtual\Views\V2;

use Tribe__Context as Context;

/**
 * Class Title
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */
class Title {

	/**
	 * Filter the plural events label for Virtual V2 Views.
	 *
	 * @since 1.0.1
	 *
	 * @param string  $label   The plural events label as it's been generated thus far.
	 * @param Context $context The context used to build the title, it could be the global one, or one externally
	 *                         set.
	 *
	 * @return string the original label or updated label for virtual archives.
	 */
	public function filter_views_v2_wp_title_plural_events_label( $label, Context $context ) {

		$context = $context ? $context : tribe_context();

		if ( $context->is( 'virtual' ) ) {
			return tribe_get_virtual_event_label_plural();
		}

		return $label;
	}
}
