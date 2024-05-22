<?php

namespace IAWP\Models;

use IAWP\Geoposition;
use IAWP\Illuminate_Builder;
use IAWP\Query;
use IAWP\Utils\Salt;
/**
 * How to use:
 *
 * Example IP from the Netherlands
 * $visitor = new Visitor('92.111.145.208', 'some ua string');
 *
 * Example IP from the United States
 * $visitor = new Visitor('98.111.145.208', 'some ua string');
 *
 * Access visitor token
 * $visitor->id();
 * @internal
 */
class Visitor
{
    private $id;
    private $geoposition;
    /**
     * New instances should be created with a string ip address
     *
     * @param string $ip
     * @param string $user_agent
     */
    public function __construct(string $ip, string $user_agent)
    {
        $this->id = $this->fetch_visitor_id($this->calculate_hash($ip, $user_agent));
        $this->geoposition = new Geoposition($ip);
    }
    public function geoposition() : Geoposition
    {
        return $this->geoposition;
    }
    /**
     * Get the id for the most recent view for a visitor
     *
     * @return int|null
     */
    public function current_view_id() : ?int
    {
        global $wpdb;
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $id = $wpdb->get_var($wpdb->prepare("\n                SELECT views.id as id\n                FROM {$views_table} AS views\n                         LEFT JOIN {$sessions_table} AS sessions ON sessions.session_id = views.session_id\n                WHERE sessions.visitor_id = %s\n                ORDER BY views.viewed_at DESC\n                LIMIT 1\n                ", $this->id()));
        if (\is_null($id)) {
            return null;
        }
        return \intval($id);
    }
    /**
     * Get the id for the most recent view for a visitor
     *
     * @return int|null
     */
    public function current_session_initial_view_id() : ?int
    {
        global $wpdb;
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $id = $wpdb->get_var($wpdb->prepare("\n                    SELECT sessions.initial_view_id as id\n                    FROM {$views_table} AS views\n                             LEFT JOIN {$sessions_table} AS sessions ON sessions.session_id = views.session_id\n                    WHERE sessions.visitor_id = %s\n                    ORDER BY views.viewed_at DESC\n                    LIMIT 1\n                    ", $this->id()));
        if (\is_null($id)) {
            return null;
        }
        return \intval($id);
    }
    /**
     * Return the database id for a visitor
     *
     * @return string
     */
    public function id() : string
    {
        return $this->id;
    }
    /**
     * @param string $ip
     * @param string $user_agent
     * @return string
     */
    private function calculate_hash(string $ip, string $user_agent) : string
    {
        $salt = Salt::visitor_token_salt();
        $result = $salt . $ip . $user_agent;
        return \md5($result);
    }
    private function fetch_visitor_id(string $hash) : int
    {
        $visitors_table = Query::get_table_name(Query::VISITORS);
        Illuminate_Builder::get_builder()->from($visitors_table)->insertOrIgnore([['hash' => $hash]]);
        return Illuminate_Builder::get_builder()->from($visitors_table)->where('hash', '=', $hash)->value('visitor_id');
    }
}
