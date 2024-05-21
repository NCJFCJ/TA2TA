<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Database;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Content_Table;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\BerlinDB\Database\Query as BaseQuery;
/**
 * Base query class for our custom database table.
 */
class Query extends BaseQuery
{
    /**
     * {@inheritdoc}
     */
    protected $prefix = 'barn2';
    /**
     * {@inheritdoc}
     */
    protected $table_name = 'tables';
    /**
     * {@inheritdoc}
     */
    protected $table_alias = 'b2tbs';
    /**
     * {@inheritdoc}
     */
    protected $table_schema = __NAMESPACE__ . '\\Schema';
    /**
     * {@inheritdoc}
     */
    protected $item_name = 'table';
    /**
     * {@inheritdoc}
     */
    protected $item_name_plural = 'tables';
    /**
     * {@inheritdoc}
     */
    protected $item_shape = Content_Table::class;
    /**
     * {@inheritdoc}
     */
    protected $cache_group = 'tables';
    /**
     * {@inheritdoc}
     */
    public function __construct(string $plugin_prefix, $query = [])
    {
        $this->prefix = $plugin_prefix;
        parent::__construct($query);
    }
    /**
     * {@inheritdoc}
     */
    public function get_table_name()
    {
        return $this->get_db()->{$this->table_name};
    }
}
