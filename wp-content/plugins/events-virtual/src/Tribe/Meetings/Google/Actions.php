<?php
/**
 * Manages the Google API Actions.
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Integrations\Abstract_Actions;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Event_Meta;

/**
 * Class Actions
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
class Actions extends Abstract_Actions {

	/**
	 * Actions constructor.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {
		static::$api_id = Google_Event_Meta::$key_source_id;

		$this->setup( static::$api_id );
	}
}
