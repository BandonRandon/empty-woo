<?php
/**
 * Plugin Name: Empty WooCommerce Product and Order data
 * Description: Removes Product and Order Data from WooCommerce
 * Version: 0.0.2
 * Author: Brooke.
 * Author URI: https://brooke.codes
 * Requires at least: 4.4
 * Tested up to: 5.1
 *
 */


// Get the modified uninstall file from WooCommerce if ?wc=true

function empty_woo(){

  if(  !isset( $_GET['wc_remove'] ) ||  $_GET['wc_remove'] !== "true"){
    return;
  }

  $uninstall_file = trailingslashit( dirname( __FILE__ ) ) . 'uninstall_products_and_orders.php';

	if ( file_exists ( $uninstall_file ) ) {
    include_once( $uninstall_file );
	}
}

add_action( 'admin_init', 'empty_woo' );
