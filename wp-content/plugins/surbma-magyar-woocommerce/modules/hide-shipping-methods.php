<?php

// Remove all Shipping methods on the Cart page
add_filter( 'woocommerce_cart_ready_to_calc_shipping', function( $show_shipping ) {
	$options = get_option( 'surbma_hc_fields' );
	$hideshippingmethodscartValue = isset( $options['hideshippingmethods-cart'] ) && 1 == $options['hideshippingmethods-cart'] ? 1 : 0;

	if ( $hideshippingmethodscartValue && is_cart() ) {
		return false;
	}

	return $show_shipping;
}, 99 );

add_filter( 'woocommerce_package_rates', function( $available_shipping_methods, $package ) {
	$options = get_option( 'surbma_hc_fields' );
	$shippingmethodstohideValue = isset( $options['shippingmethodstohide'] ) ? $options['shippingmethodstohide'] : 'showall';

	if ( 'showall' == $shippingmethodstohideValue ) {
		return $available_shipping_methods;
	}

	$new_shipping_methods = array();

	if ( !empty( $available_shipping_methods ) ) {

		// Allow only Free shipping methods
		if ( 'hideall' === $shippingmethodstohideValue ) {
			foreach ( $available_shipping_methods as $methods => $details ) {
				if ( 'free_shipping' === $details->method_id ) {
					$new_shipping_methods[$methods] = $details;
				}
			}
		}

		// Allow Free shipping and Local pickup methods
		if ( 'hideall_except_local' === $shippingmethodstohideValue ) {
			// Check if Free shipping is available
			foreach ( $available_shipping_methods as $methods => $details ) {
				if ( 'free_shipping' === $details->method_id ) {
					$new_shipping_methods[$methods] = $details;
					break;
				}
			}

			// Let's build the available shipping methods array again
			if ( !empty( $new_shipping_methods ) ) {
				$new_shipping_methods = array();
				foreach ( $available_shipping_methods as $methods => $details ) {
					if ( 'free_shipping' === $details->method_id || 'local_pickup' === $details->method_id ) {
						$new_shipping_methods[$methods] = $details;
					}
				}
			}
		}

		// Allow Free shipping, Local pickup and all Hungarian "pont" shipping methods
		if ( 'hideall_except_pickups' === $shippingmethodstohideValue ) {
			/*
			 ** Possible shipping methods to add in future:
			 **
			 ** flat_rate
			 ** advanced_flat_rate_shipping
			 ** table_rate
			 ** flexible_shipping_single
			 */

			// Check if Free shipping is available
			foreach ( $available_shipping_methods as $methods => $details ) {
				if ( 'free_shipping' === $details->method_id ) {
					$new_shipping_methods[$methods] = $details;
					break;
				}
			}

			// Let's build the available shipping methods array again
			if ( !empty( $new_shipping_methods ) ) {
				$new_shipping_methods = array();
				foreach ( $available_shipping_methods as $methods => $details ) {
					if ( 'free_shipping' === $details->method_id || 'local_pickup' === $details->method_id || 'vp_pont' === $details->method_id || 'wc_pont_shipping_method' === $details->method_id || 'foxpost_woo_parcel_apt_shipping' === $details->method_id || 'foxpost_package_point' === $details->method_id || 'wc_postapont' === $details->method_id ) {
						$new_shipping_methods[$methods] = $details;
					}
				}
			}
		}

	}

	return !empty( $new_shipping_methods ) ? $new_shipping_methods : $available_shipping_methods;
}, 10, 2 );
