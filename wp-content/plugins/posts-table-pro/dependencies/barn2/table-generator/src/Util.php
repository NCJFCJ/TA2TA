<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

/**
 * Utility methods.
 */
class Util
{
    /**
     * Sanitize anything.
     *
     * @param mixed $var the thing to sanitize.
     * @return mixed
     */
    public static function clean($var)
    {
        if (\is_array($var)) {
            return \array_map('self::clean', $var);
        } elseif (\is_scalar($var) && !\is_bool($var)) {
            return \sanitize_text_field($var);
        } else {
            return $var;
        }
    }
    /**
     * Get a list of registered post types.
     *
     * @return array
     */
    public static function get_registered_post_types()
    {
        // Get all post types as objects.
        $registered_types = \get_post_types([], 'objects');
        // Internal WP post types.
        $internal_pts = ['revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'wp_block'];
        // CPTs added by plugins which are not relevant or unsupported.
        $unsupported_pts = ['acf-field', 'acf-field-group', 'nf_sub', 'edd_log', 'edd_payment', 'edd_discount', 'product_variation', 'shop_order_refund', 'tribe-ea-record', 'deleted_event', 'user_request', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation', 'shop_coupon', 'shop_order_placehold', 'ept_post_type'];
        $registered_types = \array_diff_key($registered_types, \array_flip(\array_merge($internal_pts, $unsupported_pts)));
        $names = [];
        foreach ($registered_types as $post_type => $post_type_obj) {
            $names[$post_type] = $post_type_obj->labels->name;
        }
        return $names;
    }
    /**
     * Parse an array and format it to match the pattern supported by the react
     * select control component.
     *
     * @param array $array
     * @return array
     */
    public static function parse_array_for_dropdown(array $array)
    {
        $values = [];
        foreach ($array as $key => $value) {
            $values[] = ['value' => $key, 'label' => \html_entity_decode($value)];
        }
        return $values;
    }
    /**
     * Recursively remove a key from an array.
     *
     * @param string $haystack
     * @param array $needle
     * @return array
     */
    public static function array_unset_recursive($haystack, $needle)
    {
        if (\is_array($haystack)) {
            unset($haystack[$needle]);
            foreach ($haystack as $k => $value) {
                $haystack[$k] = self::array_unset_recursive($value, $needle);
            }
        }
        return $haystack;
    }
    /**
     * Returns an array of formatted taxonomy terms.
     *
     * @param string $taxonomy
     * @param array $ids
     * @return array
     */
    public static function get_formatted_taxonomy_terms($taxonomy, $ids)
    {
        return \get_terms(['taxonomy' => $taxonomy, 'hide_empty' => \false, 'fields' => 'id=>name', 'include' => $ids]);
    }
    /**
     * Get the human readable taxonomy name.
     *
     * @param string $taxonomy
     * @return string|boolean
     */
    public static function get_taxonomy_name($taxonomy)
    {
        if ($taxonomy === 'categories') {
            $taxonomy = 'category';
        } elseif ($taxonomy === 'tags') {
            $taxonomy = 'post_tag';
        }
        $taxonomy = \get_taxonomy($taxonomy);
        return isset($taxonomy->label) ? $taxonomy->label : \false;
    }
    /**
     * Get the name of a post status.
     *
     * @param string $status
     * @return string|boolean
     */
    public static function get_formatted_post_status_name($status)
    {
        $stati = \get_post_stati([], 'objects');
        return isset($stati[$status]) ? $stati[$status]->label : \false;
    }
    /**
     * Get the formatted list of custom fields.
     *
     * @param array $fields
     * @return array
     */
    public static function get_formatted_custom_fields($fields)
    {
        return \array_map(function ($field) {
            return $field['name'];
        }, $fields);
    }
    /**
     * Get the formatted list of names of posts.
     *
     * @param string $post_type
     * @param array $ids
     * @return array
     */
    public static function get_formatted_post_names($post_type, $ids)
    {
        $query = new \WP_Query(['post_type' => $post_type, 'post__in' => $ids]);
        $titles = [];
        $posts = $query->get_posts();
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $titles[] = $post->post_title;
            }
        }
        return $titles;
    }
    /**
     * Determine if a taxonomy has at least one term.
     *
     * @param string $taxonomy
     * @return boolean
     */
    public static function taxonomy_has_terms($taxonomy)
    {
        $terms = \get_terms($taxonomy, ['number' => 1, 'hide_empty' => \true]);
        return \is_array($terms) && \count($terms) > 0;
    }
    /**
     * Verify if a string ends with the given characters.
     *
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    public static function string_ends_with($haystack, $needle)
    {
        return \substr_compare($haystack, $needle, -\strlen($needle)) === 0;
    }
    /**
     * Check if a string starts with a specific subset.
     *
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    public static function string_starts_with($haystack, $needle)
    {
        return \strpos($haystack, $needle) === 0;
    }
    /**
     * Get the list of registered taxonomies for the given post type.
     *
     * @param string $post_type
     * @return array
     */
    public static function get_registered_taxonomies(string $post_type)
    {
        $taxonomies = \get_object_taxonomies(\sanitize_text_field($post_type), 'objects');
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy => $config) {
                if (!self::taxonomy_has_terms($taxonomy)) {
                    unset($taxonomies[$taxonomy]);
                }
            }
        }
        return $taxonomies;
    }
    /**
     * Insert items into an array at the specified position.
     *
     * @param array $array
     * @param string $search_key
     * @param string $insert_key
     * @param mixed $insert_value
     * @param boolean $insert_after_founded_key
     * @param boolean $append_if_not_found
     * @return array
     */
    public static function insert_into_array($array, $search_key, $insert_key, $insert_value, $insert_after_founded_key = \true, $append_if_not_found = \false)
    {
        $new_array = [];
        foreach ($array as $key => $value) {
            if ($key === $search_key && !$insert_after_founded_key) {
                $new_array[$insert_key] = $insert_value;
            }
            $new_array[$key] = $value;
            if ($key === $search_key && $insert_after_founded_key) {
                $new_array[$insert_key] = $insert_value;
            }
        }
        if ($append_if_not_found && \count($array) == \count($new_array)) {
            $new_array[$insert_key] = $insert_value;
        }
        return $new_array;
    }
}
