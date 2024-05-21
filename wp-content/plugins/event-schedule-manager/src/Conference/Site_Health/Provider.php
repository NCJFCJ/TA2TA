<?php
/**
 * Service Provider for interfacing with TEC\Conference\Site Health.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Site_Health
 */

namespace TEC\Conference\Site_Health;

use TEC\Conference\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Site_Health
 */
class Provider extends Service_Provider {

	/**
	 * Internal placeholder to pass around the section slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	public function register() {
		$this->slug = Info_Section::get_slug();
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Add the action hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {

	}

	/**
	 * Add the filter hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'debug_information', [ $this, 'filter_include_info_sections' ] );
		add_filter( 'tec_conference_debug_info_sections', [ $this, 'filter_include_sections' ] );
	}

	/**
	 * Includes the info sections controlled by Common.
	 *
	 * @since 1.0.0
	 *
	 * @param array $info Current set of info sections.
	 *
	 * @return array
	 */
	public function filter_include_info_sections( $info ): array {
		return $this->container->make( Factory::class )->filter_include_info_sections( (array) $info );
	}

	/**
	 * Includes the Section for ESM.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, \TEC\Common\Site_Health\Info_Section_Abstract> $sections Existing sections.
	 *
	 * @return array<string, \TEC\Common\Site_Health\Info_Section_Abstract>
	 */
	public function filter_include_sections( $sections ) {
		$sections[ Info_Section::get_slug() ] = $this->container->make( Info_Section::class );

		return $sections;
	}
}
