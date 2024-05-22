<?php

namespace IAWP;

use IAWP\Models\Page_Author_Archive;
use IAWP\Models\Page_Singular;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class Track_Resource_Changes
{
    public function __construct()
    {
        \add_action('wp_after_insert_post', [$this, 'handle_updated_post'], 10, 3);
        \add_action('profile_update', [$this, 'handle_updated_author']);
    }
    public function handle_updated_post($post_id, $post, $is_update)
    {
        if (!$is_update) {
            return;
        }
        $post = \get_post($post_id);
        if (\is_null($post) || $post->post_status === 'trash') {
            return;
        }
        $row = (object) ['resource' => 'singular', 'singular_id' => $post_id];
        $page = new Page_Singular($row);
        $page->update_cache();
        $campaigns_table = \IAWP\Query::get_table_name(\IAWP\Query::CAMPAIGNS);
        $sessions_table = \IAWP\Query::get_table_name(\IAWP\Query::SESSIONS);
        $views_table = \IAWP\Query::get_table_name(\IAWP\Query::VIEWS);
        $resources_table = \IAWP\Query::get_table_name(\IAWP\Query::RESOURCES);
        \IAWP\Illuminate_Builder::get_builder()->from($campaigns_table, 'campaigns')->join("{$sessions_table} AS sessions", function (JoinClause $join) {
            $join->on('sessions.campaign_id', '=', 'campaigns.campaign_id');
        })->join("{$views_table} AS views", function (JoinClause $join) {
            $join->on('views.id', '=', 'sessions.initial_view_id');
        })->join("{$resources_table} AS resources", function (JoinClause $join) {
            $join->on('resources.id', '=', 'views.resource_id');
        })->where('resources.singular_id', '=', $post_id)->update(['campaigns.landing_page_title' => $page->title()]);
    }
    public function handle_updated_author($user_id)
    {
        $row = (object) ['resource' => 'author', 'author_id' => $user_id];
        $page = new Page_Author_Archive($row);
        $page->update_cache();
        // TODO - This doesn't update resources where this author is attributed such as a singular
        //  where this author is the author. It'll have the old user data, such as the old name,
        //  until it's viewed.
    }
}
