<?php
/**
 * Manages the Webex API Actions.
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Integrations\Abstract_Actions;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Event_Meta;

/**
 * Class Actions
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Actions extends Abstract_Actions {

	/**
	 * Actions constructor.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {
		static::$api_id = Webex_Event_Meta::$key_source_id;

		$this->setup( static::$api_id );
	}
}
