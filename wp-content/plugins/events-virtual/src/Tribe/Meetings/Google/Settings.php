<?php
/**
 * Manages the Google settings.
 *
 * @since   1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Integrations\Abstract_Settings;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Meta;

/**
 * Class Settings
 *
 * @since   1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
class Settings extends Abstract_Settings {

	/**
	 * {@inheritDoc}
	 */
	public static $option_prefix = 'tec_google_';

	/**
	 * Settings constructor.
	 *
	 * @since 1.11.0
	 *
	 * @param Api                    $api                    An instance of the Google API handler.
	 * @param Template_Modifications $template_modifications An instance of the Template_Modifications handler.
	 * @param Url                    $url                    An instance of the URL handler.
	 */
	public function __construct( Api $api, Template_Modifications $template_modifications, Url $url ) {
		$this->api                    = $api;
		$this->template_modifications = $template_modifications;
		$this->url                    = $url;
		self::$api_id                 = Google_Meta::$key_source_id;
	}
}
