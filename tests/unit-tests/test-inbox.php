<?php

/**
 * Testing the common functionality of Feed Add-On based steps.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Inbox extends GF_UnitTestCase {

	/**
	 * @var int
	 */
	protected $form_id;

	/**
	 * @var Gravity_Flow_API
	 */
	protected $api;

	/**
	 * Creates a form and uses it to initialise the Gravity Flow API.
	 */
	public function setUp() {
		parent::setUp();

		$this->form_id = $this->factory->form->create();
		$this->api     = new Gravity_Flow_API( $this->form_id );
		$this->_include_inbox();
	}

	function test_get_filter_key_anonymous() {
		$this->_set_anonymous();
		$key = Gravity_Flow_Inbox::get_filter_key();
		$this->assertEmpty( $key );
	}

	function test_get_filter_key_current_user() {
		$this->_set_user();
		$key = Gravity_Flow_Inbox::get_filter_key();
		$this->assertEquals( 'workflow_user_id_1', $key );
	}

	function test_get_filter_key_access_token() {
		$this->_set_access_token();
		$key = Gravity_Flow_Inbox::get_filter_key();
		$this->assertEquals( 'workflow_email_test@test.test', $key );
	}

	function test_get_search_criteria_anonymous() {
		$this->_set_anonymous();
		$search_criteria = Gravity_Flow_Inbox::get_search_criteria();
		$this->assertEmpty( $search_criteria );
	}

	function test_get_search_criteria_current_user() {
		$expected = array(
			'field_filters' => array(
				array(
					'key'   => 'workflow_user_id_1',
					'value' => 'pending',
				),
				array(
					'key'   => 'workflow_role_administrator',
					'value' => 'pending',
				),
				'mode' => 'any',
			),
			'status'        => 'active',
		);

		$this->_set_user();
		$search_criteria = Gravity_Flow_Inbox::get_search_criteria();
		$this->assertEquals( $expected, $search_criteria );
	}

	function test_get_search_criteria_access_token() {
		$expected = array(
			'field_filters' => array(
				array(
					'key'   => 'workflow_email_test@test.test',
					'value' => 'pending',
				),
				'mode' => 'any',
			),
			'status'        => 'active',
		);

		$this->_set_access_token();
		$search_criteria = Gravity_Flow_Inbox::get_search_criteria();
		$this->assertEquals( $expected, $search_criteria );
	}

	/* HELPERS */

	/**
	 * Include the Gravity_Flow_Inbox class.
	 */
	public function _include_inbox() {
		require_once( gravity_flow()->get_base_path() . '/includes/pages/class-inbox.php' );
	}

	/**
	 * Ensures the current user global and the access token query string parameter are empty.
	 */
	public function _set_anonymous() {
		wp_set_current_user( 0 );
		unset( $_GET['gflow_access_token'] );
	}

	/**
	 * Populates the current user global for the admin user.
	 */
	public function _set_user() {
		wp_set_current_user( 1 );
	}

	/**
	 * Clears the current user global and sets the access token query string parameter.
	 */
	public function _set_access_token() {
		wp_set_current_user( 0 );

		// Decode the token to prevent a validation failure when base64 decoded in Gravity_Flow::validate_access_token().
		$_GET['gflow_access_token'] = urldecode( gravity_flow()->generate_access_token( new Gravity_Flow_Assignee( 'email|test@test.test' ) ) );
	}

}
