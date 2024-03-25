<?php

// Prevent direct access to the plugin
defined( 'ABSPATH' ) || exit;

// Add new fields
add_filter( 'woocommerce_billing_fields', function( $fields ) {
	$options = get_option( 'surbma_hc_fields' );
	$billingcompanycheckValue = isset( $options['billingcompanycheck'] ) ? $options['billingcompanycheck'] : 0;
	$woocommercecheckoutcompanyfieldValue = get_option( 'woocommerce_checkout_company_field' ) != false ? get_option( 'woocommerce_checkout_company_field' ) : 'optional';
	if ( 'optional' == $woocommercecheckoutcompanyfieldValue && 1 == $billingcompanycheckValue ) {
		$fields['billing_company_check'] = array(
			'type' 			=> 'checkbox',
			'class'         => array( 'form-row-wide', 'woocommerce-form-row', 'woocommerce-form-row--wide', 'company' ),
			'label' 		=> '<span>' . __( 'Company billing', 'surbma-magyar-woocommerce' ) . '</span>',
			'label_class'   => array( 'woocommerce-form__label', 'woocommerce-form__label-for-checkbox' ),
			'input_class'   => array( 'woocommerce-form__input', 'woocommerce-form__input-checkbox' ),
			'priority' 		=> 29,
			'clear' 		=> true,
			'required' 		=> false
		);
	}
	return $fields;
} );

add_action( 'woocommerce_checkout_process', function() {
	// Nonce verification before doing anything
	check_ajax_referer( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce', false );

	$woocommercecheckoutcompanyfieldValue = get_option( 'woocommerce_checkout_company_field' ) != false ? get_option( 'woocommerce_checkout_company_field' ) : 'optional';

	if ( 'optional' == $woocommercecheckoutcompanyfieldValue && ( !empty( $_POST['billing_company_check'] ) && 1 == $_POST['billing_company_check'] ) && empty( $_POST['billing_company'] ) ) {
		$field_label = __( 'Company name', 'woocommerce' );
		/* translators: %s: Field label */
		$field_label = sprintf( _x( 'Billing %s', 'checkout-validation', 'woocommerce' ), $field_label );
		/* translators: %s: Field label */
		$noticeError = sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' );
		wc_add_notice( $noticeError, 'error' );
	}
} );

// Pre-populate billing_country field, if it's hidden
add_filter( 'default_checkout_billing_country', function( $value ) {
	$options = get_option( 'surbma_hc_fields' );
	$nocountryValue = isset( $options['nocountry'] ) ? $options['nocountry'] : 0;
	if ( 1 == $nocountryValue ) {
		// The country/state
		$store_raw_country = get_option( 'woocommerce_default_country' );
		// Split the country/state
		$split_country = explode( ':', $store_raw_country );
		// Country and state separated:
		$store_country = $split_country[0];
		// $store_state = $split_country[1];

		$value = $store_country;
	}
	return $value;
} );

add_filter( 'woocommerce_billing_fields', function( $address_fields ) {
	$options = get_option( 'surbma_hc_fields' );
	$emailtothetopValue = isset( $options['emailtothetop'] ) ? $options['emailtothetop'] : 0;
	if ( 1 == $emailtothetopValue ) {
		$address_fields['billing_email']['priority'] = 5;
	}
	return $address_fields;
}, 10, 1 );

// Customize the checkout default address fields
add_filter( 'woocommerce_default_address_fields' , function( $address_fields ) {
	$woocommercecheckoutaddress2fieldValue = get_option( 'woocommerce_checkout_address_2_field' ) != false ? get_option( 'woocommerce_checkout_address_2_field' ) : 'optional';
	$options = get_option( 'surbma_hc_fields' );

	$postcodecitypairValue = isset( $options['postcodecitypair'] ) ? $options['postcodecitypair'] : 0;
	if ( 1 == $postcodecitypairValue ) {
		$address_fields['postcode']['priority'] = 69;
		$address_fields['postcode']['class'] = array( 'form-row-first' );
		$address_fields['city']['class'] = array( 'form-row-last' );
	}

	return $address_fields;
} );

// Customize the checkout fields
add_filter( 'woocommerce_checkout_fields' , function( $fields ) {
	$options = get_option( 'surbma_hc_fields' );

	$companytaxnumberpairValue = isset( $options['companytaxnumberpair'] ) ? $options['companytaxnumberpair'] : 0;
	if ( isset( $fields['billing']['billing_company'] ) && isset( $fields['billing']['billing_tax_number'] ) && 1 == $companytaxnumberpairValue ) {
		$fields['billing']['billing_company']['class'] = array( 'form-row-first' );
		$fields['billing']['billing_tax_number']['class'] = array( 'form-row-last' );
	}

	$phoneemailpairValue = isset( $options['phoneemailpair'] ) ? $options['phoneemailpair'] : 0;
	$emailtothetopValue = isset( $options['emailtothetop'] ) ? $options['emailtothetop'] : 0;
	if ( isset( $fields['billing']['billing_phone'] ) && isset( $fields['billing']['billing_email'] ) && 1 == $phoneemailpairValue && 1 != $emailtothetopValue ) {
		$fields['billing']['billing_phone']['class'] = array( 'form-row-first' );
		$fields['billing']['billing_email']['class'] = array( 'form-row-last' );
	}

	$noordercommentsValue = isset( $options['noordercomments'] ) ? $options['noordercomments'] : 0;
	if ( isset( $fields['order']['order_comments'] ) && 1 == $noordercommentsValue ) {
		unset( $fields['order']['order_comments'] );
	}

	return $fields;
}, 9999 );

// Remove Additional information section
add_action( 'woocommerce_before_checkout_form' , function() {
	$options = get_option( 'surbma_hc_fields' );
	$noadditionalinformationValue = isset( $options['noadditionalinformation'] ) ? $options['noadditionalinformation'] : 0;
	if ( 1 == $noadditionalinformationValue ) {
		add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );
	}
} );

