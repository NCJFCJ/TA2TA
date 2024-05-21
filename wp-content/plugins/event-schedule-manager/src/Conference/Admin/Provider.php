<?php

/**
 * Provider for Admin Related Functionality.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Contracts\Service_Provider;
use TEC\Conference\Admin\Meta\Session as Session_Meta;
use TEC\Conference\Admin\Meta\Speaker as Speaker_Meta;
use TEC\Conference\Admin\Meta\Sponsor as Sponsor_Meta;
use TEC\Conference\Plugin;
use WP_Query;

/**
 * Class Provider
 *
 * Provides the functionality to register and manage post types for the conference.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		// Register the SP on the container.
		$this->container->singleton( 'tec.conference.admin.provider', $this );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since 1.0.0
	 */
	protected function add_actions() {
		add_action( 'admin_init', [ $this, 'run_updates' ], 1, 0 );

		add_action( 'admin_menu', [ $this, 'add_conference_schedule_menu' ] );
		add_action( 'admin_menu', [ $this, 'organize_post_types' ] );
		add_action( 'admin_menu', [ $this, 'remove_duplicate_submenu' ], 99 );
		add_action( 'pre_get_posts', [ $this, 'admin_sessions_pre_get_posts' ] );
		add_action( 'manage_posts_custom_column', [ $this, 'manage_post_types_columns_output' ], 10, 2 );

		add_action( 'admin_init', [ $this, 'options_init' ] );
		add_action( 'admin_menu', [ $this, 'options_page' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_posttype_assets' ] );

		add_action( 'save_post', [ $this, 'save_post_session' ], 10, 2 );
		add_action( 'cmb2_admin_init', [ $this, 'session_metabox' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		add_action( 'cmb2_admin_init', [ $this, 'speaker_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'sponsor_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'sponsor_level_metabox' ] );
	}

	/**
	 * Run Updates on Plugin Upgrades.
	 *
	 * @since 1.0.0
	 */
	public function run_updates() {
		$updater = new Updater( Plugin::VERSION );
		$updater->run_updates();
	}

	/**
	 * Registers the sessions post type.
	 *
	 * @since 1.0.0
	 */
	public function add_conference_schedule_menu() {
		$this->container->make( Menu::class )->add_conference_schedule_menu();
	}

	/**
	 * Organizes the post types under the Event Schedule Manager menu item.
	 *
	 * @since 1.0.0
	 */
	public function organize_post_types() {
		$this->container->make( Menu::class )->organize_post_types();
	}

	/**
	 * Remove duplicate submenu items.
	 * Required as Speaker and Sponsor custom post would display twice in the menu.
	 * This is expected as the admin list for both of those keeps the Conference Submenu open.
	 *
	 * @since 1.0.0
	 */
	public function remove_duplicate_submenu() {
		$this->container->make( Menu::class )->remove_duplicate_submenu();
	}

	/**
	 * Runs during pre_get_posts in admin.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query The WP_Query object.
	 */
	public function admin_sessions_pre_get_posts( $query ) {
		$this->container->make( Columns::class )->admin_sessions_pre_get_posts( $query );
	}

	/**
	 * Output for custom columns in the admin screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column The name of the current column.
	 * @param int $post_id The ID of the current post.
	 */
	public function manage_post_types_columns_output( string $column, int $post_id ) {
		$this->container->make( Columns::class )->manage_post_types_columns_output( $column, $post_id );
	}

	/**
	 * Initializes settings and fields.
	 *
	 * @since 1.0.0
	 */
	public function options_init() {
		$this->container->make( Settings::class )->init();
	}

	/**
	 * Registers options page for settings.
	 *
	 * @since 1.0.0
	 */
	public function options_page() {
		$this->container->make( Settings::class )->options_page();
	}

	/**
	 * Registers admin css.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_assets() {
		$this->container->make( Assets::class )->register_admin_assets();
	}

	/**
	 * Enqueue custom post type admin assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_posttype_assets() {
		$this->container->make( Assets::class )->enqueue_admin_posttype_assets();
	}

	/**
	 * Saves post session details.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_post_session( $post_id, $post ) {
		$this->container->make( Session_Meta::class )->save_post_session( $post_id, $post );
	}

	/**
	 * Adds the session information meta box.
	 *
	 * @since 1.0.0
	 */
	public function session_metabox() {
		$this->container->make( Session_Meta::class )->session_metabox();
	}

	/**
	 * Adds meta boxes for the session post type.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		$this->container->make( Session_Meta::class )->add_meta_boxes();
	}

	/**
	 * Adds the speaker information meta box.
	 *
	 * @since 1.0.0
	 */
	public function speaker_metabox() {
		$this->container->make( Speaker_Meta::class )->speaker_metabox();
	}

	/**
	 * Adds the sponsor information meta box.
	 *
	 * @since 1.0.0
	 */
	public function sponsor_metabox() {
		$this->container->make( Sponsor_Meta::class )->sponsor_metabox();
	}

	public function sponsor_level_metabox() {
		$this->container->make( Sponsor_Meta::class )->sponsor_level_metabox();
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {
		add_filter( 'manage_tec_session_posts_columns',[ $this, 'manage_post_types_columns' ] );
		add_filter( 'manage_edit-tec_session_sortable_columns', [ $this, 'manage_sortable_columns' ] );
		add_filter( 'tec_filter_session_speaker_meta_field', [ $this, 'filter_session_speaker_meta_field' ] );
		add_filter( 'posts_clauses', [ $this, 'sort_by_tax' ], 10, 2 );
	}

	/**
	 * Adds or modifies the columns in the admin screen for custom post types.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns The existing columns.
	 *
	 * @return array The modified columns.
	 */
	public function manage_post_types_columns( array $columns ): array {
		return $this->container->make( Columns::class )->manage_post_types_columns( $columns );
	}

	/**
	 * Defines sortable columns in the admin screen.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sortable The existing sortable columns.
	 *
	 * @return array The modified sortable columns.
	 */
	public function manage_sortable_columns( array $sortable ): array {
		return $this->container->make( Columns::class )->manage_sortable_columns( $sortable );
	}

	/**
	 * Sorts posts by taxonomy terms in the admin list.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string|mixed> $clauses SQL clauses for fetching posts.
	 * @param WP_Query $wp_query The WP_Query object.
	 *
	 * @return array<string|mixed> Modified SQL clauses.
	 */
	public function sort_by_tax( Array $clauses, WP_Query $wp_query ): array {
		return $this->container->make( Columns::class )->sort_by_tax( $clauses, $wp_query );
	}

	/**
	 * Filters session speaker meta field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $cmb The current CMB2 box object.
	 */
	public function filter_session_speaker_meta_field( $cmb ) {
		return $this->container->make( Speaker_Meta::class )->filter_session_speaker_meta_field( $cmb );
	}
}
