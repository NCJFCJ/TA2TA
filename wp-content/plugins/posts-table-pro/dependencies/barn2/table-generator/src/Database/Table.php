<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Database;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\BerlinDB\Database\Table as BaseTable;
/**
 * Setup the custom database table definition.
 */
class Table extends BaseTable
{
    /**
     * {@inheritdoc}
     */
    protected $prefix = 'barn2';
    /**
     * {@inheritdoc}
     */
    protected $name = 'tables';
    /**
     * {@inheritdoc}
     */
    protected $version = 202208024000;
    /**
     * {@inheritdoc}
     */
    protected $schema = __NAMESPACE__ . '\\Schema';
    /**
     * {@inheritdoc}
     *
     * @var array<string, string>
     */
    protected $upgrades = ['202208024000' => 202208024000];
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function set_schema()
    {
        $this->schema = "\r\n\t\t\tid bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\r\n\t\t\ttitle text NOT NULL,\r\n\t\t\tsettings json NULL,\r\n\t\t\tis_completed tinyint(1) signed NOT NULL default '0',\r\n\t\t\tPRIMARY KEY (id),\r\n\t\t\tKEY table_is_completed (id, is_completed)";
    }
    /**
     * Initialize the database table and set the prefix.
     *
     * @param string $plugin_prefix
     */
    public function __construct(string $plugin_prefix)
    {
        $this->prefix = $plugin_prefix;
        parent::__construct();
    }
    /**
     * Upgrade to version 202208024000
     * - Update the tables db table to have `is_completed`
     *
     * @return bool
     */
    protected function __202208024000()
    {
        $result = $this->column_exists('is_completed');
        if (\false === $result) {
            $result = $this->get_db()->query("\r\n\t\t\t\tALTER TABLE {$this->table_name} ADD COLUMN `is_completed` tinyint SIGNED NOT NULL default '0' AFTER `settings`;\r\n\t\t\t");
        }
        return $this->is_success($result);
    }
}
