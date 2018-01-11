<?php
/**
 * Plugin Name: f(x) Primary Term
 * Plugin URI: http://genbumedia.com/plugins/fx-primary-term/
 * Description: Set Primary Term. Fallback for WP SEO.
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * Text Domain: fx-primary-term
 * Domain Path: /languages/
**/

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Constants.
define( 'FX_PRIMARY_TERM_VERSION', '1.0.0' );
define( 'FX_PRIMARY_TERM_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'FX_PRIMARY_TERM_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Plugin Init.
 *
 * @since 1.0.0
 */
function fx_primary_term_init() {
	require_once( FX_PRIMARY_TERM_PATH . 'includes/functions.php' );
}
add_action( 'plugins_loaded', 'fx_primary_term_init' );

