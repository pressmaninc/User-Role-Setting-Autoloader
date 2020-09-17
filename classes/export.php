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
		add_action( 'wp_ajax_export-all-roles-capabilities', array( $this, 'export_all_roles_capabilities' ) );
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
			header('Content-Disposition: attachment; filename="' . $time . USA_JSON_FILE_NAME . '"');
			$data = $roles_json;
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
