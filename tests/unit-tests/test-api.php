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
class Tests_Gravity_Flow_API extends GF_UnitTestCase {

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
	function test_approval_process(){

		$step1_id = $this->_add_approval_step();

		$settings['step_name'] = 'Approval 2';
		$settings['destination_rejected'] = $step1_id;

		$step2_id = $this->_add_approval_step( $settings );

		$steps = $this->api->get_steps();
		$count_steps = count( $steps );
		$this->assertEquals( 3, $count_steps );

		$this->_create_entries();
		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];

		$entry_id = $entry['id'];

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Approve
		$step1 = $this->api->get_step( $step1_id, $entry );
		$step1->update_user_status( 1, 'approved' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has moved to next step
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $step2_id, $current_step->get_id() );


		// Reject step 2
		$step2 = $this->api->get_step( $step2_id, $entry );
		$step2->update_user_status( 1, 'rejected' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has moved to first step
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $step1_id, $current_step->get_id() );

		// Approve Step 1
		$step1 = $this->api->get_step( $step1_id, $entry );
		$step1->update_user_status( 1, 'approved' );
		$this->api->process_workflow( $entry_id );

		// Approve step 2
		$step2 = $this->api->get_step( $step2_id, $entry );
		$step2->update_user_status( 1, 'approved' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has finished
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( false, $current_step );

		$final_status = $this->api->get_status( $entry );

		$this->assertEquals( 'approved', $final_status );
	}

