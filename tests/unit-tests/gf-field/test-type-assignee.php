<?php

/**
 * Testing the default behaviour of the GF_Field methods overridden by Tests_Gravity_Flow_Field_Assignee_Select.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Field_Assignee_Select extends GF_Field_UnitTestCase {

	/**
	 * @var GF_UnitTest_Factory
	 */
	protected $factory;

	/**
	 * @var int
	 */
	protected $form_id;

	protected function _field_properties() {
		return array(
			'type'                               => 'workflow_assignee_select',
			'choices'                            => array(
				array( 'text' => 'user1', 'value' => 'user_id|1' ),
				array( 'text' => 'user2', 'value' => 'user_id|2' ),
				array( 'text' => 'user3', 'value' => 'user_id|3' ),
				array( 'text' => 'Administrator', 'value' => 'role|administrator' ),
				array( 'text' => 'Subscriber', 'value' => 'role|subscriber' ),
				array( 'text' => 'User (Created by)', 'value' => 'entry|created_by' ),
			),
			'gravityflowAssigneeFieldShowUsers'  => true,
			'gravityflowAssigneeFieldShowRoles'  => true,
			'gravityflowAssigneeFieldShowFields' => true,
		);
	}

	/**
	 * Creates a requireLogin form for tests.
	 */
	public function setUp() {
		GF_UnitTestCase::setUp();

		$this->form_id = $this->factory->form->create( array( 'requireLogin' => true ) );

		$settings = wp_parse_args( $this->_field_properties(), array(
			'id'      => 1,
			'formId'  => $this->form_id,
			'label'   => 'Test Label',
			'type'    => 'test',
			'choices' => null,
			'inputs'  => null,
		) );

		$this->field = GF_Fields::create( $settings );
	}

	/**
	 * @covers       Tests_Gravity_Flow_Field_Assignee_Select::validate
	 *
	 * @dataProvider data_provider_validate
	 *
	 * @param string $value
	 * @param bool $is_valid
	 * @param string $message
	 */
	public function test_validate( $value, $is_valid, $message = '' ) {
		if ( ! empty( $message ) ) {
			$this->field->errorMessage = $message;
		}

		$this->field->validate( $value, array() );

		if ( $is_valid ) {
			$this->assertObjectNotHasAttribute( 'failed_validation', $this->field );
			$this->assertObjectNotHasAttribute( 'validation_message', $this->field );
		} else {
			$this->assertTrue( $this->field->failed_validation );

			if ( empty( $message ) ) {
				$message = 'Invalid selection. Please select one of the available choices.';
			}

			$this->assertSame( $message, $this->field->validation_message );
		}
	}

	/**
	 * The dataProvider for test_validate().
	 *
	 * @return array
	 */
	function data_provider_validate() {
		$user_id = wp_create_user( 'test_' . wp_generate_password( 8 ), wp_generate_password( 12 ), 'test_' . wp_generate_password( 8 ) . '@gmail.com' );

		return array(
			array( 'user_id|' . $user_id, true ),
			array( (string) $user_id, false ),
			array( 'role|administrator', true ),
			array( 'administrator', false ),
			array( 'entry|created_by', true ),
		);
	}

}
