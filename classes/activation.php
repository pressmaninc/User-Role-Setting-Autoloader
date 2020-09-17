<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class USA_Activation
 *
 * plugin activation Logic.
 */
class USA_Activation {
	/**
	 * Activation hook execute.
	 *
	 * @return void
	 */
	public static function execute() {
		if ( ! self::dir_exists() ) {
			self::create_dir();
		}

		if ( ! self::htaccess_exists() ) {
			self::create_htaccess();
		}
	}

	/**
	 * Import directory exist.
	 *
	 * @return void
	 */
	private static function dir_exists() {
		return file_exists( USA_IMPORT_DIR_PATH );
	}

	/**
	 * Create a directory.
	 *
	 * @return void
	 */
	public static function create_dir() {
		mkdir( USA_IMPORT_DIR_PATH );
	}

	/**
	 * htaccess file exist.
	 *
	 * @return void
	 */
	private static function htaccess_exists() {
		return file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' );
	}

	/**
	 * Create htaccess file.
	 *
	 * @return void
	 */
	private static function create_htaccess() {
		$file_handle = fopen( USA_IMPORT_DIR_PATH . '/.htaccess', 'w' );
		fwrite( $file_handle, 'deny from all' );
		fclose( $file_handle );
	}
}