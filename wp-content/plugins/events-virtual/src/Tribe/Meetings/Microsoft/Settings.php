<?php
/**
 * Manages the Microsoft settings.
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Integrations\Abstract_Settings;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Meta;

/**
 * Class Settings
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Settings extends Abstract_Settings {

	/**
	 * {@inheritDoc}
	 */
	public static $option_prefix = 'tec_microsoft_';

	/**
	 * Settings constructor.
	 *
	 * @since 1.13.0
	 *
	 * @param Api                    $api                    An instance of the Microsoft API handler.
	 * @param Template_Modifications $template_modifications An instance of the Template_Modifications handler.
	 * @param Url                    $url                    An instance of the URL handler.
	 */
	public function __construct( Api $api, Template_Modifications $template_modifications, Url $url ) {
		$this->api                    = $api;
		$this->template_modifications = $template_modifications;
		$this->url                    = $url;
		self::$api_id                 = Microsoft_Meta::$key_source_id;
	}
}
