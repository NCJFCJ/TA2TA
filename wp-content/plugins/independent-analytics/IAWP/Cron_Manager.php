<?php

namespace IAWP;

use IAWP\Utils\Salt;
use IAWPSCOPED\Proper\Timezone;
/** @internal */
class Cron_Manager
{
    public function __construct()
    {
        \add_action('update_option_iawp_refresh_salt', [$this, 'schedule_refresh_salt'], 10, 0);
        \add_action('add_option_iawp_refresh_salt', [$this, 'schedule_refresh_salt'], 10, 0);
        \add_action('iawp_refresh_salt', [$this, 'refresh_salt']);
    }
    public function schedule_refresh_salt()
    {
        if (\get_option('iawp_refresh_salt') !== \true && \get_option('iawp_refresh_salt') !== '1') {
            $this->unschedule_daily_salt_refresh();
            return;
        }
        $refresh_time = new \DateTime('tomorrow midnight', Timezone::site_timezone());
        \wp_schedule_event($refresh_time->getTimestamp(), 'daily', 'iawp_refresh_salt');
    }
    public function unschedule_daily_salt_refresh()
    {
        $timestamp = \wp_next_scheduled('iawp_refresh_salt');
        \wp_unschedule_event($timestamp, 'iawp_refresh_salt');
    }
    public function refresh_salt()
    {
        Salt::refresh_visitor_token_salt();
    }
}
