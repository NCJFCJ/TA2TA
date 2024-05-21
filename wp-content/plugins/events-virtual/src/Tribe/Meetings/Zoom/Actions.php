<?php
/**
 * Manages the Zoom API Actions.
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Integrations\Abstract_Actions;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Event_Meta;

/**
 * Class Actions
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Actions extends Abstract_Actions {

	/**
	 * The name of the action used to get an account setup to generate a Zoom meeting or webinar.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $validate_user_action = 'events-virtual-zoom-user-validate';

	/**
	 * The name of the action used to generate a webinar creation link.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $webinar_create_action = 'events-virtual-meetings-zoom-webinar-create';

	/**
	 * The name of the action used to remove a webinar link.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $webinar_remove_action = 'events-virtual-meetings-zoom-webinar-remove';

	/**
	 * Actions constructor.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {
		static::$api_id = Zoom_Event_Meta::$key_source_id;

		$this->setup( static::$api_id );
	}
}
