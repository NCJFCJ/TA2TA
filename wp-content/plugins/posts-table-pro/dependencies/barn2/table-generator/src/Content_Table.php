<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

use DateTime;
use JsonSerializable;
/**
 * The content table "model" represents a table generated and stored
 * into the database that has been created via the table generator library.
 */
class Content_Table implements JsonSerializable
{
    /**
     * ID of the table.
     *
     * @var int
     */
    public $id;
    /**
     * Name of the content table.
     *
     * @var string
     */
    public $title;
    /**
     * All the settings of the table.
     *
     * @var string
     */
    public $settings;
    /**
     * Whether the creation process of the table was completed.
     *
     * @var boolean
     */
    public $is_completed;
    /**
     * Initialize a content table model class.
     *
     * @param array<mixed> $data Data to create an model from.
     */
    public function __construct($data)
    {
        foreach ((array) $data as $key => $value) {
            $this->{$key} = $value;
        }
        if (!empty($this->id)) {
            $this->id = (int) $this->id;
        }
        if (!empty($this->title)) {
            $this->title = (string) $this->title;
        }
        $this->is_completed = $this->is_completed === '1';
    }
    /**
     * Get the ID of the content table.
     *
     * @return int
     */
    public function get_id()
    {
        return $this->id;
    }
    /**
     * Get the title of the content table.
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }
    /**
     * Get the settings array of the table.
     *
     * @return array
     */
    public function get_settings()
    {
        return \json_decode($this->settings, \true);
    }
    /**
     * Get a setting value.
     * Looks to see if the specified setting exists, returns default if not.
     *
     * @param string $key
     * @param midex $default
     * @return mixed
     */
    public function get_setting(string $key, $default = \false)
    {
        return isset($this->get_settings()[$key]) ? $this->get_settings()[$key] : $default;
    }
    /**
     * Determines if the table creation process was completed.
     *
     * @return boolean
     */
    public function is_completed()
    {
        return $this->is_completed;
    }
    /**
     * Get the content type assigned to the table.
     *
     * @return string
     */
    public function get_content_type($formatted = \false)
    {
        $type = $this->get_setting('content_type', \false);
        if ($formatted) {
            $types = Util::get_registered_post_types();
            return $types[$type] ?? '';
        }
        return $type;
    }
    /**
     * Get the names of the columns of the table.
     *
     * @return array
     */
    public function get_columns_names()
    {
        $columns = $this->get_setting('columns', []);
        $names = [];
        foreach ($columns as $column) {
            if (!isset($column['name'])) {
                continue;
            }
            $names[] = $column['name'];
        }
        return $names;
    }
    /**
     * Returns a formatted list of all items selected via
     * the "refinements" parameter.
     *
     * @return array
     */
    public function get_selection()
    {
        $selection = [];
        $includes = $this->get_setting('refine', []);
        if (empty($includes)) {
            return [];
        }
        // get the "refinements" property from the $includes array
        $includes = $includes['refinements'] ?? [];
        $default_taxonomies = ['category', 'post_tag', 'tag'];
        foreach ($includes as $type => $values) {
            if (\in_array($type, $default_taxonomies, \true) || \taxonomy_exists($type)) {
                $taxonomy = $type;
                if ($taxonomy === 'tag') {
                    $taxonomy = 'post_tag';
                }
                if (!\taxonomy_exists($taxonomy)) {
                    continue;
                }
                $terms = isset($values['terms']) ? $values['terms'] : [];
                $ids = \array_map(function ($term) {
                    return $term['value'];
                }, $terms);
                $selection[] = ['name' => Util::get_taxonomy_name($taxonomy), 'values' => Util::get_formatted_taxonomy_terms($taxonomy, $ids)];
            } elseif ($type === 'term') {
                $selected_taxonomies = $values;
                foreach ($selected_taxonomies as $taxonomy => $ids) {
                    if (!\taxonomy_exists($taxonomy)) {
                        continue;
                    }
                    $selection[] = ['name' => Util::get_taxonomy_name($taxonomy), 'values' => Util::get_formatted_taxonomy_terms($taxonomy, isset($ids['ids']) ? $ids['ids'] : $ids)];
                }
            } elseif ($type === 'status') {
                $names = [];
                $stati = isset($values['stati']) ? $values['stati'] : [];
                $stati = \array_map(function ($status) {
                    return $status['value'];
                }, $stati);
                foreach ($stati as $status) {
                    $label = Util::get_formatted_post_status_name($status);
                    if ($label) {
                        $names[] = $label;
                    }
                }
                if (!empty($names)) {
                    $selection[] = ['name' => __('Status','posts-table-pro' ), 'values' => $names];
                }
            } elseif ($type === 'cf') {
                $selection[] = ['name' => __('Custom fields','posts-table-pro' ), 'values' => Util::get_formatted_custom_fields($values)];
            } elseif ($type === 'date') {
                $day = isset($values['day']) ? $values['day'] : \false;
                $month = isset($values['month']) ? $values['month'] : \false;
                $year = isset($values['year']) ? $values['year'] : \false;
                $date = DateTime::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}");
                if ($date instanceof DateTime) {
                    $selection[] = ['name' => __('Date','posts-table-pro' ), 'values' => $date->format(\get_option('date_format', 'Y-m-d'))];
                }
            } elseif ($type === 'include') {
                $post_type = $this->get_content_type();
                $ids = \array_map(function ($item) {
                    return $item['value'];
                }, $values);
                $posts = Util::get_formatted_post_names($post_type, $ids);
                if (!empty($posts)) {
                    $selection[] = ['name' => $this->get_content_type(\true), 'values' => $posts];
                }
            }
        }
        return $selection;
    }
    /**
     * Get the list of items selected for the "include" parameter.
     *
     * @return array
     */
    public function get_include_pool()
    {
        $refine = $this->get_setting('refine', []);
        $refinements = $refine['refinements'] ?? [];
        return \array_keys($refinements);
    }
    /**
     * Get the list of items selected for the "exclude" parameter.
     *
     * @return array
     */
    public function get_exclude_pool()
    {
        $excludes = $this->get_setting('exclude', []);
        return \array_keys($excludes);
    }
    /**
     * Get the settigs inside the "include" parameter.
     *
     * @param string $type
     * @return mixed
     */
    public function get_inclusion(string $type)
    {
        $refine = $this->get_setting('refine', []);
        $includes = $refine['refinements'] ?? [];
        return isset($includes[$type]) ? $includes[$type] : \false;
    }
    /**
     * Get the settings inside the "exclude" parameter.
     *
     * @param string $type
     * @return mixed
     */
    public function get_exclusion(string $type)
    {
        $excludes = $this->get_setting('exclude');
        return isset($excludes[$type]) ? $excludes[$type] : \false;
    }
    /**
     * Get the value of a parameter.
     *
     * @param string $key
     * @param bool $exclusion
     * @return mixed
     */
    public function get_parameter(string $key, bool $exclusion = \false)
    {
        if ($exclusion) {
            return $this->get_exclusion($key);
        }
        return $this->get_inclusion($key);
    }
    /**
     * Determine if the table supports categories.
     * Categories are currently only supported by the "post"
     * post type.
     *
     * @return bool
     */
    public function supports_categories()
    {
        return $this->get_content_type() === 'post' && (\in_array('category', $this->get_include_pool(), \true) || \in_array('category', $this->get_exclude_pool(), \true));
    }
    /**
     * Get the list of categories assigned to the table.
     *
     * @param boolean $as_string as_string Whether or not we should be returning the list as a comma saparated string.
     * @param bool $exclusion whether or not we should look into the "exclude" pool.
     * @return array|string
     */
    public function get_categories(bool $as_string = \false, bool $exclusion = \false)
    {
        $categories = $this->get_parameter('category', $exclusion);
        if (!$categories) {
            return $as_string ? '' : [];
        }
        $included = \array_map(function ($category) {
            return \absint($category['value']);
        }, $categories['terms'] ?? []);
        return $as_string ? \implode(',', $included) : $included;
    }
    /**
     * Determine if the table supports tags.
     * Tags are supported only by the "post" post type.
     *
     * @return bool
     */
    public function supports_tags()
    {
        return $this->get_content_type() === 'post' && (\in_array('post_tag', $this->get_include_pool(), \true) || \in_array('post_tag', $this->get_exclude_pool(), \true));
    }
    /**
     * Get the list of tags assigned to the table.
     *
     * @param boolean $as_string as_string Whether or not we should be returning the list as a comma saparated string.
     * @param bool $exclusion whether or not we should look into the "exclude" pool.
     * @return array|string
     */
    public function get_tags(bool $as_string = \false, bool $exclusion = \false)
    {
        $tags = $this->get_parameter('post_tag', $exclusion);
        if (!$tags) {
            return $as_string ? '' : [];
        }
        $included = \array_map(function ($tag) {
            return \absint($tag['value']);
        }, $tags['terms'] ?? []);
        return $as_string ? \implode(',', $included) : $included;
    }
    /**
     * Get the post status assigned to the table.
     *
     * @param boolean $exclusion
     * @return array
     */
    public function get_post_status(bool $as_string = \false, bool $exclusion = \false)
    {
        $status = $this->get_parameter('status', $exclusion) ?? [];
        if (!$status) {
            return $as_string ? '' : [];
        }
        $included = \array_map(function ($status) {
            return $status['value'];
        }, $status['stati'] ?? []);
        return $as_string ? \implode(',', $included) : $included;
    }
    /**
     * Get the author assigned to the table.
     *
     * @param boolean $as_string as_string Whether or not we should be returning the list as a comma saparated string.
     * @param bool $exclusion whether or not we should look into the "exclude" pool.
     * @return array|string
     */
    public function get_author(bool $as_string = \false, bool $exclusion = \false)
    {
        $authors = $this->get_parameter('author', $exclusion);
        if (!$authors) {
            return $as_string ? '' : [];
        }
        $included = \array_map(function ($author) {
            return \absint($author['value']);
        }, $authors ?? []);
        return $as_string ? \implode(',', $included) : $included;
    }
    /**
     * Get the specific ids of posts assigned to the table.
     *
     * @param boolean $as_string as_string Whether or not we should be returning the list as a comma saparated string.
     * @param bool $exclusion whether or not we should look into the "exclude" pool.
     * @return array|string
     */
    public function get_specific_ids(bool $as_string = \false, bool $exclusion = \false)
    {
        $ids = $this->get_parameter('include', $exclusion);
        if (!$ids) {
            return $as_string ? '' : [];
        }
        $included = \array_map(function ($post) {
            return \absint($post['value']);
        }, $ids ?? []);
        return $as_string ? \implode(',', $included) : $included;
    }
    /**
     * Get the mime type assigned to the table.
     *
     * @param boolean $as_string as_string Whether or not we should be returning the list as a comma saparated string.
     * @param bool $exclusion whether or not we should look into the "exclude" pool.
     * @return array|string
     */
    public function get_mime_type(bool $as_string = \false, bool $exclusion = \false)
    {
        $mime = $this->get_parameter('mime', $exclusion);
        if (!$mime) {
            return $as_string ? '' : [];
        }
        return $as_string ? \implode(',', $mime) : $mime;
    }
    /**
     * Get the year assigned to the table.
     *
     * @return string
     */
    public function get_year()
    {
        return $this->get_parameter('date')['year'] ?? '';
    }
    /**
     * Get the month assigned to the table.
     *
     * @return string
     */
    public function get_month()
    {
        return $this->get_parameter('date')['month'] ?? '';
    }
    /**
     * Get the day assigned to the table.
     *
     * @return string
     */
    public function get_day()
    {
        return $this->get_parameter('date')['day'] ?? '';
    }
    /**
     * Get the list of valid custom fields assigned to the table.
     *
     * @param boolean $as_string Whether or not we should be returning the list as a comma saparated string.
     * @return array|string
     */
    public function get_custom_fields(bool $as_string = \false)
    {
        $fields = $this->get_parameter('cf') ?? [];
        if ($as_string) {
            $prepared = [];
            foreach ($fields as $field) {
                $prepared[] = $field['name'] . ':' . $field['value'];
            }
            return \implode(',', $prepared);
        }
        return $fields;
    }
    /**
     * Get the list of terms for the table.
     *
     * @param boolean $as_string
     * @param boolean $exclusion
     * @return array|string
     */
    public function get_terms(bool $as_string = \false, bool $exclusion = \false)
    {
        $terms = $this->get_possible_terms();
        if (!\is_array($terms)) {
            $terms = [];
        }
        $include_match_all = $this->get_setting('include_match_taxonomies', \false);
        if ($as_string) {
            $prepared = [];
            $taxonomies = \array_keys($terms);
            foreach ($taxonomies as $taxonomy) {
                $match_all = isset($terms[$taxonomy]['match']) ? $terms[$taxonomy]['match'] : \false;
                $matching_symbol = $match_all ? '+' : ',';
                $taxonomy_terms = \implode($matching_symbol, $terms[$taxonomy]['ids']);
                $prepared[] = $taxonomy . ':' . $taxonomy_terms;
            }
            $completed_string = \implode($include_match_all ? '+' : ',', $prepared);
            return $completed_string;
        }
        return $terms;
    }
    /**
     * Get the list of possible terms for the table.
     *
     * @return array
     */
    private function get_possible_terms()
    {
        $refine = $this->get_setting('refine', []);
        $includes = $refine['refinements'] ?? [];
        $post_type = $this->get_content_type();
        $possible_taxonomies = \array_keys($includes);
        $registered_taxonomies = Util::get_registered_taxonomies($post_type);
        if (empty($possible_taxonomies) || empty($registered_taxonomies)) {
            return [];
        }
        $terms = [];
        $possible_taxonomies = \array_filter($possible_taxonomies, function ($taxonomy) use($registered_taxonomies) {
            return \array_key_exists($taxonomy, $registered_taxonomies);
        });
        if (empty($possible_taxonomies)) {
            return $terms;
        }
        // Get the parameters for each taxonomy.
        foreach ($possible_taxonomies as $taxonomy) {
            $parameter = $this->get_parameter($taxonomy);
            if (!$parameter) {
                continue;
            }
            $terms[$taxonomy] = ['ids' => \array_map(function ($term) {
                return \absint($term['value']);
            }, $parameter['terms'] ?? []), 'match' => $parameter['match'] ?? \false];
        }
        return $terms;
    }
    /**
     * Get the list of columns and their titles.
     *
     * @param boolean $as_string
     * @return array|string
     */
    public function get_columns(bool $as_string = \false)
    {
        $columns = [];
        $selected_columns = $this->get_setting('columns', []);
        if (!$as_string) {
            return $selected_columns;
        }
        foreach ($selected_columns as $column) {
            $is_custom_field = isset($column['slug']) && $column['slug'] === 'cf';
            $name = isset($column['settings']['visibility']) && $column['settings']['visibility'] === 'false' ? 'blank' : $column['name'];
            if ($is_custom_field) {
                $columns[] = "{$column['slug']}:{$column['settings']['input']}:{$name}";
            } else {
                $columns[] = "{$column['slug']}:{$name}";
            }
        }
        return \implode(',', $columns);
    }
    /**
     * Return the list of filters assigned to the table.
     *
     * @param boolean $as_string
     * @return array|string
     */
    public function get_filters(bool $as_string = \false)
    {
        $filters = \false;
        $filter_mode = $this->get_setting('filter_mode', 'disabled');
        if (!$filter_mode || $filter_mode === 'disabled') {
            return \false;
        }
        if ($filter_mode === 'columns') {
            return \true;
        }
        if ($filter_mode === 'custom') {
            $filters = $this->get_setting('filters', []);
            $formatted = [];
            foreach ($filters as $filter) {
                $name = $filter['settings']['input'] ?: ($filter['name'] ?: \false);
                $tax = $filter['slug'];
                if (empty($name)) {
                    continue;
                }
                $formatted[] = $tax . ':' . $name;
            }
            return $as_string ? \implode(',', $formatted) : $filters;
        }
        return $filters;
    }
    /**
     * Prepare json output.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['id' => $this->get_id(), 'title' => $this->get_title(), 'settings' => $this->get_settings(), 'content_type' => $this->get_content_type(\true), 'columns_names' => $this->get_columns_names(), 'selection' => $this->get_selection()];
    }
}
