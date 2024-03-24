<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

/*
 *
 * HuCommerce Whitelist
 *
 * We have a whitelist for domains, that don't need API key to use PRO version
 *
*/

// Get the current website's domain
$current_domain = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : parse_url( get_site_url(), PHP_URL_HOST );

// Whitelist enabled domains
if ( ( ( defined( 'SURBMA_HC_WHITELIST' ) && false !== SURBMA_HC_WHITELIST ) && ( 'www.hucommerce.hu' == $current_domain || 'demo.hucommerce.hu' == $current_domain || 'woocommerce.local' == $current_domain ) ) || ( !defined( 'SURBMA_HC_WHITELIST' ) && ( 'www.hucommerce.hu' == $current_domain || 'demo.hucommerce.hu' == $current_domain || 'woocommerce.local' == $current_domain ) ) ) {
	$status = 'active';
	$whitelisted = true;
} else {
	// Check for manual request
	$manual_request = isset( $_GET['hc-request'] ) ? true : false;

	// Prepare to check the difference between current time and last checked time
	$license_status = get_option( 'surbma_hc_license_status', array() );
	$last_check = isset( $license_status['last_check'] ) && $license_status['last_check'] ? $license_status['last_check'] : false;
	$current_time = current_datetime();
	$current_time = $current_time->getTimestamp() + $current_time->getOffset();
	$last_check_diff = $last_check ? $current_time - $last_check : '259201';

	// Update license status after 3 days OR manual request has been processed
	if ( $last_check_diff > ( 3 * 24 * 60 * 60 ) || $manual_request ) {
		$response = wp_remote_get( 'https://pub-6f3752f70b634a50bf297697d2ae59a6.r2.dev/hucommerce-whitelist.json' );
		$domains = array();

		// We have the JSON file
		if ( !is_wp_error( $response ) ) {

			if ( is_array( $response ) && $response['body'] ) {
				$domains = json_decode( $response['body'], true );
			}

			// Check if current domain is in the whitelist. If it is, enable PRO version.
			if ( is_array( $domains ) && in_array( $current_domain, $domains ) ) {
				$status = 'active';
				$whitelisted = true;

				$license_status = array(
					'last_check' => $current_time,
					'status' => 'active',
					'success' => true,
					'unlimited_activations' => true,
					'total_activations_purchased' => '',
					'total_activations' => '',
					'activations_remaining' => '',
					'activated' => true
				);
				update_option( 'surbma_hc_license_status', $license_status );
			}
		}
	}
}

