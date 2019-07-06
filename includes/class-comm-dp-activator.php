<?php

/**
 * Fired during plugin activation
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Comm_Dp
 * @subpackage Comm_Dp/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Comm_Dp
 * @subpackage Comm_Dp/includes
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Comm_Dp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		flush_rewrite_rules();
	}

}
