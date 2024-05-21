<?php
namespace Tribe\Events\Virtual\Editor\Template;

use Tribe\Events\Virtual\Plugin;
/**
 * Allow including of Gutenberg Template.
 *
 * @since 1.7.1
 */
class Frontend extends \Tribe__Template {
	/**
	 * Building of the Class template configuration.
	 *
	 * @since 1.7.1
	 */
	public function __construct() {
		$this->set_template_origin( tribe( Plugin::class ) );

		$this->set_template_folder( 'src/views' );

		// Configures this templating class extract variables.
		$this->set_template_context_extract( true );

		// Uses the public folders.
		$this->set_template_folder_lookup( true );

	}

	/**
	 * Return the attributes of the template.
	 *
	 * @since 1.7.1
	 *
	 * @param array<string|string> $default_attributes the default attributes to be overridden.
	 *
	 * @return array<string|string> The modified attributes.
	 */
	public function attributes( $default_attributes = [] ) {
		return wp_parse_args(
			$this->get( 'attributes', [] ),
			$default_attributes
		);
	}

	/**
	 * Return a specific attribute.
	 *
	 * @since 1.7.1
	 *
	 * @param  mixed $default.
	 *
	 * @return mixed|null
	 */
	public function attr( $index, $default = null ) {
		return $this->get( array_merge( [ 'attributes' ], (array) $index ), [], $default );
	}
}
