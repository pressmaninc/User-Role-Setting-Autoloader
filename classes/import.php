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

		register_activation_hook(
			plugin_dir_path( plugin_dir_path( __FILE__ ) ) . 'user-setting-autoloader.php',
			array( $this, 'import_all_roles_capabilities_controller' )
		);
	}

	/**
	 * Controller to import all the user's roles/capabilities from the json file.
	 */
	public function import_all_roles_capabilities_controller() {
		$result = $this->get_json_content();

		if ( $result['content'] ) {
			$this->import_all_roles_capabilities( $result['content'] );
			add_option( 'usa_import_timestamp', $result['timestamp'] );
		}
	}

	/**
	 * Get json content for import.
	 *
	 * @return array
	 */
	public function get_json_content() {
		$default_import_file_dir = plugin_dir_path( plugin_dir_path( __FILE__ ) ) . 'import';
		$import_file_dir = get_option( 'usa_import_dir_path' ) ? esc_attr( get_option( 'usa_import_dir_path' ) ) : $default_import_file_dir;
		$time_stamp = get_option( 'usa_import_timestamp' );
		$result = array(
			'content' => '',
			'timestamp' => $time_stamp
		);

		if ( ! $time_stamp ) {
			$time_stamps = array();
			if ( is_dir( $import_file_dir ) ) {
				foreach ( glob( $import_file_dir . '/*' ) as $file ) {
					$file_name_array = explode( '/', $file );
					$file_name = array_pop( $file_name_array );
					$time_stamp = str_replace( '-user-setting.json', '', $file_name );
					array_push( $time_stamps, $time_stamp );
				}
			}
			rsort( $time_stamps );
			$time_stamp = $time_stamps[0];
			$result['timestamp'] = $time_stamp;
		}

		$file_path = $import_file_dir . '/' . $time_stamp . '-user-setting.json';

		if ( file_exists( $file_path ) ) {
			$content = json_decode( file_get_contents( $file_path ) );

			if ( json_last_error() === 0 ) {
				// convert stdClass Object into an array:
				$result['content'] = json_decode( json_encode( $content ), true );
			}
		}

		return $result;
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
