<?php
/**
 * Class USA_Activation_TEST
 *
 * @package User_Role_Setting_Autoloader
 */

class USA_Activation_TEST extends WP_UnitTestCase {
	/**
	 * Test plugin activation.
	 */
	public function test_execute()
	{
		if ( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) ) {
			unlink( USA_IMPORT_DIR_PATH . '/.htaccess' );
		}
		if ( file_exists( USA_IMPORT_DIR_PATH ) ) {
			rmdir( USA_IMPORT_DIR_PATH );
		}

		$this->assertFalse( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) );
		USA_Activation::execute();
		$this->assertTrue( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) );
		$this->assertSame( file_get_contents( USA_IMPORT_DIR_PATH . '/.htaccess' ), 'deny from all' );

		// Second activation.
		USA_Activation::execute();
		$this->assertTrue( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) );
		$this->assertSame( file_get_contents( USA_IMPORT_DIR_PATH . '/.htaccess' ), 'deny from all' );
	}

	/**
	 * Test import dir exists.
	 *
	 * @return void
	 */
	public function test_dir_exists()
	{
		if ( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) ) {
			unlink( USA_IMPORT_DIR_PATH . '/.htaccess' );
		}
		if ( file_exists( USA_IMPORT_DIR_PATH ) ) {
			rmdir( USA_IMPORT_DIR_PATH );
		}

		$this->assertFalse( $this->invokeStaticMethod( 'USA_Activation', 'dir_exists' ) );
		$this->invokeStaticMethod( 'USA_Activation', 'create_dir' );
		$this->assertTrue( $this->invokeStaticMethod( 'USA_Activation', 'dir_exists' ) );
	}

	/**
	 * Test import create dir.
	 *
	 * @return void
	 */
	public function test_create_dir()
	{
		if ( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) ) {
			unlink( USA_IMPORT_DIR_PATH . '/.htaccess' );
		}
		if ( file_exists( USA_IMPORT_DIR_PATH ) ) {
			rmdir( USA_IMPORT_DIR_PATH );
		}

		$this->assertFalse( file_exists( USA_IMPORT_DIR_PATH ) );
		$this->invokeStaticMethod( 'USA_Activation', 'create_dir' );
		$this->assertTrue( file_exists( USA_IMPORT_DIR_PATH ) );
	}

	/**
	 * Test import htaccess file exists.
	 *
	 * @return void
	 */
	public function test_htaccess_exists()
	{
		if ( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) ) {
			unlink( USA_IMPORT_DIR_PATH . '/.htaccess' );
		}
		if ( ! file_exists( USA_IMPORT_DIR_PATH ) ) {
			$this->invokeStaticMethod( 'USA_Activation', 'create_dir' );
		}

		$this->assertFalse( $this->invokeStaticMethod( 'USA_Activation', 'htaccess_exists' ) );
		$this->invokeStaticMethod( 'USA_Activation', 'create_htaccess' );
		$this->assertTrue( $this->invokeStaticMethod( 'USA_Activation', 'htaccess_exists' ) );
	}

	/**
	 * Test import create htaccess file.
	 *
	 * @return void
	 */
	public function test_create_htaccess()
	{
		if ( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) ) {
			unlink( USA_IMPORT_DIR_PATH . '/.htaccess' );
		}
		if ( ! file_exists( USA_IMPORT_DIR_PATH ) ) {
			$this->invokeStaticMethod( 'USA_Activation', 'create_dir' );
		}

		$this->assertFalse( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) );
		$this->invokeStaticMethod( 'USA_Activation', 'create_htaccess' );
		$this->assertTrue( file_exists( USA_IMPORT_DIR_PATH . '/.htaccess' ) );

		$this->assertSame( file_get_contents( USA_IMPORT_DIR_PATH . '/.htaccess' ), 'deny from all' );
	}

	public static function invokeStaticMethod( $class_obj, $method_name, $args = array() )
	{
		$test_class = new ReflectionClass( $class_obj );
		$method = $test_class->getMethod( $method_name );
		$method->setAccessible( true );
		return $method->invokeArgs( null, $args );
	}
}
