<?php

/**
 * Fired during plugin activation
 *
 * @link       piwebsolution.com
 * @since      1.0.0
 *
 * @package    Pisol_Fsnw
 * @subpackage Pisol_Fsnw/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pisol_Fsnw
 * @subpackage Pisol_Fsnw/includes
 * @author     PI Websolution <sales@piwebsolution.com>
 */
class Pisol_Fsnw_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_option('pi_fsnw_do_activation_redirect', true);
	}

}
