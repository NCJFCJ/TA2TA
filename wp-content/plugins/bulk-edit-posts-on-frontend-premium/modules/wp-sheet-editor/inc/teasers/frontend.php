<?php defined( 'ABSPATH' ) || exit;
if (!class_exists('WP_Sheet_Editor_Frontend_Teaser')) {

	/**
	 * Display frontend item in the toolbar to tease users of the free 
	 * version into purchasing the premium plugin.
	 */
	class WP_Sheet_Editor_Frontend_Teaser {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {

			if (class_exists('WP_Sheet_Editor_Frontend_Editor')) {
				return;
			}
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar_items'));
		}

		function register_toolbar_items($editor) {

			if (!empty(VGSE()->options['be_disable_extension_offerings'])) {
				return;
			}
			$post_types = $editor->args['enabled_post_types'];
			foreach ($post_types as $post_type) {
				$editor->args['toolbars']->register_item('share_frontend', array(
					'type' => 'button',
					'content' => __('Display spreadsheet editor on the frontend', 'vg_sheet_editor' ),
					'extra_html_attributes' => 'data-remodal-target="modal-frontend-teaser"',
					'toolbar_key' => 'primary',
					'footer_callback' => array($this, 'render_popup'),
					'allow_in_frontend' => false,
					'parent' => 'share'
						), $post_type);
			}
		}

		function render_popup($current_post_type) {
			?>

			<style>
				.vg-naked-list {	
					list-style: initial;
					text-align: left;
					margin-left: 30px;
				}
			</style>
			<div class="remodal" data-remodal-id="modal-frontend-teaser" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

				<div class="modal-content">
					<h3><?php _e('Frontend Spreadsheets', 'vg_sheet_editor' ); ?></h3>

					<p><?php _e('We have an extension for displaying this spreadsheet on the frontend. You can select the columns to display, and select the spreadsheet tools for the frontend users (search, bulk, edit, import, export, etc.). For example:', 'vg_sheet_editor' ); ?></p>

					<ul class="vg-naked-list">
						<li><?php _e('Allow your clients to edit WooCommerce Products using the spreadsheet without wp-admin', 'vg_sheet_editor' ); ?></li>
						<li><?php _e('Allow your readers to submit blog posts using the spreadsheet', 'vg_sheet_editor' ); ?></li>
						<li><?php _e('Allow your visitors to publish events', 'vg_sheet_editor' ); ?></li>
						<li><?php _e('Allow your store employees to manage stock and prices', 'vg_sheet_editor' ); ?></li>
						<li><?php _e('Allow your marketplace sellers to import products on the frontend', 'vg_sheet_editor' ); ?></li>
						<li><?php _e('Allow your marketplace sellers to bulk edit their products', 'vg_sheet_editor' ); ?></li>
						<li><?php _e('Allow your store buyers to download the catalog', 'vg_sheet_editor' ); ?></li>
						<li><?php _e('Allow your store customers to make advanced catalog searches ', 'vg_sheet_editor' ); ?></li>
						<li><?php _e('And more.', 'vg_sheet_editor' ); ?></li>						
					</ul>
					<hr>
					<h3><?php _e('Demo video', 'vg_sheet_editor' ); ?></h3>
					<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/kEovWuNImok?start=24" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>					
					<p><?php _e('All the features available in the backend spreadsheet can be used in the frontend spreadsheet.', 'vg_sheet_editor' ); ?></p>
				</div>
				<br>
				<a href="<?php echo esc_url(VGSE()->get_site_link('https://wpsheeteditor.com/extensions/frontend-spreadsheet-editor/', 'frontend-teaser')); ?>" class="remodal-confirm" target="_blank"><?php _e('Buy extension now!', 'vg_sheet_editor' ); ?></a>
				<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', 'vg_sheet_editor' ); ?></button>
			</div>
			<?php
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 * 
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Frontend_Teaser::$instance) {
				WP_Sheet_Editor_Frontend_Teaser::$instance = new WP_Sheet_Editor_Frontend_Teaser();
				WP_Sheet_Editor_Frontend_Teaser::$instance->init();
			}
			return WP_Sheet_Editor_Frontend_Teaser::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}


add_action('vg_sheet_editor/initialized', 'vgse_init_frontend_teaser');

if (!function_exists('vgse_init_frontend_teaser')) {

	function vgse_init_frontend_teaser() {
		WP_Sheet_Editor_Frontend_Teaser::get_instance();
	}

}
