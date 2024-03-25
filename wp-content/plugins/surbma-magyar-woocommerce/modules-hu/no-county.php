<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

// Customize the checkout fields if country is Hungary
add_filter( 'woocommerce_get_country_locale', function( $locale ) {
	$locale['HU']['state']['required'] = false;
	return $locale;
} );

// Default billing state reset function
add_filter( 'default_checkout_billing_state', function() {
	return null;
} );

// Default shipping state reset function
add_filter( 'default_checkout_shipping_state', function() {
	return null;
} );

// Hide the state field
add_filter( 'woocommerce_states', function( $states ) {
	$states['HU'] = array();
	return $states;
} );

/*
// Alternative method to hide State fields
// TODO: Make a condition to dinamically hide it only if Country is Hungary.
add_filter( 'woocommerce_checkout_fields' , function( $fields ) {
	unset( $fields['billing']['billing_state'] );
	unset( $fields['shipping']['shipping_state'] );
	return $fields;
} );
*/
