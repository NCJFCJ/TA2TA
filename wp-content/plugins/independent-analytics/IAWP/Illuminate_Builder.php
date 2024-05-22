<?php

namespace IAWP;

use IAWPSCOPED\Illuminate\Database\Capsule\Manager as Capsule;
use IAWPSCOPED\Illuminate\Database\Connection;
use IAWPSCOPED\Illuminate\Database\Query\Builder;
/**
 * Connects to the WordPress database using Illuminate from Laravel
 *
 * Usage:
 *
 * $builder = Illuminate_Builder::get_builder();
 * @internal
 */
class Illuminate_Builder
{
    private static $connection = null;
    public static function get_builder() : Builder
    {
        if (self::$connection === null) {
            self::$connection = self::get_connection();
        }
        return new Builder(self::$connection);
    }
    public static function ray(Builder $builder)
    {
        $add_slashes = \str_replace('?', "'?'", $builder->toSql());
        $escape_mysql_format_percentages = \str_replace('%', '%%', $add_slashes);
        $replace_question_marks = \str_replace('?', '%s', $escape_mysql_format_percentages);
        if (\function_exists('IAWPSCOPED\\ray')) {
            return ray(\vsprintf($replace_question_marks, $builder->getBindings()));
        }
    }
    private static function get_connection() : Connection
    {
        global $wpdb;
        $raw_host = $wpdb->dbhost;
        $database_name = $wpdb->dbname;
        $charset = $wpdb->dbcharset ?? 'utf8';
        $username = $wpdb->dbuser;
        $password = $wpdb->dbpassword;
        $host_data = $wpdb->parse_db_host($raw_host);
        list($host, $port, $socket, $is_ipv6) = $host_data;
        if ($is_ipv6 && \extension_loaded('mysqlnd')) {
            $host = "[{$host}]";
        }
        $charset_collate = $wpdb->determine_charset($charset, '');
        $charset = $charset_collate['charset'];
        // $collation       = $charset_collate['collate'];
        // Collation is no longer added due to issue with WP Rocket with testing 1.23.0. In the future,
        // $connection_options should have the collation property added only if it's defined. It should
        // not get set for empty strings.
        $connection_options = ['driver' => 'mysql', 'database' => $database_name, 'username' => $username, 'password' => $password, 'charset' => $charset, 'prefix' => ''];
        if (isset($socket)) {
            $connection_options['unix_socket'] = $socket;
        } else {
            $connection_options['host'] = $host;
            if (isset($port)) {
                $connection_options['port'] = $port;
            }
        }
        $capsule = new Capsule();
        $capsule->addConnection($connection_options);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule->getConnection();
    }
}
