<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_24 extends \IAWP\Migrations\Step_Migration
{
    /**
     * @return int
     */
    protected function database_version() : int
    {
        return 24;
    }
    /**
     * @return array
     */
    protected function queries() : array
    {
        return [$this->add_column(), $this->insert_campaigns(), $this->link_sessions_with_campaigns(), $this->delete_unused_campaigns()];
    }
    private function add_column() : string
    {
        $campaigns_table = Query::get_table_name(Query::CAMPAIGNS);
        return "\n           ALTER TABLE {$campaigns_table}\n           ADD COLUMN landing_page_title VARCHAR(128) AFTER campaign_id\n        ";
    }
    private function insert_campaigns() : string
    {
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $campaigns_table = Query::get_table_name(Query::CAMPAIGNS);
        $resources_table = Query::get_table_name(Query::RESOURCES);
        return "\n            INSERT INTO {$campaigns_table} (landing_page_title, utm_source, utm_medium, utm_campaign, utm_term, utm_content)\n            SELECT DISTINCT\n                initial_resource.cached_title,\n                campaigns.utm_source,\n                campaigns.utm_medium,\n                campaigns.utm_campaign,\n                campaigns.utm_term,\n                campaigns.utm_content\n            FROM {$resources_table} AS initial_resource\n            JOIN {$views_table} AS initial_view ON initial_resource.id = initial_view.resource_id\n            JOIN {$sessions_table} AS sessions ON initial_view.session_id = sessions.session_id AND initial_view.id = sessions.initial_view_id\n            JOIN {$campaigns_table} AS campaigns ON sessions.campaign_id = campaigns.campaign_id \n        ";
    }
    private function link_sessions_with_campaigns() : string
    {
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $campaigns_table = Query::get_table_name(Query::CAMPAIGNS);
        $resources_table = Query::get_table_name(Query::RESOURCES);
        return "\n           UPDATE {$sessions_table} AS sessions\n            JOIN {$views_table} AS initial_view ON initial_view.session_id = sessions.session_id AND sessions.initial_view_id = initial_view.id\n            JOIN {$resources_table} AS initial_resource ON initial_view.resource_id = initial_resource.id\n            JOIN {$campaigns_table} AS campaigns ON sessions.campaign_id = campaigns.campaign_id\n            JOIN {$campaigns_table} AS new_campaign ON initial_resource.cached_title = new_campaign.landing_page_title\n                AND campaigns.utm_source = new_campaign.utm_source\n                AND campaigns.utm_medium = new_campaign.utm_medium\n                AND campaigns.utm_campaign = new_campaign.utm_campaign\n                AND (campaigns.utm_term = new_campaign.utm_term OR (campaigns.utm_term IS NULL AND new_campaign.utm_term IS NULL))\n                AND (campaigns.utm_content = new_campaign.utm_content OR (campaigns.utm_content IS NULL AND new_campaign.utm_content IS NULL))\n            SET sessions.campaign_id = new_campaign.campaign_id \n        ";
    }
    private function delete_unused_campaigns() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $campaigns_table = Query::get_table_name(Query::CAMPAIGNS);
        return "\n            DELETE campaigns from {$campaigns_table} AS campaigns\n            LEFT JOIN {$sessions_table} AS sessions ON campaigns.campaign_id = sessions.campaign_id\n            WHERE sessions.session_id IS NULL \n        ";
    }
}
