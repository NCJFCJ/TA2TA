<?php
/**
 * Class Tribe__Events__Community__Plugin_Register
 *
 * @since 4.6
 */
class  Tribe__Events__Community__Plugin_Register extends Tribe__Abstract_Plugin_Register {

	protected $main_class   = 'Tribe__Events__Community__Main';
	protected $dependencies = [
		'parent-dependencies' => [
			'Tribe__Events__Main' => '6.1.2-dev',
		],
	];

	/**
	 * Constructor method.
	 *
	 * @since 4.6
	 */
	public function __construct() {
		$this->base_dir = EVENTS_COMMUNITY_FILE;
		$this->version  = Tribe__Events__Community__Main::VERSION;

		add_filter( 'tribe_register_Tribe__Events__Community__Main_plugin_dependencies', [ $this, 'add_tec_tickets_as_dependency_if_active' ] );

		$this->register_plugin();
	}

	/**
	 * Add Event Tickets dependency if it's active.
	 *
	 * @since 4.10.17
	 *
	 * @param array $dependencies An array of dependencies for the plugins. These can include parent, add-on and other dependencies.
	 *
	 * @return array
	 */
	public function add_tec_tickets_as_dependency_if_active( $dependencies ) {
		if ( class_exists( 'Tribe__Tickets__Main', false ) ) {
			$dependencies['parent-dependencies']['Tribe__Tickets__Main'] = '5.9.1-dev';
		}

		return $dependencies;
	}
}
