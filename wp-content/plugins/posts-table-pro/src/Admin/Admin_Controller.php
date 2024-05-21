<?php
namespace Barn2\Plugin\Posts_Table_Pro\Admin;

use Barn2\Plugin\Posts_Table_Pro\Util\Util,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Util as Lib_Util,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Service_Container,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Plugin\Plugin,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Plugin\Admin\Admin_Links;

/**
 * Handles general admin functions, such as adding links to our settings page in the Plugins menu.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements Registerable, Service, Conditional {

	use Service_Container;

	private $plugin;

	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function is_required() {
		return Lib_Util::is_admin();
	}

	public function register() {
		$this->register_services();

		add_action( 'admin_enqueue_scripts', [ $this, 'load_settings_page_scripts' ] );
	}

	public function get_services() {
		return [
			new Admin_Links( $this->plugin ),
			new Page_List(),
			new TinyMCE()
		];
	}

	public function load_settings_page_scripts( $hook ) {
		if ( 'post-tables_page_posts_table' !== $hook ) {
			return;
		}

		$suffix = Lib_Util::get_script_suffix();

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'ptp-admin', Util::get_asset_url( 'css/admin/admin.css' ), [], $this->plugin->get_version() );
		wp_enqueue_script( 'ptp-admin', Util::get_asset_url( "js/admin/posts-table-pro-admin.js" ), [ 'jquery', 'wp-color-picker' ], $this->plugin->get_version(), true );
	}

}
