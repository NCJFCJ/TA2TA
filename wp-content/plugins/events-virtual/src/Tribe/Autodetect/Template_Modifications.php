<?php
/**
 * Handles the templates modifications required by the Autodetect feature.
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */

namespace Tribe\Events\Virtual\Autodetect;

use Tribe\Events\Virtual\Template;
use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Template_Modifications as Base_Modifications;

/**
 * Class Template_Modifications
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */
class Template_Modifications {

	/**
	 * An instance of the front-end template handler.
	 *
	 * @since 1.8.0
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * An instance of the admin template handler.
	 *
	 * @since 1.8.0
	 *
	 * @var Template
	 */
	protected $admin_template;

	/**
	 * Template_Modifications constructor.
	 *
	 * @since 1.8.0
	 *
	 * @param Template           $template           An instance of the front-end template handler.
	 * @param Admin_Template     $admin_template     An instance of the backend template handler.
	 * @param URL                $url                An instance of the URL handler.
	 * @param Base_Modifications $base_modifications An instance of base Virtual Event template modifications instance.
	 */
	public function __construct(
		Template $template, Admin_Template $admin_template, Url $url, Base_Modifications $base_modifications
	) {
		$this->template           = $template;
		$this->admin_template     = $admin_template;
		$this->url                = $url;
		$this->base_modifications = $base_modifications;
	}

	/**
	 * The message template to display on setting changes.
	 *
	 * @since 1.8.0
	 *
	 * @param string $message The message to display.
	 * @param string $type    The type of message, either updated or error.
	 *
	 * @return string The message with html to display.
	 */
	public function get_settings_message_template( $message, $type = 'updated' ) {
		return $this->admin_template->template( 'components/message', [
			'message' => $message,
			'type'    => $type,
		] );
	}
}
