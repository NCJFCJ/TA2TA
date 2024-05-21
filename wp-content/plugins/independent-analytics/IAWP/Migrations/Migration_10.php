<?php

namespace IAWP\Migrations;

use IAWP\Known_Referrers;
use IAWP\Query;
/** @internal */
class Migration_10 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '10';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $views_table = Query::get_table_name(Query::VIEWS);
        $wc_orders_table = Query::get_table_name(Query::WC_ORDERS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $referrer_groups_table = Query::get_table_name(Query::REFERRER_GROUPS);
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        $wpdb->query("\n           UPDATE {$referrers_table} SET domain = SUBSTRING(domain, 1, 255) WHERE LENGTH(domain) > 255;\n        ");
        $wpdb->query("\n           ALTER TABLE {$referrers_table} MODIFY COLUMN domain VARCHAR(255) NOT NULL;\n        ");
        $wpdb->query("\n           UPDATE {$referrer_groups_table} SET name = SUBSTRING(name, 1, 255) WHERE LENGTH(name) > 255;\n        ");
        $wpdb->query("\n           ALTER TABLE {$referrer_groups_table} MODIFY COLUMN name VARCHAR(255) NOT NULL;\n        ");
        $wpdb->query("\n           UPDATE {$referrer_groups_table} SET domain = SUBSTRING(domain, 1, 255) WHERE LENGTH(domain) > 255;\n        ");
        $wpdb->query("\n           ALTER TABLE {$referrer_groups_table} MODIFY COLUMN domain VARCHAR(255) NOT NULL;\n        ");
        $wpdb->query("\n           UPDATE {$referrer_groups_table} SET domain_to_match = SUBSTRING(domain_to_match, 1, 255) WHERE LENGTH(domain_to_match) > 255;\n        ");
        $wpdb->query("\n           ALTER TABLE {$referrer_groups_table} MODIFY COLUMN domain_to_match VARCHAR(255) NOT NULL;\n        ");
        $wpdb->query("\n           CREATE INDEX views_viewed_at_index\n           ON {$views_table} (viewed_at);\n        ");
        $wpdb->query("\n           CREATE INDEX views_resource_id_index\n           ON {$views_table} (resource_id);\n        ");
        $wpdb->query("\n           CREATE INDEX wc_orders_view_id_index\n           ON {$wc_orders_table} (view_id);\n        ");
        $wpdb->query("\n           CREATE INDEX wc_orders_created_at_index\n           ON {$wc_orders_table} (created_at);\n        ");
        $wpdb->query("\n           CREATE INDEX sessions_created_at_index\n           ON {$sessions_table} (created_at);\n        ");
        // Remove any duplicate referrer groups where a given domain_to_match already exists
        $wpdb->query("\n            DELETE referrer_groups FROM {$referrer_groups_table} AS referrer_groups\n                INNER JOIN (\n                    SELECT\n                        domain_to_match,\n                        MIN(referrer_group_id) AS min_referrer_group_id\n                    FROM\n                        {$referrer_groups_table}\n                    GROUP BY\n                        domain_to_match\n                    HAVING\n                        COUNT(*) > 1) AS duplicates ON referrer_groups.domain_to_match = duplicates.domain_to_match\n                AND referrer_groups.referrer_group_id > duplicates.min_referrer_group_id;\n        ");
        $wpdb->query("\n           CREATE UNIQUE INDEX referrer_groups_domain_to_match_index\n           ON {$referrer_groups_table} (domain_to_match);\n        ");
        $wpdb->query("\n            UPDATE\n                {$sessions_table} AS sessions\n                INNER JOIN {$referrers_table} AS referrers ON sessions.referrer_id = referrers.id\n                INNER JOIN (\n                    SELECT\n                        MIN(referrers.id) AS min_referrer_id,\n                        referrers.domain\n                    FROM\n                        {$sessions_table} AS sessions\n                        INNER JOIN {$referrers_table} AS referrers ON sessions.referrer_id = referrers.id\n                    GROUP BY\n                        referrers.domain) AS matcher ON referrers.domain = matcher.domain\n                AND referrers.id != matcher.min_referrer_id SET sessions.referrer_id = matcher.min_referrer_id;\n        ");
        $wpdb->query("\n            DELETE referrers FROM {$referrers_table} AS referrers\n                LEFT JOIN {$sessions_table} AS sessions ON referrers.id = sessions.referrer_id\n                WHERE sessions.session_id IS NULL;\n        ");
        $wpdb->query("\n           CREATE UNIQUE INDEX referrers_domain_index\n           ON {$referrers_table} (domain);\n        ");
        $wpdb->query("\n           ALTER TABLE {$referrer_groups_table} MODIFY COLUMN type ENUM ('Search', 'Social', 'Referrer') NOT NULL;\n        ");
        Known_Referrers::replace_known_referrers_table();
    }
}
