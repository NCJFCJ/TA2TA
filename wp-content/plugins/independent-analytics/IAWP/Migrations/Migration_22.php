<?php

namespace IAWP\Migrations;

use IAWP\Database;
use IAWP\Query;
/** @internal */
class Migration_22 extends \IAWP\Migrations\Step_Migration
{
    /**
     * @return int
     */
    protected function database_version() : int
    {
        return 22;
    }
    /**
     * @return array
     */
    protected function queries() : array
    {
        return [$this->remove_index(), $this->add_columns(), $this->clip_domains(), $this->reduce_domain_column_size(), $this->populate_group_data(), $this->update_non_grouped_referrers(), $this->match_session_with_new_referrers(), $this->remove_duplicates(), $this->restore_index(), $this->create_direct_referrer(), $this->link_direct_sessions_to_direct_referrer(), $this->drop_referrer_groups()];
    }
    private function remove_index() : ?string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        if (!Database::has_index($referrers_table, 'referrers_domain_index')) {
            return null;
        }
        return "\n            ALTER TABLE {$referrers_table} DROP INDEX referrers_domain_index;\n        ";
    }
    private function add_columns() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        return "\n           ALTER TABLE {$referrers_table}\n               ADD COLUMN type ENUM('Ad', 'Direct', 'Referrer', 'Search', 'Social'),\n               ADD COLUMN referrer VARCHAR(128)\n        ";
    }
    private function clip_domains() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        return "\n            UPDATE {$referrers_table}\n            SET domain = SUBSTRING(domain, 1, 128)\n            WHERE LENGTH(domain) > 128;\n        ";
    }
    private function reduce_domain_column_size() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        return "\n            ALTER TABLE {$referrers_table}\n            MODIFY COLUMN domain VARCHAR(128) NOT NULL;\n        ";
    }
    private function populate_group_data() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        $referrer_groups_table = Query::get_table_name(Query::REFERRER_GROUPS);
        return "\n            UPDATE {$referrers_table} AS referrers\n            JOIN  {$referrer_groups_table} AS referrer_groups ON referrers.domain = referrer_groups.domain_to_match\n            SET\n                referrers.referrer = referrer_groups.name,\n                referrers.type = referrer_groups.type,\n                referrers.domain = referrer_groups.domain\n        ";
    }
    private function update_non_grouped_referrers() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        return "\n            UPDATE {$referrers_table} AS referrers\n            SET type = 'Referrer', referrer = IF(referrers.domain LIKE 'www.%', SUBSTR(referrers.domain, 5), referrers.domain)\n            WHERE referrers.referrer IS NULL\n        ";
    }
    private function match_session_with_new_referrers() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            UPDATE {$sessions_table} AS sessions\n            JOIN {$referrers_table} AS referrers ON sessions.referrer_id = referrers.id\n            JOIN (\n                SELECT\n                    MIN(referrers.id) AS referrer_id,\n                    domain,\n                    type,\n                    referrer\n                FROM {$sessions_table} AS sessions\n                JOIN {$referrers_table} AS referrers ON sessions.referrer_id = referrers.id\n                WHERE domain IS NOT NULL AND type IS NOT NULL AND referrer IS NOT NULL\n                GROUP BY domain, type, referrer\n            ) AS first_match\n            ON referrers.domain = first_match.domain\n                AND referrers.type = first_match.type\n                AND referrers.referrer = first_match.referrer\n            SET sessions.referrer_id = first_match.referrer_id\n        ";
    }
    private function remove_duplicates() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            DELETE referrers FROM {$referrers_table} AS referrers\n            LEFT JOIN {$sessions_table} AS sessions on referrers.id = sessions.referrer_id\n            WHERE sessions.session_id IS NULL\n        ";
    }
    private function restore_index() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        return "\n           CREATE UNIQUE INDEX referrers_domain_index ON {$referrers_table} (domain);\n        ";
    }
    private function create_direct_referrer() : string
    {
        global $wpdb;
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        return $wpdb->prepare("\n            INSERT INTO {$referrers_table}\n                (domain, type, referrer)\n            VALUES (%s, %s, %s); \n        ", '', 'Direct', 'Direct');
    }
    private function link_direct_sessions_to_direct_referrer() : string
    {
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            UPDATE {$sessions_table}  AS sessions\n            SET\n                sessions.referrer_id = (\n                    SELECT id FROM {$referrers_table} WHERE domain = ''\n                )\n            WHERE sessions.referrer_id IS NULL \n        ";
    }
    private function drop_referrer_groups() : string
    {
        $referrer_groups_table = Query::get_table_name(Query::REFERRER_GROUPS);
        return "\n            DROP TABLE IF EXISTS {$referrer_groups_table}\n        ";
    }
}
