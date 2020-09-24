<?php
/**
 * Class USA_Export_TEST
 *
 * @package User_Role_Setting_Autoloader
 */

require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';

class USA_Export_TEST extends WP_Ajax_UnitTestCase {
	/**
	 * Test to export all the user's roles and capabilities.
	 */
	public function test_usa_export()
	{
		$action = 'export-all-roles-capabilities';

		$_POST['action'] = $action;
		$_POST['nonce'] = wp_create_nonce( $action );
		try {
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response_data = json_decode( $this->_last_response, true );
		$result = json_decode( json_encode( $response_data ), true );

		$expected = array();
		foreach ( wp_roles()->roles as $key => $value ) {
			$expected[$key] = $value;
		}

		$this->assertEquals( $expected, $result );
	}
}
