<?php

// Add multiselect checkbox for user profiles
add_action( 'show_user_profile', 'cps_hc_wcgems_add_payment_methods_field', 9999 );
add_action( 'edit_user_profile', 'cps_hc_wcgems_add_payment_methods_field', 9999 );

function cps_hc_wcgems_add_payment_methods_field( $user ) {
	$selected_methods = get_user_meta( $user->ID, 'cps_hc_wcgems_payment_methods', true );
	$available_gateways = WC()->payment_gateways->payment_gateways();

	echo '<br>';
	echo '<h2>' . esc_html__( 'Limit Payment Methods', 'surbma-magyar-woocommerce' ) . ' (' . esc_html__( 'HuCommerce module', 'surbma-magyar-woocommerce' ) . ')</h2>';
	echo '<p>' . esc_html__( 'Disable any Payment Methods for a particular user. The disabled Payment Method will not be shown to the Customer on the Checkout page.', 'surbma-magyar-woocommerce' ) . '</p>';

	echo '<table class="form-table">';
	foreach ( $available_gateways as $gateway_id => $gateway) {
		$checked = in_array( $gateway_id, (array) $selected_methods ) ? 'checked' : '';
		echo '<tr>';
		echo '<th><label for="cps_hc_wcgems_payment_methods_' . esc_attr( $gateway_id ) . '">' . esc_html( $gateway->get_title() ) . '</label></th>';
		echo '<td><input type="checkbox" id="cps_hc_wcgems_payment_methods_' . esc_attr( $gateway_id ) . '" name="cps_hc_wcgems_payment_methods[]" value="' . esc_attr( $gateway_id ) . '" ' . $checked . '></td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '<br>';
}

// Save user payment methods
add_action( 'personal_options_update', 'cps_hc_wcgems_save_payment_methods_field' );
add_action( 'edit_user_profile_update', 'cps_hc_wcgems_save_payment_methods_field' );

function cps_hc_wcgems_save_payment_methods_field( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$selected_methods = isset( $_POST['cps_hc_wcgems_payment_methods'] ) ? $_POST['cps_hc_wcgems_payment_methods'] : array();
	update_user_meta( $user_id, 'cps_hc_wcgems_payment_methods', $selected_methods);
}

// Filter available payment methods by user
add_filter( 'woocommerce_available_payment_gateways', function( $available_gateways ) {
	if ( is_user_logged_in() ) {
		$user_payment_methods = get_user_meta( get_current_user_id(), 'cps_hc_wcgems_payment_methods', true );

		foreach ( $available_gateways as $gateway_id => $gateway ) {
			if ( in_array( $gateway_id, (array) $user_payment_methods ) ) {
				unset( $available_gateways[ $gateway_id ] );
			}
		}
	}

	return $available_gateways;
} );
