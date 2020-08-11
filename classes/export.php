<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class USA_Export
 *
 * Class for exporting all the user's roles/capabilities as a json file.
 */
class USA_Export {
	/**
	 * USA_Export constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'my_enqueue' ) );
		add_action( 'wp_ajax_export-all-roles-capabilities', array( $this, 'export_all_roles_capabilities' ) );
	}

	/**
	 * Load script.
	 */
	public function my_enqueue() {
		global $wpdb;
		$handle = 'user-setting-autoloader-script';

		wp_enqueue_script( 'axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', array(), '', true );
		wp_enqueue_script( $handle, plugin_dir_url( __FILE__ ) . '../js/user-setting-autoloader.js', array('jquery'), '1.0', true );
		wp_localize_script( $handle, 'USA_CONFIG', [
			'api'    => admin_url( 'admin-ajax.php' ),
			'prefix' => $wpdb->prefix,
			'action' => 'export-all-roles-capabilities',
			'nonce' => wp_create_nonce( 'export-all-roles-capabilities' ),
		]);
	}

	/**
	 * Export all the user's roles/capabilities as a json file.
	 */
	public function export_all_roles_capabilities() {
		$action = 'export-all-roles-capabilities';

		if ( check_ajax_referer( $action, 'nonce', false ) ) {
			$data = array();
			$roles = wp_roles()->roles;
			$roles_json = array();

			foreach ( $roles as $key => $value ) {
				$roles_json[$key] = $value;
			}

			$time = time();
			header('Content-Disposition: attachment; filename="' . $time . '-user-setting.json"');
			$data['data'] = json_encode( $roles_json );
			$status = 200;
		} else {
			$data['data'] = 'Forbidden';
			$data['message'] = 'Forbidden';
			$status = 403;
		}

		wp_send_json( $data, $status );
		die();
	}
}

new USA_Export();
