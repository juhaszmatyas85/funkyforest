<?php

add_filter( 'woocommerce_add_to_cart_validation', function( $passed, $added_product_id ) {
	$options = get_option( 'surbma_hc_fields' );
	$module_oneproductincartValue = isset( $options['module-oneproductincart'] ) ? $options['module-oneproductincart'] : false;
	if ( $module_oneproductincartValue ) {
		wc_empty_cart();
	}
	return $passed;
}, 9999, 2 );
