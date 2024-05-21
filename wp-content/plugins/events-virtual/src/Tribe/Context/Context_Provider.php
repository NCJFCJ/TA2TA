<?php
/**
 * Handles the filtering of the Context to add Virtual specific locations.
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Service_Providers;
 */

namespace Tribe\Events\Virtual\Context;

use Tribe__Context;
use TEC\Common\Contracts\Service_Provider;

class Context_Provider extends Service_Provider {

	/**
	 * Stores the query variable used by Whodat for meeting provider authorization and for internal AJAX.
	 * 'state' is sent from whodat with nonce for Microsoft, Google, and Webex.
	 *
	 * @since 1.14.0
	 *
	 * @var string
	 */
	const AUTH_STATE_QUERY_VAR = 'state';

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.1
	 */
	public function register() {
		$this->container->singleton( 'events-virtual.context', $this );

		add_filter( 'tribe_context_locations', [ $this, 'filter_context_locations' ] );
	}

	/**
	 * Filters the context locations to add the ones used by The Events Calendar PRO.
	 *
	 * @since 1.0.1
	 *
	 * @param array $locations The array of context locations.
	 *
	 * @return array The modified context locations.
	 */
	public function filter_context_locations( array $locations = [] ) {
		$locations = array_merge( $locations, [
			'virtual' => [
				'read' => [
					Tribe__Context::REQUEST_VAR => [ 'virtual' ],
					Tribe__Context::QUERY_VAR   => [ 'virtual' ],
				],
			],
		] );

		return $locations;
	}
}
