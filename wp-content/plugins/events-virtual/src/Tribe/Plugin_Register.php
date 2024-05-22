<?php
/**
 * Handles the Events Virtual plugin dependency manifest registration.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use Tribe__Abstract_Plugin_Register as Abstract_Plugin_Register;

/**
 * Class Plugin_Register.
 *
 * @see     Tribe__Abstract_Plugin_Register For the plugin dependency manifest registration.
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 *
 */
class Plugin_Register extends Abstract_Plugin_Register {
	/**
	 * The version of the plugin.
	 * Replaced the Plugin::VERSION constant, which now is an alias to this one.
	 *
	 * @since 1.14.0
	 *
	 * @var string
	 */
	public const VERSION  = '1.15.8';

	/**
	 * The dependencies of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var array[] The dependencies of the plugin.
	 */
	protected $dependencies = [
		'parent-dependencies' => [
			'Tribe__Events__Main' => '6.2.6-dev',
		],
	];

	/**
	 * Configures the base_dir property which is the path to the plugin bootstrap file.
	 *
	 * @since 1.14.0
	 *
	 * @param string $file Which is the path to the plugin bootstrap file.
	 */
	public function set_base_dir( string $file ): void {
		$this->base_dir = $file;
	}

	/**
	 * Gets the previously configured base_dir property.
	 *
	 * @since 1.14.0
	 *
	 * @return string
	 */
	public function get_base_dir(): string {
		return $this->base_dir;
	}

	/**
	 * Gets the main class of the Plugin, stored on the main_class property.
	 *
	 * @since 1.14.0
	 *
	 * @return string
	 */
	public function get_plugin_class(): string {
		return $this->main_class;
	}

	/**
	 * File path to the main class of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string The path to the main class of the plugin.
	 */
	protected $base_dir;

	/**
	 * Alias to the VERSION constant.
	 *
	 * @since 1.0.0
	 *
	 * @var string The version of the plugin.
	 */
	protected $version = self::VERSION;

	/**
	 * Fully qualified name of the main class of the plugin.
	 * Do not use the Plugin::class constant here, we need this value without loading the Plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var string The main class of the plugin.
	 */
	protected $main_class = '\Tribe\Events\Virtual\Plugin';
}
