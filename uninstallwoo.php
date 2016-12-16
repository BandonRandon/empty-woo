<?php
/**
 * Plugin Name: Empty WooCommerce
 * Description: Empties a WooCommerce install without needing to delete the plugin
 * Version: 0.0.1-beta-1
 * Author: Brooke Dukes
 * Author URI: https://brooke.codes
 * Requires at least: 4.4
 * Tested up to: 4.7
 *
 */

//Set our constant to TRUE to allow for the removal of data.
if ( ! defined( 'WC_REMOVE_ALL_DATA' ) ) {
    DEFINE ( 'WC_REMOVE_ALL_DATA', TRUE );
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    DEFINE ( 'WP_UNINSTALL_PLUGIN', TRUE );
}

// Get the Uninstall file from WooCommerce

function empty_woo(){
	$uninstall_file = trailingslashit( dirname( __FILE__ ) ) . 'uninstall.php';

	if ( file_exists ( $uninstall_file ) ) {
    		include_once( $uninstall_file );
	}
}

add_action( 'admin_init', 'empty_woo' );
