<?php

/**
 * Testing the methods used when getting the inbox entries.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_API_Inbox extends GF_UnitTestCase {

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
	 * Cleanup before testing.
	 */
	function clean_up_global_scope() {
		// Clears the access token.
		parent::clean_up_global_scope();

		// Clear the current user.
		wp_set_current_user( 0 );

		// Clear the cached workflow form IDs.
		gravity_flow()->form_ids = null;
	}

	/**
	 * Tests that the filter key is empty when a user is not logged in and an access token is not found.
	 */
	function test_get_filter_key_anonymous() {
		$key = Gravity_Flow_API::get_inbox_filter_key();
		$this->assertEmpty( $key );
	}

	/**
	 * Tests that the filter key is generated for the logged in user.
	 */
	function test_get_filter_key_current_user() {
		$this->_set_user();
		$key = Gravity_Flow_API::get_inbox_filter_key();
		$this->assertEquals( 'workflow_user_id_1', $key );
	}

	/**
	 * Tests that the filter key is generated for the access token user.
	 */
	function test_get_filter_key_access_token() {
		$this->_set_access_token();
		$key = Gravity_Flow_API::get_inbox_filter_key();
		$this->assertEquals( 'workflow_email_test@test.test', $key );
	}

	/**
	 * Tests that the filter key can be retrieved from the inbox page arguments.
	 */
	function test_get_filter_key_from_args() {
		$args = array( 'filter_key' => 'my_custom_key' );
		$key  = Gravity_Flow_API::get_inbox_filter_key( $args );
		$this->assertEquals( 'my_custom_key', $key );
	}

	/**
	 * Tests that the search criteria is empty when a user is not logged in and an access token is not found.
	 */
	function test_get_search_criteria_anonymous() {
		$search_criteria = Gravity_Flow_API::get_inbox_search_criteria();
		$this->assertEmpty( $search_criteria );
	}

	/**
	 * Tests that the search criteria is generated for the logged in user.
	 */
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
		$search_criteria = Gravity_Flow_API::get_inbox_search_criteria();
		$this->assertEquals( $expected, $search_criteria );
	}

	/**
	 * Tests that the search criteria is generated for the access token user.
	 */
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
		$search_criteria = Gravity_Flow_API::get_inbox_search_criteria();
		$this->assertEquals( $expected, $search_criteria );
	}

	/**
	 * Tests that the gravityflow_inbox_search_criteria can override the search criteria.
	 */
	function test_get_search_criteria_filter() {
		$this->_set_user();
		add_filter( 'gravityflow_inbox_search_criteria', array( $this, '_get_bingo_array' ) );
		$search_criteria = Gravity_Flow_API::get_inbox_search_criteria();
		remove_filter( 'gravityflow_inbox_search_criteria', array( $this, '_get_bingo_array' ) );
		$this->assertEquals( $this->_get_bingo_array(), $search_criteria );
	}

	/**
	 * Tests that the form IDs can be retrieved from the inbox page arguments.
	 */
	function test_get_form_ids_from_args() {
		$args            = $this->_get_form_id_args();
		$search_criteria = array();
		$form_ids        = Gravity_Flow_API::get_inbox_form_ids( $args, $search_criteria );
		$this->assertEquals( $this->form_id, $form_ids );
	}

	/**
	 * Tests that no form IDs are returned when a feed does not exist.
	 */
	function test_get_form_ids_no_feeds() {
		$args            = array();
		$search_criteria = array();
		$form_ids        = Gravity_Flow_API::get_inbox_form_ids( $args, $search_criteria );
		$this->assertEmpty( $form_ids );
	}

	/**
	 * Tests that the form IDs are returned when a feed does exist.
	 */
	function test_get_form_ids_has_feed() {
		$expected = array(
			$this->form_id
		);

		// Adding a feed so Gravity_Flow::get_workflow_form_ids() can find the form.
		$this->_add_approval_step();

		$args            = array();
		$search_criteria = array();
		$form_ids        = Gravity_Flow_API::get_inbox_form_ids( $args, $search_criteria );
		$this->assertEquals( $expected, $form_ids );
	}

	/**
	 * Tests that the gravityflow_form_ids_inbox filter can override the form IDs.
	 */
	function test_get_form_ids_filter() {
		$args            = array();
		$search_criteria = array();
		add_filter( 'gravityflow_form_ids_inbox', array( $this, '_get_bingo_array' ) );
		$form_ids = Gravity_Flow_API::get_inbox_form_ids( $args, $search_criteria );
		remove_filter( 'gravityflow_form_ids_inbox', array( $this, '_get_bingo_array' ) );
		$this->assertEquals( $this->_get_bingo_array(), $form_ids );
	}

	/**
	 * Tests that the default paging arguments are returned.
	 */
	function test_get_paging() {
		$expected = array(
			'page_size' => 150,
		);
		$paging   = Gravity_Flow_API::get_inbox_paging();
		$this->assertEquals( $expected, $paging );
	}

	/**
	 * Tests that the gravityflow_inbox_paging filter can override the paging arguments.
	 */
	function test_get_paging_filter() {
		add_filter( 'gravityflow_inbox_paging', array( $this, '_get_bingo_array' ) );
		$paging = Gravity_Flow_API::get_inbox_paging();
		remove_filter( 'gravityflow_inbox_paging', array( $this, '_get_bingo_array' ) );
		$this->assertEquals( $this->_get_bingo_array(), $paging );
	}

	/**
	 * Tests that the paging arguments can be defined by the inbox configuration arguments.
	 */
	function test_get_paging_args() {
		$args = array(
			'paging' => array(
				'page_size' => 50,
			),
		);

		// Test that the page_size property was overridden by the args.
		$paging = Gravity_Flow_API::get_inbox_paging( $args );
		$this->assertEquals( $args['paging'], $paging );

		// Define the paging offset.
		$args['paging']['offset'] = 50;

		// Test that the offset property was added.
		$paging = Gravity_Flow_API::get_inbox_paging( $args );
		$this->assertEquals( $args['paging'], $paging );
	}

	/**
	 * Tests that the default sorting arguments are returned.
	 */
	function test_get_sorting() {
		$sorting = Gravity_Flow_API::get_inbox_sorting();
		$this->assertEmpty( $sorting );
	}

	/**
	 * Tests that the gravityflow_inbox_sorting filter can override the sorting arguments.
	 */
	function test_get_sorting_filter() {
		add_filter( 'gravityflow_inbox_sorting', array( $this, '_get_bingo_array' ) );
		$sorting = Gravity_Flow_API::get_inbox_sorting();
		remove_filter( 'gravityflow_inbox_sorting', array( $this, '_get_bingo_array' ) );
		$this->assertEquals( $this->_get_bingo_array(), $sorting );
	}

	/**
	 * Tests that the sorting arguments can be defined by the inbox configuration arguments.
	 */
	function test_get_sorting_args() {
		$args = array(
			'sorting' => array(
				'key' => 'id',
			),
		);

		// Test that the key property was added.
		$sorting = Gravity_Flow_API::get_inbox_sorting( $args );
		$this->assertEquals( $args['sorting'], $sorting );

		// Define the sorting direction.
		$args['sorting']['direction'] = 'ASC';

		// Test that the direction property was added.
		$sorting = Gravity_Flow_API::get_inbox_sorting( $args );
		$this->assertEquals( $args['sorting'], $sorting );

		// Define if sorting numerically.
		$args['sorting']['is_numeric'] = false;

		// Test that the is_numeric property was added.
		$sorting = Gravity_Flow_API::get_inbox_sorting( $args );
		$this->assertEquals( $args['sorting'], $sorting );
	}

	/**
	 * Tests that entries are not returned when a user is not logged in and an access token is not found.
	 */
	function test_get_inbox_entries_anonymous() {
		// Create 30 entries with half assigned to a user.
		$this->_create_entries( 'workflow_user_id_1' );

		// Confirm that no entries are returned for the anonymous user.
		$entries = Gravity_Flow_API::get_inbox_entries( $this->_get_form_id_args() );
		$this->assertEmpty( $entries );
	}

	/**
	 * Tests that the expected entries are returned when a user is logged in.
	 */
	function test_get_inbox_entries_current_user() {
		// Create 30 entries with half assigned to the current user.
		$expected_ids = $this->_create_entries( 'workflow_user_id_1' );

		// Get the entries for the current user.
		$this->_set_user();
		$total   = 0;
		$entries = Gravity_Flow_API::get_inbox_entries( $this->_get_form_id_args(), $total );

		// Confirm fifteen entries were found.
		$expected_count = 15;
		$this->assertEquals( $expected_count, $total );

		// Confirm the found entry IDs match the created IDs.
		$actual_ids = wp_list_pluck( $entries, 'id' );
		$this->assertEquals( $expected_ids, $actual_ids );
	}

	/**
	 * Tests that the expected entries are returned when a user is logged in and the paging arguments are overridden.
	 */
	function test_get_inbox_entries_current_user_paging() {
		// Gets the IDs of 5 entries assigned to the current user.
		$expected_ids = array_slice( $this->_create_entries( 'workflow_user_id_1' ), 0, 5 );

		$args = $this->_get_form_id_args();

		$args['paging']['page_size'] = 5;

		// Get the entries for the current user.
		$this->_set_user();
		$entries = Gravity_Flow_API::get_inbox_entries( $args );
		$this->assertEquals( 5, count( $entries ) );

		// Confirm the found entry IDs match the created IDs.
		$actual_ids = wp_list_pluck( $entries, 'id' );
		$this->assertEquals( $expected_ids, $actual_ids );
	}

	/**
	 * Tests that the expected entries are returned for an access token user.
	 */
	function test_get_inbox_entries_access_token() {
		// Create 30 entries with half assigned to the current user.
		$expected_ids = $this->_create_entries( 'workflow_email_test@test.test' );

		// Get the entries for the current access token.
		$this->_set_access_token();
		$total   = 0;
		$entries = Gravity_Flow_API::get_inbox_entries( $this->_get_form_id_args(), $total );

		// Confirm fifteen entries were found.
		$expected_count = 15;
		$this->assertEquals( $expected_count, $total );

		// Confirm the found entry IDs match the created IDs.
		$actual_ids = wp_list_pluck( $entries, 'id' );
		$this->assertEquals( $expected_ids, $actual_ids );
	}

	/**
	 * Tests that the expected entries are returned for the custom filter key.
	 */
	function test_get_inbox_entries_custom_filter_key() {
		// Create 30 entries with half assigned to the custom filter key.
		$expected_ids = $this->_create_entries( 'my_custom_key' );

		$args               = $this->_get_form_id_args();
		$args['filter_key'] = 'my_custom_key';

		// Get the entries for the current access token.
		$total   = 0;
		$entries = Gravity_Flow_API::get_inbox_entries( $args, $total );

		// Confirm fifteen entries were found.
		$expected_count = 15;
		$this->assertEquals( $expected_count, $total );

		// Confirm the found entry IDs match the created IDs.
		$actual_ids = wp_list_pluck( $entries, 'id' );
		$this->assertEquals( $expected_ids, $actual_ids );
	}

	/**
	 * Tests that the expected entries are returned for an access token user when the sorting arguments are overridden.
	 */
	function test_get_inbox_entries_access_token_sorting() {
		// Create 30 entries with half assigned to the current user.
		$expected_ids = $this->_create_entries( 'workflow_email_test@test.test' );

		$args = $this->_get_form_id_args();

		$args['sorting']['direction'] = 'ASC';

		// Get the entries for the current access token.
		$this->_set_access_token();
		$entries = Gravity_Flow_API::get_inbox_entries( $args );

		// Confirm the found entry IDs match the created IDs.
		$actual_ids = wp_list_pluck( $entries, 'id' );
		$this->assertEquals( array_reverse( $expected_ids ), $actual_ids );
	}

	/**
	 * Tests that no entries are found when a user is not logged in and an access token is not found.
	 */
	function test_get_inbox_entries_count_anonymous() {
		// Create 30 entries with half assigned to a user.
		$this->_create_entries( 'workflow_user_id_1' );

		// Confirm that no entries are found for the anonymous user.
		$count = Gravity_Flow_API::get_inbox_entries_count( $this->_get_form_id_args() );
		$expected_count = 0;
		$this->assertEquals( $expected_count, $count );
	}

	/**
	 * Tests that the expected number of entries are found when a user is logged in.
	 */
	function test_get_inbox_entries_count_current_user() {
		// Create 30 entries with half assigned to the current user.
		$this->_create_entries( 'workflow_user_id_1' );

		// Get the entries count for the current user.
		$this->_set_user();
		$count = Gravity_Flow_API::get_inbox_entries_count( $this->_get_form_id_args() );

		// Confirm fifteen entries were found.
		$expected_count = 15;
		$this->assertEquals( $expected_count, $count );
	}

	/**
	 * Tests that the expected number of entries are found for an access token user.
	 */
	function test_get_inbox_entries_count_access_token() {
		// Create 30 entries with half assigned to the current user.
		$this->_create_entries( 'workflow_email_test@test.test' );

		// Get the entries count for the current access token.
		$this->_set_access_token();
		$count = Gravity_Flow_API::get_inbox_entries_count( $this->_get_form_id_args() );

		// Confirm fifteen entries were found.
		$expected_count = 15;
		$this->assertEquals( $expected_count, $count );
	}

	/**
	 * Tests that the expected number of entries are found for a custom filter key.
	 */
	function test_get_inbox_entries_count_custom_filter_key() {
		// Create 30 entries with half assigned to the custom filter key.
		$this->_create_entries( 'my_custom_key' );

		$args               = $this->_get_form_id_args();
		$args['filter_key'] = 'my_custom_key';

		// Get the entries count for the current access token.
		$count = Gravity_Flow_API::get_inbox_entries_count( $args );

		// Confirm fifteen entries were found.
		$expected_count = 15;
		$this->assertEquals( $expected_count, $count );
	}

	/* HELPERS */

	/**
	 * Populates the current user global for the admin user.
	 */
	public function _set_user() {
		wp_set_current_user( 1 );
	}

	/**
	 * Sets the access token query string parameter.
	 */
	public function _set_access_token() {
		// Decode the token to prevent a validation failure when base64 decoded in Gravity_Flow::validate_access_token().
		$_GET['gflow_access_token'] = urldecode( gravity_flow()->generate_access_token( new Gravity_Flow_Assignee( 'email|test@test.test' ) ) );
	}

	/**
	 * Returns a basic inbox page arguments array.
	 *
	 * @return array
	 */
	public function _get_form_id_args() {
		return array( 'form_id' => $this->form_id );
	}

	/**
	 * Used to test the filters.
	 *
	 * @return array
	 */
	public function _get_bingo_array() {
		return array( 'bingo' );
	}

	/**
	 * Creates an Approval type step.
	 *
	 * @param array $override_settings The additional step settings.
	 *
	 * @return mixed
	 */
	public function _add_approval_step( $override_settings = array() ) {
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
	 * Returns a random entry (not saved).
	 *
	 * @return array
	 */
	public function _generate_random_entry() {
		$form = $this->factory->form->get_form_by_id( $this->form_id );

		$random_entry_object            = $this->factory->entry->generate_random_entry_object( $form );
		$random_entry_object['form_id'] = $form['id'];

		return $random_entry_object;
	}

	/**
	 * Saves thirty entries to the database and returns the IDs of fifteen which are assigned to the current user/access token.
	 *
	 * @param string $filter_key The key which will be used in the search criteria when getting the entries later.
	 *
	 * @return array
	 */
	public function _create_entries( $filter_key ) {
		// Create 30 unassigned entries.
		$created_entry_ids = $this->factory->entry->create_many( 30, $this->_generate_random_entry() );

		// Get the keys to 15 random entries.
		$random_keys = array_rand( $created_entry_ids, 15 );

		// Assign the random entries to the current filter key.
		$return_entry_ids = array();
		foreach ( $random_keys as $key ) {
			$entry_id           = $created_entry_ids[ $key ];
			$return_entry_ids[] = $entry_id;
			gform_update_meta( $entry_id, $filter_key, 'pending' );
		}

		// Reverse to match default descending sort order used by GFAPI::get_entries().
		return array_reverse( $return_entry_ids );
	}

}
