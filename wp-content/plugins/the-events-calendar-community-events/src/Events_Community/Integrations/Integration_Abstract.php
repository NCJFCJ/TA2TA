<?php

namespace TEC\Events_Community\Integrations;

use TEC\Common\Integrations\Integration_Abstract as Common_Integration_Abstract;

/**
 * Class Integration_Abstract
 *
 * @since   4.10.13
 *
 * @link    https://docs.theeventscalendar.com/apis/integrations/including-new-integrations/
 *
 * @package TEC\Tickets\Integrations
 */
abstract class Integration_Abstract extends Common_Integration_Abstract {

	/**
	 * @inheritDoc
	 */
	public static function get_parent(): string {
		return 'events-community';
	}
}
