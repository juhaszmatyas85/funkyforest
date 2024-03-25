<?php

add_shortcode( 'hc-tel', function( $atts, $content = null ) {
	return '<a href="tel:+' . preg_replace('/\D/', '', $content) . '">' . $content . '</a>';
} );

add_shortcode( 'hc-mailto', function( $atts, $content = null ) {
	$encodedemail = '';
	for ( $i = 0; $i <strlen( $content ); $i++ ) {
		$encodedemail .= '&#' . ord( $content[$i] ) . ';';
	}
	return '<a href="mailto:' . $encodedemail . '">' . $encodedemail . '</a>';
} );

add_shortcode( 'hc-nev', function() {
	$options = get_option( 'surbma_hc_fields' );
	return $options['globalinfoname'];
} );

add_shortcode( 'hc-ceg', function() {
	$options = get_option( 'surbma_hc_fields' );
	return $options['globalinfocompany'];
} );

add_shortcode( 'hc-szekhely', function() {
	$options = get_option( 'surbma_hc_fields' );
	return $options['globalinfoheadquarters'];
} );

add_shortcode( 'hc-adoszam', function() {
	$options = get_option( 'surbma_hc_fields' );
	return $options['globalinfotaxnumber'];
} );

add_shortcode( 'hc-cegjegyzekszam', function() {
	$options = get_option( 'surbma_hc_fields' );
	return $options['globalinforegnumber'];
} );

add_shortcode( 'hc-cim', function() {
	$options = get_option( 'surbma_hc_fields' );
	return $options['globalinfoaddress'];
} );

add_shortcode( 'hc-bankszamlaszam', function() {
	$options = get_option( 'surbma_hc_fields' );
	return $options['globalinfobankaccount'];
} );

add_shortcode( 'hc-mobiltelefon', function() {
	$options = get_option( 'surbma_hc_fields' );
	return '<a href="tel:+' . preg_replace('/\D/', '', $options['globalinfomobile']) . '">' . $options['globalinfomobile'] . '</a>';
} );

add_shortcode( 'hc-telefon', function() {
	$options = get_option( 'surbma_hc_fields' );
	return '<a href="tel:+' . preg_replace('/\D/', '', $options['globalinfophone']) . '">' . $options['globalinfophone'] . '</a>';
} );

add_shortcode( 'hc-email', function() {
	$options = get_option( 'surbma_hc_fields' );
	$email = $options['globalinfoemail'];
	$encodedemail = '';
	for ( $i = 0; $i <strlen( $email ); $i++ ) {
		$encodedemail .= '&#' . ord( $email[$i] ) . ';';
	}
	return '<a href="mailto:' . $encodedemail . '">' . $encodedemail . '</a>';
} );

add_shortcode( 'hc-rolunk', function() {
	$options = get_option( 'surbma_hc_fields' );
	return $options['globalinfoaboutus'];
} );
