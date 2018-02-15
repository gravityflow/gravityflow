<?php

/**
 * Testing the Gravity Flow API Functions.
 *
 * Note: all the database operations are wrapped in a transaction and rolled back at teh end of each test.
 * So when debugging it's best not to stop the execution completely - best to let the tests run till the end.
 * This also means that if you check the database directly in the middle of debugging a test you won't see any changes - it'll appear empty.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Webhooks extends GF_UnitTestCase {

	/**
	 * @var GF_UnitTest_Factory
	 */
	protected $factory;

	/**
	 * @var int
	 */
	protected $form_id;

	/**
	 * @var Gravity_Flow_API
	 */
	protected $api;

	function setUp() {
		parent::setUp();

		$this->form_id = $this->factory->form->create();
		$this->api = new Gravity_Flow_API( $this->form_id );
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_webhook_process_200() {

		$this->_create_webhook_test( '200', 'complete' );

	}

	function test_webhook_process_400() {

		$this->_create_webhook_test( '400', 'error_client' );

	}

	function test_webhook_process_500() {

		$this->_create_webhook_test( '500', 'error_server' );

	}

	function test_webhook_process_other() {

		$this->_create_webhook_test( 'other', 'error' );

	}

	function test_the_tests() {

		$t = 1;
		$this->assertEquals( 1, $t );
	}

	/* HELPERS */
	function get_form_id() {
		return $this->form_id;
	}

	function _create_entries() {
		$form_id = $this->get_form_id();
		$this->factory->entry->create_many( 1, array(
			'form_id' => $form_id,
			'date_created' => '2013-11-28 11:00',
			'1' => 'Second Choice',
			'2.2' => 'Second Choice',
			'8' => '1',
			'13.6' => 'Spain',
		));
	}

	function _add_approval_step( $override_settings = array() ) {
		$default_settings = array(
			'step_name' => 'Approval',
			'description' => '',
			'step_type' => 'approval',
			'feed_condition_logic_conditional_logic' => false,
			'feed_condition_conditional_logic_object' => array(),
			'type' => 'select',
			'assignees' => array( 'user_id|1' ),
			'routing' => array(),
			'unanimous_approval' => '',
			'assignee_notification_enabled' => false,
			'assignee_notification_message' => 'A new entry is pending your approval',
			'destination_complete' => 'next',
			'destination_rejected' => 'complete',
			'destination_approved' => 'next',
		);

		$settings = wp_parse_args( $override_settings, $default_settings );

		return $this->api->add_step( $settings );
	}

	function _add_webhook_step( $override_settings = array() ) {
		$default_settings = array(
			'step_name' => 'Webhook',
			'description' => '',
			'step_type' => 'webhook',
			'feed_condition_logic_conditional_logic' => '0',
			'feed_condition_conditional_logic_object' => array(),
			'routing' => array(),
			'destination_complete' => 'next',
			'method' => 'post',
			'authentication' => '',
			'requestHeaders' => '',
			'body' => 'select',
			'format' => 'json',
			'body_type' => 'all_fields',
			'destination_complete' => 'next',
			'destination_error-client' => 'next',
			'destination_error-server' => 'next',
			'destination_error' => 'next',
		);

		$settings = wp_parse_args( $override_settings, $default_settings );

		return $this->api->add_step( $settings );
	}

	function _create_webhook_test( $response_code, $expected_status ) {

		$settings = array();
		$settings['step_name'] = 'Webhook - ' . $response_code;
		$settings['url'] = 'http://unit-test-webhook.com/' . $response_code;
		$webhook_step_1_id = $this->_add_webhook_step( $settings );

		$settings = array();
		$settings['step_name'] = 'Processing Issue';
		$approval_step_2_id = $this->_add_approval_step( $settings );

		$webhook_step_1 = $this->api->get_step( $webhook_step_1_id );

		$approval_step_2 = $this->api->get_step( $approval_step_2_id );

		switch ( $response_code ) :
			case '200':
				$webhook_step_1->destination_complete = 'complete';
				$webhook_step_1->destination_error_client = $approval_step_2;
				$webhook_step_1->destination_error_server = $approval_step_2;
				$webhook_step_1->destination_error = $approval_step_2;
				break;
			case '400':
				$webhook_step_1->destination_complete = $approval_step_2;
				$webhook_step_1->destination_error_client = 'complete';
				$webhook_step_1->destination_error_server = $approval_step_2;
				$webhook_step_1->destination_error = $approval_step_2;
				break;
			case '500':
				$webhook_step_1->destination_complete = $approval_step_2;
				$webhook_step_1->destination_error_client = $approval_step_2;
				$webhook_step_1->destination_error_server = 'complete';
				$webhook_step_1->destination_error = $approval_step_2;
				break;
			case 'other':
				$webhook_step_1->destination_complete = $approval_step_2;
				$webhook_step_1->destination_error_client = $approval_step_2;
				$webhook_step_1->destination_error_server = $approval_step_2;
				$webhook_step_1->destination_error = 'complete';
				break;
		endswitch;

		gravity_flow()->update_feed_meta( $webhook_step_1_id, $webhook_step_1->get_feed_meta() );

		$steps = $this->api->get_steps();
		$count_steps = count( $steps );
		$this->assertEquals( 2, $count_steps );

		$this->_create_entries();
		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];

		$entry_id = $entry['id'];

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$workflow_status = $this->api->get_status( $entry );
		$this->assertEquals( $expected_status, $workflow_status );

	}

}

function webhook_response_test_results( $preempt, $request, $url ) {
	if ( strpos( $url, 'unit-test-webhook.com' ) == false ) {
		return false;
	}

	$url_pieces = explode( '/', $url );
	switch ( end( $url_pieces ) ) :
		case '200':
			$response = array(
				'headers'  => array(),
				'body'     => '{"test":"202POST"}',
				'response' => array(
					'code'    => 202,
					'message' => 'Accepted',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case '400':
			$response = array(
				'headers'  => array(),
				'body'     => '{"test":"402POST"}',
				'response' => array(
					'code'    => 402,
					'message' => 'Payment Required',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case '500':
			$response = array(
				'headers'  => array(),
				'body'     => '{"test":"502POST"}',
				'response' => array(
					'code'    => 502,
					'message' => 'Bad Gateway',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case 'other':
			$response = array(
				'headers'  => array(),
				'body'     => '{"test":"OTHERPOST"}',
				'response' => array(
					'code'    => 302,
					'message' => 'Found',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		default:
			$response = false;

	endswitch;
	return $response;
}
add_filter( 'pre_http_request', 'webhook_response_test_results', 10, 3 );
