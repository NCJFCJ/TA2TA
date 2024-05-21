<?php
/**
 * Export functions for Zoom.
 *
 * @since   1.7.3
 * @package Tribe\Events\Virtual\Service_Providers;
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Export\Abstract_Export;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Event_Meta;

/**
 * Class Event_Export
 *
 * @since 1.7.3
 * @since 1.13.0 - Moved modify_video_source_export_output() to the abstract class.
 *
 * @package Tribe\Events\Virtual\Export;
 */
class Event_Export extends Abstract_Export {

	/**
	 * Event_Export constructor.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {
		self::$api_id = Zoom_Event_Meta::$key_source_id;
	}
}
