<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Comm_Dp
 * @subpackage Comm_Dp/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Comm_Dp
 * @subpackage Comm_Dp/includes
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Comm_Dp_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

}
