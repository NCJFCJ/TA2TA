<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.1
 * @package           CMB2 conditional logic
 *
 * @wordpress-plugin
 * Plugin Name:       CMB2 Conditional Logic
 * Plugin URI:        https://github.com/awran5/CMB2-conditional-logic
 * Description:       A lightweight plugin for adding conditional logic fields to CMB2 plugin. 
 * Version:           1.0.0
 * Author:            Awran5
 * Author URI:        https://github.com/awran5/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cmb2-conditional-logic
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
 
class TEC_cmb2_conditional_logic {
	/**
	 * This plugin's version number. Used for busting caches.
	 *
	 * @var string
	 */
	public $version = '1.0.1';

    /**
     * Construct the plugin object
     */
    public function __construct() {} 

    /**
     * Activate the plugin
     */
    public function activate() {
       add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
    }

    public function enqueues() {
    	wp_enqueue_script('tec_cmb2_conditional_logic', plugins_url( 'js/cmb2-conditional-logic.min.js', __FILE__ ),
    		array('jquery'), 
    		$this->version,
    		true
    	);
    }
} 

if( class_exists('TEC_cmb2_conditional_logic') ) {
	$TEC_cmb2_conditional_logic = new TEC_cmb2_conditional_logic();
	$TEC_cmb2_conditional_logic->activate();
}

