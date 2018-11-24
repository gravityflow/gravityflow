<?php

/**
 * Testing Gravity_Flow_Common::format_date().
 *
 * @since 2.3.2
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Common_Format_Date extends GF_UnitTestCase {

	/**
	 * @var int The default timestamp.
	 */
	protected $timestamp = 1539433800;

	/**
	 * @var string The default date/time string.
	 */
	protected $date_time = '2018-10-13 12:30:00';


	// # TIMESTAMP INPUT TESTS ----------------------------------------------------------------------------------------


	/**
	 * Tests that the expected date is returned when only passing the timestamp.
	 */
	public function test_timestamp_input_default_args() {
		$timestamp_integer = $this->timestamp;
		$timestamp_string  = (string) $timestamp_integer;
		$expected_output   = 'October 13, 2018';

		$output_from_integer = Gravity_Flow_Common::format_date( $timestamp_integer );
		$this->assertEquals( $expected_output, $output_from_integer, 'integer timestamp' );

		$output_from_string = Gravity_Flow_Common::format_date( $timestamp_string );
		$this->assertEquals( $expected_output, $output_from_string, 'string timestamp' );
	}

	/**
	 * Tests that the expected date is returned when only passing the timestamp and WordPress has custom date/time formats configured.
	 */
	public function test_timestamp_input_default_args_custom_wp_format() {
		$this->_set_custom_wp_format();
		$expected_output = '10/13/2018';
		$actual_output   = Gravity_Flow_Common::format_date( $this->timestamp );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing the timestamp and a custom format.
	 */
	public function test_timestamp_input_format_arg() {
		$format          = 'd M Y g:i a';
		$expected_output = '13 Oct 2018 12:30 pm';
		$actual_output   = Gravity_Flow_Common::format_date( $this->timestamp, $format );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing the timestamp and the $is_human argument is set to true.
	 */
	public function test_timestamp_input_is_human_arg() {
		$expected_output = 'October 13, 2018';
		$actual_output   = Gravity_Flow_Common::format_date( $this->timestamp, '', true );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected value is returned when passing a recent timestamp and the $is_human argument is set to true.
	 */
	public function test_timestamp_input_is_human_arg_recent() {
		$timestamp       = strtotime( '-1 hour' );
		$expected_output = '1 hour ago';
		$actual_output   = Gravity_Flow_Common::format_date( $timestamp, '', true );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing the timestamp and the include_time argument is set to true.
	 */
	public function test_timestamp_input_include_time_arg() {
		$expected_output = 'October 13, 2018 at 12:30 pm';
		$actual_output   = Gravity_Flow_Common::format_date( $this->timestamp, '', false, true );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing the timestamp, the include_time argument is set to true, and WordPress has custom date/time formats configured.
	 */
	public function test_timestamp_input_include_time_arg_custom_wp_format() {
		$this->_set_custom_wp_format();
		$expected_output = '10/13/2018 at 12:30';
		$actual_output   = Gravity_Flow_Common::format_date( $this->timestamp, '', false, true );
		$this->assertEquals( $expected_output, $actual_output );
	}


	// # DATE TIME INPUT TESTS ----------------------------------------------------------------------------------------

	/**
	 * Tests that the expected date is returned when passing a date/time string.
	 */
	public function test_date_time_input_default_args() {
		$expected_output = 'October 13, 2018';
		$actual_output   = Gravity_Flow_Common::format_date( $this->date_time );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing a date/time string and WordPress has custom date/time formats configured.
	 */
	public function test_date_time_input_default_args_custom_wp_format() {
		$this->_set_custom_wp_format();
		$expected_output = '10/13/2018';
		$actual_output   = Gravity_Flow_Common::format_date( $this->date_time );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing a date/time string and a custom format.
	 */
	public function test_date_time_input_format_arg() {
		$format          = 'd M Y g:i a';
		$expected_output = '13 Oct 2018 12:30 pm';
		$actual_output   = Gravity_Flow_Common::format_date( $this->date_time, $format );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing a date/time string and the $is_human argument is set to true.
	 */
	public function test_date_time_input_is_human_arg() {
		$expected_output = 'October 13, 2018';
		$actual_output   = Gravity_Flow_Common::format_date( $this->date_time, '', true );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected value is returned when passing a recent date/time string and the $is_human argument is set to true.
	 */
	public function test_date_time_input_is_human_arg_recent() {
		$expected_output = '1 hour ago';
		$actual_output   = Gravity_Flow_Common::format_date( date( 'Y-m-d H:i:s', strtotime( '-1 hour' ) ), '', true );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing a date/time string and the include_time argument is set to true.
	 */
	public function test_date_time_input_include_time_arg() {
		$expected_output = 'October 13, 2018 at 12:30 pm';
		$actual_output   = Gravity_Flow_Common::format_date( $this->date_time, '', false, true );
		$this->assertEquals( $expected_output, $actual_output );
	}

	/**
	 * Tests that the expected date is returned when passing a date/time string, the include_time argument is set to true, and WordPress has custom date/time formats configured.
	 */
	public function test_date_time_input_include_time_arg_custom_wp_format() {
		$this->_set_custom_wp_format();
		$expected_output = '10/13/2018 at 12:30';
		$actual_output   = Gravity_Flow_Common::format_date( $this->date_time, '', false, true );
		$this->assertEquals( $expected_output, $actual_output );
	}


	// # HELPERS ------------------------------------------------------------------------------------------------------

	/**
	 * Updates the WordPress date and time formats.
	 */
	public function _set_custom_wp_format() {
		update_option( 'date_format', 'm/d/Y' );
		update_option( 'time_format', 'H:i' );
	}

}
