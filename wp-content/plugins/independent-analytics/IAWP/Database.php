<?php

namespace IAWP;

use IAWPSCOPED\Illuminate\Database\QueryException;
/**
 * There are plenty of good reasons to avoid $wpdb->get_charset_collate. As users move their site
 * around, old tables might have one collation while newly created tables might have a different
 * one.
 *
 * @internal
 */
class Database
{
    private static $character_set = null;
    private static $collation = null;
    private static $user_privileges = null;
    private static $required_privileges = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'INDEX', 'DROP'];
    public static function has_correct_database_privileges() : bool
    {
        return \count(self::missing_database_privileges()) === 0;
    }
    public static function missing_database_privileges() : array
    {
        return \array_diff(self::$required_privileges, self::user_privileges());
    }
    public static function is_missing_all_tables() : bool
    {
        global $wpdb;
        $tables = \IAWP\Illuminate_Builder::get_builder()->select('*')->from('INFORMATION_SCHEMA.TABLES')->where('TABLE_SCHEMA', '=', $wpdb->dbname)->where('TABLE_NAME', 'LIKE', $wpdb->prefix . 'independent_analytics_%');
        $views_table = \IAWP\Query::get_table_name(\IAWP\Query::VIEWS);
        $views_query = \IAWP\Illuminate_Builder::get_builder()->selectRaw('0 AS number')->from($views_table);
        try {
            $views_query->doesntExist();
            $missing_views_table = \false;
        } catch (QueryException $exception) {
            $missing_views_table = \true;
        }
        return $tables->doesntExist() && $missing_views_table;
    }
    public static function character_set() : string
    {
        if (\is_null(self::$character_set)) {
            self::populate_character_set_and_collation();
        }
        return self::$character_set;
    }
    public static function collation() : string
    {
        if (\is_null(self::$collation)) {
            self::populate_character_set_and_collation();
        }
        return self::$collation;
    }
    public static function has_table(string $table) : bool
    {
        global $wpdb;
        $tables = $wpdb->get_row($wpdb->prepare("SHOW TABLES LIKE %s", $table));
        return !\is_null($tables);
    }
    public static function has_index(string $table, string $index) : bool
    {
        global $wpdb;
        if (!self::has_table($table)) {
            return \false;
        }
        $row = $wpdb->get_row($wpdb->prepare("\n                SHOW INDEX FROM {$table} WHERE Key_name = %s\n            ", $index));
        return !\is_null($row);
    }
    public static function get_character_set_and_collation_for(string $table_name) : string
    {
        global $wpdb;
        $query = \IAWP\Illuminate_Builder::get_builder()->selectRaw('CCSA.CHARACTER_SET_NAME AS character_set_name')->selectRaw('CCSA.COLLATION_NAME AS collation_name')->from('information_schema.TABLES', 'THE_TABLES')->leftJoin('information_schema.COLLATION_CHARACTER_SET_APPLICABILITY AS CCSA', 'CCSA.COLLATION_NAME', '=', 'THE_TABLES.TABLE_COLLATION')->where('THE_TABLES.TABLE_SCHEMA', '=', $wpdb->dbname)->where('THE_TABLES.TABLE_NAME', '=', $table_name);
        $result = $query->first();
        $character_set = $result->character_set_name ?? null;
        $collation = $result->collation_name ?? null;
        if ($character_set === null || $collation === null) {
            return '';
        }
        return "DEFAULT CHARACTER SET {$character_set} COLLATE {$collation}";
    }
    /**
     * From MySQL: It is not possible to deny a privilege granted at a higher level by absence of that privilege at a lower level.
     *
     * @return string[]
     */
    private static function user_privileges() : array
    {
        global $wpdb;
        if (!\is_null(self::$user_privileges)) {
            return self::$user_privileges;
        }
        $user = \IAWP\Illuminate_Builder::get_builder()->selectRaw('CURRENT_USER() as user')->value('user');
        $parts = \explode('@', $user);
        $grantee = "'" . $parts[0] . "'@'" . $parts[1] . "'";
        $global_privileges_query = \IAWP\Illuminate_Builder::get_builder()->select('*')->from('information_schema.user_privileges')->where('grantee', '=', $grantee);
        $global_privileges = \array_map(function ($record) {
            return $record->PRIVILEGE_TYPE;
        }, $global_privileges_query->get()->all());
        $database_privileges_query = \IAWP\Illuminate_Builder::get_builder()->select('*')->from('information_schema.schema_privileges')->where('grantee', '=', $grantee)->where('table_schema', '=', $wpdb->dbname);
        $database_privileges = \array_map(function ($record) {
            return $record->PRIVILEGE_TYPE;
        }, $database_privileges_query->get()->all());
        $privileges = \array_unique(\array_merge($global_privileges, $database_privileges), \SORT_REGULAR);
        // If SELECT is missing, that means our query is broken and should be ignored
        if (!\in_array('SELECT', $privileges)) {
            self::$user_privileges = self::$required_privileges;
        } else {
            self::$user_privileges = $privileges;
        }
        return self::$user_privileges;
    }
    private static function populate_character_set_and_collation() : void
    {
        global $wpdb;
        $query = \IAWP\Illuminate_Builder::get_builder()->selectRaw('CCSA.CHARACTER_SET_NAME AS character_set_name')->selectRaw('CCSA.COLLATION_NAME AS collation_name')->from('information_schema.TABLES', 'THE_TABLES')->leftJoin('information_schema.COLLATION_CHARACTER_SET_APPLICABILITY AS CCSA', 'CCSA.COLLATION_NAME', '=', 'THE_TABLES.TABLE_COLLATION')->where('THE_TABLES.TABLE_SCHEMA', '=', $wpdb->dbname)->where('THE_TABLES.TABLE_NAME', '=', \IAWP\Query::get_table_name(\IAWP\Query::REFERRERS));
        $result = $query->first();
        self::$character_set = $result->character_set_name ?? null;
        self::$collation = $result->collation_name ?? null;
        if (\is_null(self::$character_set) || \is_null(self::$collation)) {
            self::$character_set = $wpdb->charset;
            self::$collation = $wpdb->collate;
        }
    }
}
