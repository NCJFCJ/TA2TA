<?php

/**
 * Provider for Taxonomy Related Functionality.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * Provides the functionality to register and manage post types for the conference.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		// Register the SP on the container.
		$this->container->singleton( 'tec.conference.taxonomies.provider', $this );
		$this->container->singleton( Tracks::class, Tracks::class );
		$this->container->singleton( Locations::class, Locations::class );
		//$this->container->singleton( Tags::class, Tags::class ); // Hide tags per TEC-4925 and will add better support later.
		$this->container->singleton( Sponsor_Levels::class, Sponsor_Levels::class );
		$this->container->singleton( Groups::class, Groups::class );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds required actions for taxonomies.
	 *
	 * @since 1.0.0
	 */
	protected function add_actions() {
		add_action( 'init', [ $this, 'register_tracks_taxonomy' ] );
		add_action( 'init', [ $this, 'register_locations_taxonomy' ] );
		//add_action( 'init', [ $this, 'register_tags_taxonomy' ] ); // Hide tags per TEC-4925 and will add better support later.
		add_action( 'init', [ $this, 'register_sponsor_level_taxonomy' ] );
		add_action( 'init', [ $this, 'register_groups_taxonomy' ] );
	}

	/**
	 * Registers the tracks taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register_tracks_taxonomy() {
		$this->container->make( Tracks::class )->register_taxonomy();
	}

	/**
	 * Registers the locations taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register_locations_taxonomy() {
		$this->container->make( Locations::class )->register_taxonomy();
	}

	/**
	 * Registers the tags taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register_tags_taxonomy() {
		$this->container->make( Tags::class )->register_taxonomy();
	}

	/**
	 * Registers the sponsor level taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register_sponsor_level_taxonomy() {
		$this->container->make( Sponsor_Levels::class )->register_taxonomy();
	}

	/**
	 * Registers the groups taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register_groups_taxonomy() {
		$this->container->make( Groups::class )->register_taxonomy();
	}

	/**
	 * Adds required filters for taxonomies.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {
		add_filter( 'parent_file', [ $this, 'keep_groups_menu_open' ] );
		add_filter( 'parent_file', [ $this, 'keep_sponsor_level_menu_open' ] );
	}

	/**
	 * Modify the parent file to keep the conference menu open for Speaker Groups.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_file The parent file string.
	 *
	 * @return string The parent file string.
	 */
	public function keep_groups_menu_open( $parent_file ) {
		return $this->container->make( Groups::class )->keep_taxonomy_menu_open( $parent_file );
	}

	/**
	 * Modify the parent file to keep the conference menu open for Sponsor Levels.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_file The parent file string.
	 *
	 * @return string The parent file string.
	 */
	public function keep_sponsor_level_menu_open( $parent_file ) {
		return $this->container->make( Sponsor_Levels::class )->keep_taxonomy_menu_open( $parent_file );
	}
}
