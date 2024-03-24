<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

/*
// Custom translations
add_filter( 'gettext', function( $translation, $text, $domain ) {
	switch ( $translation ) {
		case '' :
			$translation = '';
			break;
	}
	return $translation;
}, 20, 3 );
*/

// Custom translations for plural strings without context
add_filter( 'ngettext', function( $translation, $single, $plural, $number, $domain ) {
	// _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'storefront' )
	if ( 'hu_HU' == get_locale() && 'storefront' === $domain ) {
		switch ( $single ) {
			case '%d item':
				$single = '%d termék';
				break;
		}
		return $single;

		switch ( $plural ) {
			case '%d items':
				$plural = '%d termék';
				break;
		}
		return $plural;
	}
	return $translation;
}, 20, 5 );

// Custom translations for plural strings with context
add_filter( 'ngettext_with_context', function( $translation, $single, $plural, $number, $context, $domain ) {
	// _nx( '%1$s Item', '%1$s Items', $items_number, 'WooCommerce items number', 'Divi' )
	if ( 'hu_HU' == get_locale() && 'Divi' === $domain && 'WooCommerce items number' == $context ) {
		switch ( $single ) {
			case '%1$s Item':
				$single = '%1$s Termék';
				break;
		}
		return $single;

		switch ( $plural ) {
			case '%1$s Items':
				$plural = '%1$s Termék';
				break;
		}
		return $plural;
	}
	return $translation;
}, 20, 6 );
