# empty-woo
Plugin to Force all Woo Data to be Erased  

## Usage

Define `WP_UNINSTALL_PLUGIN` and `WC_REMOVE_ALL_DATA` as `TRUE` in wp-config.php. Next, visit WP-Admin and add `?wc_remove=true` to an admin URL. Finally, refresh the page.

```
if ( ! defined( 'WC_REMOVE_ALL_DATA' ) ) {
    DEFINE ( 'WC_REMOVE_ALL_DATA', TRUE );
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    DEFINE ( 'WP_UNINSTALL_PLUGIN', TRUE );
}
```
