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

	function test_webhook_complete_process() {

		$settings = array();
		$settings['step_name'] = 'Webhook - 200';
		$settings['url'] = 'http://unit-test-webhook.com/200';
		$webhook_step_1_id = $this->_add_webhook_step( $settings );

		$settings['step_name'] = 'Webhook - 400';
		$settings['url'] = 'http://unit-test-webhook.com/400';
		$webhook_step_2_id = $this->_add_webhook_step( $settings );

		$settings['step_name'] = 'Webhook - 500';
		$settings['url'] = 'http://unit-test-webhook.com/500';
		$webhook_step_3_id = $this->_add_webhook_step( $settings );

		$settings['step_name'] = 'Webhook - Other';
		$settings['url'] = 'http://unit-test-webhook.com/other';
		$webhook_step_4_id = $this->_add_webhook_step( $settings );

		$settings = array();
		$settings['step_name'] = 'Catch - Should Not Arrive';
		$approval_step_5_id = $this->_add_approval_step( $settings );

		$settings['step_name'] = 'Catch - 200';
		$approval_step_6_id = $this->_add_approval_step( $settings );

		$settings['step_name'] = 'Catch - 400';
		$approval_step_7_id = $this->_add_approval_step( $settings );

		$settings['step_name'] = 'Catch - 500';
		$approval_step_8_id = $this->_add_approval_step( $settings );

		$settings['step_name'] = 'Catch - Other';
		$approval_step_9_id = $this->_add_approval_step( $settings );

		$webhook_step_1 = $this->api->get_step( $webhook_step_1_id );
		$webhook_step_2 = $this->api->get_step( $webhook_step_2_id );
		$webhook_step_3 = $this->api->get_step( $webhook_step_3_id );
		$webhook_step_4 = $this->api->get_step( $webhook_step_4_id );

		echo "\n";
		echo "\n200 Step = " . $webhook_step_1->get_id();
		echo "\n400 Step = " . $webhook_step_2->get_id();
		echo "\n500 Step = " . $webhook_step_3->get_id();
		echo "\nOther Step = " . $webhook_step_4->get_id();

		$approval_step_5 = $this->api->get_step( $approval_step_5_id );
		$approval_step_6 = $this->api->get_step( $approval_step_6_id );
		$approval_step_7 = $this->api->get_step( $approval_step_7_id );
		$approval_step_8 = $this->api->get_step( $approval_step_8_id );
		$approval_step_9 = $this->api->get_step( $approval_step_9_id );

		echo "\nCatch - N/A = " . $approval_step_5->get_id();
		echo "\nCatch - 200 = " . $approval_step_6->get_id();
		echo "\nCatch - 400 = " . $approval_step_7->get_id();
		echo "\nCatch - 500 = " . $approval_step_8->get_id();
		echo "\nCatch - Other = " . $approval_step_9->get_id();

		$webhook_step_1->destination_complete = $approval_step_6_id;
		$webhook_step_2->destination_complete = $approval_step_6_id;
		$webhook_step_3->destination_complete = $approval_step_6_id;
		$webhook_step_4->destination_complete = $approval_step_6_id;

		/* //Parse error on error-client
		$webhook_step_1->destination_error-client = $approval_step_9_id;
		$webhook_step_2->destination_error-client = $approval_step_9_id;
		$webhook_step_3->destination_error-client = $approval_step_9_id;
		$webhook_step_4->destination_error-client = $approval_step_9_id;
		*/

		/* //Parse error on error-server
		$webhook_step_1->destination_error-server = $approval_step_9_id;
		$webhook_step_2->destination_error-server = $approval_step_9_id;
		$webhook_step_3->destination_error-server = $approval_step_9_id;
		$webhook_step_4->destination_error-server = $approval_step_9_id;
		*/

		$webhook_step_1->destination_error = $approval_step_9_id;
		$webhook_step_2->destination_error = $approval_step_9_id;
		$webhook_step_3->destination_error = $approval_step_9_id;
		$webhook_step_4->destination_error = $approval_step_9_id;

		gravity_flow()->update_feed_meta( $webhook_step_1_id, $webhook_step_1->get_feed_meta() );
		gravity_flow()->update_feed_meta( $webhook_step_2_id, $webhook_step_2->get_feed_meta() );
		gravity_flow()->update_feed_meta( $webhook_step_3_id, $webhook_step_3->get_feed_meta() );
		gravity_flow()->update_feed_meta( $webhook_step_4_id, $webhook_step_4->get_feed_meta() );

		$steps = $this->api->get_steps();
		$count_steps = count( $steps );
		$this->assertEquals( 9, $count_steps );

		$this->_create_entries();
		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];

		$entry_id = $entry['id'];

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Check workflow has moved to Catch - 200
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $approval_step_6_id, $current_step->get_id() );

		// Send for webhook 400
		$this->api->send_to_step( $entry, $webhook_step_2_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Check workflow has moved to Catch - 400
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $approval_step_7_id, $current_step->get_id() );

		// Send for webhook 500
		$this->api->send_to_step( $entry, $webhook_step_3_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Check workflow has moved to Catch - 500
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $approval_step_8_id, $current_step->get_id() );

		// Send for webhook Other
		$this->api->send_to_step( $entry, $webhook_step_4_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Check workflow has moved to Catch - Other
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $approval_step_9_id, $current_step->get_id() );

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
		$this->factory->entry->create_many( 1, array( 'form_id' => $form_id, 'date_created' => '2013-11-28 11:00', '1' => 'Second Choice', '2.2' => 'Second Choice', '8' => '1', '13.6' => 'Spain' ) );
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

}

function webhook_response_test_results( $preempt, $request, $url ) {
	echo "\nhttp_request: " . $url;
	if ( strpos( $url, 'unit-test-webhook.com' ) == false ) {
		echo "\nNo further processing";
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
				'body'     => '{"test":"302POST"}',
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
