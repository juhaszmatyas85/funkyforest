<?php

// Add custom fields in the General tab of the Product data metabox
add_action( 'woocommerce_product_options_general_product_data', function() {
	// We are on the edit product page, right? Let's get the product ID from the URL.
	$product_id = ( isset( $_GET['post'] ) ) ? $_GET['post'] : false;

	// If we are on the product edit page, get the product object. If it is not the product edit page, return.
	if ( $product_id ) {
		$product = wc_get_product( $product_id );
	} else {
		return;
	}

	echo '<div class="options_group productpriceadditions">';
	echo '<p class="form-field" style="margin-left: -150px;text-transform: uppercase;"><strong>' . __( 'HuCommerce module', 'surbma-magyar-woocommerce' ) . ': ' . __( 'Product price additions', 'surbma-magyar-woocommerce' ) . '</strong></p>';
	echo '<p class="form-field" style="margin: -18px 0 18px -150px;">' . __( 'Fields below will overwrite the global prefixes and suffixes set in HuCommerce settings.', 'surbma-magyar-woocommerce' ) . '</p>';

	// Text to set custom price prefix on product page
	woocommerce_wp_text_input( array(
		'id' => '_hc_productpriceadditions_product_prefix',
		'label' => __( 'Prefix on Product page', 'surbma-magyar-woocommerce' ),
		'wrapper_class' => 'form-field-wide',
		'description' => __( 'This text will be displayed just before the Product\'s price on the Product page.', 'surbma-magyar-woocommerce' ),
		'desc_tip' => true
	) );
	// Checkbox to disable price prefix on product page
	woocommerce_wp_checkbox( array(
		'id' => '_hc_productpriceadditions_disable_product_prefix',
		'label' => '',
		'wrapper_class' => 'form-field-wide',
		'description' => __( 'Disable the price prefix for this product on product page.', 'surbma-magyar-woocommerce' ),
		'desc_tip' => false
	) );
	// Text to set custom price suffix on product page
	woocommerce_wp_text_input( array(
		'id' => '_hc_productpriceadditions_product_suffix',
		'label' => __( 'Suffix on Product page', 'surbma-magyar-woocommerce' ),
		'wrapper_class' => 'form-field-wide',
		'description' => __( 'This text will be displayed after the Product\'s price on the Product page.', 'surbma-magyar-woocommerce' ),
		'desc_tip' => true
	) );
	// Checkbox to disable price suffix on product page
	woocommerce_wp_checkbox( array(
		'id' => '_hc_productpriceadditions_disable_product_suffix',
		'label' => '',
		'wrapper_class' => 'form-field-wide',
		'description' => __( 'Disable the price suffix for this product on product page.', 'surbma-magyar-woocommerce' ),
		'desc_tip' => false
	) );
	// Text to set custom price prefix on archive pages
	woocommerce_wp_text_input( array(
		'id' => '_hc_productpriceadditions_archive_prefix',
		'label' => __( 'Prefix on Archive pages', 'surbma-magyar-woocommerce' ),
		'wrapper_class' => 'form-field-wide',
		'description' => __( 'This text will be displayed just before the Product\'s price on the Archive pages.', 'surbma-magyar-woocommerce' ),
		'desc_tip' => true
	) );
	// Checkbox to disable price prefix on archive pages
	woocommerce_wp_checkbox( array(
		'id' => '_hc_productpriceadditions_disable_archive_prefix',
		'label' => '',
		'wrapper_class' => 'form-field-wide',
		'description' => __( 'Disable the price prefix for this product on archive pages.', 'surbma-magyar-woocommerce' ),
		'desc_tip' => false
	) );
	// Text to set custom price suffix on archive pages
	woocommerce_wp_text_input( array(
		'id' => '_hc_productpriceadditions_archive_suffix',
		'label' => __( 'Suffix on Archive pages', 'surbma-magyar-woocommerce' ),
		'wrapper_class' => 'form-field-wide',
		'description' => __( 'This text will be displayed after the Product\'s price on the Archive pages.', 'surbma-magyar-woocommerce' ),
		'desc_tip' => true
	) );
	// Checkbox to disable price suffix on archive pages
	woocommerce_wp_checkbox( array(
		'id' => '_hc_productpriceadditions_disable_archive_suffix',
		'label' => '',
		'wrapper_class' => 'form-field-wide',
		'description' => __( 'Disable the price suffix for this product on archive pages.', 'surbma-magyar-woocommerce' ),
		'desc_tip' => false
	) );

	echo '</div>';
} );

// Save meta data for the Product
add_action( 'woocommerce_admin_process_product_object', function( $product ) {
	$product->update_meta_data( '_hc_productpriceadditions_product_prefix', $_POST['_hc_productpriceadditions_product_prefix'] );
	$product->update_meta_data( '_hc_productpriceadditions_disable_product_prefix', $_POST['_hc_productpriceadditions_disable_product_prefix'] );
	$product->update_meta_data( '_hc_productpriceadditions_product_suffix', $_POST['_hc_productpriceadditions_product_suffix'] );
	$product->update_meta_data( '_hc_productpriceadditions_disable_product_suffix', $_POST['_hc_productpriceadditions_disable_product_suffix'] );
	$product->update_meta_data( '_hc_productpriceadditions_archive_prefix', $_POST['_hc_productpriceadditions_archive_prefix'] );
	$product->update_meta_data( '_hc_productpriceadditions_disable_archive_prefix', $_POST['_hc_productpriceadditions_disable_archive_prefix'] );
	$product->update_meta_data( '_hc_productpriceadditions_archive_suffix', $_POST['_hc_productpriceadditions_archive_suffix'] );
	$product->update_meta_data( '_hc_productpriceadditions_disable_archive_suffix', $_POST['_hc_productpriceadditions_disable_archive_suffix'] );
}, 10, 2 );

