<?php

namespace IAWP\Migrations;

use IAWP\Utils\Dir;
/** @internal */
class Migration_18 extends \IAWP\Migrations\Migration
{
    /**
     * @inheritdoc
     */
    protected $database_version = '18';
    /**
     * @inheritDoc
     */
    protected function migrate() : void
    {
        try {
            $directory = \trailingslashit(\wp_upload_dir()['basedir']) . 'iawp/';
            Dir::delete($directory);
        } catch (\Throwable $e) {
        }
    }
}
