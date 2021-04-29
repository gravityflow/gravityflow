<?php

/**
 * Testing the default behaviour of the GF_Field methods overridden by Tests_Gravity_Flow_Field_User.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Field_User extends GF_Field_UnitTestCase {

	protected function _field_properties() {
		return array(
			'type'    => 'workflow_user',
			'choices' => array(
				array( 'text' => 'user1', 'value' => 'user_id|1' ),
				array( 'text' => 'user2', 'value' => 'user_id|2' ),
				array( 'text' => 'user3', 'value' => 'user_id|3' ),
				array( 'text' => 'user4', 'value' => 'user_id|4' ),
				array( 'text' => 'user5', 'value' => 'user_id|5' ),
			),
		);
	}

	/**
	 * @covers       Gravity_Flow_Field_User::validate
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
			array( (string) $user_id, true ),
			array( 'abcd', false ),
		);
	}

}
