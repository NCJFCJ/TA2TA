<?php
/**
 * Manages the Google URLs for the plugin.
 *
 * @since   1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Integrations\Abstract_Url;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Event_Meta;

/**
 * Class Url
 *
 * @since   1.11.0
 * @since   1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
class Url extends Abstract_Url {

	/**
	 * Url constructor.
	 *
	 * @since 1.13.0
	 * @since 1.13.3 - Remove API dependency to prevent maximum function nesting level.
	 *
	 * @param Actions $actions An instance of the Google Actions handler.
	 */
	public function __construct( Actions $actions ) {
		self::$api_id        = Google_Event_Meta::$key_source_id;
		self::$authorize_url = 'https://whodat.theeventscalendar.com/oauth/google/v1/authorize';
		self::$refresh_url   = 'https://whodat.theeventscalendar.com/oauth/google/v1/refresh';
		self::$revoke_url    = 'https://whodat.theeventscalendar.com/oauth/google/v1/revoke';
		$this->actions       = $actions;
	}
}
