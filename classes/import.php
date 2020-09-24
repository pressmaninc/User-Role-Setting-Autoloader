<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class USA_Import
 *
 * Class for　importing all the user's roles/capabilities from the json file.
 */
class USA_Import {
	private $current_roles = array();

	/**
	 * USA_Import constructor.
	 */
	public function __construct() {
		foreach ( wp_roles()->roles as $key => $value ) {
			$this->current_roles[$key] = $value['capabilities'];
		}

		add_action( 'admin_init', array( $this, 'import_all_roles_capabilities_controller' ) );
	}

	/**
	 * Controller to import all the user's roles/capabilities from the json file.
	 */
	public function import_all_roles_capabilities_controller() {
		if ( false === get_transient( 'usa_import_data' ) ) {
			$result = $this->get_json_content();

			if ( $result['content'] ) {
				$usa_transient_expiration = 300;
				$usa_transient_expiration = apply_filters( 'usa_transient_expiration', $usa_transient_expiration );
				set_transient( 'usa_import_data', $result, $usa_transient_expiration );
				$this->import_all_roles_capabilities( $result['content'] );
				delete_transient( 'usa_import_data' );
				update_option( 'usa_import_timestamp', $result['timestamp'] );
			}
		}
	}

	/**
	 * Get json content for import.
	 *
	 * @return array
	 */
	public function get_json_content() {
		$import_dir_path = USA_IMPORT_DIR_PATH;
		$import_dir_path = apply_filters( 'usa-import-dir', $import_dir_path );
		$timestamp_on_import = get_option( 'usa_import_timestamp' );
		$result = array(
			'content' => '',
			'timestamp' => $timestamp_on_import
		);
		$latest_timestamp = $this->get_latest_timestamp_from_import_dir_path( $import_dir_path );

		// On the first import, or when $latest_timestamp > $timestamp_on_import, the import is executed.
		if ( ! $timestamp_on_import || $latest_timestamp > $timestamp_on_import ) {
			$result['timestamp'] = $latest_timestamp;
			$file_path = $import_dir_path . '/' . $result['timestamp'] . USA_JSON_FILE_NAME;

			if ( file_exists( $file_path ) ) {
				$content = json_decode( file_get_contents( $file_path ) );

				if ( json_last_error() === 0 ) {
					// convert stdClass Object into an array:
					$result['content'] = json_decode( json_encode( $content ), true );
				}
			}
		}

		return $result;
	}

	/**
	 * Get latest timestamp from import dir path.
	 *
	 * @param $import_dir_path
	 *
	 * @return string
	 */
	public function get_latest_timestamp_from_import_dir_path( $import_dir_path ) {
		$time_stamps = array();

		if ( is_dir( $import_dir_path ) ) {
			foreach ( glob( $import_dir_path . '/*', GLOB_NOSORT ) as $file ) {
				$file_name_array = explode( '/', $file );
				$file_name = array_pop( $file_name_array );
				$time_stamp = str_replace( USA_JSON_FILE_NAME, '', $file_name );
				array_push( $time_stamps, $time_stamp );
			}
		}
		rsort( $time_stamps );

		return count( $time_stamps ) > 0 ? $time_stamps[0] : '';
	}

	/**
	 * Import all the user's roles/capabilities.
	 *
	 * @param $roles
	 */
	public function import_all_roles_capabilities( $roles ) {
		$current_roles_key = array_keys( $this->current_roles );
		$roles_key = array_keys( $roles );

		foreach ( $roles as $key => $value ) {
			// When updating capabilities, remove and add the role
			if ( in_array( $key, $current_roles_key ) ) {
				remove_role( $key );
			}
			add_role( $key, $value['name'], $value['capabilities'] );
		}

		foreach ( $this->current_roles as $key => $value ) {
			// If $key is not included in the exported roles, delete it
			if ( ! in_array( $key, $roles_key ) ) {
				remove_role( $key );
			}
		}
	}
}

new USA_Import();
