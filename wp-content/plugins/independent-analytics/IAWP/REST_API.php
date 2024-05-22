<?php

namespace IAWP;

use IAWP\Models\Visitor;
use IAWP\Utils\Device;
use IAWP\Utils\Request;
use IAWP\Utils\Salt;
use IAWP\Utils\Security;
use IAWP\Utils\String_Util;
use IAWP\Utils\URL;
/** @internal */
class REST_API
{
    public function __construct()
    {
        \add_action('wp_footer', [$this, 'echo_tracking_script']);
        \add_action('rest_api_init', [$this, 'register_rest_api']);
        // Support for PDF Viewer by Themencode (free and pro versions)
        \add_action('tnc_pvfw_viewer_head', [$this, 'echo_tracking_script']);
        \add_action('tnc_pvfw_head', [$this, 'echo_tracking_script']);
        // Support for Coming Soon and Maintenance by Colorlib
        \add_action('ccsm_header', [$this, 'echo_tracking_script']);
    }
    public function echo_tracking_script()
    {
        if (!\get_option('iawp_track_authenticated_users') && \is_user_logged_in()) {
            // Todo - Can we clear the cache plugins to make sure that the pages so tracking code as user logs in & out?
            return;
        }
        if ($this->block_user_role()) {
            return;
        }
        // Don't track post or page previews
        if (\is_preview()) {
            return;
        }
        $payload = [];
        $current_resource = \IAWP\Resource_Identifier::for_resource_being_viewed();
        if (\is_null($current_resource)) {
            return;
        }
        $payload['resource'] = $current_resource->type();
        if ($current_resource->has_meta()) {
            $payload[$current_resource->meta_key()] = $current_resource->meta_value();
        }
        $payload['page'] = \max(1, \get_query_var('paged'));
        $data = ['payload' => $payload];
        $data['signature'] = \md5(Salt::request_payload_salt() . \json_encode($data['payload']));
        $url = \get_rest_url() . 'iawp/search';
        ?>
        <script>
            (function () {
                document.addEventListener("DOMContentLoaded", function (e) {
                    if (document.hasOwnProperty("visibilityState") && document.visibilityState === "prerender") {
                        return;
                    }

                    <?php 
        if (!\defined('IAWP_TESTING')) {
            ?>
                        if (navigator.webdriver || /bot|crawler|spider|crawling|semrushbot|chrome-lighthouse/i.test(navigator.userAgent)) {
                            return;
                        }
                    <?php 
        }
        ?>
                    
                    let referrer_url = null;

                    if (typeof document.referrer === 'string' && document.referrer.length > 0) {
                        referrer_url = document.referrer;
                    }

                    const params = location.search.slice(1).split('&').reduce((acc, s) => {
                        const [k, v] = s.split('=')
                        return Object.assign(acc, {[k]: v})
                    }, {})

                    const url = "<?php 
        echo $url;
        ?>"
                    const body = {
                        referrer_url,
                        utm_source: params.utm_source,
                        utm_medium: params.utm_medium,
                        utm_campaign: params.utm_campaign,
                        utm_term: params.utm_term,
                        utm_content: params.utm_content,
                        gclid: params.gclid,
                        ...<?php 
        echo \json_encode($data);
        ?>
                    }
                    const xhr = new XMLHttpRequest()
                    xhr.open("POST", url, true)
                    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8")
                    xhr.send(JSON.stringify(body))
                })
            })();
        </script>
        <?php 
    }
    public function register_rest_api()
    {
        \register_rest_route('iawp', '/search', ['methods' => 'POST', 'callback' => [$this, 'track_view'], 'permission_callback' => function () {
            return \true;
        }]);
    }
    public function track_view($request)
    {
        if (Device::getInstance()->is_bot() && !\defined('IAWP_TESTING')) {
            return;
        }
        \IAWP\Migrations\Migrations::handle_migration_18_error();
        \IAWP\Migrations\Migrations::handle_migration_22_error();
        \IAWP\Migrations\Migrations::handle_migration_29_error();
        \IAWP\Migrations\Migrations::create_or_migrate();
        if (\IAWP\Migrations\Migrations::is_migrating()) {
            return;
        }
        if ($this->blocked_ip(Request::ip())) {
            return;
        }
        $visitor = new Visitor(Request::ip(), Request::user_agent());
        $signature = \md5(Salt::request_payload_salt() . \json_encode($request['payload']));
        $campaign = [];
        if (\IAWPSCOPED\iawp_is_pro()) {
            $campaign = ['utm_source' => $this->decode_or_nullify($request['utm_source']), 'utm_medium' => $this->decode_or_nullify($request['utm_medium']), 'utm_campaign' => $this->decode_or_nullify($request['utm_campaign']), 'utm_term' => $this->decode_or_nullify($request['utm_term']), 'utm_content' => $this->decode_or_nullify($request['utm_content'])];
        }
        if ($signature == $request['signature']) {
            new \IAWP\View($request['payload'], $this->calculate_referrer_url($request), $visitor, $campaign);
            return new \WP_REST_Response(['success' => \true], 200, ['X-IAWP' => 'iawp']);
        } else {
            return new \WP_REST_Response(['success' => \false], 200, ['X-IAWP' => 'iawp']);
        }
    }
    private function calculate_referrer_url($request) : ?string
    {
        $referrer_url = $request['referrer_url'];
        $url = new URL($referrer_url ?? '');
        if (!\is_null($this->decode_or_nullify($request['gclid'])) && $url->get_domain() !== 'googleads.g.doubleclick.net') {
            $referrer_url = 'https://googleads.iawp';
        }
        if (\is_null($referrer_url)) {
            return null;
        }
        return $referrer_url;
    }
    private function decode_or_nullify($string)
    {
        if (!isset($string)) {
            return null;
        }
        $safe_string = \trim(\urldecode($string));
        $safe_string = \str_replace('+', ' ', $safe_string);
        $safe_string = Security::string($safe_string);
        if (\strlen($safe_string) === 0) {
            return null;
        }
        return $safe_string;
    }
    private function blocked_ip($visitor_ip)
    {
        if (\defined('IAWP_TEST_IP')) {
            $visitor_ip = \IAWP_TEST_IP;
        }
        $blocked_ips = \IAWPSCOPED\iawp()->get_option('iawp_blocked_ips', []);
        if (\count($blocked_ips) == 0) {
            return \false;
        }
        if (\in_array($visitor_ip, $blocked_ips)) {
            return \true;
        }
        $wildcard_ips = [];
        foreach ($blocked_ips as $blocked_ip) {
            if (String_Util::str_contains($blocked_ip, '*')) {
                $wildcard_ips[] = $blocked_ip;
            }
        }
        if (\count($wildcard_ips) == 0) {
            return \false;
        }
        $delimeter = String_Util::str_contains($visitor_ip, '.') ? '.' : ':';
        $visitor_parts = \explode($delimeter, $visitor_ip);
        $goal = \count($visitor_parts);
        foreach ($wildcard_ips as $blocked_ip) {
            $blocked_parts = \explode($delimeter, $blocked_ip);
            $matches = 0;
            for ($i = 0; $i < \count($visitor_parts); $i++) {
                if (!\array_key_exists($i, $blocked_parts)) {
                    $matches++;
                } elseif ($visitor_parts[$i] == $blocked_parts[$i] || $blocked_parts[$i] == '*') {
                    $matches++;
                    continue;
                } else {
                    break;
                }
            }
            if ($matches == $goal) {
                return \true;
            }
        }
        return \false;
    }
    private function block_user_role() : bool
    {
        $blocked_roles = \IAWPSCOPED\iawp()->get_option('iawp_blocked_roles', []);
        foreach (\wp_get_current_user()->roles as $visitor_role) {
            if (\in_array($visitor_role, $blocked_roles)) {
                return \true;
            }
        }
        return \false;
    }
}
