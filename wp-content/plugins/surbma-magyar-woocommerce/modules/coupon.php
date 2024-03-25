<?php

// Show Coupons in upper case
add_action( 'init', function() {
	$options = get_option( 'surbma_hc_fields' );
	$couponuppercaseValue = isset( $options['couponuppercase'] ) ? $options['couponuppercase'] : 0;
	if ( 1 == $couponuppercaseValue ) {
		remove_filter( 'woocommerce_coupon_code', 'wc_strtolower' );
		add_filter( 'woocommerce_coupon_code', 'wc_strtoupper' );
	}
} );

// Remove coupon field
add_filter( 'woocommerce_coupons_enabled', function( $enabled ) {
	$options = get_option( 'surbma_hc_fields' );
	$couponfieldhiddenoncartValue = isset( $options['couponfieldhiddenoncart'] ) ? $options['couponfieldhiddenoncart'] : 0;
	$couponfieldhiddenoncheckoutValue = isset( $options['couponfieldhiddenoncheckout'] ) ? $options['couponfieldhiddenoncheckout'] : 0;
	if ( 1 == $couponfieldhiddenoncartValue && is_cart() ) {
		$enabled = false;
	}
	if ( 1 == $couponfieldhiddenoncheckoutValue && is_checkout() ) {
		$enabled = false;
	}
	return $enabled;
} );

// Coupon field always visible
add_action( 'wp_head', function() {
	if ( is_checkout() ) {
		$options = get_option( 'surbma_hc_fields' );
		$couponfieldalwaysvisibleValue = isset( $options['couponfieldalwaysvisible'] ) ? $options['couponfieldalwaysvisible'] : 0;
		if ( 1 == $couponfieldalwaysvisibleValue ) {
			echo '<style id="cps-wcgems-hc-form-coupon-inline-css">.woocommerce-checkout .woocommerce-form-coupon-toggle {display: none;} .woocommerce-checkout .woocommerce-form-coupon {display: block !important;}</style>';
		}
	}
} );

// Coupon field reposition
add_action( 'woocommerce_before_checkout_form', function() {
	$options = get_option( 'surbma_hc_fields' );
	$couponfieldpositionValue = isset( $options['couponfieldposition'] ) ? $options['couponfieldposition'] : 'beforecheckoutform';
	if ( 'aftercheckoutform' == $couponfieldpositionValue ) {
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form');
		add_action( 'woocommerce_after_checkout_form', 'woocommerce_checkout_coupon_form' );
	}
}, 0 );
