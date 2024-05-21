<?php
/**
 * Handles the filtering of the Views repository arguments.
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */

namespace Tribe\Events\Virtual\Views\V2;

use Tribe__Context as Context;

/**
 * Class Repository
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */
class Repository {

	/**
	 * Filters a View repository args to add the virtual ones.
	 *
	 * @since 1.0.1
	 *
	 * @param array        $repository_args The current repository args.
	 * @param Context|null $context         An instance of the context the View is using or `null` to use the
	 *                                      global Context.
	 *
	 * @return array The filtered repository args.
	 */
	public function filter_repository_args( array $repository_args, Context $context = null ) {
		$context = null !== $context ? $context : tribe_context();

		if ( $context->is( 'virtual' ) ) {
			$repository_args['virtual'] = true;
		}

		return $repository_args;
	}
}
