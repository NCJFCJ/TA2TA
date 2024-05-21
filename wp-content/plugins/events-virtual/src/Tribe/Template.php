<?php
/**
 * Provides a template instance specialized for the Virtual Event plugin to serve front-end views.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

/**
 * Class Template
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */
class Template extends \Tribe__Template {

	/**
	 * Template constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->set_template_origin( tribe( Plugin::class ) );
		$this->set_template_folder( 'src/views' );

		// Setup to look for theme files.
		$this->set_template_folder_lookup( true );

		// Configures this templating class extract variables.
		$this->set_template_context_extract( true );
	}
}
