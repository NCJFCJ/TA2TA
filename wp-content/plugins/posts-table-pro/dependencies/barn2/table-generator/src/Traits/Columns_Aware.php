<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Table_Generator;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Util;
use Barn2\Plugin\Easy_Post_Types_Fields\Util as EPT_Util;
/**
 * The trait provides support for inheritance of columns
 * from plugins that make use of the library.
 *
 * In order to provide a formatted list of supported columns,
 * the class making use of this trait, must also make use of the Generator_Aware trait.
 *
 * Columns are then inherited from the `get_default_columns` method of the generator.
 */
trait Columns_Aware
{
    /**
     * Get list of supported columns.
     *
     * @param string|boolean $content_type use to retrieve columns supported by the given content type only.
     * @return array
     */
    public function get_columns_list($content_type = '')
    {
        /** @var Table_Generator $generator */
        $generator = $this->get_generator();
        $columns = $generator->get_default_columns();
        $formatted = [];
        foreach ($columns as $key => $column) {
            $formatted[$key] = $column['heading'];
            if ($key === 'button') {
                $formatted[$key] = __('Button','posts-table-pro' );
            } else {
                if ($key === 'date_modified') {
                    $formatted[$key] = __('Last modified date','posts-table-pro' );
                }
            }
        }
        if (!empty($content_type)) {
            if (!\post_type_supports($content_type, 'author')) {
                unset($formatted['author']);
            }
            if (!\post_type_supports($content_type, 'excerpt')) {
                unset($formatted['excerpt']);
            }
            if (!\post_type_supports($content_type, 'thumbnail')) {
                unset($formatted['image']);
            }
            if ($content_type !== 'post') {
                unset($formatted['categories']);
                unset($formatted['tags']);
            }
        }
        // Inject registered taxonomies.
        $taxonomies = $this->get_taxonomies($content_type);
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $key => $label) {
                $formatted = Util::insert_into_array($formatted, 'status', $key, $label, \false, \true);
            }
        }
        // Inject registered custom fields.
        $custom_fields = $this->get_custom_fields($content_type);
        $formatted = \array_merge($formatted, $custom_fields);
        $formatted['cf'] = __('Additional custom field','posts-table-pro' );
        return $formatted;
    }
    /**
     * Get a formatted list of taxonomies for the given post type.
     *
     * @param string $post_type
     * @return array
     */
    private function get_taxonomies($post_type)
    {
        $registered = Util::get_registered_taxonomies($post_type);
        $taxonomies = [];
        foreach ($registered as $taxonomy) {
            if ($post_type === 'post' && \in_array($taxonomy->name, ['category', 'post_tag'], \true)) {
                continue;
            }
            $taxonomies['tax:' . $taxonomy->name] = $taxonomy->label;
        }
        return $taxonomies;
    }
    /**
     * Get custom fields.
     *
     * @param string $post_type
     * @return array
     */
    private function get_custom_fields($post_type)
    {
        $custom_fields = [];
        // Get ACF custom fields.
        if (\class_exists('ACF')) {
            $custom_fields = $this->get_acf_custom_fields($post_type);
        }
        // Get Easy Post Types custom fields.
        if (\class_exists('Barn2\\Plugin\\Easy_Post_Types_Fields\\Util')) {
            $custom_fields = \array_merge($custom_fields, $this->get_ept_custom_fields($post_type));
        }
        // Sort fields by name.
        \asort($custom_fields);
        return $custom_fields;
    }
    /**
     * Get ACF custom fields.
     *
     * @param string $post_type
     * @return array
     */
    private function get_acf_custom_fields($post_type)
    {
        $acf_fields = [];
        $groups = acf_get_field_groups(['post_type' => $post_type]);
        foreach ((array) $groups as $group) {
            $fields = acf_get_fields($group['key']);
            foreach ((array) $fields as $field) {
                $acf_fields['cf:' . $field['name']] = $field['label'];
            }
        }
        return $acf_fields;
    }
    /**
     * Get Easy Post Types custom fields.
     *
     * @param string $post_type
     * @return array
     */
    private function get_ept_custom_fields($post_type)
    {
        $ept_fields = [];
        $fields = EPT_Util::get_custom_fields($post_type);
        foreach ((array) $fields as $field) {
            $ept_fields['cf:' . $field['slug']] = $field['name'];
        }
        return $ept_fields;
    }
}
