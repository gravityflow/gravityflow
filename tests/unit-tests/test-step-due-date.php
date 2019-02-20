<?php

/**
 * Testing the step due_date functionality.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Step_Due_Date extends GF_UnitTestCase {

	/**
	 * @var int
	 */
	protected $form_id;

	/**
	 * @var Gravity_Flow_API
	 */
	protected $api;

	/**
	 * @var int The default timestamp.
	 */
	protected $timestamp = 1539388800;
	/**
	 * @var string The default date.
	 */
	protected $date = '2018-10-13';

	/**
	 * Creates a form and uses it to initialise the Gravity Flow API.
	 */
	public function setUp() {
		parent::setUp();

		$this->form_id = $this->factory->form->create();
		$this->api     = new Gravity_Flow_API( $this->form_id );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date type due date.
	 */
	public function test_get_due_date_timestamp_type_date() {
		$step_id = $this->_add_step();
		$step    = $this->api->get_step( $step_id );

		$expected_timestamp = 1539388800; // 2018-10-13 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date.
	 */
	public function test_get_due_date_timestamp_type_date_field() {
		$step_id = $this->_add_step();
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1539388800; // 2018-10-13 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date with a 5 min offset.
	 */
	public function test_get_due_date_timestamp_type_date_field_offset_minutes_after() {
		$settings = array(
			'due_date_type'                    => 'date_field',
			'due_date_date_field_offset'       => '5',
			'due_date_date_field_offset_unit'  => 'minutes',
			'due_date_date_field_before_after' => 'after',
		);

		$step_id = $this->_add_step( $settings );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1539389100; // 2018-10-13 00:05:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date with 5 min offset.
	 */
	public function test_get_due_date_timestamp_type_date_field_offset_minutes_before() {
		$settings = array(
			'due_date_type'                    => 'date_field',
			'due_date_date_field_offset'       => '5',
			'due_date_date_field_offset_unit'  => 'minutes',
			'due_date_date_field_before_after' => 'before',
		);

		$step_id = $this->_add_step( $settings );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1539388500; // 2018-10-12 23:55:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date with a 5 hour offset.
	 */
	public function test_get_due_date_timestamp_type_date_field_offset_hours_after() {
		$settings = array(
			'due_date_type'                    => 'date_field',
			'due_date_date_field_offset'       => '5',
			'due_date_date_field_offset_unit'  => 'hours',
			'due_date_date_field_before_after' => 'after',
		);

		$step_id = $this->_add_step( $settings );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1539406800; // 2018-10-13 05:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date with a 5 hour offset.
	 */
	public function test_get_due_date_timestamp_type_date_field_offset_hours_before() {
		$settings = array(
			'due_date_type'                    => 'date_field',
			'due_date_date_field_offset'       => '5',
			'due_date_date_field_offset_unit'  => 'hours',
			'due_date_date_field_before_after' => 'before',
		);

		$step_id = $this->_add_step( $settings );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1539370800; // 2018-10-12 20:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date with a 5 day offset.
	 */
	public function test_get_due_date_timestamp_type_date_field_offset_days_after() {
		$settings = array(
			'due_date_type'                    => 'date_field',
			'due_date_date_field_offset'       => '5',
			'due_date_date_field_offset_unit'  => 'days',
			'due_date_date_field_before_after' => 'after',
		);

		$step_id = $this->_add_step( $settings );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1539820800; // 2018-10-18 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date with a 5 day offset.
	 */
	public function test_get_due_date_timestamp_type_date_field_offset_days_before() {
		$settings = array(
			'due_date_type'                    => 'date_field',
			'due_date_date_field_offset'       => '5',
			'due_date_date_field_offset_unit'  => 'days',
			'due_date_date_field_before_after' => 'before',
		);

		$step_id = $this->_add_step( $settings );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1538956800; // 2018-10-08 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date with a 5 week offset.
	 */
	public function test_get_due_date_timestamp_type_date_field_offset_weeks_after() {
		$settings = array(
			'due_date_type'                    => 'date_field',
			'due_date_date_field_offset'       => '5',
			'due_date_date_field_offset_unit'  => 'weeks',
			'due_date_date_field_before_after' => 'after',
		);

		$step_id = $this->_add_step( $settings );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1542412800; // 2018-11-17 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a date field type due date with a 5 week offset.
	 */
	public function test_get_due_date_timestamp_type_date_field_offset_weeks_before() {
		$settings = array(
			'due_date_type'                    => 'date_field',
			'due_date_date_field_offset'       => '5',
			'due_date_date_field_offset_unit'  => 'weeks',
			'due_date_date_field_before_after' => 'before',
		);

		$step_id = $this->_add_step( $settings );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );

		$expected_timestamp = 1536364800; // 2018-09-08 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a delay type due date.
	 */
	public function test_get_due_date_timestamp_type_delay() {
		$settings = array(
			'due_date_type' => 'delay',
		);

		$step_id = $this->_add_step( $settings );
		$entry   = $this->_create_entry( $step_id );
		$step    = $this->api->get_step( $step_id, $entry );

		$expected_timestamp = $this->timestamp; // 2018-10-13 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a delay type due date with a 5 min offset.
	 */
	public function test_get_due_date_timestamp_type_delay_offset_minutes() {
		$settings = array(
			'due_date_type'         => 'delay',
			'due_date_delay_offset' => '5',
			'due_date_delay_unit'   => 'minutes',
		);

		$step_id = $this->_add_step( $settings );
		$entry   = $this->_create_entry( $step_id );
		$step    = $this->api->get_step( $step_id, $entry );

		$expected_timestamp = 1539389100; // 2018-10-13 00:05:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a delay type due date with a 5 hour offset.
	 */
	public function test_get_due_date_timestamp_type_delay_offset_hours() {
		$settings = array(
			'due_date_type'         => 'delay',
			'due_date_delay_offset' => '5',
			'due_date_delay_unit'   => 'hours',
		);

		$step_id = $this->_add_step( $settings );
		$entry   = $this->_create_entry( $step_id );
		$step    = $this->api->get_step( $step_id, $entry );

		$expected_timestamp = 1539406800; // 2018-10-13 05:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a delay type due date with a 5 day offset.
	 */
	public function test_get_due_date_timestamp_type_delay_offset_days() {
		$settings = array(
			'due_date_type'         => 'delay',
			'due_date_delay_offset' => '5',
			'due_date_delay_unit'   => 'days',
		);

		$step_id = $this->_add_step( $settings );
		$entry   = $this->_create_entry( $step_id );
		$step    = $this->api->get_step( $step_id, $entry );

		$expected_timestamp = 1539820800; // 2018-10-18 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the expected timestamp is returned when the feed is configured with a delay type due date with a 5 week offset.
	 */
	public function test_get_due_date_timestamp_type_delay_offset_weeks() {
		$settings = array(
			'due_date_type'         => 'delay',
			'due_date_delay_offset' => '5',
			'due_date_delay_unit'   => 'weeks',
		);

		$step_id = $this->_add_step( $settings );
		$entry   = $this->_create_entry( $step_id );
		$step    = $this->api->get_step( $step_id, $entry );

		$expected_timestamp = 1542412800; // 2018-11-17 00:00:00.
		$output_timestamp   = $step->get_due_date_timestamp();
		$this->assertEquals( $expected_timestamp, $output_timestamp );
	}

	/**
	 * Tests that the gravityflow_step_due_date_timestamp filter overrides the timestamp.
	 */
	public function test_get_due_date_timestamp_filter() {
		$step_id = $this->_add_step();
		$step    = $this->api->get_step( $step_id );

		add_filter( 'gravityflow_step_due_date_timestamp', array( $this, 'filter_timestamp' ) );
		$output_timestamp = $step->get_due_date_timestamp();
		remove_filter( 'gravityflow_step_due_date_timestamp', array( $this, 'filter_timestamp' ) );
		$this->assertEquals( $this->filter_timestamp(), $output_timestamp );
	}

	/**
	 * Tests that the steps is_overdue() method returns the expected result for a due date date in the past.
	 */
	public function test_is_overdue_past_date() {
		$step_id = $this->_add_step();
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );
		$this->assertTrue( $step->is_overdue() );
	}

	/**
	 * Tests that the steps is_expired() method returns the expected result for a due date date in the future.
	 */
	public function test_is_expired_future_date() {
		$step_id = $this->_add_step( array( 'due_date_date' => date( 'Y-m-d', strtotime( '+1 week' ) ) ) );
		$step    = $this->api->get_step( $step_id, $this->_create_entry() );
		$this->assertFalse( $step->is_expired() );
	}

	/* HELPERS */

	/**
	 * Creates an Approval type step.
	 *
	 * @param array $override_settings The additional step settings.
	 *
	 * @return mixed
	 */
	function _add_step( $override_settings = array() ) {
		$default_settings = array(
			'step_name'             => 'Approval',
			'step_type'             => 'approval',
			'due_date'            => true,
			'due_date_type'       => 'date',
			'due_date_date'       => $this->date,
			'due_date_date_field' => '10',
		);

		$settings = wp_parse_args( $override_settings, $default_settings );

		return $this->api->add_step( $settings );
	}

	/**
	 * Creates and returns an entry.
	 *
	 * @param int|bool $step_id The ID of the step which the step timestamp should be set for.
	 *
	 * @return array|WP_Error
	 */
	function _create_entry( $step_id = false ) {
		$entry_id = $this->factory->entry->create( array(
			'form_id'      => $this->form_id,
			'date_created' => $this->date . ' 00:00:00',
			'10'           => $this->date
		) );

		if ( $step_id ) {
			gform_update_meta( $entry_id, 'workflow_step_' . $step_id . '_timestamp', $this->timestamp );
		}

		return $this->factory->entry->get_entry_by_id( $entry_id );
	}

	/**
	 * Callback for the gravityflow_step_due_date_timestamp filter.
	 *
	 * @return int
	 */
	public function filter_timestamp() {
		return 1514764800; // 2018-01-01 00:00:00
	}

}
