<?php
/**
 * Handles the filtering of the Views query arguments.
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */

namespace Tribe\Events\Virtual\Views\V2;

use Tribe\Events\Views\V2\View_Interface;

/**
 * Class Query
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */
class Query {

	/**
	 * Filters the View URL query arguments to add the ones handled by Virtual Events.
	 *
	 * @since 1.0.1
	 *
	 * @param array          $query_args The current View URL query arguments.
	 * @param View_Interface $view       The View whose URL arguments are being filtered.
	 *
	 * @return array The filtered View URL query arguments.
	 */
	public function filter_view_query_args( array $query_args, View_Interface $view ) {
		$context = $view->get_context();
		if ( $context->is( 'virtual' ) ) {
			$query_args['virtual'] = true;
		}

		return $query_args;
	}
}
