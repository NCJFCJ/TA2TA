<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_13 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '13';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        $this->remove_city_neighborhood_data();
        $this->add_index_for_visitors_table();
        $this->create_cities_table();
        $this->create_countries_table();
        $this->add_foreign_keys_to_sessions();
        $this->populate_countries();
        $this->populate_cities();
        $this->link_sessions_to_city_and_country();
        $this->drop_visitors_table();
    }
    private function add_index_for_visitors_table()
    {
        global $wpdb;
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $wpdb->query("\n            CREATE INDEX visitors_migration_index\n            ON {$visitors_table} (country_code, subdivision, city)\n        ");
    }
    private function create_cities_table()
    {
        global $wpdb;
        $charset_and_collation = $wpdb->get_charset_collate();
        $cities_table = Query::get_table_name(Query::CITIES);
        $wpdb->query("DROP TABLE IF EXISTS {$cities_table}");
        $wpdb->query("\n            CREATE TABLE {$cities_table} (\n               city_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               country_id bigint(20) UNSIGNED NOT NULL,\n               subdivision varchar(64) NOT NULL,\n               city varchar(64) NOT NULL,\n               PRIMARY KEY (city_id)\n            ) {$charset_and_collation}\n        ");
        $wpdb->query("\n            CREATE UNIQUE INDEX cities_unique_index\n            ON {$cities_table} (country_id, subdivision, city)\n        ");
    }
    private function create_countries_table()
    {
        global $wpdb;
        $charset_and_collation = $wpdb->get_charset_collate();
        $countries_table = Query::get_table_name(Query::COUNTRIES);
        $wpdb->query("DROP TABLE IF EXISTS {$countries_table}");
        $wpdb->query("\n            CREATE TABLE {$countries_table} (\n               country_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               country_code varchar(4) NOT NULL,\n               country varchar(64) NOT NULL,\n               continent varchar(16) NOT NULL,\n               PRIMARY KEY (country_id)\n            ) {$charset_and_collation}\n        ");
        $wpdb->query("\n            CREATE UNIQUE INDEX countries_unique_index\n            ON {$countries_table} (country_code, country, continent)\n        ");
    }
    private function add_foreign_keys_to_sessions()
    {
        global $wpdb;
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $wpdb->query("\n            ALTER TABLE {$sessions_table}\n            ADD COLUMN city_id BIGINT(20) UNSIGNED,\n            ADD COLUMN country_id BIGINT(20) UNSIGNED;\n        ");
        $wpdb->query("\n            ALTER TABLE {$sessions_table}\n               ADD INDEX(city_id),\n               ADD INDEX(country_id);\n        ");
    }
    private function remove_city_neighborhood_data()
    {
        global $wpdb;
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $wpdb->query("\n           UPDATE\n                {$visitors_table}\n            SET\n                city = TRIM(SUBSTRING_INDEX(city, '(', 1)) \n        ");
    }
    private function populate_countries()
    {
        global $wpdb;
        $countries_tables = Query::get_table_name(Query::COUNTRIES);
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $wpdb->query("\n            INSERT IGNORE INTO {$countries_tables} (continent, country_code, country)\n            SELECT\n                continent,\n                country_code,\n                country\n            FROM\n                {$visitors_table}\n            WHERE\n                continent IS NOT NULL\n                AND country_code IS NOT NULL\n                AND country IS NOT NULL\n            GROUP BY\n                continent,\n                country_code,\n                country \n        ");
    }
    private function populate_cities()
    {
        global $wpdb;
        $cities_tables = Query::get_table_name(Query::CITIES);
        $countries_table = Query::get_table_name(Query::COUNTRIES);
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $wpdb->query("\n            INSERT IGNORE INTO {$cities_tables} (country_id, subdivision, city)\n            SELECT\n                countries.country_id,\n                subdivision,\n                city\n            FROM\n                {$visitors_table} AS visitors\n                LEFT JOIN {$countries_table} AS countries ON visitors.country_code = countries.country_code\n            WHERE\n                subdivision IS NOT NULL\n                AND city IS NOT NULL\n            GROUP BY\n                countries.country_id,\n                subdivision,\n                city\n        ");
    }
    private function link_sessions_to_city_and_country()
    {
        global $wpdb;
        $cities_tables = Query::get_table_name(Query::CITIES);
        $countries_table = Query::get_table_name(Query::COUNTRIES);
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $wpdb->query("\n           UPDATE\n                {$sessions_table} AS sessions\n                LEFT JOIN {$visitors_table} AS visitors ON sessions.visitor_id = visitors.visitor_id\n                LEFT JOIN (\n                    SELECT\n                        countries.country_id,\n                        cities.city_id,\n                        countries.country_code,\n                        cities.subdivision,\n                        cities.city\n                    FROM\n                        {$cities_tables} AS cities\n                        LEFT JOIN {$countries_table} AS countries ON cities.country_id = countries.country_id) AS locations ON visitors.country_code = locations.country_code\n                    AND visitors.subdivision = locations.subdivision\n                    AND visitors.city = locations.city\n                    SET sessions.country_id = locations.country_id, sessions.city_id = locations.city_id\n        ");
    }
    private function drop_visitors_table()
    {
        global $wpdb;
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $wpdb->query("DROP TABLE IF EXISTS {$visitors_table}");
    }
}
