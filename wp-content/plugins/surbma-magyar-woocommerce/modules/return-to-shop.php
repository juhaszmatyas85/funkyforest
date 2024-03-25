<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

$options = get_option( 'surbma_hc_fields' );
$returntoshopcartpositionValue = isset( $options['returntoshopcartposition'] ) ? $options['returntoshopcartposition'] : 'cartactions';
$returntoshopcheckoutpositionValue = isset( $options['returntoshopcheckoutposition'] ) ? $options['returntoshopcheckoutposition'] : 'nocheckout';

$continueshoppingmessageHook = '';
$continueshoppingmessagePriority = 10;

$continueshoppingbuttonHook = '';
$continueshoppingbuttonPriority = 10;

if ( 'beforecarttable' == $returntoshopcartpositionValue ) {
	$continueshoppingmessageHook = 'woocommerce_before_cart_table';
}
if ( 'aftercarttable' == $returntoshopcartpositionValue ) {
	$continueshoppingmessageHook = 'woocommerce_after_cart_table';
}
if ( 'cartactions' == $returntoshopcartpositionValue ) {
	$continueshoppingbuttonHook = 'woocommerce_cart_actions';
}
if ( 'proceedtocheckout' == $returntoshopcartpositionValue ) {
	$continueshoppingbuttonHook = 'woocommerce_proceed_to_checkout';
	$continueshoppingbuttonPriority = 999;
}

if ( 'beforecheckoutform' == $returntoshopcheckoutpositionValue ) {
	$continueshoppingmessageHook = 'woocommerce_before_checkout_form';
	$continueshoppingmessagePriority = 0;
}
if ( 'aftercheckoutform' == $returntoshopcheckoutpositionValue ) {
	$continueshoppingmessageHook = 'woocommerce_after_checkout_form';
}

add_action( $continueshoppingmessageHook, function() {
	$options = get_option( 'surbma_hc_fields' );
	$returntoshopmessageValue = isset( $options['returntoshopmessage'] ) ? $options['returntoshopmessage'] : __( 'Would you like to continue shopping?', 'surbma-magyar-woocommerce' );
	echo '<div class="woocommerce-message returntoshop">';
	echo esc_html( $returntoshopmessageValue ) . ' <a href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '" class="button wc-forward">' . esc_html__( 'Return to shop', 'woocommerce' ) . '</a>';
	echo '</div>';
}, $continueshoppingmessagePriority );

add_action( $continueshoppingbuttonHook, function() {
	echo '<a class="button wc-backward returntoshop" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '">';
	echo esc_html__( 'Return to shop', 'woocommerce' );
	echo '</a>';
}, $continueshoppingbuttonPriority );
