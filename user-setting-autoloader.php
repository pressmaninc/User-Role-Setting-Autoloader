<?php
/*
Plugin Name: User Setting Autoloader
Plugin URI:
Description:
Version: 1.0.0
Author: PRESSMAN
Author URI: https://www.pressman.ne.jp/
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Require files.
require_once( plugin_dir_path( __FILE__ ) . 'classes/option.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/export.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/import.php' );

