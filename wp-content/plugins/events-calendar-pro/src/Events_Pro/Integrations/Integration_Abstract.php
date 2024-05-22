<?php
/**
 * Integration abstract class for Events Calendar Pro.
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Events\Integrations
 */

namespace TEC\Events_Pro\Integrations;

use TEC\Common\Integrations\Integration_Abstract as Common_Integration_Abstract;

/**
 * Class Integration_Abstract
 *
 * @link    https://docs.theeventscalendar.com/apis/integrations/including-new-integrations/
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Events\Integrations
 */
abstract class Integration_Abstract extends Common_Integration_Abstract {

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public static function get_parent(): string {
		return 'events-pro';
	}
}
