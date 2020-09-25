<?php
/*
Plugin Name: User Role Setting Autoloader
Plugin URI:
Description: Import or Export all the user's roles and capabilities.
Version: 1.0.0
Author: PRESSMAN
Author URI: https://www.pressman.ne.jp/
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'USA_IMPORT_DIR_PATH',  plugin_dir_path( __FILE__ ) . 'import' );
define( 'USA_JSON_FILE_NAME', '-user-role-setting.json' );

// Require files.
require_once( plugin_dir_path( __FILE__ ) . 'classes/option.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/export.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/import.php' );
require_once( plugin_dir_path( __FILE__ ) . 'classes/activation.php' );

register_activation_hook( __FILE__, array( 'USA_Activation', 'execute' ) );
