<?php
/**
 * Plugin Name: f(x) Primary Term
 * Plugin URI: http://genbumedia.com/plugins/fx-primary-term/
 * Description: Functionality plugin to set primary term of taxonomy in a post.
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
 * WP SEO Notice.
 * Primary Term is already available in WP SEO plugin.
 *
 * @since 1.0.0
 * @link https://wordpress.org/plugins/wordpress-seo/
 */
function fx_primary_term_wp_seo_notice() {
?>

<div class="notice notice-error">
	<?php echo wpautop( __( 'Please deactivate f(x) Primary Term Plugin. Primary Term functionality is already available in WP SEO plugin.', 'fx-primary-term' ) ); ?>
</div>

<?php
}

// Bail if WP SEO Active (Yoast)
if ( defined( 'WPSEO_VERSION' ) && WPSEO_VERSION ) {
	add_action( 'admin_notices', 'fx_primary_term_wp_seo_notice' );
	return;
}

/**
 * PHP Version Notice.
 * Minimum PHP Version is 5.3
 *
 * @since 1.0.0
 */
function fx_primary_term_php_notice() {
?>

<div class="notice notice-error">
	<?php
	// translators: %1$s minimum PHP version, %2$s current PHP version.
	echo wpautop( sprintf( __( 'f(x) Primary Term Plugin requires at least PHP %1$s. You are running PHP %2$s. Please upgrade and try again.', 'fx-primary-term' ), '<code>5.3.0</code>', '<code>' . PHP_VERSION . '</code>' ) );
	?>
</div>

<?php
}

// Check for PHP version.
if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action( 'admin_notices', 'fx_primary_term_php_notice' );
	return;
}

/**
 * Plugin Init.
 *
 * @since 1.0.0
 */
function fx_primary_term_init() {

	// Load text domain.
	load_plugin_textdomain( dirname( plugin_basename( __FILE__ ) ), false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load plugin function.
	require_once( FX_PRIMARY_TERM_PATH . 'includes/functions.php' );
}
add_action( 'plugins_loaded', 'fx_primary_term_init' );
