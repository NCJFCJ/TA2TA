<?php

namespace IAWP;

/** @internal */
class Report
{
    private $row;
    private static $report_types = ['views', 'referrers', 'geo', 'devices', 'campaigns'];
    public function __construct($row)
    {
        $this->row = $row;
    }
    public function id() : int
    {
        return $this->row->report_id;
    }
    public function type() : string
    {
        return $this->row->type;
    }
    public function name() : string
    {
        return $this->row->name;
    }
    public function url() : string
    {
        if (!$this->is_saved_report()) {
            return \IAWPSCOPED\iawp_dashboard_url(['tab' => $this->row->type]);
        }
        return \IAWPSCOPED\iawp_dashboard_url(['tab' => $this->row->type, 'report' => $this->row->report_id]);
    }
    public function is_saved_report() : bool
    {
        return \property_exists($this->row, 'report_id');
    }
    public function is_current() : bool
    {
        $report_id = \array_key_exists('report', $_GET) ? \sanitize_text_field($_GET['report']) : null;
        return $this->id() === \intval($report_id);
    }
    public function is_favorite() : bool
    {
        if ($this->is_saved_report()) {
            return \intval(\get_user_meta(\get_current_user_id(), 'iawp_favorite_report_id', \true)) === $this->id();
        }
        return \get_user_meta(\get_current_user_id(), 'iawp_favorite_report_type', \true) === $this->type();
    }
    public function to_array() : array
    {
        $array = (array) $this->row;
        if (\array_key_exists('columns', $array) && !\is_null($array['columns'])) {
            $array['columns'] = \json_decode($array['columns'], \true);
        }
        if (\array_key_exists('filters', $array) && !\is_null($array['filters'])) {
            $array['filters'] = \json_decode($array['filters'], \true);
        }
        if (\array_key_exists('visible_datasets', $array) && !\is_null($array['visible_datasets'])) {
            $array['visible_datasets'] = \json_decode($array['visible_datasets'], \true);
        }
        return $array;
    }
    public static function is_valid_report_type(string $type) : bool
    {
        return \in_array($type, self::$report_types);
    }
}
