<?php

namespace IAWP\Migrations;

use IAWP\Known_Referrers;
use IAWP\Query;
use IAWP\Utils\URL;
/** @internal */
class Migration_1_8 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '1.8';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $views_table = Query::get_table_name(Query::VIEWS);
        $resources_table = Query::get_table_name(Query::RESOURCES);
        $referrer_groups_table = Query::get_table_name(Query::REFERRER_GROUPS);
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        $known_referrers = Known_Referrers::referrers();
        // Create the groups table
        $wpdb->query("DROP TABLE IF EXISTS {$referrer_groups_table}");
        $wpdb->query("CREATE TABLE {$referrer_groups_table} (\n               referrer_group_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               name varchar(2048) NOT NULL,\n               domain varchar(2048) NOT NULL,\n               domain_to_match varchar(2048) NOT NULL,\n               type ENUM ('Search', 'Social') NOT NULL,\n               PRIMARY KEY (referrer_group_id)\n           ) {$charset_collate}");
        // Insert predefined groups
        foreach ($known_referrers as $group) {
            foreach ($group['domains'] as $domain) {
                $wpdb->insert($referrer_groups_table, ['name' => $group['name'], 'domain' => $group['domains'][0], 'domain_to_match' => $domain, 'type' => $group['type']]);
            }
        }
        $wpdb->query("\n            ALTER TABLE {$referrers_table} CHANGE COLUMN url domain varchar(2048) NOT NULL;\n        ");
        $rows = $wpdb->get_results("SELECT * FROM {$referrers_table}");
        foreach ($rows as $row) {
            $potential_url = new URL($row->domain);
            if ($potential_url->is_valid_url()) {
                $wpdb->query($wpdb->prepare("UPDATE {$referrers_table} SET domain = %s WHERE id = %d", $potential_url->get_domain(), $row->id));
            }
        }
        // add a new page field to view table
        $wpdb->query("\n            ALTER TABLE {$views_table} ADD COLUMN page bigint(20) UNSIGNED NOT NULL DEFAULT 1;\n        ");
        // move the page number to the view and set the resource_id to first of similar resources
        $wpdb->query("\n        UPDATE {$views_table} AS views\n        INNER JOIN {$resources_table} AS resources ON views.resource_id = resources.id\n        INNER JOIN\n        (\n            SELECT MIN({$resources_table}.id) as resource_id,\n                   resource,\n                   singular_id,\n                   author_id,\n                   date_archive,\n                   search_query,\n                   post_type,\n                   term_id,\n                   not_found_url\n            FROM {$views_table}\n                     INNER JOIN {$resources_table}\n                                ON {$resources_table}.id = {$views_table}.resource_id\n            GROUP BY resource, singular_id, author_id, date_archive, search_query, post_type, term_id, not_found_url\n        ) AS matcher ON resources.resource = matcher.resource\n            AND (resources.singular_id = matcher.singular_id OR resources.singular_id IS NULL OR matcher.singular_id IS NULL)\n            AND (resources.author_id = matcher.author_id OR resources.author_id IS NULL OR matcher.author_id IS NULL)\n            AND (resources.date_archive = matcher.date_archive OR resources.date_archive IS NULL OR matcher.date_archive IS NULL)\n            AND (resources.search_query = matcher.search_query OR resources.search_query IS NULL OR matcher.search_query IS NULL)\n            AND (resources.post_type = matcher.post_type OR resources.post_type IS NULL OR matcher.post_type IS NULL)\n            AND (resources.term_id = matcher.term_id OR resources.term_id IS NULL OR matcher.term_id IS NULL)\n            AND (resources.not_found_url = matcher.not_found_url OR resources.not_found_url IS NULL OR matcher.not_found_url IS NULL)\n            AND resources.id != matcher.resource_id\n        SET views.resource_id = matcher.resource_id, views.page = resources.page\n        ");
        // delete the extra duplicate resources (or don't?)
        $wpdb->query("\n        DELETE resources FROM {$resources_table} AS resources \n        LEFT JOIN {$views_table} AS views ON resources.id = views.resource_id\n        WHERE views.id IS NULL\n        ");
        // remove the page column from resources
        $wpdb->query("\n            ALTER TABLE {$resources_table} DROP COLUMN page;\n        ");
    }
}
