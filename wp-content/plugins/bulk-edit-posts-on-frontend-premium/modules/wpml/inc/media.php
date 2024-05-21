<?php defined( 'ABSPATH' ) || exit;

if (!class_exists('WPSE_WPML_Media')) {

	class WPSE_WPML_Media {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {
			if( !  class_exists('WPML_Media_File_Factory')){
				return;
			}
			add_filter('vg_sheet_editor/media/sql_posts_containing_image_by_url', array(WP_Sheet_Editor_WPML_Obj(), 'filter_posts_query_by_language'));
			add_filter('vg_sheet_editor/media/total_unattached_images_sql', array(WP_Sheet_Editor_WPML_Obj(), 'filter_posts_query_by_language'));
			add_filter('vg_sheet_editor/media/featured_images_sql', array(WP_Sheet_Editor_WPML_Obj(), 'filter_posts_query_by_language'));
			add_filter('vg_sheet_editor/media/all_gallery_images_sql', array(WP_Sheet_Editor_WPML_Obj(), 'filter_posts_query_by_language'));
			add_filter('vg_sheet_editor/media/gallery_images_with_parent_sql', array(WP_Sheet_Editor_WPML_Obj(), 'filter_posts_query_by_language'));
			add_filter('vg_sheet_editor/media/unattached_image_ids_sql', array(WP_Sheet_Editor_WPML_Obj(), 'filter_posts_query_by_language'));
			add_filter('vg_sheet_editor/media/get_all_urls_related_to_image', array($this, 'get_urls_related_to_image'), 10, 2);
			add_action('vg_sheet_editor/editor/register_columns', array($this, 'register_columns'), 11);
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns($editor) {
			$post_types = $editor->args['enabled_post_types'];
			if (!in_array('attachment', $post_types, true)) {
				return;
			}
			foreach ($post_types as $post_type) {
				if (WP_Sheet_Editor_WPML_Obj()->is_the_default_language()) {
					$editor->args['columns']->register_item('wpml_duplicate', $post_type, array(
						'save_value_callback' => array($this, 'duplicate_to_language'),
							), true);
				}
			}
		}

		function duplicate_to_language($post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns) {
			global $sitepress, $wpdb, $wpse_wpml_media_translation;
			VGSE()->helpers->remove_all_post_actions($post_type);
			$original_image = get_post($post_id);
			$args = array(
				'translated-attachment-id' => null,
				'original-attachment-id' => $post_id,
				'restore-media' => 0,
				'update-media-file' => 0,
				'translation' => array(
					'title' => $original_image->post_title,
					'post_excerpt' => $original_image->post_excerpt,
					'post_content' => $original_image->post_content,
				)
			);
			$langs = array_filter(array_map('trim', explode(',', strtolower($data_to_save))));
			$original_POST = $_POST;

			if (!$wpse_wpml_media_translation) {
				$wpse_wpml_media_translation = new WPSE_WPML_Media_Fork($sitepress, $wpdb, new WPML_Media_File_Factory(), new WPML_Translation_Element_Factory($sitepress));
			}



			$trid = WP_Sheet_Editor_WPML_Obj()->get_main_translation_id($post_id, 'post_' . $post_type);
			$sql = $wpdb->prepare("SELECT language_code FROM " . $wpdb->prefix . "icl_translations WHERE element_type = %s AND trid = %d", 'post_' . $post_type, (int) $trid);
			$existing_translation_languages = array_filter($wpdb->get_col($sql));
			foreach ($langs as $lang) {
				if (in_array($lang, $existing_translation_languages, true)) {
					continue;
				}
				$args['translated-language'] = $lang;
				$_POST = $args;
				$response = $wpse_wpml_media_translation->save_media_translation();
			}
			$_POST = $original_POST;
		}

		function get_urls_related_to_image($urls, $attachment_id) {
			$original_attachment_id = WP_Sheet_Editor_WPML_Obj()->get_main_id($attachment_id, 'post_attachment');
			remove_filter('vg_sheet_editor/media/get_all_urls_related_to_image', array($this, 'get_urls_related_to_image'), 10);
			$urls = $GLOBALS['wpse_media_sheet_object']->_get_all_urls_related_to_image($original_attachment_id);
			add_filter('vg_sheet_editor/media/get_all_urls_related_to_image', array($this, 'get_urls_related_to_image'), 10, 2);
			return $urls;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_WPML_Media::$instance) {
				WPSE_WPML_Media::$instance = new WPSE_WPML_Media();
				WPSE_WPML_Media::$instance->init();
			}
			return WPSE_WPML_Media::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_WPML_Media_Obj')) {

	function WPSE_WPML_Media_Obj() {
		return WPSE_WPML_Media::get_instance();
	}

}
WPSE_WPML_Media_Obj();
