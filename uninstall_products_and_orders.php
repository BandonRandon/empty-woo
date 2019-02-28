<?php
/**
 * WooCommerce Uninstall
 *
 * Uninstalling WooCommerce deletes user roles, pages, tables, and options.
 *
 * @package WooCommerce\Uninstaller
 * @version 2.3.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb, $wp_version;

/*
 * Only remove ALL product and page data if WC_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
	//include_once dirname( __FILE__ ) . '/includes/class-wc-install.php';


	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}woocommerce_attribute_taxonomies';" ) ) {
		$wc_attributes = array_filter( (array) $wpdb->get_col( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies;" ) );
	} else {
		$wc_attributes = array();
	}


		// Delete posts + data.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'product', 'product_variation', 'shop_coupon', 'shop_order', 'shop_order_refund' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

	$wpdb->query( "DELETE FROM {$wpdb->comments} WHERE comment_type IN ( 'order_note' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->commentmeta} meta LEFT JOIN {$wpdb->comments} comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL;" );

	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woocommerce_order_items" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woocommerce_order_itemmeta" );

	// Delete terms if > WP 4.2 (term splitting was added in 4.2).
	if ( version_compare( $wp_version, '4.2', '>=' ) ) {
		// Delete term taxonomies.
		foreach ( array( 'product_cat', 'product_tag', 'product_shipping_class', 'product_type' ) as $taxonomy ) {
			$wpdb->delete(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => $taxonomy,
				)
			);
		}

		// Delete term attributes.
		foreach ( $wc_attributes as $taxonomy ) {
			$wpdb->delete(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => 'pa_' . $taxonomy,
				)
			);
		}

		// Delete orphan relationships.
		$wpdb->query( "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;" );

		// Delete orphan terms.
		$wpdb->query( "DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL;" );

		// Delete orphan term meta.
		if ( ! empty( $wpdb->termmeta ) ) {
			$wpdb->query( "DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL;" );
		}
	}

	//re-create tables

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$collate = '';

	if ( $wpdb->has_cap( 'collation' ) ) {
		$collate = $wpdb->get_charset_collate();
	}

	$tables = "
	CREATE TABLE {$wpdb->prefix}woocommerce_order_items (
	  order_item_id BIGINT UNSIGNED NOT NULL auto_increment,
	  order_item_name TEXT NOT NULL,
	  order_item_type varchar(200) NOT NULL DEFAULT '',
	  order_id BIGINT UNSIGNED NOT NULL,
	  PRIMARY KEY  (order_item_id),
	  KEY order_id (order_id)
	) $collate;
	CREATE TABLE {$wpdb->prefix}woocommerce_order_itemmeta (
	  meta_id BIGINT UNSIGNED NOT NULL auto_increment,
	  order_item_id BIGINT UNSIGNED NOT NULL,
	  meta_key varchar(255) default NULL,
	  meta_value longtext NULL,
	  PRIMARY KEY  (meta_id),
	  KEY order_item_id (order_item_id),
	  KEY meta_key (meta_key(32))
	) $collate;
";

	dbDelta( $tables );

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
