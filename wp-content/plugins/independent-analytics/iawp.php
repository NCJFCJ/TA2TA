<?php

/**
 * Plugin Name:       Independent Analytics
 * Plugin URI:        https://independentwp.com/
 * Description:       User-friendly website analytics built for WordPress
 * Version:           2.3.2
 * Requires at least: 5.9
 * Tested up to:      6.4.3
 * Requires PHP:      7.3.33
 * Author:            Independent Analytics
 * Author URI:        https://independentwp.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       independent-analytics
 * Domain Path:       /languages
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'IAWP_FS' ) ) {
    IAWP_FS()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'IAWP_FS' ) ) {
        // Create a helper function for easy SDK access.
        function IAWP_FS()
        {
            global  $ia_fs ;
            
            if ( !isset( $ia_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_9944_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_9944_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $ia_fs = fs_dynamic_init( [
                    'id'             => '9944',
                    'slug'           => 'independent-analytics',
                    'premium_slug'   => 'independent-analytics-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_c228acaa28759b55d58766b1076d4',
                    'is_premium'     => false,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => [
                    'slug'    => 'independent-analytics',
                    'contact' => false,
                    'support' => false,
                    'pricing' => false,
                ],
                    'is_live'        => true,
                ] );
            }
            
            return $ia_fs;
        }
        
        // Init Freemius.
        IAWP_FS();
        // Signal that SDK was initiated.
        do_action( 'ia_fs_loaded' );
    }
    
    // ... Your plugin's main file logic ...
    require_once rtrim( plugin_dir_path( __FILE__ ), DIRECTORY_SEPARATOR ) . '/iawp-bootstrap.php';
}
