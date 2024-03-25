<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              piwebsolution.com
 * @since             1.4.7.3
 * @package           Pisol_Fsnw
 *
 * @wordpress-plugin
 * Plugin Name:       Free shipping notification WooCommerce Pro
 * Plugin URI:        piwebsolution.com/free-shipping-notification-documentation
 * Description:       Free shipping notification for woocommerce will show a notification bar on the top, how much more you need to buy to get free shipping
 * Version:           1.4.7.3
 * Author:            PI Websolution
 * Author URI:        piwebsolution.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pisol-fsnw
 * Domain Path:       /languages
 * WC tested up to: 5.7.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* 
    Making sure woocommerce is there 
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(!is_plugin_active( 'woocommerce/woocommerce.php')){
    function pi_fsnw_pro_woocommerce_error_notice() {
        ?>
        <div class="error notice">
            <p><?php _e( 'Please Install and Activate WooCommerce plugin, without that this plugin cant work', 'pi-edd' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_fsnw_pro_woocommerce_error_notice' );
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}

if(is_plugin_active( 'free-shipping-notification-woocommerce/pisol-fsnw.php')){
    function pi_pisol_free_error_notice() {
        ?>
        <div class="error notice">
            <p><?php _e( 'Please uninstall or deactivate the Free version first'); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_pisol_free_error_notice' );
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}else{

/**
 * Currently plugin version.
 * Start at version 1.4.7.3 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PISOL_FSNW_VERSION', '1.4.7.3' );
define( 'PISOL_FSNW_DELETE_SETTING', false );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pisol-fsnw-activator.php
 */
function activate_pisol_fsnw() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pisol-fsnw-activator.php';
	Pisol_Fsnw_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pisol-fsnw-deactivator.php
 */
function deactivate_pisol_fsnw() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pisol-fsnw-deactivator.php';
	Pisol_Fsnw_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pisol_fsnw' );
register_deactivation_hook( __FILE__, 'deactivate_pisol_fsnw' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pisol-fsnw.php';

add_action('init', 'pisol_fsnw_update_checking');
function pisol_fsnw_update_checking()
{
    new pisol_update_notification_v1(plugin_basename(__FILE__), PISOL_FSNW_VERSION);
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pisol_fsnw() {

	$plugin = new Pisol_Fsnw();
	$plugin->run();

}
run_pisol_fsnw();


}