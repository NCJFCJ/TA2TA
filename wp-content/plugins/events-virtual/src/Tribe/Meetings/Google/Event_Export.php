<?php
/**
 * Export functions for Google.
 *
 * @since 1.11.0
 * @package Tribe\Events\Virtual\Meetings\Google;
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Export\Abstract_Export;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Event_Meta;

/**
 * Class Event_Export
 *
 * @since 1.11.0
 * @since 1.13.0 - Moved modify_video_source_export_output() to the abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Google;
 */
class Event_Export extends Abstract_Export {

	/**
	 * Event_Export constructor.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {
		self::$api_id = Google_Event_Meta::$key_source_id;
	}
}
