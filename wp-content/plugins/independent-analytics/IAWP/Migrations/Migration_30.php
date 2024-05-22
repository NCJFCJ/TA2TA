<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_30 extends \IAWP\Migrations\Step_Migration
{
    /**
     * @return int
     */
    protected function database_version() : int
    {
        return 30;
    }
    /**
     * @return array
     */
    protected function queries() : array
    {
        return [$this->add_session_index(), $this->add_session_country_index(), $this->add_session_city_index(), $this->add_session_device_type_index(), $this->add_session_device_browser_index(), $this->add_session_device_os_index(), $this->add_session_campaign_index(), $this->add_views_index()];
    }
    private function add_session_index() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            CREATE UNIQUE INDEX sessions_summary_index ON {$sessions_table} (created_at, session_id, visitor_id, referrer_id, total_views)\n        ";
    }
    private function add_session_country_index() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            CREATE UNIQUE INDEX sessions_country_summary_index ON {$sessions_table} (created_at, session_id, visitor_id, country_id, total_views)\n        ";
    }
    private function add_session_city_index() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            CREATE UNIQUE INDEX sessions_city_summary_index ON {$sessions_table} (created_at, session_id, visitor_id, city_id, total_views)\n        ";
    }
    private function add_session_device_type_index() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            CREATE UNIQUE INDEX sessions_device_type_summary_index ON {$sessions_table} (created_at, session_id, visitor_id, device_type_id, total_views)\n        ";
    }
    private function add_session_device_browser_index() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            CREATE UNIQUE INDEX sessions_device_browser_summary_index ON {$sessions_table} (created_at, session_id, visitor_id, device_browser_id, total_views)\n        ";
    }
    private function add_session_device_os_index() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            CREATE UNIQUE INDEX sessions_device_os_summary_index ON {$sessions_table} (created_at, session_id, visitor_id, device_os_id, total_views)\n        ";
    }
    private function add_session_campaign_index() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            CREATE UNIQUE INDEX sessions_campaign_summary_index ON {$sessions_table} (created_at, session_id, visitor_id, campaign_id, total_views)\n        ";
    }
    private function add_views_index() : string
    {
        $views_table = Query::get_table_name(Query::VIEWS);
        return "\n            CREATE INDEX views_summary_index ON {$views_table} (session_id, viewed_at, resource_id)\n        ";
    }
}
