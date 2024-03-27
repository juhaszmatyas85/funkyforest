<?php

/*
Plugin Name: HuCommerce | Magyar WooCommerce kiegészítések
Plugin URI: https://www.hucommerce.hu/
Description: Hasznos javítások a magyar nyelvű WooCommerce webáruházakhoz.

Version: 2023.3.1

Author: HuCommerce.hu
Author URI: https://www.hucommerce.hu/
Developer: Surbma
Developer URI: https://surbma.com/

Text Domain: surbma-magyar-woocommerce
Domain Path: /languages

WC requires at least: 4.6
WC tested up to: 8.4

License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Prevent direct access
defined( 'ABSPATH' ) || exit;

define( 'SURBMA_HC_PLUGIN_VERSION_NUMBER', '2023.3.1' );
define( 'SURBMA_HC_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'SURBMA_HC_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'SURBMA_HC_PLUGIN_FILE', __FILE__ );

// Check if WooCommerce is active
add_action( 'plugins_loaded', function() {
	if ( class_exists( 'WooCommerce' ) ) {
		// Start the engines.
		require_once SURBMA_HC_PLUGIN_DIR . '/lib/start.php';
	} else {
		// Notify user, that WooCommerce is not active.
		add_action( 'admin_notices', function() {
			?>
			<div class="notice notice-error">
				<div style="padding: 20px;">
					<a href="https://www.hucommerce.hu" target="_blank"><img src="<?php echo esc_url( SURBMA_HC_PLUGIN_URL ); ?>/assets/images/hucommerce-logo.png" alt="HuCommerce" class="alignright"></a>
					<p><strong><?php esc_html_e( 'Thank you for installing HuCommerce plugin!', 'surbma-magyar-woocommerce' ); ?></strong></p>
					<p><?php esc_html_e( 'To use HuCommerce plugin, you must activate WooCommerce also.', 'surbma-magyar-woocommerce' ); ?>
					<br><?php esc_html_e( 'If you don\'t want to use WooCommerce, please deactivate HuCommerce plugin!', 'surbma-magyar-woocommerce' ); ?></p>
					<p><a href="<?php admin_url(); ?>plugins.php" class="button button-primary button-large"><span class="dashicons dashicons-admin-plugins" style="position: relative;top: 5px;left: -3px;"></span> <?php esc_html_e( 'Plugins' ); ?></a></p>
				</div>
			</div>
			<?php
		} );
	}
} );

// Declare compatibility: Custom order tables
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

// Declare incompatibility: Cart & Checkout blocks
add_action( 'before_woocommerce_init', function() {

    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {

        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );

    }

} );

// Create a check for WooCommerce version. Used for deprecated functions for older WooCommerce versions.
function surbma_hc_woocommerce_version_check( $version ) {
	if ( class_exists( 'WooCommerce' ) ) {
		global $woocommerce;
		if ( version_compare( $woocommerce->version, $version, '>=' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Overwrite iThemes Security plugin's PHP Execution settings to enable HuCommerce plugin's product-price-history-display.php file.
 * Settings -> Advanced -> System Tweaks -> PHP Execution -> Disable PHP in Plugins
**/
if ( !has_filter( 'itsec_filter_apache_server_config_modification' ) ) {
	add_filter( 'itsec_filter_apache_server_config_modification', function ( $modification ) {
		$modification = str_replace( 'RewriteRule ^wp\-content/plugins/.*\.(?:php[1-7]?|pht|phtml?|phps)\.?$ - [NC,F]', 'RewriteRule ^wp\-content/plugins/(?!surbma\-magyar\-woocommerce/modules\-hu/product\-price\-history\-display\.php).*\.(?:php[1-7]?|pht|phtml?|phps)\.?$ - [NC,F]', $modification );
		return $modification;
	}, PHP_INT_MAX - 5 );
}


