<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

add_action( 'woocommerce_checkout_process', function() {
	// Nonce verification before doing anything
	check_ajax_referer( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' );

	$woocommercecheckoutaddress2fieldValue = get_option( 'woocommerce_checkout_address_2_field' ) != false ? get_option( 'woocommerce_checkout_address_2_field' ) : '';

	$options = get_option( 'surbma_hc_fields' );
	$validatebillingtaxfieldValue = isset( $options['validatebillingtaxfield'] ) ? $options['validatebillingtaxfield'] : 0;
	$validatebillingcityfieldValue = isset( $options['validatebillingcityfield'] ) ? $options['validatebillingcityfield'] : 0;
	$validatebillingaddressfieldValue = isset( $options['validatebillingaddressfield'] ) ? $options['validatebillingaddressfield'] : 0;
	$validatebillingphonefieldValue = isset( $options['validatebillingphonefield'] ) ? $options['validatebillingphonefield'] : 0;
	$validateshippingcityfieldValue = isset( $options['validateshippingcityfield'] ) ? $options['validateshippingcityfield'] : 0;
	$validateshippingaddressfieldValue = isset( $options['validateshippingaddressfield'] ) ? $options['validateshippingaddressfield'] : 0;

	if ( !empty( $_POST['billing_country'] ) && 'HU' == $_POST['billing_country'] ) {
		$billing_tax_number = sanitize_text_field( $_POST['billing_tax_number'] );
		// $billing_postcode = sanitize_text_field( $_POST['billing_postcode'] );
		$billing_city = sanitize_text_field( $_POST['billing_city'] );
		$billing_address_1 = sanitize_text_field( $_POST['billing_address_1'] );
		$billing_phone = sanitize_text_field( $_POST['billing_phone'] );
		$shipping_city = sanitize_text_field( $_POST['shipping_city'] );
		$ship_to_different_address = !empty( $_POST['ship_to_different_address'] ) ? sanitize_text_field( $_POST['ship_to_different_address'] ) : 0;
		$shipping_address_1 = sanitize_text_field( $_POST['shipping_address_1'] );

		$billing_tax_number_pattern_short = '/^\d{11}$/';
		$billing_tax_number_pattern_full = '/^\d{8}-\d{1}-\d{2}$/';
		$billing_tax_number_pattern_eu = '/^HU\d{8}$/';
		// $checkout_postcode_pattern = '/^\d{4}$/';
		$checkout_city_pattern = '/^([\p{L}])+([\p{L} ])*$/iu';
		$checkout_address_1_pattern = '/^(?=.*\p{L})(?=.*\d)(?=.* )/iu';

		if ( 1 == $validatebillingtaxfieldValue && !empty( $billing_tax_number ) && !preg_match( $billing_tax_number_pattern_short, $billing_tax_number ) && !preg_match( $billing_tax_number_pattern_full, $billing_tax_number ) && !preg_match( $billing_tax_number_pattern_eu, $billing_tax_number ) ) {
			$noticeError = __( '<strong>Billing VAT number</strong> field is invalid. Please check again!', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		/*
		if ( !empty( $billing_postcode ) && !preg_match( $checkout_postcode_pattern, $billing_postcode ) ) {
			$noticeError = __( '<strong>Billing Postcode</strong> field is invalid. Please check again!', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		if ( !empty( $_POST['billing_postcode'] ) && strlen( sanitize_text_field( $_POST['billing_postcode'] ) ) < 4 ) {
			$noticeError = __( '<strong>Billing Postcode</strong> field is invalid: too few characters.', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		if ( !empty( $_POST['billing_postcode'] ) && strlen( sanitize_text_field( $_POST['billing_postcode'] ) ) > 4 ) {
			$noticeError = __( '<strong>Billing Postcode</strong> field is invalid: too much characters.', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		*/
		if ( 1 == $validatebillingcityfieldValue && !empty( $billing_city ) && !preg_match( $checkout_city_pattern, $billing_city ) ) {
			$noticeError = __( '<strong>Billing City</strong> field is invalid: only letters and space are allowed.', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		if ( 1 == $validatebillingaddressfieldValue && !empty( $billing_address_1 ) && empty( $_POST['billing_address_2'] ) && !preg_match( $checkout_address_1_pattern, $billing_address_1 ) ) {
			$noticeError = __( '<strong>Billing Address</strong> field is invalid: must have at least one letter, one number and one space in the address.', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		if ( 1 == $validatebillingphonefieldValue && !empty( $billing_phone ) && strlen( $billing_phone ) < 11 ) {
			$noticeError = __( '<strong>Billing Phone</strong> field is invalid: too few characters.', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		if ( 1 == $validatebillingphonefieldValue && !empty( $billing_phone ) && substr( $billing_phone, 3, 1 ) == 0 ) {
			$noticeError = __( '<strong>Billing Phone</strong> field is invalid: wrong format.', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		if ( 1 == $validateshippingcityfieldValue && 1 == $ship_to_different_address && !empty( $shipping_city ) && !preg_match( $checkout_city_pattern, $shipping_city ) ) {
			$noticeError = __( '<strong>Shipping City</strong> field is invalid: only letters and space are allowed.', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
		if ( 1 == $validateshippingaddressfieldValue && 1 == $ship_to_different_address && !empty( $shipping_address_1 ) && empty( $_POST['shipping_address_2'] ) && !preg_match( $checkout_address_1_pattern, $shipping_address_1 ) ) {
			$noticeError = __( '<strong>Shipping Address</strong> field is invalid: must have at least one letter, one number and one space in the address.', 'surbma-magyar-woocommerce' );
			wc_add_notice( $noticeError, 'error' );
		}
	}
} );

add_action( 'wp_enqueue_scripts', function() {
	$options = get_option( 'surbma_hc_fields' );
	$validatebillingtaxfieldValue = isset( $options['validatebillingtaxfield'] ) ? $options['validatebillingtaxfield'] : 0;

	if ( 1 == $validatebillingtaxfieldValue ) {
		ob_start();
?>
jQuery(document).ready(function($){
	// Check Billing tax number field value
	$('#billing_tax_number').on('keyup change blur focus', function() {
		const billing_tax_number_field = document.querySelector('#billing_tax_number_field');
		// If field is empty
		if ( $(this).val().length == 0 ) {
			// Only invalid, if field is required
			if ( billing_tax_number_field.classList.contains('validate-required') ) {
				$('#billing_tax_number_field').addClass('woocommerce-invalid woocommerce-invalid-required-field');
			}
		// If field has any value
		} else {
			// Check for Hungarian tax number formats
			if ( /^\d{11}$/.test( $(this).val() ) || /^\d{8}-\d{1}-\d{2}$/.test( $(this).val() ) || /^HU\d{8}$/.test( $(this).val() ) ) {
				$('#billing_tax_number_field').addClass('woocommerce-validated');
			} else {
				if ( billing_tax_number_field.classList.contains('validate-required') ) {
					$('#billing_tax_number_field').addClass('woocommerce-invalid woocommerce-invalid-required-field');
				} else {
					$('#billing_tax_number_field').addClass('woocommerce-invalid');
				}
			}
		}
	});
});
<?php
		$script = ob_get_contents();
		ob_end_clean();

		wp_add_inline_script( 'cps-jquery-fix', $script );
	}
} );
