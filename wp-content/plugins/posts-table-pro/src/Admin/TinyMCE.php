<?php
namespace Barn2\Plugin\Posts_Table_Pro\Admin;

use Barn2\Plugin\Posts_Table_Pro\Util\Util,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Util as Lib_Util;

/**
 * This class handles our TinyMCE toolbar button.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class TinyMCE implements Registerable, Conditional {

	public function is_required() {
		return Lib_Util::is_admin();
	}

	public function register() {
		add_action( 'init', [ $this, 'maybe_add_mce_button' ] );
	}

	public function maybe_add_mce_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' !== get_user_option( 'rich_editing' ) ) {
			return;
		}

		add_filter( 'mce_external_plugins', [ $this, 'add_tinymce_plugin' ] );
		add_filter( 'mce_buttons_2', [ $this, 'add_tinymce_button' ] );
	}

	public function add_tinymce_plugin( $plugins ) {
		$suffix                   = Lib_Util::get_script_suffix();
		$plugins['poststablepro'] = Util::get_asset_url( "js/admin/tinymce-posts-table.js" );
		return $plugins;
	}

	public function add_tinymce_button( $buttons ) {
		array_push( $buttons, 'poststablepro' );
		return $buttons;
	}

}
