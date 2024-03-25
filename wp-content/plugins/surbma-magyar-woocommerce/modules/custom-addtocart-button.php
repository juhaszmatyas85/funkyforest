<?php

add_filter( 'woocommerce_product_add_to_cart_text' , 'cps_hc_wcgems_custom_addtocart_button', 10, 1 );
add_filter( 'woocommerce_product_single_add_to_cart_text' , 'cps_hc_wcgems_custom_addtocart_button', 10, 1 );
add_filter( 'woocommerce_booking_single_add_to_cart_text' , 'cps_hc_wcgems_custom_addtocart_button', 10, 1 );
function cps_hc_wcgems_custom_addtocart_button( $text ) {
	$options = get_option( 'surbma_hc_fields' );
	global $product;

	if ( !isset( $product ) || !is_object( $product ) ) {
		return $text;
	}

	$product_type = $product->get_type();

	$custom_addtocart_button_single_simpleValue = isset( $options['custom-addtocart-button-single-simple'] ) && $options['custom-addtocart-button-single-simple'] ? $options['custom-addtocart-button-single-simple'] : $text;
	$custom_addtocart_button_single_groupedValue = isset( $options['custom-addtocart-button-single-grouped'] ) && $options['custom-addtocart-button-single-grouped'] ? $options['custom-addtocart-button-single-grouped'] : $text;
	$custom_addtocart_button_single_externalValue = isset( $options['custom-addtocart-button-single-external'] ) && $options['custom-addtocart-button-single-external'] ? $options['custom-addtocart-button-single-external'] : $text;
	$custom_addtocart_button_single_variableValue = isset( $options['custom-addtocart-button-single-variable'] ) && $options['custom-addtocart-button-single-variable'] ? $options['custom-addtocart-button-single-variable'] : $text;
	$custom_addtocart_button_single_subscriptionValue = isset( $options['custom-addtocart-button-single-subscription'] ) && $options['custom-addtocart-button-single-subscription'] ? $options['custom-addtocart-button-single-subscription'] : $text;
	$custom_addtocart_button_single_variablesubscriptionValue = isset( $options['custom-addtocart-button-single-variable-subscription'] ) && $options['custom-addtocart-button-single-variable-subscription'] ? $options['custom-addtocart-button-single-variable-subscription'] : $text;
	$custom_addtocart_button_single_bookingValue = isset( $options['custom-addtocart-button-single-booking'] ) && $options['custom-addtocart-button-single-booking'] ? $options['custom-addtocart-button-single-booking'] : $text;
	$custom_addtocart_button_archive_simpleValue = isset( $options['custom-addtocart-button-archive-simple'] ) && $options['custom-addtocart-button-archive-simple'] ? $options['custom-addtocart-button-archive-simple'] : $custom_addtocart_button_single_simpleValue;
	$custom_addtocart_button_archive_groupedValue = isset( $options['custom-addtocart-button-archive-grouped'] ) && $options['custom-addtocart-button-archive-grouped'] ? $options['custom-addtocart-button-archive-grouped'] : $custom_addtocart_button_single_groupedValue;
	$custom_addtocart_button_archive_externalValue = isset( $options['custom-addtocart-button-archive-external'] ) && $options['custom-addtocart-button-archive-external'] ? $options['custom-addtocart-button-archive-external'] : $custom_addtocart_button_single_externalValue;
	$custom_addtocart_button_archive_variableValue = isset( $options['custom-addtocart-button-archive-variable'] ) && $options['custom-addtocart-button-archive-variable'] ? $options['custom-addtocart-button-archive-variable'] : $custom_addtocart_button_single_variableValue;
	$custom_addtocart_button_archive_subscriptionValue = isset( $options['custom-addtocart-button-archive-subscription'] ) && $options['custom-addtocart-button-archive-subscription'] ? $options['custom-addtocart-button-archive-subscription'] : $custom_addtocart_button_single_subscriptionValue;
	$custom_addtocart_button_archive_variablesubscriptionValue = isset( $options['custom-addtocart-button-archive-variable-subscription'] ) && $options['custom-addtocart-button-archive-variable-subscription'] ? $options['custom-addtocart-button-archive-variable-subscription'] : $custom_addtocart_button_single_variablesubscriptionValue;
	$custom_addtocart_button_archive_bookingValue = isset( $options['custom-addtocart-button-archive-booking'] ) && $options['custom-addtocart-button-archive-booking'] ? $options['custom-addtocart-button-archive-booking'] : $custom_addtocart_button_single_bookingValue;

	if ( is_product() ) { // Single product pages

		switch ( $product_type ) {
			case 'simple':
				return $custom_addtocart_button_single_simpleValue;
			break;
			case 'grouped':
				return $custom_addtocart_button_single_groupedValue;
			break;
			case 'external':
				return $custom_addtocart_button_single_externalValue;
			break;
			case 'variable':
				return $custom_addtocart_button_single_variableValue;
			break;
			case 'subscription':
				return $custom_addtocart_button_single_subscriptionValue;
			break;
			case 'variable-subscription':
				return $custom_addtocart_button_single_variablesubscriptionValue;
			break;
			case 'booking':
				return $custom_addtocart_button_single_bookingValue;
			break;
			default:
				return $text;
		}

	} else { // Archive pages

		switch ( $product_type ) {
			case 'simple':
				return $custom_addtocart_button_archive_simpleValue;
			break;
			case 'grouped':
				return $custom_addtocart_button_archive_groupedValue;
			break;
			case 'external':
				return $custom_addtocart_button_archive_externalValue;
			break;
			case 'variable':
				return $custom_addtocart_button_archive_variableValue;
			break;
			case 'subscription':
				return $custom_addtocart_button_archive_subscriptionValue;
			break;
			case 'variable-subscription':
				return $custom_addtocart_button_archive_variablesubscriptionValue;
			break;
			case 'booking':
				return $custom_addtocart_button_archive_bookingValue;
			break;
			default:
				return $text;
		}

	}

}