// Do the stuff if website is not whitelisted
if ( !isset( $whitelisted ) ) :

	// Create the API request URL
	function surbma_hc_license_create_url( $request_args ) {
		$base_url = 'https://www.hucommerce.hu/';
		$base_url = add_query_arg( 'wc-api', 'wc-am-api', $base_url );
		return $base_url . '&' . http_build_query( $request_args );
	}

	// Update the surbma_hc_license_status option
	function surbma_hc_license_status_update() {
		$license_options = get_option( 'surbma_hc_license', array() );

		// API variables
		$api_key = isset( $license_options['licensekey'] ) && $license_options['licensekey'] ? $license_options['licensekey'] : false;
		$product_id = isset( $license_options['product_id'] ) && $license_options['product_id'] ? $license_options['product_id'] : false;
		$instance = isset( $license_options['instance'] ) && $license_options['instance'] ? $license_options['instance'] : false;

		$license_status = get_option( 'surbma_hc_license_status', array() );

		$status = isset( $license_status['status'] ) && $license_status['status'] ? $license_status['status'] : 'free';

		// Check status
		if ( $api_key && $product_id && $instance ) {
			$request_args = array(
				'wc_am_action' => 'status',
				'api_key'      => $api_key,
				'product_id'   => $product_id,
				'instance' 	   => $instance
			);
			$request_url = surbma_hc_license_create_url( $request_args );
			$request_response = wp_remote_get( $request_url );
			$request_response_array = array();

			if ( !is_wp_error( $request_response ) && is_array( $request_response ) && $request_response['body'] ) {
				$request_response_array = json_decode( $request_response['body'], true );
			}
		}

		$success = isset( $request_response_array['success'] ) && 1 == $request_response_array['success'] ? true : false;
		$unlimited_activations = isset( $request_response_array['data']['unlimited_activations'] ) && 1 == $request_response_array['data']['unlimited_activations'] ? true : false;
		$total_activations_purchased = isset( $request_response_array['data']['total_activations_purchased'] ) && $request_response_array['data']['total_activations_purchased'] ? $request_response_array['data']['total_activations_purchased'] : false;
		$total_activations = isset( $request_response_array['data']['total_activations'] ) && $request_response_array['data']['total_activations'] ? $request_response_array['data']['total_activations'] : false;
		$activations_remaining = isset( $request_response_array['data']['activations_remaining'] ) && $request_response_array['data']['activations_remaining'] ? $request_response_array['data']['activations_remaining'] : false;
		$activated = isset( $request_response_array['data']['activated'] ) && 1 == $request_response_array['data']['activated'] ? true : false;

		/* TESTING
		$api_key = true;
		$success = true;
		$activated = true;
		*/

		// Set the license status
		if ( $api_key ) {
			if ( $success ) {
				if ( $activated ) {
					$status = 'active'; // Set plugin license to active, if license is activated and user has an active subscription.
				} else {
					$status = 'inactive'; // Set plugin license to inactive, if license key is valid, but not activated.
				}
			} else {
				// $status = 'invalid'; // Set plugin license to invalid, if user has set a license key, but it is invalid or expired.
				$status = $status; // Keep existing status, as user has an API key, but the request was not successful to get license data from hucommerce.hu website.
			}
		} else {
			$status = 'free'; // Set plugin license to free if no license key given.
		}

		$current_time = current_datetime();
		$current_time = $current_time->getTimestamp() + $current_time->getOffset();

		$license_status = array(
			'last_check' => $current_time,
			'status' => $status,
			'success' => $success,
			'unlimited_activations' => $unlimited_activations,
			'total_activations_purchased' => $total_activations_purchased,
			'total_activations' => $total_activations,
			'activations_remaining' => $activations_remaining,
			'activated' => $activated
		);
		update_option( 'surbma_hc_license_status', $license_status );
	}

	// Everyday I'm shuffling...I mean updating the status
	add_action( 'init', function() {
		$license_status = get_option( 'surbma_hc_license_status', array() );
		$last_check = isset( $license_status['last_check'] ) && $license_status['last_check'] ? $license_status['last_check'] : false;
		$current_time = current_datetime();
		$current_time = $current_time->getTimestamp() + $current_time->getOffset();
		$last_check_diff = $last_check ? $current_time - $last_check : '259201';

		if ( $last_check_diff > ( 3 * 24 * 60 * 60 ) ) {
			surbma_hc_license_status_update();
		}
	} );

	// Send the requested action to the API Manager
	function surbma_hc_license_api_manager_action( $action ) {
		// Stop if action is not valid
		if ( 'activate' != $action && 'deactivate' != $action && 'status' != $action ) {
			return;
		}

		$license_options = get_option( 'surbma_hc_license', array() );
		$home_url = parse_url( get_option( 'home' ) );

		// API variables
		$api_key = isset( $license_options['licensekey'] ) && $license_options['licensekey'] ? $license_options['licensekey'] : false;
		$product_id = isset( $license_options['product_id'] ) && $license_options['product_id'] ? $license_options['product_id'] : false;
		$instance = isset( $license_options['instance'] ) && $license_options['instance'] ? $license_options['instance'] : false;
		$object = isset( $home_url['host'] ) ? $home_url['host'] : '';

		// Stop if we don't have the required data
		if ( !$api_key || !$product_id || !$instance ) {
			return;
		}

		// Preparing activate request
		if ( 'activate' == $action ) {
			$request_args = array(
				'wc_am_action' => 'activate',
				'api_key'      => $api_key,
				'product_id'   => $product_id,
				'instance' 	   => $instance,
				'object' 	   => $object
			);
		}

		// Preparing deactivate request
		if ( 'deactivate' == $action ) {
			$request_args = array(
				'wc_am_action' => 'deactivate',
				'api_key'      => $api_key,
				'product_id'   => $product_id,
				'instance' 	   => $instance
			);
		}

		// Preparing status request
		if ( 'status' == $action ) {
			$request_args = array(
				'wc_am_action' => 'status',
				'api_key'      => $api_key,
				'product_id'   => $product_id,
				'instance' 	   => $instance
			);
		}

		// Execute request
		$request_url = surbma_hc_license_create_url( $request_args );
		$request_response = wp_remote_get( $request_url );
	}

	// License management page actions
	add_action( 'current_screen', function() {
		$screen = get_current_screen();
		global $surbma_hc_license_page;

		// Stop if we are not on the License Management page
		if ( $surbma_hc_license_page != $screen->base ) {
			return;
		}

		$update_request = isset( $_GET['settings-updated'] ) && true == $_GET['settings-updated'] ? true : false;
		$manual_request = isset( $_GET['hc-request'] ) ? $_GET['hc-request'] : false;

		// Update license status if License Management page settings are updated
		if ( $update_request ) {
			surbma_hc_license_status_update();
		}

		// Stop if there is no manual request
		if ( !$manual_request ) {
			return;
		}

		// Stop if there is manual request, but not valid
		if ( 'activate' != $manual_request && 'deactivate' != $manual_request && 'status' != $manual_request ) {
			return;
		}

		// We are indeed on the License Management page and there is a valid manual request...

		// Activate request sent from HuCommerce Pro menu with the "Frissítés & Újra aktiválás" button
		if ( 'activate' == $manual_request ) {
			surbma_hc_license_api_manager_action( 'activate' );
		}

		// Dectivate request sent from HuCommerce Pro menu with the "Megtartás & deaktiválás" button
		if ( 'deactivate' == $manual_request ) {
			surbma_hc_license_api_manager_action( 'deactivate' );
		}

		// Status request sent from HuCommerce Pro menu with the "API szinkronizálás" link
		if ( 'status' == $manual_request ) {
			surbma_hc_license_api_manager_action( 'status' );
		}

		// Update license status
		surbma_hc_license_status_update();

		// Remove query parameter from url
		$url = esc_url_raw( remove_query_arg( 'hc-request' ) );
		$url = add_query_arg( 'hc-request-finished', $manual_request, $url );
		wp_redirect( $url );
	} );

	// Fires when the surbma_hc_license option is added
	add_action( 'add_option_surbma_hc_license', function( $name, $value ) {
		// update_option( 'surbma_hc_license_test', $value['licensekey'] );
		$home_url = parse_url( get_option( 'home' ) );

		// API variables
		$api_key = isset( $value['licensekey'] ) && $value['licensekey'] ? $value['licensekey'] : false;
		$product_id = isset( $value['product_id'] ) && $value['product_id'] ? $value['product_id'] : '1135';
		$instance = isset( $value['instance'] ) && $value['instance'] ? $value['instance'] : false;
		$object = isset( $home_url['host'] ) ? $home_url['host'] : '';

		$request_args = array(
			'wc_am_action'	=> 'activate',
			'api_key'		=> $api_key,
			'product_id'	=> $product_id,
			'instance'		=> $instance,
			'object'		=> $object
		);

		$request_url = surbma_hc_license_create_url( $request_args );
		$request_response = wp_remote_get( $request_url );

		// Update license status
		surbma_hc_license_status_update();
	}, 10, 2 );

	// Fires when the surbma_hc_license option is updated with new values
	add_action( 'update_option_surbma_hc_license', function( $old_value, $value ) {
		$home_url = parse_url( get_option( 'home' ) );

		// API variables
		$api_key = isset( $value['licensekey'] ) && $value['licensekey'] ? $value['licensekey'] : false;
		$product_id = isset( $value['product_id'] ) && $value['product_id'] ? $value['product_id'] : false;
		$instance = isset( $value['instance'] ) && $value['instance'] ? $value['instance'] : false;
		$object = isset( $home_url['host'] ) ? $home_url['host'] : '';

		$old_api_key = isset( $old_value['licensekey'] ) && $old_value['licensekey'] ? $old_value['licensekey'] : false;
		$old_product_id = isset( $old_value['product_id'] ) && $old_value['product_id'] ? $old_value['product_id'] : false;
		$old_instance = isset( $old_value['instance'] ) && $old_value['instance'] ? $old_value['instance'] : false;

		// Deactivate previous API key
		if ( $old_api_key && $old_product_id && $old_instance ) {
			$deactivate_request_args = array(
				'wc_am_action'	=> 'deactivate',
				'api_key'		=> $old_api_key,
				'product_id'	=> $old_product_id,
				'instance'		=> $old_instance
			);
			$deactivate_request_url = surbma_hc_license_create_url( $deactivate_request_args );
			$deactivate_request_response = wp_remote_get( $deactivate_request_url );
		}

		// Activate new API key
		if ( $api_key && $product_id && $instance ) {
			$activate_request_args = array(
				'wc_am_action'	=> 'activate',
				'api_key'		=> $api_key,
				'product_id'	=> $product_id,
				'instance'		=> $instance,
				'object'		=> $object
			);
			$activate_request_url = surbma_hc_license_create_url( $activate_request_args );
			$activate_request_response = wp_remote_get( $activate_request_url );
		}

		// Update license status
		surbma_hc_license_status_update();
	}, 10, 2 );

