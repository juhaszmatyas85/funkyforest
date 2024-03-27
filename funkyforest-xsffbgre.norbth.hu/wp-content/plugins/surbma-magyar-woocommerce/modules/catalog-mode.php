<?php

/*
add_action( 'after_setup_theme', function() {
	add_filter( 'woocommerce_is_purchasable', '__return_false', 999999 );
	add_filter( 'woocommerce_get_price_html', '__return_false', 999999 );

	// Archive pages
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 ); // Storefront theme
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

	// Single products
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_single_product_summary', 'surbma_hc_show_termekartortenet_single', 11 );
	remove_action( 'woocommerce_single_variation', 'surbma_hc_show_termekartortenet_variation', 11 );
} );
*/

add_filter( 'woocommerce_is_purchasable', '__return_false', 999999 );
add_filter( 'woocommerce_get_price_html', '__return_false', 999999 );

// Archive pages
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 ); // Storefront theme
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// Single products
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'surbma_hc_show_termekartortenet_single', 11 );
remove_action( 'woocommerce_single_variation', 'surbma_hc_show_termekartortenet_variation', 11 );

add_action( 'template_redirect', function() {
	if ( is_cart() || is_checkout() || is_account_page() ) {
		wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
		exit;
	}
} );

// Disable Storefront's WooCommerce specific functions
if ( ! function_exists( 'storefront_is_woocommerce_activated' ) ) {
	function storefront_is_woocommerce_activated() {
		return false;
	}
}
