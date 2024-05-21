<?php

namespace Barn2\Plugin\Posts_Table_Pro\Admin;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Plugin\Plugin_Activation_Listener,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Plugin\Licensed_Plugin,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Registerable;

/**
 * Plugin Setup
 *
 * @package   Barn2/posts-table-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin_Setup implements Plugin_Activation_Listener, Registerable {

	/**
	 * Instance of the plugin.
	 *
	 * @var Licensed_Plugin
	 */
	private $plugin;

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Constructor.
	 *
	 * @param mixed $file
	 * @param Licensed_Plugin $plugin
	 */
	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->slug   = $plugin->get_slug();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		register_activation_hook( $this->plugin->get_file(), [ $this, 'on_activate' ] );
		add_action( 'admin_init', [ $this, 'after_plugin_activation' ] );
	}

	/**
	 * On plugin activation
	 */
	public function on_activate() {
        $this->create_search_results_page();
		$this->maybe_redirect();
	}

	/**
	 * On plugin deactivation.
	 */
	public function on_deactivate() {}

    /**
     * Creates the search results page.
     */
    private function create_search_results_page() {
		$search_results_page_id = get_option( 'ptp_search_page' );

		if ( $search_results_page_id === false || get_post_status( (int) $search_results_page_id ) !== 'publish' ) {
			Lib_Util::create_page(
				_x( 'posts-search', 'Page slug', 'posts-table-pro' ),
				'ptp_search_page',
				_x( 'Search', 'Page title', 'posts-table-pro' )
			);
		}
    }

	/**
     * Determine if the transient was created.
     *
     * @return bool
     */
    public function detected() {
        return get_transient("_{$this->slug}_activation_redirect");
    }

    /**
     * Creates a short timed transient which is used to detect if the wizard should start.
     *
     * @return void
     */
    public function create_transient() {
        set_transient("_{$this->slug}_activation_redirect", \true, 30);
    }

    /**
     * Delete the short timed transient.
     *
     * @return void
     */
    public function delete_transient() {
        delete_transient("_{$this->slug}_activation_redirect");
    }

	/**
	 * Maybe redirect to the wizard on activation.
	 *
	 * @return void
	 */
	public function maybe_redirect() {
		if ( $this->plugin->has_valid_license() ) {
			return;
		}
		$this->create_transient();
	}

	/**
	 * Check if the transient has been found, if found redirect.
	 *
	 * @return void
	 */
	public function after_plugin_activation() {
		if ( ! $this->detected() ) {
			return;
		}

		$this->delete_transient();
		$this->redirect();
	}

	/**
	 * Redirect to the generator page with the `wizard` flag enabled.
	 *
	 * @return void
	 */
	public function redirect() {
		$url = admin_url( 'admin.php?page=' . $this->slug . '-table-generator-add-new&wizard=1' );
        wp_safe_redirect($url);
        exit;
	}

}
