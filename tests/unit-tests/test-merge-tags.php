<?php

/**
 * Testing the Gravity Flow merge tags.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Merge_Tags extends GF_UnitTestCase {

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
	}

	/**
	 * Tests that the assignee merge tag aborts early if the current step is not passed in the init arguments.
	 */
	public function test_assignees_no_step() {
		$merge_tag = $this->_get_merge_tag( 'assignees', array( 'entry' => $this->_create_entry() ) );

		$text_in           = '{assignees}';
		$expected_text_out = '{assignees}';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out );
	}

	/**
	 * Tests that the assignee merge tag outputs the expected content when the current step is passed in the init arguments.
	 */
	public function test_assignees() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$args  = array(
			'step'  => $this->api->get_current_step( $entry ),
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'assignees', $args );

		$display_name = version_compare( get_bloginfo( 'version' ), '4.4', '>=' ) ? 'admin' : '1';

		$text_in         = '{assignees}';
		$expected_text_out = $display_name . ', ' . WP_TESTS_EMAIL . ' (Pending)';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{assignees: status=false}';
		$expected_text_out = $display_name . ', ' . WP_TESTS_EMAIL;
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{assignees: user_email=false}';
		$expected_text_out = $display_name . ' (Pending)';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{assignees: display_name=false}';
		$expected_text_out = WP_TESTS_EMAIL . ' (Pending)';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{assignees: user_email=false status=false}';
		$expected_text_out = $display_name;
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the created_by merge tag does not return content when the entry created_by property is not set.
	 */
	public function test_created_by_null() {
		$merge_tag = $this->_get_merge_tag( 'created_by', array( 'entry' => $this->_create_entry() ) );

		$text_in         = '{created_by}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out );
	}

	/**
	 * Tests that the created_by merge tag does not return content when the entry created_by property is set to a nonexistent user.
	 */
	public function test_created_by_invalid() {
		$entry = $this->_create_entry();

		$entry['created_by'] = 9999;

		$merge_tag = $this->_get_merge_tag( 'created_by', array( 'entry' => $entry ) );

		$text_in         = '{created_by}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out );
	}

	/**
	 * Tests that the created_by merge tag returns the expected content when the entry created_by property is set to a valid user.
	 */
	public function test_created_by_admin() {
		$entry = $this->_create_entry();

		$entry['created_by'] = 1;

		$merge_tag = $this->_get_merge_tag( 'created_by', array( 'entry' => $entry ) );

		$text_in           = '{created_by}';
		$expected_text_out = '1';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{created_by:ID}';
		$expected_text_out = '1';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{created_by:user_login}';
		$expected_text_out = 'admin';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{created_by:roles}';
		$expected_text_out = 'administrator';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the current_step merge tag aborts early if the current step is not passed in the init arguments.
	 */
	public function test_current_step_no_step() {
		$merge_tag = $this->_get_merge_tag( 'current_step', array( 'entry' => $this->_create_entry() ) );

		$text_in         = '{current_step}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out );
	}

	/**
	 * Tests that the current_step merge tag outputs the expected content when the current step is passed in the init arguments.
	 */
	public function test_current_step() {
		$step_id = $this->_add_approval_step();
		$entry   = $this->_create_entry();
		$args    = array(
			'step'  => $this->api->get_current_step( $entry ),
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'current_step', $args );

		$text_in           = '{current_step}';
		$expected_text_out = 'Approval';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{current_step:ID}';
		$expected_text_out = $step_id;
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{current_step:type}';
		$expected_text_out = 'approval';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		// @todo the start, duration, expiration, and schedule modifiers.

	}

	/**
	 * Tests that the workflow_approve merge tags do not output content when the step is not passed.
	 */
	public function test_workflow_approve_no_step() {
		$merge_tag = $this->_get_merge_tag( 'workflow_approve', array( 'entry' => $this->_create_entry() ) );

		$text_in         = '{workflow_approve_url}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{workflow_approve_link}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve_url merge tags output the expected content.
	 */
	public function test_workflow_approve_url() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_approve_url}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in ) );

		$decoded_token = gravity_flow()->decode_access_token( $access_token );

		// Verify the token action is correct.
		$expected_action = 'approve';
		$token_action    = rgars( $decoded_token, 'scopes/action' );
		$this->assertEquals( $expected_action, $token_action );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( $decoded_token );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );

		// Remove the access token and verify the remaining arguments are correct.
		unset( $actual_query_args['gflow_access_token'] );
		$expected_query_args = array(
			'page' => 'gravityflow-inbox',
			'view' => 'entry',
			'id'   => $this->form_id,
			'lid'  => $entry['id'],
		);
		$this->assertEquals( $expected_query_args, $actual_query_args, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve_link merge tags output the expected content.
	 */
	public function test_workflow_approve_link() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_approve_link}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">Approve<\/a>/', $text_out, $this->_get_message( $text_in ) );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_approve_link: text=testing}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">testing<\/a>/', $text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve_token merge tag does not output content when the assignee is not passed.
	 */
	public function test_workflow_approve_token_no_assignee() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve_token', $args );

		$text_in         = '{workflow_approve_token}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve_token merge tag outputs the expected content.
	 */
	public function test_workflow_approve_token() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve_token', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_approve_token}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out );

		$decoded_token = gravity_flow()->decode_access_token( urldecode( $text_out ) );

		// Verify the token action is correct.
		$expected_action = 'approve';
		$token_action    = rgars( $decoded_token, 'scopes/action' );
		$this->assertEquals( $expected_action, $token_action );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( $decoded_token );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );
	}

	/**
	 * Tests that the workflow_cancel merge tags do not output content when the assignee is not passed.
	 */
	public function test_workflow_cancel_no_assignee() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_cancel', $args );

		$text_in         = '{workflow_cancel_url}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{workflow_cancel_link}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve_url merge tags output the expected content.
	 */
	public function test_workflow_cancel_url() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_cancel', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_cancel_url}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in ) );

		$decoded_token = gravity_flow()->decode_access_token( $access_token );

		// Verify the token action is correct.
		$expected_action = 'cancel_workflow';
		$token_action    = rgars( $decoded_token, 'scopes/action' );
		$this->assertEquals( $expected_action, $token_action );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( $decoded_token );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );

		// Remove the access token and verify the remaining arguments are correct.
		unset( $actual_query_args['gflow_access_token'] );
		$expected_query_args = array(
			'page' => 'gravityflow-inbox',
			'view' => 'entry',
			'id'   => $this->form_id,
			'lid'  => $entry['id'],
		);
		$this->assertEquals( $expected_query_args, $actual_query_args, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_cancel_link merge tags output the expected content.
	 */
	public function test_workflow_cancel_link() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_cancel', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_cancel_link}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">Cancel Workflow<\/a>/', $text_out, $this->_get_message( $text_in ) );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_cancel_link: text=testing}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">testing<\/a>/', $text_out, $this->_get_message( $text_in ) );
	}

	}

	/**
	 * Tests that the workflow_cancel merge tags do not output content when the assignee is not passed.
	 */
	public function test_workflow_reject_no_assignee() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_reject', $args );

		$text_in         = '{workflow_reject_url}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{workflow_reject_link}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve_url merge tags output the expected content.
	 */
	public function test_workflow_reject_url() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_reject', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_reject_url}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in ) );

		$decoded_token = gravity_flow()->decode_access_token( $access_token );

		// Verify the token action is correct.
		$expected_action = 'reject';
		$token_action    = rgars( $decoded_token, 'scopes/action' );
		$this->assertEquals( $expected_action, $token_action );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( $decoded_token );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );

		// Remove the access token and verify the remaining arguments are correct.
		unset( $actual_query_args['gflow_access_token'] );
		$expected_query_args = array(
			'page' => 'gravityflow-inbox',
			'view' => 'entry',
			'id'   => $this->form_id,
			'lid'  => $entry['id'],
		);
		$this->assertEquals( $expected_query_args, $actual_query_args, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_reject_link merge tags output the expected content.
	 */
	public function test_workflow_reject_link() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_reject', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_reject_link}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">Reject<\/a>/', $text_out, $this->_get_message( $text_in ) );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_reject_link: text=testing}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $text_in );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">testing<\/a>/', $text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_reject_token merge tag does not output content when the assignee is not passed.
	 */
	public function test_workflow_reject_token_no_assignee() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_reject_token', $args );

		$text_in         = '{workflow_reject_token}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_reject_token merge tag outputs the expected content.
	 */
	public function test_workflow_reject_token() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_reject_token', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_reject_token}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out );

		$decoded_token = gravity_flow()->decode_access_token( urldecode( $text_out ) );

		// Verify the token action is correct.
		$expected_action = 'reject';
		$token_action    = rgars( $decoded_token, 'scopes/action' );
		$this->assertEquals( $expected_action, $token_action );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( $decoded_token );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );
	}

	/**
	 * Tests that the workflow_entry_url merge tag outputs the expected content.
	 */
	public function test_workflow_entry_url() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = '{workflow_entry_url}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args   = $this->_parse_workflow_url( $text_out );
		$expected_query_args = array(
			'page' => 'gravityflow-inbox',
			'view' => 'entry',
			'id'   => $this->form_id,
			'lid'  => $entry['id'],
		);
		$this->assertEquals( $expected_query_args, $actual_query_args, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_entry_url merge tag outputs the expected content.
	 */
	public function test_workflow_entry_url_token() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = '{workflow_entry_url: token=true}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in ) );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( gravity_flow()->decode_access_token( $access_token ) );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );
	}

	/**
	 * Tests that the workflow_entry_link merge tag outputs the expected content.
	 */
	public function test_workflow_entry_link() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = '{workflow_entry_link}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">Entry<\/a>/', $text_out, $this->_get_message( $text_in ) );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_entry_link: text=testing}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $text_in );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">testing<\/a>/', $text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_inbox_url merge tag outputs the expected content.
	 */
	public function test_workflow_inbox_url() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = '{workflow_inbox_url}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args   = $this->_parse_workflow_url( $text_out );
		$expected_query_args = array(
			'page' => 'gravityflow-inbox',
		);
		$this->assertEquals( $expected_query_args, $actual_query_args, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_entry_url merge tag outputs the expected content.
	 */
	public function test_workflow_inbox_url_token() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'step'     => $step,
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = '{workflow_inbox_url: token=true}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in ) );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( gravity_flow()->decode_access_token( $access_token ) );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );
	}

	/**
	 * Tests that the workflow_inbox_link merge tags output the expected content.
	 */
	public function test_workflow_inbox_link() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_inbox_link}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">Inbox<\/a>/', $text_out, $this->_get_message( $text_in ) );

		// Verify the merge tag was replaced.
		$text_in  = '{workflow_inbox_link: text=testing}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $text_in );

		// Verify the link HTML matches the expected pattern.
		$this->assertRegExp( '/<a(.*)href="([^"]*)">testing<\/a>/', $text_out, $this->_get_message( $text_in ) );
	}

	/* HELPERS */

	/**
	 * Returns an array of query string arguments from the supplied URL.
	 *
	 * @param string $url The URL from the merge tag.
	 *
	 * @return array
	 */
	public function _parse_workflow_url( $url ) {
		$url_query_string = parse_url( str_replace( '&amp;', '&', $url ), PHP_URL_QUERY );
		parse_str( $url_query_string, $query_args );

		return $query_args;
	}

	/**
	 * Returns the assertion failure message.
	 *
	 * @param string $merge_tag The merge tag which was processed.
	 *
	 * @return string
	 */
	public function _get_message( $merge_tag ) {
		return 'Unexpected output for ' . $merge_tag;
	}

	/**
	 * Returns the requested merge tag object.
	 *
	 * @param string $name The merge tag name.
	 * @param array $args The merge tag init arguments.
	 *
	 * @return false|Gravity_Flow_Merge_Tag
	 */
	public function _get_merge_tag( $name, $args = array() ) {
		$args['form'] = $this->factory->form->get_form_by_id( $this->form_id );

		return Gravity_Flow_Merge_Tags::get( $name, $args );
	}

	/**
	 * Creates and returns a random entry.
	 *
	 * @return array|WP_Error
	 */
	public function _create_entry() {
		$form                           = $this->factory->form->get_form_by_id( $this->form_id );
		$random_entry_object            = $this->factory->entry->generate_random_entry_object( $form );
		$random_entry_object['form_id'] = $form['id'];
		$entry_id                       = $this->factory->entry->create( $random_entry_object );

		return $this->factory->entry->get_entry_by_id( $entry_id );
	}

	/**
	 * Creates an Approval type step.
	 *
	 * @param array $override_settings The additional step settings.
	 *
	 * @return mixed
	 */
	function _add_approval_step( $override_settings = array() ) {
		$default_settings = array(
			'step_name'                               => 'Approval',
			'description'                             => '',
			'step_type'                               => 'approval',
			'feed_condition_logic_conditional_logic'  => false,
			'feed_condition_conditional_logic_object' => array(),
			'type'                                    => 'select',
			'assignees'                               => array( 'user_id|1' ),
			'routing'                                 => array(),
			'unanimous_approval'                      => '',
			'assignee_notification_enabled'           => false,
			'assignee_notification_message'           => 'A new entry is pending your approval',
			'destination_complete'                    => 'next',
			'destination_rejected'                    => 'complete',
			'destination_approved'                    => 'next',
		);

		$settings = wp_parse_args( $override_settings, $default_settings );

		return $this->api->add_step( $settings );
	}

}
