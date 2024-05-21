<?php

namespace IAWP\Models;

use IAWP\Geoposition;
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
        $this->id = self::calculate_id($ip, $user_agent);
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
    public function most_recent_view_id() : ?int
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
    private function calculate_id(string $ip, string $user_agent) : string
    {
        $salt = Salt::visitor_token_salt();
        $result = $salt . $ip . $user_agent;
        return \md5($result);
    }
}
