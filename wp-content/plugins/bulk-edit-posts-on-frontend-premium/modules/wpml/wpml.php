<?php defined( 'ABSPATH' ) || exit;

if (!class_exists('WP_Sheet_Editor_WPML')) {

	class WP_Sheet_Editor_WPML {

		static private $instance = false;

		private function __construct() {
			
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_WPML::$instance) {
				WP_Sheet_Editor_WPML::$instance = new WP_Sheet_Editor_WPML();
				WP_Sheet_Editor_WPML::$instance->init();
			}
			return WP_Sheet_Editor_WPML::$instance;
		}

		function init() {
			if (!defined('ICL_SITEPRESS_VERSION') || !interface_exists('IWPML_Action')) {
				return;
			}
			$files = VGSE()->helpers->get_files_list(__DIR__ . '/inc');
			foreach ($files as $file) {
				require_once $file;
			}
			add_filter('vg_sheet_editor/global_js_data', array($this, 'always_use_initial_language'));
			add_filter('vg_sheet_editor/data/taxonomy_terms/cache_key', array($this, 'modify_cache_key'));
		}

		function modify_cache_key($cache_key) {
			global $sitepress;
			return $cache_key . $sitepress->get_current_language();
		}

		function always_use_initial_language($settings) {
			global $sitepress;
			$settings['ajax_url'] = esc_url(add_query_arg('lang', $sitepress->get_current_language(), $settings['ajax_url']));
			return $settings;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

		function filter_posts_query_by_language($sql) {
			global $wpdb, $sitepress;

			if (strpos($sql, $wpdb->posts) === false && strpos($sql, $wpdb->postmeta) !== false) {
				$sql = str_replace(' WHERE ', " LEFT JOIN " . $wpdb->posts . " p 
ON $wpdb->postmeta.post_id = p.ID WHERE ", $sql);
			}

			$sql = str_replace(' WHERE ', " LEFT JOIN " . $wpdb->prefix . "icl_translations i
ON CONCAT('post_', p.post_type ) = i.element_type
AND i.element_id = p.ID
WHERE i.language_code  = '" . esc_sql($sitepress->get_current_language()) . "' AND ", $sql);
			return $sql;
		}

		function is_not_the_default_language() {
			global $sitepress;
			return $sitepress->get_default_language() !== $sitepress->get_current_language();
		}

		function get_main_id($translation_id, $type) {
			$original_id = (int) SitePress::get_original_element_id($translation_id, $type);
			return $original_id;
		}

		function get_main_translation_id($translation_id, $type, $is_original_id = false) {
			global $wpdb;
			$original_id = (!$is_original_id ) ? $this->get_main_id($translation_id, $type) : $translation_id;

			if (!$original_id) {
				return $original_id;
			}

			$id = (int) $wpdb->get_var($wpdb->prepare("SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_type = %s AND element_id = %d", $type, $original_id));
			return $id;
		}

		function is_the_default_language() {
			return !$this->is_not_the_default_language();
		}

	}

}


if (!function_exists('WP_Sheet_Editor_WPML_Obj')) {

	function WP_Sheet_Editor_WPML_Obj() {
		return WP_Sheet_Editor_WPML::get_instance();
	}

}


add_action('vg_sheet_editor/initialized', 'WP_Sheet_Editor_WPML_Obj');
