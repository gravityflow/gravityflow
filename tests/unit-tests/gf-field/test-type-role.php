<?php

/**
 * Testing the default behaviour of the GF_Field methods overridden by Tests_Gravity_Flow_Field_role.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Field_role extends GF_Field_UnitTestCase {

	protected function _field_properties() {
		return array(
			'type'    => 'workflow_role',
			'choices' => array(
				array( 'text' => 'Administrator', 'value' => 'administrator' ),
				array( 'text' => 'Editor', 'value' => 'editor' ),
				array( 'text' => 'Contributor', 'value' => 'contributor' ),
				array( 'text' => 'Author', 'value' => 'author' ),
				array( 'text' => 'Subscriber', 'value' => 'subscriber' ),
			),
		);
	}

	/**
	 * @covers       Gravity_Flow_Field_role::validate
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
		return array(
			array( 'administrator', true ),
			array( 'abcd', false ),
		);
	}

}
