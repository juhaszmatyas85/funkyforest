<?php

/**
 * Name: Pont shipping for Woocommerce
 * Description: Select2 Ajax
 * Plugin URI: https://szathmari.hu/wordpress/
 * Version: 5.6
 * Author: szathmari.hu
 * Author URI: http://szathmari.hu/
 * Copyright: ©2020 szathmari.hu
 */
$q=filter_input(INPUT_POST, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
$c=filter_input(INPUT_POST, 'c', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$p = [];
foreach ( $c as $k ) {
	$jsonfile = $k.'pont.json';
	if ( !file_exists( $jsonfile ))
		continue;
	$t = json_decode( file_get_contents( $jsonfile ), true );
	$p = array_merge ( $p, $t );
}
(count($p)) ?: exit;
foreach ($p as $key => $value) {
	( isset( $value['zip'] ) && preg_match("/$q/i", $value['zip'] ) || isset ( $value['address'] ) && preg_match( "/$q/i", $value['address'] )) && $json[] = $value;
}
echo json_encode($json);

?>
