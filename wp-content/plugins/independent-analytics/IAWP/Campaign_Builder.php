<?php

namespace IAWP;

use IAWP\Utils\Singleton;
use IAWP\Utils\String_Util;
use IAWP\Utils\URL;
use IAWPSCOPED\Illuminate\Support\Carbon;
use IAWPSCOPED\League\Uri\Uri;
/** @internal */
class Campaign_Builder
{
    use Singleton;
    public function __construct()
    {
    }
    public function render_campaign_builder()
    {
        echo \IAWPSCOPED\iawp_blade()->run('campaign-builder', ['campaigns' => $this->get_previously_created_campaigns()]);
    }
    public function create_campaign($path, $source, $medium, $campaign, $term, $content)
    {
        global $wpdb;
        $has_errors = \false;
        $path = \strlen($path) > 0 ? $path : '';
        $path_error = null;
        $source_error = null;
        $medium_error = null;
        $campaign_error = null;
        $term = \strlen($term) > 0 ? $term : null;
        $content = \strlen($content) > 0 ? $content : null;
        $url = new URL(\site_url() . $path);
        if (!$url->is_valid_url()) {
            $has_errors = \true;
            $path_error = 'path invalid';
        }
        if (\strlen($source) === 0) {
            $has_errors = \true;
            $source_error = 'Source is required';
        }
        if (\strlen($medium) === 0) {
            $has_errors = \true;
            $medium_error = 'Medium is required';
        }
        if (\strlen($campaign) === 0) {
            $has_errors = \true;
            $campaign_error = 'Campaign is required';
        }
        if ($has_errors) {
            return \IAWPSCOPED\iawp_blade()->run('campaign-builder', ['path' => $path, 'path_error' => $path_error, 'utm_source' => $source, 'utm_source_error' => $source_error, 'utm_medium' => $medium, 'utm_medium_error' => $medium_error, 'utm_campaign' => $campaign, 'utm_campaign_error' => $campaign_error, 'utm_term' => $term, 'utm_content' => $content, 'campaigns' => $this->get_previously_created_campaigns()]);
        }
        $campaign_urls_table = \IAWP\Query::get_table_name(\IAWP\Query::CAMPAIGN_URLS);
        $wpdb->insert($campaign_urls_table, ['path' => $path, 'utm_source' => $source, 'utm_medium' => $medium, 'utm_campaign' => $campaign, 'utm_term' => $term, 'utm_content' => $content, 'created_at' => (new \DateTime())->format('Y-m-d H:i:s')]);
        $url = $this->build_url($path, $source, $medium, $campaign, $term, $content);
        return \IAWPSCOPED\iawp_blade()->run('campaign-builder', ['path' => $path, 'utm_source' => $source, 'utm_medium' => $medium, 'utm_campaign' => $campaign, 'utm_term' => $term, 'utm_content' => $content, 'new_campaign_url' => $url, 'campaigns' => $this->get_previously_created_campaigns()]);
    }
    public function build_url($path, $source, $medium, $campaign, $term = null, $content = null) : string
    {
        $path = String_Util::str_starts_with($path, '/') ? \substr($path, 1) : $path;
        $uri = Uri::createFromString(\trailingslashit(\site_url()) . $path);
        $existing_query = $uri->getQuery();
        if (\is_null($existing_query)) {
            $existing_query = [];
        } else {
            \parse_str($existing_query, $existing_query);
        }
        $existing_query['utm_source'] = $source;
        $existing_query['utm_medium'] = $medium;
        $existing_query['utm_campaign'] = $campaign;
        if (isset($term)) {
            $existing_query['utm_term'] = $term;
        }
        if (isset($content)) {
            $existing_query['utm_content'] = $content;
        }
        return $uri->withQuery(\http_build_query($existing_query));
    }
    private function get_previously_created_campaigns()
    {
        global $wpdb;
        $campaign_urls_table = \IAWP\Query::get_table_name(\IAWP\Query::CAMPAIGN_URLS);
        $results = $wpdb->get_results("\n            SELECT * FROM {$campaign_urls_table} ORDER BY created_at DESC LIMIT 100\n        ");
        return \array_map(function ($result) {
            $created_at = Carbon::parse($result->created_at)->diffForHumans();
            return ['campaign_url_id' => $result->campaign_url_id, 'result' => \json_encode((array) $result), 'created_at' => $created_at, 'url' => $this->build_url($result->path, $result->utm_source, $result->utm_medium, $result->utm_campaign, $result->utm_term, $result->utm_content)];
        }, $results);
    }
    public static function has_campaigns() : bool
    {
        $campaign_builder = new \IAWP\Campaign_Builder();
        return \count($campaign_builder->get_previously_created_campaigns()) > 0;
    }
    public static function delete_campaign(string $campaign_url_id)
    {
        $campaign_urls_table = \IAWP\Query::get_table_name(\IAWP\Query::CAMPAIGN_URLS);
        $delete_campaign = \IAWP\Illuminate_Builder::get_builder();
        $delete_campaign->from($campaign_urls_table)->where('campaign_url_id', '=', $campaign_url_id)->delete();
    }
}
