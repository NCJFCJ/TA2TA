<?php

namespace IAWP\Rows;

use IAWP\Filter_Lists\Author_Filter_List;
use IAWP\Filter_Lists\Category_Filter_List;
use IAWP\Filter_Lists\Device_Browser_Filter_List;
use IAWP\Filter_Lists\Device_OS_Filter_List;
use IAWP\Filter_Lists\Device_Type_Filter_List;
use IAWP\Filter_Lists\Page_Type_Filter_List;
use IAWP\Filter_Lists\Referrer_Type_Filter_List;
use IAWP\Utils\WordPress_Site_Date_Format_Pattern;
use IAWPSCOPED\Illuminate\Database\Query\Builder;
use JsonSerializable;
/** @internal */
class Filter implements JsonSerializable
{
    private $filter;
    public function __construct(array $filter)
    {
        $this->filter = $filter;
    }
    public function filter() : array
    {
        return $this->filter;
    }
    public function apply_to_query(Builder $query) : void
    {
        if ($this->filter['column'] === 'category') {
            $this->apply_category_filter($query);
        } else {
            $method = $this->method();
            $query->{$method}($this->column(), $this->operator(), $this->value());
        }
    }
    public function html_description() : string
    {
        return $this->condition_string($this->filter());
    }
    public function method() : string
    {
        if ($this->filter['column'] === $this->filter['database_column']) {
            return 'having';
        } else {
            return 'where';
        }
    }
    public function column() : string
    {
        return $this->filter['database_column'];
    }
    // Fix deprecation warning in PHP 8 while still working in PHP 7
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->filter;
    }
    /**
     * Category filters are different from the other filters
     *
     * @param Builder $query
     * @return void
     */
    private function apply_category_filter(Builder $query) : void
    {
        $wp_query = new \WP_Query(['posts_per_page' => -1, 'cat' => $this->filter['operand'], 'fields' => 'ids']);
        $post_ids = $wp_query->posts;
        $include = $this->filter['inclusion'] === 'include';
        $is = $this->filter['operator'] === 'is';
        if ($include === $is) {
            $query->whereIn('singular_id', $post_ids);
        } else {
            $query->where(function (Builder $query) use($post_ids) {
                $query->whereNotIn('singular_id', $post_ids)->orWhereNull('singular_id');
            });
        }
    }
    private function condition_string(array $condition) : string
    {
        $full_string = '';
        foreach ($condition as $key => $value) {
            if (!\in_array($key, ['inclusion', 'column', 'operator', 'operand'])) {
                continue;
            }
            $condition_string = '';
            if ($key == 'inclusion' && $value == 'include') {
                $condition_string = \esc_html__('Include', 'independent-analytics');
            } elseif ($key == 'inclusion' && $value == 'exclude') {
                $condition_string = \esc_html__('Exclude', 'independent-analytics');
            } elseif ($key == 'operator' && $value == 'lesser') {
                $condition_string = '<';
            } elseif ($key == 'operator' && $value == 'greater') {
                $condition_string = '>';
            } elseif ($key == 'operator' && $value == 'equal') {
                $condition_string = '=';
            } elseif ($key == 'operator' && $value == 'on') {
                $condition_string = \esc_html__('On', 'independent-analytics');
            } elseif ($key == 'operator' && $value == 'before') {
                $condition_string = \esc_html__('Before', 'independent-analytics');
            } elseif ($key == 'operator' && $value == 'after') {
                $condition_string = \esc_html__('After', 'independent-analytics');
            } elseif ($key == 'operator' && $value == 'contains') {
                $condition_string = \esc_html__('Contains', 'independent-analytics');
            } elseif ($key == 'operator' && $value == 'exact') {
                $condition_string = \esc_html__('Exactly matches', 'independent-analytics');
            } elseif ($key == 'operator' && $value == 'is') {
                $condition_string = \esc_html__('Is', 'independent-analytics');
            } elseif ($key == 'operator' && $value == 'isnt') {
                $condition_string = \esc_html__("Isn't", 'independent-analytics');
            } elseif ($key == 'operand' && $condition['column'] == 'browser') {
                $condition_string = Device_Browser_Filter_List::option($value);
            } elseif ($key == 'operand' && $condition['column'] == 'os') {
                $condition_string = Device_OS_Filter_List::option($value);
            } elseif ($key == 'operand' && $condition['column'] == 'device_type') {
                $condition_string = Device_Type_Filter_List::option($value);
            } elseif ($key == 'operand' && $condition['column'] == 'date') {
                try {
                    $date = \DateTime::createFromFormat('U', $value);
                    $condition_string = $date->format(WordPress_Site_Date_Format_Pattern::for_php());
                } catch (\Throwable $e) {
                    $condition_string = $value;
                }
            } elseif ($key == 'operand' && $condition['column'] == 'author') {
                $condition_string = Author_Filter_List::option($value);
            } elseif ($key == 'operand' && $condition['column'] == 'category') {
                $condition_string = Category_Filter_List::option($value);
            } elseif ($key == 'operand' && $condition['column'] == 'referrer_type') {
                $condition_string = Referrer_Type_Filter_List::option($value);
            } elseif ($key == 'operand' && $condition['column'] == 'type') {
                $condition_string = Page_Type_Filter_List::option($value);
            } elseif ($key == 'column' && \str_contains($value, 'wc')) {
                $condition_string = \str_replace(['_', '-'], ' ', $value);
                $condition_string = \str_replace('wc', 'WC', $condition_string);
            } else {
                $condition_string .= \ucwords(\str_replace(['_', '-'], ' ', $value)) . ' ';
            }
            if ($key == 'column' || $key == 'operand') {
                $condition_string = '<strong>' . $condition_string . '</strong> ';
            }
            $full_string .= $condition_string . ' ';
        }
        return \trim($full_string);
    }
    private function operator() : string
    {
        $operator = $this->filter['operator'];
        $result = '';
        if ($operator === 'equal' || $operator === 'is' || $operator === 'exact' || $operator === 'on') {
            $result = '=';
        }
        if ($operator === 'contains') {
            $result = 'like';
        }
        if ($operator === 'isnt') {
            $result = '!=';
        }
        if ($operator === 'greater' || $operator === 'after') {
            $result = '>';
        }
        if ($operator === 'lesser' || $operator === 'before') {
            $result = '<';
        }
        if ($this->filter['inclusion'] === 'exclude') {
            if ($result === '=') {
                return '!=';
            } elseif ($result === '!=') {
                return '=';
            } elseif ($result === '>') {
                return '<=';
            } elseif ($result === '<') {
                return '>=';
            } elseif ($result === 'like') {
                return 'not like';
            }
        }
        return $result;
    }
    private function value() : string
    {
        if ($this->filter['operator'] === 'contains') {
            return '%' . $this->filter['operand'] . '%';
        }
        if ($this->filter['database_column'] === 'cached_date') {
            try {
                $date = \DateTime::createFromFormat('U', $this->filter['operand']);
            } catch (\Throwable $e) {
                $date = new \DateTime();
            }
            return $date->format('Y-m-d');
        }
        return $this->filter['operand'];
    }
}
