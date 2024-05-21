<?php

namespace IAWP;

/** @internal */
class Report_Options_Parser
{
    private $attributes;
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }
    public function get_options_for_creating() : array
    {
        return \array_merge(['name' => $this->get_string_option('name'), 'type' => $this->get_string_option('type')], $this->get_options());
    }
    public function get_options_for_updating() : array
    {
        return $this->add_update_side_effects($this->get_options());
    }
    private function get_options() : array
    {
        return $this->strip_empty_options(['visible_datasets' => $this->get_array_option('visible_datasets'), 'exact_start' => $this->get_string_option('exact_start'), 'exact_end' => $this->get_string_option('exact_end'), 'relative_range_id' => $this->get_string_option('relative_range_id'), 'columns' => $this->get_array_option('columns'), 'filters' => $this->get_filters(), 'sort_column' => $this->get_string_option('sort_column'), 'sort_direction' => $this->get_string_option('sort_direction'), 'group_name' => $this->get_string_option('group_name'), 'chart_interval' => $this->get_string_option('chart_interval')]);
    }
    private function add_update_side_effects(array $options) : array
    {
        if (\array_key_exists('exact_start', $options) && !\is_null($options['exact_start']) && \array_key_exists('exact_end', $options) && !\is_null($options['exact_end'])) {
            $options['relative_range_id'] = null;
            $options['chart_interval'] = null;
        }
        if (\array_key_exists('relative_range_id', $options) && !\is_null($options['relative_range_id'])) {
            $options['exact_start'] = null;
            $options['exact_end'] = null;
            $options['chart_interval'] = null;
        }
        return $options;
    }
    private function strip_empty_options(array $options) : array
    {
        return \array_filter($options, function ($option) {
            return !$option instanceof \IAWP\Empty_Report_Option;
        });
    }
    /**
     * @param string $key
     *
     * @return string|Empty_Report_Option
     */
    private function get_string_option(string $key)
    {
        if (!\array_key_exists($key, $this->attributes) || !\is_string($this->attributes[$key])) {
            return new \IAWP\Empty_Report_Option();
        }
        return \sanitize_text_field($this->attributes[$key]);
    }
    /**
     * @param string $key
     *
     * @return array|Empty_Report_Option
     */
    private function get_array_option(string $key)
    {
        if (!\array_key_exists($key, $this->attributes) || !\is_array($this->attributes[$key])) {
            return new \IAWP\Empty_Report_Option();
        }
        foreach ($this->attributes[$key] as $visible_dataset) {
            if (!\is_string($visible_dataset)) {
                return new \IAWP\Empty_Report_Option();
            }
        }
        return \array_map(function ($item) {
            return \sanitize_text_field($item);
        }, $this->attributes[$key]);
    }
    /**
     * @return array|array[]|Empty_Report_Option
     */
    private function get_filters()
    {
        if (!\array_key_exists('filters', $this->attributes) || !\is_array($this->attributes['filters'])) {
            return new \IAWP\Empty_Report_Option();
        }
        foreach ($this->attributes['filters'] as $filter) {
            if (!\is_array($filter)) {
                return new \IAWP\Empty_Report_Option();
            }
            if (\array_keys($filter) !== ['inclusion', 'column', 'operator', 'operand']) {
                return new \IAWP\Empty_Report_Option();
            }
            foreach ($filter as $value) {
                if (!\is_string($value)) {
                    return new \IAWP\Empty_Report_Option();
                }
            }
        }
        return \array_map(function ($filter) {
            return ['inclusion' => \sanitize_text_field($filter['inclusion']), 'column' => \sanitize_text_field($filter['column']), 'operator' => \sanitize_text_field($filter['operator']), 'operand' => \sanitize_text_field($filter['operand'])];
        }, $this->attributes['filters']);
    }
    public static function from_json(string $json) : \IAWP\Report_Options_Parser
    {
        return new \IAWP\Report_Options_Parser(\json_decode(\stripslashes($json), \true));
    }
}