// Display prefixes and suffixes
add_filter( 'woocommerce_get_price_html', function( $price, $product ) {
	// Don't show it on admin
	if ( is_admin() ) {
		return $price;
	}

	// This function is not needed for grouped products
	if ( $product->is_type( 'grouped' ) ) {
		return $price;
	}

	$options = get_option( 'surbma_hc_fields' );
	$productpriceadditions_productprefixValue = isset( $options['productpriceadditions-product-prefix'] ) && $options['productpriceadditions-product-prefix'] ? $options['productpriceadditions-product-prefix'] : false;
	$productpriceadditions_productsuffixValue = isset( $options['productpriceadditions-product-suffix'] ) && $options['productpriceadditions-product-suffix'] ? $options['productpriceadditions-product-suffix'] : false;
	$productpriceadditions_archiveprefixValue = isset( $options['productpriceadditions-archive-prefix'] ) && $options['productpriceadditions-archive-prefix'] ? $options['productpriceadditions-archive-prefix'] : false;
	$productpriceadditions_archivesuffixValue = isset( $options['productpriceadditions-archive-suffix'] ) && $options['productpriceadditions-archive-suffix'] ? $options['productpriceadditions-archive-suffix'] : false;

	// Get the parent product object if product is variable
	if ( $product->get_parent_id() ) {
		$product = wc_get_product( $product->get_parent_id() );
	}

	$product_productprefix = $product->get_meta( '_hc_productpriceadditions_product_prefix' ) ? $product->get_meta( '_hc_productpriceadditions_product_prefix', true ) : false;
	$product_disableproductprefix = $product->get_meta( '_hc_productpriceadditions_disable_product_prefix' ) ? $product->get_meta( '_hc_productpriceadditions_disable_product_prefix', true ) : false;
	$product_productsuffix = $product->get_meta( '_hc_productpriceadditions_product_suffix' ) ? $product->get_meta( '_hc_productpriceadditions_product_suffix', true ) : false;
	$product_disableproductsuffix = $product->get_meta( '_hc_productpriceadditions_disable_product_suffix' ) ? $product->get_meta( '_hc_productpriceadditions_disable_product_suffix', true ) : false;
	$product_archiveprefix = $product->get_meta( '_hc_productpriceadditions_archive_prefix' ) ? $product->get_meta( '_hc_productpriceadditions_archive_prefix', true ) : false;
	$product_disablearchiveprefix = $product->get_meta( '_hc_productpriceadditions_disable_archive_prefix' ) ? $product->get_meta( '_hc_productpriceadditions_disable_archive_prefix', true ) : false;
	$product_archivesuffix = $product->get_meta( '_hc_productpriceadditions_archive_suffix' ) ? $product->get_meta( '_hc_productpriceadditions_archive_suffix', true ) : false;
	$product_disablearchivesuffix = $product->get_meta( '_hc_productpriceadditions_disable_archive_suffix' ) ? $product->get_meta( '_hc_productpriceadditions_disable_archive_suffix', true ) : false;

	// Price prefix and suffix on Product page
	if ( is_product() ) {
		if ( !$product_disableproductprefix ) {
			if ( $product_productprefix ) {
				$price = '<span class="hc-price-prefix">' . $product_productprefix . '</span> ' . $price;
			} elseif ( $productpriceadditions_productprefixValue ) {
				$price = '<span class="hc-price-prefix">' . $productpriceadditions_productprefixValue . '</span> ' . $price;
			}
		}

		if ( !$product_disableproductsuffix ) {
			if ( $product_productsuffix ) {
				$price = $price . ' <span class="hc-price-suffix">' . $product_productsuffix . '</span> ';
			} elseif ( $productpriceadditions_productsuffixValue ) {
				$price = $price . ' <span class="hc-price-suffix">' . $productpriceadditions_productsuffixValue . '</span> ';
			}
		}
	}

	// Price prefix and suffix on Archive pages
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		if ( !$product_disablearchiveprefix ) {
			if ( $product_archiveprefix ) {
				$price = '<span class="hc-price-prefix">' . $product_archiveprefix . '</span> ' . $price;
			} elseif ( $productpriceadditions_archiveprefixValue ) {
				$price = '<span class="hc-price-prefix">' . $productpriceadditions_archiveprefixValue . '</span> ' . $price;
			}
		}

		if ( !$product_disablearchivesuffix ) {
			if ( $product_archivesuffix ) {
				$price = $price . ' <span class="hc-price-suffix">' . $product_archivesuffix . '</span> ';
			} elseif ( $productpriceadditions_archivesuffixValue ) {
				$price = $price . ' <span class="hc-price-suffix">' . $productpriceadditions_archivesuffixValue . '</span> ';
			}
		}
	}

	return $price;
}, 100, 2 );
