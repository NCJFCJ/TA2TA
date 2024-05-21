<?php

namespace IAWP;

/** @internal */
class Report_Finder
{
    public function __construct()
    {
    }
    public function fetch_reports_by_type() : array
    {
        return [['name' => \__('Pages', 'independent-analytics'), 'reports' => $this->fetch_page_reports()], ['name' => \__('Referrers', 'independent-analytics'), 'reports' => $this->fetch_referrer_reports()], ['name' => \__('Geographic', 'independent-analytics'), 'reports' => $this->fetch_geographic_reports()], ['name' => \__('Devices', 'independent-analytics'), 'reports' => $this->fetch_device_reports()], ['name' => \__('Campaigns', 'independent-analytics'), 'reports' => $this->fetch_campaign_reports()]];
    }
    /**
     * @return Report[]
     */
    public function fetch_page_reports() : array
    {
        return $this->by_type('views');
    }
    /**
     * @return Report[]
     */
    public function fetch_referrer_reports() : array
    {
        return $this->by_type('referrers');
    }
    /**
     * @return Report[]
     */
    public function fetch_geographic_reports() : array
    {
        return $this->by_type('geo');
    }
    /**
     * @return Report[]
     */
    public function fetch_device_reports() : array
    {
        return $this->by_type('devices');
    }
    /**
     * @return Report[]
     */
    public function fetch_campaign_reports() : array
    {
        return $this->by_type('campaigns');
    }
    public function is_real_time() : bool
    {
        return \IAWP\Env::get_tab() === 'real-time';
    }
    public function is_settings_page() : bool
    {
        return \IAWP\Env::get_page() === 'independent-analytics-settings';
    }
    public function is_campaign_builder_page() : bool
    {
        return \IAWP\Env::get_page() === 'independent-analytics-campaign-builder';
    }
    public function is_page_report() : bool
    {
        return \IAWP\Env::get_tab() === 'views';
    }
    public function is_referrer_report() : bool
    {
        return \IAWP\Env::get_tab() === 'referrers';
    }
    public function is_geographic_report() : bool
    {
        return \IAWP\Env::get_tab() === 'geo';
    }
    public function is_device_report() : bool
    {
        return \IAWP\Env::get_tab() === 'devices';
    }
    public function is_campaign_report() : bool
    {
        return \IAWP\Env::get_tab() === 'campaigns';
    }
    public function is_saved_report() : bool
    {
        $report = $this->current();
        if (\is_null($report)) {
            return \false;
        }
        return $report->is_saved_report();
    }
    public function current() : ?\IAWP\Report
    {
        $report_id = \array_key_exists('report', $_GET) ? \sanitize_text_field($_GET['report']) : null;
        if (\is_null($report_id)) {
            return self::get_base_report_for_current_tab();
        }
        $report = self::by_id($report_id);
        if (\is_null($report)) {
            return self::get_base_report_for_current_tab();
        }
        return $report;
    }
    /**
     * @param array $ids
     *
     * @return array|Report[]|null
     */
    public function by_ids(array $ids) : ?array
    {
        $reports_table = \IAWP\Query::get_table_name(\IAWP\Query::REPORTS);
        $rows = \IAWP\Illuminate_Builder::get_builder()->from($reports_table)->whereIn('report_id', $ids)->get()->toArray();
        return \array_map(function ($row) {
            return new \IAWP\Report($row);
        }, $rows);
    }
    /**
     * @param string $type
     *
     * @return Report[]
     */
    public function by_type(string $type) : array
    {
        $reports_table = \IAWP\Query::get_table_name(\IAWP\Query::REPORTS);
        $builder = \IAWP\Illuminate_Builder::get_builder()->from($reports_table)->where('type', '=', $type)->orderByRaw('position IS NULL')->orderBy('position')->orderBy('report_id')->get()->escapeWhenCastingToString();
        $rows = $builder->toArray();
        return \array_map(function ($row) {
            return new \IAWP\Report($row);
        }, $rows);
    }
    public static function get_base_report_for_current_tab() : ?\IAWP\Report
    {
        return self::get_base_report_for_type(\IAWP\Env::get_tab());
    }
    public static function get_favorite() : ?\IAWP\Report
    {
        $raw_id = \get_user_meta(\get_current_user_id(), 'iawp_favorite_report_id', \true);
        $id = \filter_var($raw_id, \FILTER_VALIDATE_INT);
        if ($id !== \false) {
            return self::by_id($id);
        }
        $raw_type = \get_user_meta(\get_current_user_id(), 'iawp_favorite_report_type', \true);
        $type = \filter_var($raw_type, \FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return self::get_base_report_for_type($type);
    }
    /**
     * @param string|int $id
     *
     * @return Report|null
     */
    public static function by_id($id) : ?\IAWP\Report
    {
        $id = (string) $id;
        $reports_table = \IAWP\Query::get_table_name(\IAWP\Query::REPORTS);
        if (!\ctype_digit($id)) {
            return null;
        }
        $row = \IAWP\Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $id)->first();
        if (\is_null($row)) {
            return null;
        }
        return new \IAWP\Report($row);
    }
    public static function create_report(array $attributes) : \IAWP\Report
    {
        $reports_table = \IAWP\Query::get_table_name(\IAWP\Query::REPORTS);
        if (\array_key_exists('columns', $attributes) && \is_array($attributes['columns'])) {
            $attributes['columns'] = \json_encode($attributes['columns']);
        }
        if (\array_key_exists('filters', $attributes) && \is_array($attributes['filters'])) {
            $attributes['filters'] = \json_encode($attributes['filters']);
        }
        if (\array_key_exists('visible_datasets', $attributes) && \is_array($attributes['visible_datasets'])) {
            $attributes['visible_datasets'] = \json_encode($attributes['visible_datasets']);
        }
        $report_id = \IAWP\Illuminate_Builder::get_builder()->from($reports_table)->insertGetId($attributes);
        return self::by_id($report_id);
    }
    private static function get_base_report_for_type(string $type) : ?\IAWP\Report
    {
        switch ($type) {
            case 'views':
                return new \IAWP\Report((object) ['name' => 'Pages', 'type' => 'views']);
            case 'referrers':
                return new \IAWP\Report((object) ['name' => 'Referrers', 'type' => 'referrers']);
            case 'geo':
                return new \IAWP\Report((object) ['name' => 'Geographic', 'type' => 'geo']);
            case 'devices':
                return new \IAWP\Report((object) ['name' => 'Devices', 'type' => 'devices']);
            case 'campaigns':
                return new \IAWP\Report((object) ['name' => 'Campaigns', 'type' => 'campaigns']);
            default:
                return null;
        }
    }
}