	function test_cancel_workflow() {
		$step_id = $this->_add_approval_step();

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
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		$this->api->cancel_workflow( $entry );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'cancelled', $status );

	}

	function test_restart_workflow() {
		$step_id = $this->_add_approval_step();

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
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Approve
		$step = $this->api->get_step( $step_id, $entry );
		$assignees = $step->get_assignees();
		$this->assertEquals( 1, count( $assignees ) );
		$assignees[0]->update_status( 'approved' );

		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'approved', $status );

		$this->api->restart_workflow( $entry );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

	}

	function test_restart_step() {
		$step_id = $this->_add_approval_step();
		$step2_id = $this->_add_approval_step();

		$steps = $this->api->get_steps();
		$count_steps = count( $steps );
		$this->assertEquals( 3, $count_steps );

		$this->_create_entries();
		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];

		$entry_id = $entry['id'];

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Approve
		$step = $this->api->get_step( $step_id, $entry );
		$assignees = $step->get_assignees();
		$this->assertEquals( 1, count( $assignees ) );
		$assignees[0]->update_status( 'approved' );

		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $step2_id, $current_step->get_id() );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		$this->api->restart_step( $entry );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $step2_id, $current_step->get_id() );

	}

	function test_send_to_step() {
		$step1_id = $this->_add_approval_step();
		$step2_id = $this->_add_approval_step();

		$steps = $this->api->get_steps();
		$count_steps = count( $steps );
		$this->assertEquals( 3, $count_steps );

		$this->_create_entries();
		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];

		$entry_id = $entry['id'];

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Approve
		$step = $this->api->get_step( $step1_id, $entry );
		$assignees = $step->get_assignees();
		$this->assertEquals( 1, count( $assignees ) );
		$assignees[0]->update_status( 'approved' );

		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $step2_id, $current_step->get_id() );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Send back to first step
		$this->api->send_to_step( $entry, $step1_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $step1_id, $current_step->get_id() );


		$step2 = $this->api->get_step( $step2_id, $entry );
		$this->assertEquals( 'cancelled', $step2->get_status() );

	}

	function test_user_input_process() {

		$step1_id = $this->_add_user_input_step();

		$settings['step_name'] = 'User Input 2';

		$step2_id = $this->_add_user_input_step( $settings );

		$steps = $this->api->get_steps();
		$count_steps = count( $steps );
		$this->assertEquals( 3, $count_steps );

		$this->_create_entries();
		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];

		$entry_id = $entry['id'];

		// Start workflow
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Complete
		$step1 = $this->api->get_step( $step1_id, $entry );
		$step1->update_user_status( 1, 'complete' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has moved to next step
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $step2_id, $current_step->get_id() );


		// Complete step 2
		$step2 = $this->api->get_step( $step2_id, $entry );
		$step2->update_user_status( 1, 'complete' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has finished
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( false, $current_step );

		$final_status = $this->api->get_status( $entry );

		$this->assertEquals( 'complete', $final_status );
	}

	function test_complex_process() {

		$settings = array( 'destination_rejected' => 'next' );

		$approval_step_1_id = $this->_add_approval_step( $settings );

		$settings = array( 'destination_complete' => $approval_step_1_id );

		$input_step_1_id = $this->_add_user_input_step( $settings );

		$settings = array( 'destination_rejected' => $input_step_1_id );

		$input_step_2_id = $this->_add_user_input_step( $settings );

		$approval_step_1 = $this->api->get_step( $approval_step_1_id );

		// Set destination approved to the input step 2 as we didn't have the ID before
		$approval_step_1->destination_approved = $input_step_2_id;
		gravity_flow()->update_feed_meta( $approval_step_1_id, $approval_step_1->get_feed_meta() );

		// Add final approval step
		$approval_step_2_id = $this->_add_approval_step();

		$steps = $this->api->get_steps();
		$count_steps = count( $steps );
		$this->assertEquals( 5, $count_steps );

		$this->_create_entries();
		$entries = GFAPI::get_entries( $this->form_id );

		$entry = $entries[0];

		$entry_id = $entry['id'];

		// Start workflow
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Reject entry
		$approval_step_1 = $this->api->get_step( $approval_step_1_id, $entry );
		$approval_step_1->update_user_status( 1, 'rejected' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has moved to next step (input 1)
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $input_step_1_id, $current_step->get_id() );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Complete input step 1
		$input_step_1 = $this->api->get_step( $input_step_1_id, $entry );
		$input_step_1->update_user_status( 1, 'complete' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has moved to next step (approval 1)
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $approval_step_1_id, $current_step->get_id() );

		// Reject entry again
		$approva_step_1 = $this->api->get_step( $approval_step_1_id, $entry );
		$approva_step_1->update_user_status( 1, 'rejected' );
		$this->api->process_workflow( $entry_id );


		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has moved to next step (input 1)
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $input_step_1_id, $current_step->get_id() );


		// Complete input step 1
		$input_step_1 = $this->api->get_step( $input_step_1_id, $entry );
		$input_step_1->update_user_status( 1, 'complete' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Approve entry
		$approva_step_1 = $this->api->get_step( $approval_step_1_id, $entry );
		$approva_step_1->update_user_status( 1, 'approved' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has moved to next step (input 2)
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $input_step_2_id, $current_step->get_id() );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Complete input step 2
		$input_step_2 = $this->api->get_step( $input_step_2_id, $entry );
		$input_step_2->update_user_status( 1, 'complete' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has moved to next step (approval 2)
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( $approval_step_2_id, $current_step->get_id() );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Approve entry
		$approva_step_2 = $this->api->get_step( $approval_step_2_id, $entry );
		$approva_step_2->update_user_status( 1, 'approved' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check workflow has finished
		$current_step = $this->api->get_current_step( $entry );
		$this->assertEquals( false, $current_step );

		$final_status = $this->api->get_status( $entry );

		$this->assertEquals( 'approved', $final_status );
	}


	function test_assignee_field() {
		$form = GFAPI::get_form( $this->form_id );
		$assignee_field_properties_json = '{"type":"workflow_assignee_select","id":6,"label":"Assignee","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":93,"pageNumber":1,"choices":"","conditionalLogic":"","displayOnly":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"inputMask":false,"inputMaskValue":"","allowsPrepopulate":false,"gravityflowAssigneeFieldShowUsers":true,"gravityflowAssigneeFieldShowRoles":true,"gravityflowAssigneeFieldShowFields":true,"cssClass":""}';
		$assignee_field_properties = json_decode( $assignee_field_properties_json, true );
		$assignee_field_properties['id'] = 999;
		$assignee_field = new Gravity_Flow_Field_Assignee_Select( $assignee_field_properties );
		$form['fields'][] = $assignee_field;
		GFAPI::update_form( $form );

		$step_settings = array(
			'assignees' => array( 'assignee_field|999' ),
		);
		$step1_id = $this->_add_user_input_step( $step_settings );

		$entry_id = GFAPI::add_entry( array( 'form_id' => $this->form_id, 'date_created' => '2016-06-18 11:00', '1' => 'Second Choice', '2.2' => 'Second Choice', '8' => '1', '13.6' => 'Spain', 999 => 'user_id|1' ) );

		$entry = GFAPI::get_entry( $entry_id );

		$entry_id = $entry['id'];

		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'pending', $status );

		// Complete
		$step1 = $this->api->get_step( $step1_id, $entry );
		$step1->update_user_status( 1, 'complete' );
		$this->api->process_workflow( $entry_id );

		// Refresh entry
		$entry = GFAPI::get_entry( $entry_id );

		// Check status
		$status = $this->api->get_status( $entry );
		$this->assertEquals( 'complete', $status );
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
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-28 11:00', '1' => 'Second Choice', '2.2' => 'Second Choice', '8' => '1', '13.6' => 'Spain' ) );
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-28 11:15', '1' => 'First Choice', '2.2' => 'Second Choice', '2.3' => 'Third Choice', '8' => '2', '13.6' => 'Brazil' ) );
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-29 12:00', '1' => 'Second Choice', '2.1' => 'First Choice', '8' => '3', '13.6' => 'United Kingdom' ) );
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-29 12:00', '1' => 'Second Choice', '2.1' => 'First Choice', '2.2' => 'Second Choice', '5' => 'My text', '8' => '4', '13.6' => 'United States' ) );
		$this->factory->entry->create_many( 10, array( 'form_id' => $form_id, 'date_created' => '2013-11-29 13:00', '1' => 'Second Choice', '5' => 'Different text', '8' => '5', '13.6' => 'Canada' ) );
	}

	function _add_approval_step( $override_settings = array() ){
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

	function _add_user_input_step( $override_settings = array() ){
		$default_settings = array(
			'step_name' => 'User Input',
			'description' => '',
			'step_type' => 'user_input',
			'feed_condition_logic_conditional_logic' => '0',
			'feed_condition_conditional_logic_object' => array(),
			'assignee_policy' => 'any',
			'type' => 'select',
			'assignees' => array( 'user_id|1' ),
			'routing' => array(),
			'assignee_notification_enabled' => '0',
			'assignee_notification_message' => 'A new entry is pending your input',
			'destination_complete' => 'next',
		);

		$settings = wp_parse_args( $override_settings, $default_settings );

		return $this->api->add_step( $settings );
	}

}
