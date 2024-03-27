<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( hp_is_plugin_activated( 'gravityforms', 'gravityforms.php' ) ) {

	$gform_installation_wizard_license_key = array(
		'license_key'  => 'ea247f6f2342a58670ad96bf98781ebc',
		'accept_terms' => true,
		'is_valid_key' => true,
	);
	HP_check_options( 'rg_gforms_key', 'ea247f6f2342a58670ad96bf98781ebc' );
	HP_check_options( 'gform_installation_wizard_license_key', $gform_installation_wizard_license_key );
}
