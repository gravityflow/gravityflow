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


	// # ASSIGNEES ----------------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_assignees_invalid_text() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$args  = array(
			'step'  => $this->api->get_current_step( $entry ),
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'assignees', $args );

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
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


	// # CREATED BY ---------------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_created_invalid_text() {
		$merge_tag = $this->_get_merge_tag( 'created_by', array( 'entry' => $this->_create_entry() ) );

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
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

		$text_in           = '{created_by:user_pass}';
		$expected_text_out = '';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );
	}


	// # CURRENT STEP -------------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_current_step_invalid_text() {
		$this->_add_approval_step();
		$entry   = $this->_create_entry();
		$args    = array(
			'step'  => $this->api->get_current_step( $entry ),
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'current_step', $args );

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
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

		$text_in           = '{current_step:start}';
		$expected_text_out = 'October 8, 2018 at 9:30 am';
		gform_update_meta( $entry['id'], 'workflow_step_' . $step_id . '_timestamp', strtotime( '2018-10-08 09:30' ) );
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{current_step:duration}';
		$expected_text_out = method_exists( 'DateTime', 'diff' ) ? '1h' : '3600';
		gform_update_meta( $entry['id'], 'workflow_step_' . $step_id . '_timestamp', strtotime( '-1 hour' ) );
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{current_step:duration_minutes}';
		$expected_text_out = '120';
		gform_update_meta( $entry['id'], 'workflow_step_' . $step_id . '_timestamp', strtotime( '-2 hour' ) );
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{current_step:duration_seconds}';
		$expected_text_out = '10800';
		gform_update_meta( $entry['id'], 'workflow_step_' . $step_id . '_timestamp', strtotime( '-3 hour' ) );
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{current_step:expiration}';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{current_step:schedule}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the {current_step:expiration} merge tag outputs the expected content when the step expiration settings are configured.
	 */
	public function test_current_step_due_date() {
		$timestamp = time() - 10000;

		$step_id   = $this->_add_approval_step( array(
			'due_date'            => true,
			'due_date_type'       => 'date',
			'due_date_date'       => date( 'y-m-d h:m a', $timestamp ),
			'due_date_date_field' => '10',
		) );
		$entry   = $this->_create_entry();

		gform_update_meta( $entry['id'], 'workflow_step_' . $step_id . '_timestamp', $timestamp );

		$args    = array(
			'step'  => $this->api->get_current_step( $entry ),
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'current_step', $args );

		$text_in           = '{current_step:due_date}';
		$expected_text_out = date( 'F j, Y', $timestamp );
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		$text_in           = '{current_step:due_status}';
		$expected_text_out = __( 'Overdue', 'gravityflow' );;
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the {current_step:expiration} merge tag outputs the expected content when the step expiration settings are configured.
	 */
	public function test_current_step_expiration() {
		$step_id = $this->_add_approval_step( array(
			'expiration'              => true,
			'expiration_type'         => 'delay',
			'expiration_delay_offset' => '1',
			'expiration_delay_unit'   => 'weeks'
		) );
		$entry   = $this->_create_entry();
		$args    = array(
			'step'  => $this->api->get_current_step( $entry ),
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'current_step', $args );

		$timestamp            = time();
		$expiration_timestamp = strtotime( '+1 week', $timestamp );
		gform_update_meta( $entry['id'], 'workflow_step_' . $step_id . '_timestamp', $timestamp );

		$text_in           = '{current_step:expiration}';
		$expected_text_out = date( 'F j, Y \a\t g:i a', $expiration_timestamp );
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the {current_step:schedule} merge tag outputs the expected content when the step schedule settings are configured.
	 */
	public function test_current_step_schedule() {
		$step_id = $this->_add_approval_step( array(
			'scheduled'             => true,
			'schedule_type'         => 'delay',
			'schedule_delay_offset' => '1',
			'schedule_delay_unit'   => 'weeks'
		) );
		$entry   = $this->_create_entry();
		$args    = array(
			'step'  => $this->api->get_current_step( $entry ),
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'current_step', $args );

		$timestamp           = time();
		$scheduled_timestamp = strtotime( '+1 week', $timestamp );
		gform_update_meta( $entry['id'], 'workflow_step_' . $step_id . '_timestamp', $timestamp );

		$text_in           = '{current_step:schedule}';
		$expected_text_out = date( 'F j, Y \a\t g:i a', $scheduled_timestamp );
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );
	}


	// # WORKFLOW APPROVE ---------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_approve_invalid_text() {
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

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
	}

	/**
	 * Tests that the workflow_approve merge tags do not output content when the step and assignee are not passed.
	 */
	public function test_workflow_approve_no_step_no_assignee() {
		$merge_tag = $this->_get_merge_tag( 'workflow_approve', array( 'entry' => $this->_create_entry() ) );

		$text_in         = '{workflow_approve_url}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{workflow_approve_link}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve merge tags do not output content when the step is not passed.
	 */
	public function test_workflow_approve_no_step() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry'    => $entry,
			'assignee' => $assignee
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve', $args );

		$text_in         = '{workflow_approve_url}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{workflow_approve_link}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve merge tags do not output content when the assignee is not passed.
	 */
	public function test_workflow_approve_no_assignee() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve', $args );

		$text_in         = '{workflow_approve_url}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{workflow_approve_link}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve_url merge tags output the expected base URL.
	 */
	public function test_workflow_approve_url_base() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry'    => $entry,
			'step'     => $step,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve', $args );

		// Verify the merge tag is replaced with the admin URL.
		$text_in  = "{workflow_approve_url}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( admin_url( 'admin.php' ), $text_out, $this->_get_message( $text_in ) );

		$post_id = $this->_create_post();

		// Verify the merge tag is replaced with the URL for the specified front-end page.
		$text_in  = "{workflow_approve_url: page_id='{$post_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( get_permalink( $post_id ), $text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_approve_url merge tags output the expected content when using the step attribute.
	 */
	public function test_workflow_approve_url_step_attr() {
		$step_id  = $this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve', $args );

		// Verify the merge tag is replaced by an empty string when an invalid step is specified.
		$text_in  = "{workflow_approve_url: step=wrong}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $text_out, $this->_get_message( $text_in ) );

		// Verify the merge tag is replaced with a URL when a valid step is specified.
		$text_in  = "{workflow_approve_url: step='{$step_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );

		$decoded_token = gravity_flow()->decode_access_token( $access_token );

		// Verify the token action is correct.
		$token_step_id = rgars( $decoded_token, 'scopes/step_id' );
		$this->assertEquals( $step_id, $token_step_id, $this->_get_message( $text_in . ' - scopes step id' ) );
	}

	/**
	 * Tests that the workflow_approve_url merge tags output the expected content when using the assignee attribute.
	 */
	public function test_workflow_approve_url_assignee_attr() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry' => $entry,
			'step'  => $step,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_approve', $args );

		// Verify the merge tag is replaced with a URL when a valid assignee is specified.
		$text_in  = "{workflow_approve_url: assignee='user_id|1'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );

		$decoded_token = gravity_flow()->decode_access_token( $access_token );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( $decoded_token );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );
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


	// # WORKFLOW APPROVE TOKEN ---------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_approve_token_invalid_text() {
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

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
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


	// # WORKFLOW CANCEL ----------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_cancel_invalid_text() {
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

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
	}

	/**
	 * Tests that the workflow_cancel merge tags do not output content when the step and assignee are not passed.
	 */
	public function test_workflow_cancel_no_step_no_assignee() {
		$merge_tag = $this->_get_merge_tag( 'workflow_cancel', array( 'entry' => $this->_create_entry() ) );

		$text_in         = '{workflow_cancel_url}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{workflow_cancel_link}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_cancel merge tags do not output content when the step is not passed.
	 */
	public function test_workflow_cancel_no_step() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry'    => $entry,
			'assignee' => $assignee
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
	 * Tests that the workflow_cancel_url merge tags output the expected base URL.
	 */
	public function test_workflow_cancel_url_base() {
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

		// Verify the merge tag is replaced with the admin URL.
		$text_in  = "{workflow_cancel_url}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( admin_url( 'admin.php' ), $text_out, $this->_get_message( $text_in ) );

		$post_id = $this->_create_post();

		// Verify the merge tag is replaced with the URL for the specified front-end page.
		$text_in  = "{workflow_cancel_url: page_id='{$post_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( get_permalink( $post_id ), $text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_cancel_url merge tags output the expected content when using the step attribute.
	 */
	public function test_workflow_cancel_url_step_attr() {
		$step_id  = $this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_cancel', $args );

		// Verify the merge tag is replaced by an empty string when an invalid step is specified.
		$text_in  = "{workflow_cancel_url: step=wrong}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $text_out, $this->_get_message( $text_in ) );

		// Verify the merge tag is replaced with a URL when a valid step is specified.
		$text_in  = "{workflow_cancel_url: step='{$step_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );
	}

	/**
	 * Tests that the workflow_cancel_url merge tags output the expected content when using the assignee attribute.
	 */
	public function test_workflow_cancel_url_assignee_attr() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry' => $entry,
			'step'  => $step,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_cancel', $args );

		// Verify the merge tag is replaced with a URL when a valid assignee is specified.
		$text_in  = "{workflow_cancel_url: assignee='user_id|1'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );

		$decoded_token = gravity_flow()->decode_access_token( $access_token );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( $decoded_token );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );
	}

	/**
	 * Tests that the workflow_cancel_url merge tags output the expected content.
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


	// # WORKFLOW FIELDS ----------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_fields_invalid_text() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_fields', $args );

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
	}

	/**
	 * Tests that the workflow_fields merge tag aborts early if the current step is not passed in the init arguments.
	 */
	public function test_workflow_fields_no_step() {
		$merge_tag = $this->_get_merge_tag( 'workflow_fields', array( 'entry' => $this->_create_entry() ) );

		$text_in           = '{workflow_fields}';
		$expected_text_out = '{workflow_fields}';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out );
	}

	/**
	 * Tests that the workflow_fields merge tag outputs a table.
	 */
	public function test_workflow_fields() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_fields', $args );

		$text_in           = '{workflow_fields}';
		$expected_text_out = '<table width="99%"';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( $expected_text_out, $actual_text_out );
	}


	// # WORKFLOW NOTE ------------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_note_invalid_text() {
		$entry = $this->_create_entry();
		$args  = array(
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_note', $args );

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
	}

	/**
	 * Tests that the workflow_note merge tag aborts early if the entry is not passed in the init arguments.
	 */
	public function test_workflow_note_no_entry() {
		$merge_tag = $this->_get_merge_tag( 'workflow_note' );

		$text_in           = '{workflow_note}';
		$expected_text_out = '{workflow_note}';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out );
	}

	/**
	 * Tests that the workflow_note merge tags output the expected content.
	 */
	public function test_workflow_note() {

		// Create the steps and entry.
		$step_1_id = $this->_add_approval_step( array( 'step_name' => 'first' ) );
		$step_2_id = $this->_add_approval_step();
		$entry     = $this->_create_entry();

		$time = time();
		$date = date( 'F j, Y \a\t g:i a', $time );

		// Add the notes.
		$notes = array(
			array(
				'id'           => 1,
				'step_id'      => $step_1_id,
				'assignee_key' => 'user_id|1',
				'timestamp'    => strtotime( 'yesterday' ),
				'value'        => 'step 1 test note 1',
			),
			array(
				'id'           => 2,
				'step_id'      => $step_1_id,
				'assignee_key' => 'user_id|1',
				'timestamp'    => $time,
				'value'        => 'step 1 test note 2',
			),
			array(
				'id'           => 3,
				'step_id'      => $step_2_id,
				'assignee_key' => 'user_id|1',
				'timestamp'    => $time,
				'value'        => 'step 2 test note',
			)
		);
		gform_update_meta( $entry['id'], 'workflow_notes', json_encode( $notes ) );

		$merge_tag = $this->_get_merge_tag( 'workflow_note', array( 'entry' => $entry ) );

		$display_name = version_compare( get_bloginfo( 'version' ), '4.4', '>=' ) ? 'admin' : '1';

		// Test that the basic merge tag returns the latest note.
		$text_in           = '{workflow_note}';
		$expected_text_out = '<br />
step 2 test note';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		// Test that the display_name modifier works.
		$text_in           = '{workflow_note: display_name=true}';
		$expected_text_out = $display_name . '<br />
step 2 test note';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		// Test that the display_date modifier works.
		$text_in           = '{workflow_note: display_date=true}';
		$expected_text_out = $date . '<br />
step 2 test note';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		// Test that multiple modifiers work.
		$text_in           = '{workflow_note: display_name=true display_date=true}';
		$expected_text_out = $display_name . ': ' . $date . '<br />
step 2 test note';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		// Test that the step_id modifier supports numeric ids.
		$text_in           = "{workflow_note: step_id='{$step_1_id}'}";
		$expected_text_out = '<br />
step 1 test note 2';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		// Test that the step_id modifier supports step names.
		$text_in           = "{workflow_note: step_id=first}";
		$expected_text_out = '<br />
step 1 test note 2';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );

		// Test that the history modifier works.
		$text_in           = "{workflow_note: step_id='{$step_1_id}' history=true}";
		$expected_text_out = '<br />
step 1 test note 2<br />
<br />
<br />
step 1 test note 1';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( $text_in ) );
	}


	// # WORKFLOW REJECT ----------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_reject_invalid_text() {
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

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
	}

	/**
	 * Tests that the workflow_reject merge tags do not output content when the step and assignee are not passed.
	 */
	public function test_workflow_reject_no_step_no_assignee() {
		$merge_tag = $this->_get_merge_tag( 'workflow_reject', array( 'entry' => $this->_create_entry() ) );

		$text_in         = '{workflow_reject_url}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );

		$text_in         = '{workflow_reject_link}';
		$actual_text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $actual_text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_reject merge tags do not output content when the step is not passed.
	 */
	public function test_workflow_reject_no_step() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry'    => $entry,
			'assignee' => $assignee
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
	 * Tests that the workflow_reject merge tags do not output content when the assignee is not passed.
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
	 * Tests that the workflow_reject_url merge tags output the expected base URL.
	 */
	public function test_workflow_reject_url_base() {
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

		// Verify the merge tag is replaced with the admin URL.
		$text_in  = "{workflow_reject_url}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( admin_url( 'admin.php' ), $text_out, $this->_get_message( $text_in ) );

		$post_id = $this->_create_post();

		// Verify the merge tag is replaced with the URL for the specified front-end page.
		$text_in  = "{workflow_reject_url: page_id='{$post_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( get_permalink( $post_id ), $text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_reject_url merge tags output the expected content when using the step attribute.
	 */
	public function test_workflow_reject_url_step_attr() {
		$step_id  = $this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry'    => $entry,
			'assignee' => $assignee,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_reject', $args );

		// Verify the merge tag is replaced by an empty string when an invalid step is specified.
		$text_in  = "{workflow_reject_url: step=wrong}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEmpty( $text_out, $this->_get_message( $text_in ) );

		// Verify the merge tag is replaced with a URL when a valid step is specified.
		$text_in  = "{workflow_reject_url: step='{$step_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );

		$decoded_token = gravity_flow()->decode_access_token( $access_token );

		// Verify the token action is correct.
		$token_step_id = rgars( $decoded_token, 'scopes/step_id' );
		$this->assertEquals( $step_id, $token_step_id, $this->_get_message( $text_in . ' - scopes step id' ) );
	}

	/**
	 * Tests that the workflow_reject_url merge tags output the expected content when using the assignee attribute.
	 */
	public function test_workflow_reject_url_assignee_attr() {
		$this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry' => $entry,
			'step'  => $step,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_reject', $args );

		// Verify the merge tag is replaced with a URL when a valid assignee is specified.
		$text_in  = "{workflow_reject_url: assignee='user_id|1'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );

		$decoded_token = gravity_flow()->decode_access_token( $access_token );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( $decoded_token );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );
	}

	/**
	 * Tests that the workflow_reject_url merge tags output the expected content.
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


	// # WORKFLOW REJECT TOKEN ----------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_reject_token_invalid_text() {
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

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
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


	// # WORKFLOW TIMELINE --------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_timeline_invalid_text() {
		$merge_tag = $this->_get_merge_tag( 'workflow_timeline', array( 'entry' => $this->_create_entry() ) );

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
	}

	/**
	 * Tests that the workflow_timeline merge tag aborts early when the entry is not passed.
	 */
	public function test_workflow_timeline_no_entry() {
		$merge_tag = $this->_get_merge_tag( 'workflow_timeline' );

		$text_in           = '{workflow_timeline}';
		$expected_text_out = '{workflow_timeline}';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out );
	}

	/**
	 * Tests that the workflow_timeline merge tag outputs the expected content.
	 */
	public function test_workflow_timeline() {
		$this->_add_approval_step();
		$entry          = $this->_create_entry();
		$submitted_date = date( 'F j, Y \a\t g:i a', strtotime( $entry['date_created'] ) );

		$merge_tag = $this->_get_merge_tag( 'workflow_timeline', array( 'entry' => $entry ) );

		$text_in           = '{workflow_timeline}';
		$expected_text_out = 'Workflow: ' . $submitted_date . '<br />
Workflow Submitted';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out );

		$this->api->restart_step( $entry );
		$restart_date = date( 'F j, Y \a\t g:i a' );

		$expected_text_out = 'Workflow: ' . $restart_date . '<br />
Workflow Step restarted.<br />
<br />
Workflow: ' . $submitted_date . '<br />
Workflow Submitted';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out );
	}


	// # WORKFLOW URL -------------------------------------------------------------------------------------------------


	/**
	 * Tests the text is not replaced when the merge tag is not found.
	 */
	public function test_workflow_url_invalid_text() {
		$this->_add_approval_step();
		$entry = $this->_create_entry();
		$step  = $this->api->get_current_step( $entry );
		$args  = array(
			'step'  => $step,
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = 'no matching {merge_tag} here';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertEquals( $text_in, $text_out );
	}

	/**
	 * Tests that the workflow_entry_url merge tags output the expected base URL.
	 */
	public function test_workflow_entry_url_base() {
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

		// Verify the merge tag is replaced with the admin URL.
		$text_in  = "{workflow_entry_url}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( admin_url( 'admin.php' ), $text_out, $this->_get_message( $text_in ) );

		$post_id = $this->_create_post();

		// Verify the merge tag is replaced with the URL for the specified front-end page.
		$text_in  = "{workflow_entry_url: page_id='{$post_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( get_permalink( $post_id ), $text_out, $this->_get_message( $text_in ) );
	}

	/**
	 * Tests that the workflow_entry_url merge tag outputs the expected content.
	 */
	public function test_workflow_entry_url() {
		$entry = $this->_create_entry();
		$args  = array(
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
	 * Tests that the workflow_entry_url token merge tag does not include the token in the returned URL when the step and assignee are not passed.
	 */
	public function test_workflow_entry_url_token_attr_no_step_no_assignee() {
		$entry = $this->_create_entry();
		$args  = array(
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = '{workflow_entry_url: token=true}';
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args  = $this->_parse_workflow_url( $text_out );
		$this->assertNotEmpty( $actual_query_args, $this->_get_message( $text_in . ' - query args' ) );

		// Verify the access token is not present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );
	}

	/**
	 * Tests that the workflow_entry_url token merge tag does not include the token in the returned URL when the assignee is not passed.
	 */
	public function test_workflow_entry_url_token_attr_step_attr_no_assignee() {
		$step_id = $this->_add_approval_step();
		$entry   = $this->_create_entry();
		$args    = array(
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = "{workflow_entry_url: token=true step='{$step_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );
		$this->assertNotEmpty( $actual_query_args, $this->_get_message( $text_in . ' - query args' ) );

		// Verify the access token is not present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );
	}

	/**
	 * Tests that the workflow_entry_url merge tag outputs the expected content.
	 */
	public function test_workflow_entry_url_token_attr_assignee_attr_no_step() {
		$entry = $this->_create_entry();
		$args  = array(
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = "{workflow_entry_url: token=true assignee='user_id|1'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );

		// Verify the access token is not present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertEmpty( $access_token, $this->_get_message( $text_in . ' - token' ) );
	}

	/**
	 * Tests that the workflow_entry_url merge tag outputs the expected content.
	 */
	public function test_workflow_entry_url_token_attr() {
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
	 * Tests that the workflow_inbox_url merge tags output the expected base URL.
	 */
	public function test_workflow_inbox_url_base() {
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

		// Verify the merge tag is replaced with the admin URL.
		$text_in  = "{workflow_inbox_url}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( admin_url( 'admin.php' ), $text_out, $this->_get_message( $text_in ) );

		$post_id = $this->_create_post();

		// Verify the merge tag is replaced with the URL for the specified front-end page.
		$text_in  = "{workflow_inbox_url: page_id='{$post_id}'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertStringStartsWith( get_permalink( $post_id ), $text_out, $this->_get_message( $text_in ) );
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
	 * Tests that the workflow_inbox_url merge tag outputs the expected content when using the token, step, and assignee attributes.
	 */
	public function test_workflow_inbox_url_token_attr_step_attr_assignee_attr() {
		$step_id  = $this->_add_approval_step();
		$entry    = $this->_create_entry();
		$step     = $this->api->get_current_step( $entry );
		$assignee = $step->get_assignee( 'user_id|1' );
		$args     = array(
			'entry' => $entry,
		);

		$merge_tag = $this->_get_merge_tag( 'workflow_url', $args );

		$text_in  = "{workflow_entry_url: token=true step='{$step_id}' assignee='user_id|1'}";
		$text_out = $merge_tag->replace( $text_in );
		$this->assertNotEmpty( $text_out, $this->_get_message( $text_in ) );

		// Get the query string arguments.
		$actual_query_args = $this->_parse_workflow_url( $text_out );
		$this->assertNotEmpty( $actual_query_args, $this->_get_message( $text_in . ' - query args' ) );

		// Verify the access token is present.
		$access_token = rgar( $actual_query_args, 'gflow_access_token' );
		$this->assertNotEmpty( $access_token, $this->_get_message( $text_in ) );

		// Verify the access token belongs to the correct assignee.
		$actual_assignee = gravity_flow()->parse_token_assignee( gravity_flow()->decode_access_token( $access_token ) );
		$this->assertEquals( $assignee->get_key(), $actual_assignee->get_key() );
	}

	/**
	 * Tests that the workflow_inbox_url merge tag outputs the expected content when using the token attribute.
	 */
	public function test_workflow_inbox_url_token_attr() {
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


	// # FORMATTING TEST ----------------------------------------------------------------------------------------------


	/**
	 * Tests that the url_encode init argument and value formatting are working.
	 */
	public function test_formatting_url_encode() {
		$args = array(
			'url_encode' => true,
			'esc_html'   => false,
			'nl2br'      => false
		);

		$merge_tag = $this->_get_merge_tag( 'formatting_test', $args );
		$text_in   = '{formatting_test}';

		$expected_text_out = '%3Ethis+is+url+encoded%21';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( 'url_encode' ) );
	}

	/**
	 * Tests that the esc_html init argument and value formatting are working.
	 */
	public function test_formatting_esc_html() {
		$args = array(
			'url_encode' => false,
			'esc_html'   => true,
			'nl2br'      => false
		);

		$merge_tag = $this->_get_merge_tag( 'formatting_test', $args );
		$text_in   = '{formatting_test}';

		$expected_text_out = '&lt;this&gt;is escaped&lt;/this&gt;';
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( 'esc_html' ) );
	}

	/**
	 * Tests that the nl2br and format init arguments and value formatting are working.
	 */
	public function test_formatting_nl2br() {
		$args = array(
			'url_encode' => false,
			'esc_html'   => false,
			'nl2br'      => true,
			'format'     => 'text'
		);

		$merge_tag = $this->_get_merge_tag( 'formatting_test', $args );
		$text_in   = '{formatting_test}';

		$expected_text_out = "line one\nline two\nline three";
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( 'nl2br-text' ) );

		$args = array(
			'url_encode' => false,
			'esc_html'   => false,
			'nl2br'      => true,
			'format'     => 'html'
		);

		$merge_tag = $this->_get_merge_tag( 'formatting_test', $args );

		$expected_text_out = "line one<br />
line two<br />
line three";
		$actual_text_out   = $merge_tag->replace( $text_in );
		$this->assertEquals( $expected_text_out, $actual_text_out, $this->_get_message( 'nl2br-html' ) );
	}


	// # HELPERS ------------------------------------------------------------------------------------------------------


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

	/**
	 * Creates an empty post and returns the ID.
	 *
	 * @return int|WP_Error
	 */
	public function _create_post() {
		return wp_insert_post( array( 'post_title' => 'test' ) );
	}

}

/**
 * Class Gravity_Flow_Merge_Tag_Formatting_Test
 *
 * A custom merge tag for testing the value formatting method.
 */
class Gravity_Flow_Merge_Tag_Formatting_Test extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @var string
	 */
	public $name = 'formatting_test';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @var string
	 */
	protected $regex = '/{formatting_test}/';

	/**
	 * Replaces the merge tags.
	 *
	 * @param string $text The text being processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {
		$matches = $this->get_matches( $text );

		if ( ! empty( $matches ) ) {
			if ( $this->url_encode ) {
				$value = '>this is url encoded!';
			} elseif ( $this->esc_html ) {
				$value = '<this>is escaped</this>';
			} elseif ( $this->nl2br ) {
				$value = "line one\nline two\nline three";
			}

			$text = str_replace( '{formatting_test}', $this->format_value( $value ), $text );
		}

		return $text;
	}

}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Formatting_Test );
