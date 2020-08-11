<?php
/**
 * Class USA_Import_TEST
 *
 * @package User_Role_Setting_Autoloader
 */

class USA_Import_TEST extends WP_UnitTestCase {
	/**
	 * Test to import all the user's roles and capabilities.
	 */
	public function test_usa_import()
	{
		$current_roles = array();
		foreach ( wp_roles()->roles as $key => $value ) {
			$current_roles[$key] = $value;
		}

		$add_roles = $current_roles;
		$add_roles['test_role'] = array(
			'name' => 'test',
			'capabilities' => array(
				'read' => 1,
				'level_0' => 1
			)
		);
		$usa_import = new USA_Import();
		$usa_import->import_all_roles_capabilities( $add_roles );

		$result = array();
		foreach ( wp_roles()->roles as $key => $value ) {
			$result[$key] = $value;
		}

		$this->assertNotEquals( $current_roles, $result );
		$this->assertEquals( $add_roles, $result );
	}
}
