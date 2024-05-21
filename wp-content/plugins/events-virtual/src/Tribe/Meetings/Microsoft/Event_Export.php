<?php
/**
 * Export functions for Microsoft.
 *
 * @since 1.13.0
 * @package Tribe\Events\Virtual\Meetings\Microsoft;
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Export\Abstract_Export;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Event_Meta;

/**
 * Class Event_Export
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft;
 */
class Event_Export extends Abstract_Export {

	/**
	 * Event_Export constructor.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {
		self::$api_id = Microsoft_Event_Meta::$key_source_id;
	}
}
