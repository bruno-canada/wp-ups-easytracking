<?php
/**
 * @package UpsEasyTracking
 */
/*
Plugin Name: UPS Easy Tracking
Plugin URI: https://github.com/bruno-canada/wp-ups-easytracking
Description: This is a very light plugin to add UPS tracking field that connects to UPS web service and show the shipment activity inside your website.
Version: 1.0.0
Author: Bruno Canada
Author URI: https://github.com/bruno-canada
License: GPLv2+
Text Domain: wp-ups-easytracking
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/

//Security measure to avoid direct access to the plugin and exposure of information
if ( !function_exists( 'add_action' ) ) {
	echo 'This plugin cannot be accessed directly.';
	exit;
}

define( 'WPUPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPUPS_PLUGIN_URI', 'wp-ups-easytracking' );
define( 'WPUPS_PLUGIN_MAINFILE', 'wp-ups-easytracking/wp-ups-easytracking.php' );

require_once( WPUPS_PLUGIN_DIR . 'src/class.upseasytracking.php' );

register_activation_hook( __FILE__, array( 'UpsEasyTracking', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'UpsEasyTracking', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'UpsEasyTracking', 'uninstall' ) );

add_action( 'init', array( 'UpsEasyTracking', 'init' ) );


//Ajax Setting
require_once( WPUPS_PLUGIN_DIR . 'src/class.upseasytracking-api.php' );
add_action( "wp_ajax_processWPUPSAjax", array('UpsEasyTracking_Api',"track") );
add_action( "wp_ajax_nopriv_processWPUPSAjax", array('UpsEasyTracking_Api',"track") );


//IF Admin is logged in load class
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( WPUPS_PLUGIN_DIR . 'src/class.upseasytracking-admin.php' );
	add_action( 'init', array( 'UpsEasyTracking_Admin', 'init' ) );
}