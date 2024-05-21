<?php
/**
 * Manages the Webex URLs for the plugin.
 *
 * @since   1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Integrations\Abstract_Url;
use Tribe\Events\Virtual\Plugin;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Event_Meta;

/**
 * Class Url
 *
 * @since   1.9.0
 * @since   1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Url extends Abstract_Url {

	/**
	 * The base URL to request an access token to Webex API.
	 *
	 * @since      1.9.0
	 * @deprecated 1.13.0 - Use static::$request_url;
	 *
	 * @var string
	 *
	 * @link       https://marketplace.webex.us/docs/guides/auth/oauth
	 */
	public static $token_request_url = 'https://whodat.theeventscalendar.com/oauth/webex/v1/token';

	/**
	 * Url constructor.
	 *
	 * @since 1.13.0
	 * @since 1.13.3 - Remove API dependency to prevent maximum function nesting level.
	 *
	 * @param Actions $actions An instance of the Webex Actions handler.
	 */
	public function __construct( Actions $actions ) {
		self::$api_id        = Webex_Event_Meta::$key_source_id;
		self::$authorize_url = 'https://whodat.theeventscalendar.com/oauth/webex/v1/authorize';
		self::$refresh_url   = 'https://whodat.theeventscalendar.com/oauth/webex/v1/token';
		self::$revoke_url    = 'https://whodatdev.theeventscalendar.com/oauth/webex/v1/revoke';
		$this->actions       = $actions;
	}
}