endif;

// If $status is not yet set (not whitelisted), set it now
if ( !isset( $status ) ) :
	$status = isset( $license_status['status'] ) && $license_status['status'] ? $license_status['status'] : 'free';
endif;

/*
 *
 * SURBMA_HC_PLUGIN_LICENSE
 *
 * This global is to check license status, if user has rights to use premium features.
 * Values can be: active, inactive, invalid, free
 *
*/
define( 'SURBMA_HC_PLUGIN_LICENSE', $status );

/*
 *
 * SURBMA_HC_PREMIUM
 *
 * This global is for plugin functions to easily set conditions for free and premium features.
 * Values can be: true, false (BUT php uses it to be 1 or none)
 *
*/
if ( 'active' == $status ) {
	define( 'SURBMA_HC_PREMIUM', true );
} else {
	define( 'SURBMA_HC_PREMIUM', false );
}

/*
 *
 * SURBMA_HC_PRO_USER
 *
 * This global is to set conditions for users, who have given a license key, even if it is expired or invalid.
 * Values can be: true, false (BUT php uses it to be 1 or none)
 *
*/
if ( 'free' != $status ) {
	define( 'SURBMA_HC_PRO_USER', true );
} else {
	define( 'SURBMA_HC_PRO_USER', false );
}

// License notices
add_action( 'admin_notices', function() {
	// Invalid notice
	if ( 'invalid' == SURBMA_HC_PLUGIN_LICENSE && ( !isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && 'surbma-hucommerce-menu' != $_GET['page'] ) ) ) {
		?>
		<div class="notice notice-error notice-alt notice-large is-dismissible">
			<a href="https://www.hucommerce.hu" target="_blank"><img src="<?php echo esc_url( SURBMA_HC_PLUGIN_URL ); ?>/assets/images/hucommerce-logo.png" alt="HuCommerce" class="alignright" style="margin: 1em;"></a>
			<h3>Érvénytelen vagy lejárt licensz kulcs a HuCommerce Pro beállításánál!</h3>
			<p>Kérlek ellenőrizd az emailben küldött licensz kulcsot és add meg újra vagy frissítsd és aktiváld újra a HuCommerce beállításánál!
			<br>A licensz kulcsot a <strong>"HuCommerce Pro"</strong> almenüpontban tudod megadni a következő oldalon: <a href="<?php admin_url(); ?>admin.php?page=surbma-hucommerce-menu">WooCommerce -> HuCommerce</a></p>
		</div>
		<?php
	}

	// Inactive notice
	if ( 'inactive' == SURBMA_HC_PLUGIN_LICENSE && ( !isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && 'surbma-hucommerce-menu' != $_GET['page'] ) ) ) {
		?>
		<div class="notice notice-info notice-alt notice-large is-dismissible">
			<a href="https://www.hucommerce.hu" target="_blank"><img src="<?php echo esc_url( SURBMA_HC_PLUGIN_URL ); ?>/assets/images/hucommerce-logo.png" alt="HuCommerce" class="alignright" style="margin: 1em;"></a>
			<h3>Még nem aktivált HuCommerce Pro licensz kulcs!</h3>
			<p>A megadott HuCommerce Pro licensz kulcsod nincs aktiválva. A HuCommerce Pro almenüpont alatt tudod a megadott licensz kulcsot frissíteni vagy újra aktiválni.
			<br>Amennyiben bármi probléma merül fel az újra aktiválás során vedd fel az ügyfélszolgálattal a kapcsolatot: <a href="https://www.hucommerce.hu/ugyfelszolgalat/" target="_blank">HuCommerce Ügyfélszolgálat</a></p>
		</div>
		<?php
	}
} );
