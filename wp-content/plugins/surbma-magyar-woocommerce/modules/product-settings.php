<?php

$options = get_option( 'surbma_hc_fields' );

/*
 ** Metabox for Products
 */

// Registering Metabox for Products
add_action( 'add_meta_boxes', function() {
	add_meta_box(
		'surbma_hc_product_metabox',
		'HuCommerce ' . __( 'Product Settings', 'surbma-magyar-woocommerce' ),
		'surbma_hc_product_metabox',
		'product',
		'normal',
		'high'
	);
} );

// Metabox on the Product edit page
function surbma_hc_product_metabox() {
	global $post;

	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'surbma_hc_product_settings_nonce' );

	// Get the field data if it's already been entered
	$productsubtitle = get_post_meta( $post->ID, 'surbma_hc_product_subtitle', true );
	// $productcustom = get_post_meta( $post->ID, 'surbma_hc_product_custom', true );

	// Output the fields
	echo '<p>';
	echo '<label for="surbma_hc_product_subtitle">' . esc_html__( 'Product Subtitle', 'surbma-magyar-woocommerce' ) . '</label>';
	echo '<input id="surbma_hc_product_subtitle" name="surbma_hc_product_subtitle" type="text" value="' . esc_textarea( $productsubtitle ) . '" class="widefat">';
	echo '</p>';

	/*
	echo '<p>';
	echo '<label for="surbma_hc_product_custom">Product Custom</label>';
	echo '<input id="surbma_hc_product_custom" name="surbma_hc_product_custom" type="text" value="' . esc_textarea( $productcustom )  . '" class="widefat">';
	echo '</p>';
	*/

	echo '<hr><p style="text-align: center;font-size: smaller;">' . esc_html__( 'These settings are added by the HuCommerce plugin.', 'surbma-magyar-woocommerce' ) . '</p>';
}

// Saving the fields in the Metabox
add_action( 'save_post', function( $post_id, $post ) {

	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times.
	if ( !isset( $_POST['surbma_hc_product_subtitle'] ) /*|| !isset( $_POST['surbma_hc_product_custom'] ) */|| ( !isset( $_POST['surbma_hc_product_settings_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['surbma_hc_product_settings_nonce'] ), basename(__FILE__) ) ) ) {
		return $post_id;
	}

	// Now that we're authenticated, time to save the data.
	// This sanitizes the data from the field and saves it into an array $products_meta.
	$products_meta['surbma_hc_product_subtitle'] = sanitize_text_field( $_POST['surbma_hc_product_subtitle'] );
	// $products_meta['surbma_hc_product_custom'] = sanitize_text_field( $_POST['surbma_hc_product_custom'] );

	// Cycle through the $products_meta array.
	foreach ( $products_meta as $key => $value ) :

		// Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
		}

		if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it
			add_post_meta( $post_id, $key, $value);
		}

		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
		}

	endforeach;

}, 1, 2 );

/*
 ** Product Subtitle
 */

$productsubtitleValue = isset( $options['productsubtitle'] ) ? $options['productsubtitle'] : 0;

if ( $productsubtitleValue ) {

	// The Title filter
	add_filter( 'the_title', function( $title, $id ) {
		$productsubtitle = get_post_meta( $id, 'surbma_hc_product_subtitle', true );

		if ( 'product' == get_post_type( $id ) && in_the_loop() && $productsubtitle ) {
			$title = $title . ' <span class="product_subtitle" itemprop="description">' . $productsubtitle . '</span>';
		}

		return $title;
	}, 10, 2 );

	// Custom style for the Subtitle
	add_action( 'wp_head', function() {
		echo '<style>.product .product_subtitle {display: block;font-size: smaller;opacity: .75;}</style>';
	} );

}

/*
 ** Remove Image Zoom
 */

$productsettings_removeimagezoomValue = isset( $options['productsettings-removeimagezoom'] ) ? $options['productsettings-removeimagezoom'] : 0;

if ( $productsettings_removeimagezoomValue ) {

	add_action( 'wp', function() {
		remove_theme_support( 'wc-product-gallery-zoom' );
	}, 100 );

}

/*
 ** Add to cart button on archive pages
 */

$addtocartonarchiveValue = isset( $options['addtocartonarchive'] ) ? $options['addtocartonarchive'] : 0;

if ( $addtocartonarchiveValue ) {
	add_action( 'after_setup_theme', function() {
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10 );
	} );
}

/*
 ** Remove related products output
 */

$norelatedproductsValue = isset( $options['norelatedproducts'] ) ? $options['norelatedproducts'] : 0;

if ( $norelatedproductsValue ) {
	add_action( 'woocommerce_after_single_product_summary', function() {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}, 0 );
}

/*
 ** Products per page
 */

add_filter( 'loop_shop_per_page', function( $cols ) {
	$options = get_option( 'surbma_hc_fields' );
	$productsnumberValue = isset( $options['productsnumber'] ) ? $options['productsnumber'] : false;

	if ( $productsnumberValue ) {
		return $productsnumberValue;
	}

	return $cols;
}, 20 );

/*
 ** Products per row
 */

add_filter( 'loop_shop_columns', function( $columns ) {
	$options = get_option( 'surbma_hc_fields' );
	$productsperrowValue = isset( $options['productsperrow'] ) ? $options['productsperrow'] : false;

	if ( $productsperrowValue ) {
		return $productsperrowValue;
	}

	return $columns;
}, 999 );

/**
 * Change upsell products output
 */

add_filter( 'woocommerce_upsell_display_args', function( $args ) {
	$options = get_option( 'surbma_hc_fields' );
	$upsellproductsnumberValue = isset( $options['upsellproductsnumber'] ) ? $options['upsellproductsnumber'] : false;
	$upsellproductsperrowValue = isset( $options['upsellproductsperrow'] ) ? $options['upsellproductsperrow'] : false;

	if ( $upsellproductsnumberValue ) {
		$args['posts_per_page'] = $upsellproductsnumberValue;
	}
	if ( $upsellproductsperrowValue ) {
		$args['columns'] = $upsellproductsperrowValue;
	}

	return $args;
}, 20 );

/*
 ** Change related products output
 */

add_filter( 'woocommerce_output_related_products_args', function( $args ) {
	$options = get_option( 'surbma_hc_fields' );
	$relatedproductsnumberValue = isset( $options['relatedproductsnumber'] ) ? $options['relatedproductsnumber'] : false;
	$relatedproductsperrowValue = isset( $options['relatedproductsperrow'] ) ? $options['relatedproductsperrow'] : false;

	if ( $relatedproductsnumberValue ) {
		$args['posts_per_page'] = $relatedproductsnumberValue;
	}
	if ( $relatedproductsperrowValue ) {
		$args['columns'] = $relatedproductsperrowValue;
	}

	return $args;
}, 20 );
