<?php

/**
 * The main plugin file for Table Generator
 *
 * This file is included during the WordPress bootstrap process if the plugin is active.
 *
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 *
 * @wordpress-plugin
 * Plugin Name:     Table Generator
 * Plugin URI:      #
 * Description:
 * Version:         1.3.2
 * Author:          Barn2 Plugins
 * Author URI:      https://barn2.com
 * Text Domain:     table-generator
 * Domain Path:     /languages
 * Update URI:      https://barn2.com/table-generator/
 *
 * Copyright:       Barn2 Media Ltd
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace Barn2\Plugin\Table_Generator;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Block;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Database\Query;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Database\Table;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Demo_Extra_Fields;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Page_Header;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Plugin_Installer;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps\Columns;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps\Create;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps\Filters;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps\Performance;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps\Refine;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps\Sort;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps\Welcome;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Table_Generator;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Util;
\defined('ABSPATH') || exit;
const PLUGIN_VERSION = '1.3.2';
const PLUGIN_FILE = __FILE__;
require __DIR__ . '/vendor/autoload.php';
\add_action('init', function () {
    if (!\function_exists('\\Barn2\\Plugin\\Posts_Table_Pro\\ptp')) {
        return;
    }
    $generator = new Table_Generator(\Barn2\Plugin\Posts_Table_Pro\ptp(), 'ptp', new Welcome(), new Create(), new Refine(), new Columns(), new Filters(), new Performance(), new Sort());
    $generator->set_library_path(\plugin_dir_path(__FILE__));
    $generator->set_library_url(\plugin_dir_url(__FILE__));
    $generator->set_options_key('ptp_shortcode_defaults');
    $generator->set_options_mapping(['content_type' => 'post_type', 'lazyload' => 'lazy_load', 'cache' => 'cache', 'search_box' => 'search_box', 'sortby' => 'sort_by', 'sort_order' => 'sort_order']);
    $generator->add_datastore_field('refine');
    $generator->add_datastore_field('columns');
    $generator->add_datastore_field('filters');
    $generator->add_datastore_field('filter_mode');
    $generator->add_datastore_field('sortby');
    $generator->set_shortcode('posts_table_template');
    $generator->config(['pluginInstallerPage' => \admin_url('admin.php?page=easy-post-types-fields-setup-wizard&action=add'), 'settingsPage' => \admin_url('admin.php?page=posts_table'), 'licenseStepTitle' => \sprintf(__('Welcome to %s','posts-table-pro' ), \Barn2\Plugin\Posts_Table_Pro\ptp()->get_name()), 'licenseStepDescription' => __('Create and display beautiful tables of your website content.','posts-table-pro' ), 'indexDescription' => \sprintf(__('Create and manage your post tables on this page. Display them using the Post Table block or shortcode.','posts-table-pro' ), \admin_url('admin.php?page=posts_table')), 'isPluginInstalled' => Plugin_Installer::get_plugin_status('easy-post-types-fields/easy-post-types-fields.php')]);
    $generator->set_default_columns(\Barn2\Plugin\Posts_Table_Pro\Util\Columns_Util::column_defaults());
    $generator->set_args_resolver(\Barn2\Plugin\Posts_Table_Pro\Table_Args::class);
    $generator->set_extra_fields(Demo_Extra_Fields::class);
    $generator->boot();
    $gutenberg_block = new Block($generator);
    $gutenberg_block->set_label('Post Table');
    $gutenberg_block->set_instructions(__('Select a pre-saved posts table. Go to the <a href="#" target="_blank">Post Tables section</a> of the Dashboard to create or edit your tables.','posts-table-pro' ));
    $gutenberg_block->set_description(__('An interactive table of your posts, pages, or any custom post type.','posts-table-pro' ));
    $gutenberg_block->set_options_doc_url('#');
    $gutenberg_block->boot();
}, 200);
\add_action('plugins_loadedd', function () {
    $table = new Table('ptp');
    $table->maybe_upgrade();
});
