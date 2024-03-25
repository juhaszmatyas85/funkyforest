<?php

$options = get_option( 'surbma_hc_fields' );
$emptycartbutton_cartpageValue = isset( $options['emptycartbutton-cartpage'] ) ? $options['emptycartbutton-cartpage'] : 'none';
$emptycartbutton_checkoutpageValue = isset( $options['emptycartbutton-checkoutpage'] ) ? $options['emptycartbutton-checkoutpage'] : 'none';
$emptycartbutton_checkoutpagePosition = 'woocommerce_before_checkout_form' == $emptycartbutton_checkoutpageValue ? 999 : 0;

/*
 *
 * Show Empty Cart button on Cart page
 *
 * Possible hooks for Cart page:
 * woocommerce_cart_coupon
 * woocommerce_cart_actions
 * woocommerce_before_cart_collaterals
*/
if ( 'none' != $emptycartbutton_cartpageValue ) {
	add_action( $emptycartbutton_cartpageValue, function() {
		if ( count( WC()->cart->get_cart() ) > 1 ) {
			$options = get_option( 'surbma_hc_fields' );
			$emptycartbutton_cartpagebuttontextValue = isset( $options['emptycartbutton-cartpagebuttontext'] ) ? $options['emptycartbutton-cartpagebuttontext'] : __( 'Empty cart', 'surbma-magyar-woocommerce' );
			$emptycartbutton_checkoutpageconfirmationtextValue = isset( $options['emptycartbutton-confirmationtext'] ) ? $options['emptycartbutton-confirmationtext'] : __( 'Are you sure you want to empty the Cart?', 'surbma-magyar-woocommerce' );
			echo '<a href="' . esc_url( add_query_arg( 'hc-empty-cart', '1' ) ) . '" class="button alt hc-empty-cart-button">' . $emptycartbutton_cartpagebuttontextValue . '</a>';
			echo "<script>jQuery('.hc-empty-cart-button').on('click', function () {return confirm('" . $emptycartbutton_checkoutpageconfirmationtextValue . "');});</script>";
		}
	} );
}

/*
 *
 * Show Empty Cart button on Checkout page
 *
 * Possible hooks for Checkout page:
 * woocommerce_before_checkout_form
 * woocommerce_after_checkout_form
*/
if ( 'none' != $emptycartbutton_checkoutpageValue ) {
	add_action( $emptycartbutton_checkoutpageValue, function() {
		if ( count( WC()->cart->get_cart() ) > 1 ) {
			$options = get_option( 'surbma_hc_fields' );
			$emptycartbutton_checkoutpagemessageValue = isset( $options['emptycartbutton-checkoutpagemessage'] ) ? $options['emptycartbutton-checkoutpagemessage'] : __( 'Changed your mind?', 'surbma-magyar-woocommerce' );
			$emptycartbutton_checkoutpagelinktextValue = isset( $options['emptycartbutton-checkoutpagelinktext'] ) ? $options['emptycartbutton-checkoutpagelinktext'] : __( 'Empty cart & continue shopping', 'surbma-magyar-woocommerce' );
			$emptycartbutton_checkoutpageconfirmationtextValue = isset( $options['emptycartbutton-confirmationtext'] ) ? $options['emptycartbutton-confirmationtext'] : __( 'Are you sure you want to empty the Cart?', 'surbma-magyar-woocommerce' );
			$returnurl = esc_url( add_query_arg( 'hc-empty-cart', '1' ) );
			$notice = sprintf( '%s <a href="%s" class="button wc-forward hc-empty-cart-button">%s</a>', $emptycartbutton_checkoutpagemessageValue, $returnurl, $emptycartbutton_checkoutpagelinktextValue );

			wc_print_notice( $notice, 'notice' );
			echo "<script>jQuery('.hc-empty-cart-button').on('click', function () {return confirm('" . $emptycartbutton_checkoutpageconfirmationtextValue . "');});</script>";
		}
	}, $emptycartbutton_checkoutpagePosition );
}

// The redirection
add_action( 'template_redirect', function() {
	if ( ( is_cart() || is_checkout() ) && isset( $_GET['hc-empty-cart'] ) && 1 == esc_html( $_GET['hc-empty-cart'] ) ) {
		WC()->cart->empty_cart();
		$referer = esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) );
		wp_safe_redirect( $referer );
	}
}, 20 );
