<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */ declare( strict_types=1 );

namespace TEC\Conference\Vendor\StellarWP\Uplink\Auth\Token;

use TEC\Conference\Vendor\StellarWP\Uplink\Auth\Authorizer;
use TEC\Conference\Vendor\StellarWP\Uplink\Auth\Token\Contracts\Token_Manager;
use TEC\Conference\Vendor\StellarWP\Uplink\Auth\Token\Exceptions\InvalidTokenException;

final class Connector {

	/**
	 * @var Authorizer
	 */
	private $authorizer;

	/**
	 * @var Token_Manager
	 */
	private $token_manager;

	/**
	 * @param  Authorizer  $authorizer  Determines if the current user can perform actions.
	 * @param  Token_Manager  $token_manager The Token Manager.
	 */
	public function __construct(
		Authorizer $authorizer,
		Token_Manager $token_manager
	) {
		$this->authorizer    = $authorizer;
		$this->token_manager = $token_manager;
	}

	/**
	 * Store a token if the user is allowed to.
	 *
	 * @throws InvalidTokenException
	 */
	public function connect( string $token ): bool {
		if ( ! $this->authorizer->can_auth() ) {
			return false;
		}

		if ( ! $this->token_manager->validate( $token ) ) {
			throw new InvalidTokenException( 'Invalid token format' );
		}

		return $this->token_manager->store( $token );
	}

}
