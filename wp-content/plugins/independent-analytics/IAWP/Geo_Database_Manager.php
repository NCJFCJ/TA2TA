<?php

namespace IAWP;

use DateInterval;
use DateTime;
use IAWPSCOPED\Proper\Timezone;
use Throwable;
use ZipArchive;
/** @internal */
class Geo_Database_Manager
{
    // Updating the database? Read the Wiki page "Updating The Geo Database"
    // https://github.com/andrewjmead/independent-analytics/wiki/Updating-The-Geo-Database
    private $zip_download_url = 'https://assets.independentwp.com/iawp-geo-db-5.mmdb.zip';
    private $raw_download_url = 'https://assets.independentwp.com/iawp-geo-db-5.mmdb';
    private $database_checksum = '6fc58b0047547e4a7f718e4d3552491c';
    public function download() : void
    {
        if (!$this->should_download()) {
            return;
        }
        \update_option('iawp_is_database_downloading', '1');
        $this->download_zip_database_and_extract();
        if (!$this->is_existing_database_valid()) {
            $this->download_raw_database();
        } else {
        }
        \update_option('iawp_is_database_downloading', '0');
        $this->record_attempt();
    }
    public function should_download() : bool
    {
        if (!$this->has_attempt_interval_elapsed()) {
            return \false;
        }
        if (\get_option('iawp_is_database_downloading', '0') === '1') {
            return \false;
        }
        if ($this->is_existing_database_valid()) {
            $this->record_attempt();
            return \false;
        }
        return \true;
    }
    public function delete() : void
    {
        \wp_delete_file(self::path_to_database());
    }
    private function download_zip_database_and_extract() : void
    {
        \wp_remote_get($this->zip_download_url, ['stream' => \true, 'filename' => $this->path_to_database_zip(), 'timeout' => 60]);
        try {
            $zip = new ZipArchive();
            if ($zip->open($this->path_to_database_zip()) === \true) {
                $zip->extractTo(\IAWPSCOPED\iawp_upload_path_to('', \true));
                $zip->close();
            }
        } catch (Throwable $e) {
            // It's ok to fail
        }
        \wp_delete_file($this->path_to_database_zip());
    }
    private function download_raw_database() : void
    {
        \wp_remote_get($this->raw_download_url, ['stream' => \true, 'filename' => self::path_to_database(), 'timeout' => 60]);
    }
    private function is_existing_database_valid() : bool
    {
        if (!\file_exists(self::path_to_database())) {
            return \false;
        }
        try {
            return \verify_file_md5(self::path_to_database(), $this->database_checksum);
        } catch (Throwable $e) {
            return \false;
        }
    }
    private function has_attempt_interval_elapsed() : bool
    {
        $last_attempted_at = $this->last_attempted_at();
        $interval = new DateInterval('PT30M');
        if (\is_null($last_attempted_at)) {
            return \true;
        }
        $is_past_interval_time = $last_attempted_at->add($interval) < new DateTime('now', Timezone::utc_timezone());
        if ($is_past_interval_time) {
            return \true;
        }
        return \false;
    }
    private function last_attempted_at() : ?DateTime
    {
        $option_value = \get_option('iawp_geo_database_download_last_attempted_at', \false);
        if (!$option_value) {
            return null;
        }
        try {
            return new DateTime($option_value, Timezone::utc_timezone());
        } catch (Throwable $e) {
            return null;
        }
    }
    private function record_attempt() : void
    {
        $now = new DateTime('now', Timezone::utc_timezone());
        $value = $now->format('Y-m-d\\TH:i:s');
        \update_option('iawp_geo_database_download_last_attempted_at', $value);
    }
    private function path_to_database_zip() : string
    {
        return \IAWPSCOPED\iawp_upload_path_to('iawp-geo-db.zip', \true);
    }
    public static function path_to_database() : string
    {
        return \IAWPSCOPED\iawp_upload_path_to('iawp-geo-db.mmdb', \true);
    }
}
