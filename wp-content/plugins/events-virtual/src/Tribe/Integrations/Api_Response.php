<?php
/**
 * Models the response provided by an API to expose a fluent, Javascript promise-like, API.
 *
 * @since 1.9.0 - Moved from Meetings Namespace.
 *
 * @package Tribe\Events\Virtual\Meetings
 */
namespace Tribe\Events\Virtual\Integrations;

use WP_Error;

/**
 * Class Api_Response
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */
class Api_Response {
	/**
	 * The original response object.
	 *
	 * @since 1.9.0
	 *
	 * @var array|WP_Error
	 */
	protected $response;

	/**
	 * Whether the current response is fulfilled (it's a success) or not.
	 *
	 * @since 1.9.0
	 *
	 * @var bool
	 */
	protected $is_fulfilled;

	/**
	 * Whether the current request is rejected (it's an error) or not.
	 *
	 * @since 1.9.0
	 *
	 * @var bool
	 */
	protected $is_rejected;

	/**
	 * Api_Response constructor.
	 *
	 * @param array|WP_Error $response The result of a `wp_post_remote` function, either the response array or a
	 *                                  WP_Error to indicate a failure.
	 */
	public function __construct( $response ) {
		$this->response     = $response;
		$this->is_fulfilled = ! $response instanceof WP_Error;
		$this->is_rejected  = $response instanceof WP_Error;
	}

	/**
	 * Ensures something will become a valid response
	 *
	 * @since 1.9.0
	 *
	 * @param mixed $response The response raw data to cast to a valid response.
	 *
	 * @return static An response object.
	 */
	public static function ensure_response( $response ) {
		if ( $response instanceof static ) {
			return $response;
		}

		if ( $response instanceof WP_Error ) {
			return new static( $response );
		}

		$cast = (array) json_decode( wp_json_encode( $response ), true );

		return new static( $cast );
	}

	/**
	 * Invokes either a fulfillment callable or a rejection callable on the response result.
	 *
	 * @since 1.9.0
	 *
	 * @param callable      $on_fulfillment The callback that should be called if the request does not result in an
	 *                                      error. The callback will receive the response complete array as an input.
	 * @param callable|null $on_rejection   The optional callback that should be called if the request is an error.
	 *                                      The callback will receive the `WP_Error` instance as an input.
	 *
	 * @return $this For chaining.
	 */
	public function then( callable $on_fulfillment, callable $on_rejection = null ) {
		if ( $this->is_fulfilled ) {
			call_user_func( $on_fulfillment, $this->response );
		}

		if ( $this->is_rejected && is_callable( $on_rejection ) ) {
			call_user_func( $on_rejection, $this->response );
		}

		return $this;
	}

	/**
	 * Invokes a callable if the request results in an error.
	 *
	 * In PHP `catch` is a reserved word, so we pick the next best thing.
	 *
	 * @since 1.9.0
	 *
	 * @param callable $on_rejection The callback that should be called if the request resulted in an error.
	 *
	 * @return $this For chaining.
	 */
	public function or_catch( callable $on_rejection ) {
		if ( ! $this->is_rejected ) {
			return;
		}

		call_user_func( $on_rejection, $this->response );

		return $this;
	}
}
