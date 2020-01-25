<?php

/**
 * Testing Gravity_Flow_Step::get_timestamp_delay()
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Step_Get_Timestamp_Delay extends GF_UnitTestCase {

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

	public function test_wrong_type() {
		$step = $this->api->get_step( $this->_add_step( array( 'due_date_type' => 'other' ) ) );
		$this->assertFalse( $step->get_timestamp_delay( 'due_date' ) );
	}

	public function test_get_step_timestamp() {
		$step_id = $this->_add_step();
		$entry   = $this->_create_entry( $step_id );
		$step    = $this->api->get_step( $step_id, $entry );
		$this->assertSame( $this->timestamp, $step->get_timestamp_delay( 'due_date' ) );
	}

	/**
	 * @dataProvider data_provider_offset
	 *
	 * @param int    $expected
	 * @param string $offset
	 * @param string $unit
	 */
	public function test_offset( $expected, $offset, $unit ) {
		$step_id = $this->_add_step( array( 'due_date_delay_offset' => $offset, 'due_date_delay_unit' => $unit ) );
		$entry   = $this->_create_entry( $step_id );
		$step    = $this->api->get_step( $step_id, $entry );
		$this->assertSame( $expected, $step->get_timestamp_delay( 'due_date' ) );
	}

	public function data_provider_offset() {
		return array(
			'minutes null'            => array( $this->timestamp, null, 'minutes' ),
			'minutes false'           => array( $this->timestamp, false, 'minutes' ),
			'minutes string empty'    => array( $this->timestamp, '', 'minutes' ),
			'minutes string -'        => array( $this->timestamp, '-', 'minutes' ),
			'minutes 5'               => array( 1539389100, '5', 'minutes' ),
			'minutes leading space'   => array( 1539389100, ' 5', 'minutes' ),
			'minutes trailing space'  => array( 1539389100, '5 ', 'minutes' ),
			'minutes trailing letter' => array( $this->timestamp, '5a', 'minutes' ),
			'minutes -5'              => array( 1539388500, '-5', 'minutes' ),

			'hours null'            => array( $this->timestamp, null, 'hours' ),
			'hours false'           => array( $this->timestamp, false, 'hours' ),
			'hours string empty'    => array( $this->timestamp, '', 'hours' ),
			'hours string -'        => array( $this->timestamp, '-', 'hours' ),
			'hours 5'               => array( 1539406800, '5', 'hours' ),
			'hours leading space'   => array( 1539406800, ' 5', 'hours' ),
			'hours trailing space'  => array( 1539406800, '5 ', 'hours' ),
			'hours trailing letter' => array( $this->timestamp, '5a', 'hours' ),
			'hours -5'              => array( 1539370800, '-5', 'hours' ),

			'days null'            => array( $this->timestamp, null, 'days' ),
			'days false'           => array( $this->timestamp, false, 'days' ),
			'days string empty'    => array( $this->timestamp, '', 'days' ),
			'days string -'        => array( $this->timestamp, '-', 'days' ),
			'days 5'               => array( 1539820800, '5', 'days' ),
			'days leading space'   => array( 1539820800, ' 5', 'days' ),
			'days trailing space'  => array( 1539820800, '5 ', 'days' ),
			'days trailing letter' => array( $this->timestamp, '5a', 'days' ),
			'days -5'              => array( 1538956800, '-5', 'days' ),

			'weeks null'            => array( $this->timestamp, null, 'weeks' ),
			'weeks false'           => array( $this->timestamp, false, 'weeks' ),
			'weeks string empty'    => array( $this->timestamp, '', 'weeks' ),
			'weeks string -'        => array( $this->timestamp, '-', 'weeks' ),
			'weeks 5'               => array( 1542412800, '5', 'weeks' ),
			'weeks leading space'   => array( 1542412800, ' 5', 'weeks' ),
			'weeks trailing space'  => array( 1542412800, '5 ', 'weeks' ),
			'weeks trailing letter' => array( $this->timestamp, '5a', 'weeks' ),
			'weeks -5'              => array( 1536364800, '-5', 'weeks' ),
		);
	}

	/* HELPERS */

	/**
	 * Creates an Approval type step.
	 *
	 * @param array $override_settings The additional step settings.
	 *
	 * @return mixed
	 */
	public function _add_step( $override_settings = array() ) {
		$default_settings = array(
			'step_name'     => 'Approval',
			'step_type'     => 'approval',
			'due_date'      => true,
			'due_date_type' => 'delay',
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
	public function _create_entry( $step_id = false ) {
		$entry_id = $this->factory->entry->create( array(
			'form_id'      => $this->form_id,
			'date_created' => $this->date . ' 00:00:00',
			'10'           => $this->date,
		) );

		if ( $step_id ) {
			gform_update_meta( $entry_id, 'workflow_step_' . $step_id . '_timestamp', $this->timestamp );
		}

		return $this->factory->entry->get_entry_by_id( $entry_id );
	}

}
