<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( hp_is_plugin_activated( 'affiliate-wp', 'affiliate-wp.php' ) ) {

	$affiliate_wp = new HPack_Set_API_Servers();

	$affiliate_wp->over_api_servers( 'plugin.affiliatewp.com/wp-content/notifications.json' );
	$affiliate_wp->init();



	if ( ! function_exists( 'hp_affwp_license_global' ) ) {
		/**
		 * Hp_affwp_license_global.
		 *
		 * @version 1.0.0
		 * @since  2.0.25
		 */
		function hp_affwp_license_global() {
			$affwp_settings = get_option( 'affwp_settings' );
			if ( is_array( $affwp_settings ) ) {
				$affwp_settings['license_status']          = new stdClass();
				$affwp_settings['license_status']->license = 'valid';
				$affwp_settings['license_key']             = '';
				update_option( 'affwp_settings', $affwp_settings );
			}
			$license_data = 'valid';
			set_transient( 'affwp_license_check', $license_data, DAY_IN_SECONDS );
			delete_option( 'affwp_drm_current_state' );
			HP_check_options( 'affwp_drm_current_state', 'valid' );
		}

		add_action( 'plugins_loaded', 'hp_affwp_license_global', PHP_INT_MAX );
	}
}