// Custom submit button text
add_filter( 'woocommerce_order_button_text', function( $button_text ) {
	$options = get_option( 'surbma_hc_fields' );
	$checkout_customsubmitbuttontextValue = isset( $options['checkout-customsubmitbuttontext'] ) && '' != $options['checkout-customsubmitbuttontext'] ? $options['checkout-customsubmitbuttontext'] : false;
	if ( $checkout_customsubmitbuttontextValue ) {
		$button_text = $checkout_customsubmitbuttontextValue;
	}
	return $button_text;
} );

add_action( 'wp_footer', function() {
	if ( ! is_checkout() ) {
		return;
	}

	$woocommercecheckoutcompanyfieldValue = get_option( 'woocommerce_checkout_company_field' ) != false ? get_option( 'woocommerce_checkout_company_field' ) : 'optional';
	$options = get_option( 'surbma_hc_fields' );
	$billingcompanycheckValue = isset( $options['billingcompanycheck'] ) ? $options['billingcompanycheck'] : 0;
	$checkout_hidecompanytaxfields_value = isset( $options['checkout-hidecompanytaxfields'] ) ? $options['checkout-hidecompanytaxfields'] : 0;
	$nocountryValue = isset( $options['nocountry'] ) ? $options['nocountry'] : 0;
	$companytaxnumberpairValue = isset( $options['companytaxnumberpair'] ) ? $options['companytaxnumberpair'] : 0;
	// ob_start();
	?>
<script id="cps-hc-wcgems-checkout">
jQuery(document).ready(function($){
	// Fix for previous version, that saved '- N/A -'' value if billing_company was empty
	if ( $('#billing_company').val() == '- N/A -' ){
		$('#billing_company').val('');
	}

	<?php if ( 1 == $billingcompanycheckValue ) { ?>
		$("#billing_company_field label span").remove();
		$("#billing_tax_number_field label span").remove();
	<?php } ?>

	// All the actions, when the billing_company_check field is unchecked
	function showCompanyFields() {
		$('#billing_company_field').show();

		// Add saved values back
		if(localStorage.getItem('billing_company')){
			$('#billing_company').val(localStorage.getItem('billing_company'));
		}
		if(localStorage.getItem('billing_tax_number')){
			$('#billing_tax_number').val(localStorage.getItem('billing_tax_number'));
		}
		localStorage.removeItem('billing_company');
		localStorage.removeItem('billing_tax_number');
	}

	// All the actions, when the billing_company_check field is unchecked
	function hideCompanyFields() {
		// Save already entered value, if customer wants to enable company fields again
		localStorage.setItem('billing_company', $('#billing_company').val());
		localStorage.setItem('billing_tax_number', $('#billing_tax_number').val());

		// Hiding the company related fields
		$('#billing_company_field').hide();
		$('#billing_tax_number_field').hide();

		// Empty the company related field values, because we don't want to save company details
		$('#billing_company').val('');
		$('#billing_tax_number').val('');

		// Reset classes, as they are empty again
		$("#billing_company_field").removeClass('validate-required');
		$("#billing_company_field").removeClass('woocommerce-validated');
		$("#billing_company_field").removeClass('woocommerce-invalid woocommerce-invalid-required-field');
		$("#billing_tax_number_field").removeClass('validate-required');
		$("#billing_tax_number_field").removeClass('woocommerce-validated');
		$("#billing_tax_number_field").removeClass('woocommerce-invalid woocommerce-invalid-required-field');
	}

	<?php if ( 'optional' == $woocommercecheckoutcompanyfieldValue && 1 == $billingcompanycheckValue ) { ?>
		$('#billing_company_check_field label span.optional').hide();

		// Add required sign and remove the "not required" text from billing_company_field
		$('#billing_company_field label').append( ' <abbr class="required" title="required">*</abbr>' );
		$('#billing_company_field label span').hide();

		if($('#billing_company_check').prop('checked') == true){
			$('#billing_company_field').addClass('validate-required');
			$('#billing_tax_number_field').addClass('validate-required');
		}

		if($('#billing_company_check').prop('checked') == false){
			$('#billing_company_field').hide();
			$('#billing_tax_number_field').hide();
		}

		$('#billing_company_check').click(function(){
			if($(this).prop('checked') == true){
				$('#billing_company_field').addClass('validate-required');
				$('#billing_tax_number_field').addClass('validate-required');
				$('#billing_tax_number_field').show();
				showCompanyFields();
			}
			else if($(this).prop('checked') == false){
				hideCompanyFields();
			}
		});
	<?php } ?>

	<?php if ( 1 == $checkout_hidecompanytaxfields_value ) { ?>
		// Function to hide/show company fields based on selected country
		function hideShowCompanyFields() {
			const selectedCountry = $('#billing_country').val();

			<?php if ( 1 == $billingcompanycheckValue ) { ?>
				if ( selectedCountry !== 'HU' ) {
					// Hiding the Company checkbox
					$('#billing_company_check_field').hide();

					// Uncheck the Company checkbox
					$('#billing_company_check').prop('checked', false);

					hideCompanyFields();
				} else {
					$('#billing_company_check_field').show();
				}
			<?php } else { ?>
				if ( selectedCountry !== 'HU' ) {
					hideCompanyFields();
				} else {
					showCompanyFields();
					if ( $('#billing_company').val().trim() !== '' ) {
						$('#billing_tax_number_field').show();
					}
				}
			<?php } ?>
		}

		hideShowCompanyFields();

		// Call the function when the Country dropdown changes
		$('#billing_country').on('change', function() {
			hideShowCompanyFields();
		});
	<?php } ?>

	<?php if ( 1 == $nocountryValue ) { ?>
		$("#billing_country_field").hide();
	<?php } ?>
});
</script>
<?php
	// $script = ob_get_contents();
	// ob_end_clean();

	// wp_add_inline_script( 'cps-jquery-fix', $script );
}, 99 );
