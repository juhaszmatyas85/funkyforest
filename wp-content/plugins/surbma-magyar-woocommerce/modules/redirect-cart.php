<?php

add_action( 'template_redirect', function() {
	if ( is_cart() ) {
		if ( WC()->cart->get_cart_contents_count() == 0 ) {
			wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
		} else {
			wp_safe_redirect( wc_get_checkout_url() );
		}
		exit;
	}
} );
