<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

add_filter( 'woocommerce_login_redirect', function( $redirect, $user ) {
	$options = get_option( 'surbma_hc_fields' );
	$loginredirecturlValue = isset( $options['loginredirecturl'] ) ? $options['loginredirecturl'] : wc_get_page_permalink( 'shop' );

	$redirect_page_id = url_to_postid( $redirect );
	$checkout_page_id = wc_get_page_id( 'checkout' );

	if ( $redirect_page_id == $checkout_page_id ) {
		return $redirect;
	}

	if ( '' == $loginredirecturlValue) {
		return $redirect;
	} else {
		return $loginredirecturlValue;
	}
}, 10, 2 );

add_filter( 'woocommerce_registration_redirect', function( $var ) {
	$options = get_option( 'surbma_hc_fields' );
	$registrationredirecturlValue = isset( $options['registrationredirecturl'] ) ? $options['registrationredirecturl'] : wc_get_page_permalink( 'shop' );

	if ( '' == $registrationredirecturlValue ) {
		return $var;
	} else {
		return $registrationredirecturlValue;
	}
}, 10, 1 );
