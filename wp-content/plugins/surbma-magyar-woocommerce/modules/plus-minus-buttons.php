<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

// Code reference: https://stackoverflow.com/questions/52367826/custom-plus-and-minus-quantity-buttons-in-woocommerce-3

add_action( 'woocommerce_before_quantity_input_field', function() {
	if ( is_product() ) {
		global $product;
		if ( $product && $product instanceof WC_Product && $product->get_max_purchase_quantity() != 1 ) {
			echo '<button type="button" class="qty-button minus">-</button>';
		}
	}
} );

add_action( 'woocommerce_after_quantity_input_field', function() {
	if ( is_product() ) {
		global $product;
		if ( $product && $product instanceof WC_Product && $product->get_max_purchase_quantity() != 1 ) {
			echo '<button type="button" class="qty-button plus">+</button>';
		}
	}
} );

add_filter( 'woocommerce_cart_item_quantity', function( $product_quantity, $cart_item_key, $cart_item ) {
	$product_data = $cart_item['data'];
	if ( is_object( $product_data ) ) {
		$product_properties = $product_data->get_data();
		if ( array_key_exists( 'sold_individually', $product_properties ) ) {
			$sold_individually = $product_properties['sold_individually'];
			if ( ! $sold_individually ) {
				$minusButton = '<button type="button" class="qty-button minus">-</button>';
				$plusButton = '<button type="button" class="qty-button plus">+</button>';
				return '<div class="quantity-wrap">' . $minusButton . $product_quantity . $plusButton . '</div>';
			}
		}
	}
	return $product_quantity;
}, 10, 3 );

add_action( 'wp_footer', function() {
	if ( is_product() || is_cart() ) {
	?>
<script>
jQuery( function( $ ) {
	if ( ! String.prototype.getDecimals ) {
		String.prototype.getDecimals = function() {
			var num = this,
				match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
			if ( ! match ) {
				return 0;
			}
			return Math.max( 0, ( match[1] ? match[1].length : 0 ) - ( match[2] ? +match[2] : 0 ) );
		}
	}
	// Quantity "plus" and "minus" buttons
	$( document.body ).on( 'click', '.plus, .minus', function() {
		<?php if ( is_product() ) { // Get the right quantity input field on the product page ?>
		var $qty        = $( this ).closest( '.quantity' ).find( '.qty'),
			currentVal  = parseFloat( $qty.val() ),
			max         = parseFloat( $qty.attr( 'max' ) ),
			min         = parseFloat( $qty.attr( 'min' ) ),
			step        = $qty.attr( 'step' );
		<?php } ?>
		<?php if ( is_cart() ) { // Get the right quantity input field on the cart page ?>
		var $qty        = $( this ).closest( '.quantity-wrap' ).find( '.qty'),
			currentVal  = parseFloat( $qty.val() ),
			max         = parseFloat( $qty.attr( 'max' ) ),
			min         = parseFloat( $qty.attr( 'min' ) ),
			step        = $qty.attr( 'step' );
		<?php } ?>
		// Format values
		if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
		if ( max === '' || max === 'NaN' ) max = '';
		if ( min === '' || min === 'NaN' ) min = 0;
		if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;

		// Change the value
		if ( $( this ).is( '.plus' ) ) {
			if ( max && ( currentVal >= max ) ) {
				$qty.val( max );
			} else {
				$qty.val( ( currentVal + parseFloat( step )).toFixed( step.getDecimals() ) );
			}
		}
		if ( $( this ).is( '.minus' ) ) {
			if ( min && ( currentVal <= min ) ) {
				$qty.val( min );
			} else if ( currentVal > 0 ) {
				$qty.val( ( currentVal - parseFloat( step )).toFixed( step.getDecimals() ) );
			}
		}

		// Trigger input event
		$qty.trigger( 'input' );
	});
});
</script>
	<?php
	}
} );

add_action( 'wp_head', function() {
	if ( is_product() || is_cart() ) {
		?>
<style id="plus-minus-buttons-style">
	td.product-quantity .quantity-wrap {display: flex;gap: 3px;}
	.quantity input::-webkit-outer-spin-button,
	.quantity input::-webkit-inner-spin-button {-webkit-appearance: none !important;margin: 0; !important}
	.quantity input {appearance: textfield !important;-moz-appearance: textfield !important;}
	.quantity .qty-button {cursor: pointer !important;}
	.woocommerce-cart table.cart .quantity .qty-button {vertical-align: middle;}
<?php if ( wp_basename( get_bloginfo( 'template_directory' ) ) == 'storefront' ) { ?>
	.quantity .qty-button {box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.125);}
	.woocommerce-cart table.cart .quantity .qty-button {vertical-align: unset;}
	table.cart td.product-quantity .qty {padding: .6180469716em;}
	table.cart .product-quantity .minus, table.cart .product-quantity .plus {display: inline-block;}
<?php } ?>
<?php if ( wp_basename( get_bloginfo( 'template_directory' ) ) == 'Divi' ) { ?>
	.woocommerce .quantity .qty-button {height: 49px !important;border-radius: 3px !important;font-weight: 500 !important;}
	.woocommerce .quantity .qty-button:hover {color: #fff!important;background-color: rgba(0,0,0,.2) !important;}
	.woocommerce .quantity {width: auto;}
<?php } ?>
</style>
	<?php
	}
} );
