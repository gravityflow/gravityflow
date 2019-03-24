<?php

/**
 * Testing gravity_flow()->get_users_as_choices().
 *
 * @since 2.5
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Get_Users_As_Choices extends GF_UnitTestCase {

	/**
	 * @var GF_UnitTest_Factory
	 */
	protected $factory;

	/**
	 * @var int
	 */
	protected $form_id;

	/**
	 * Creates 10 users for tests.
	 */
	public function setUp() {
		parent::setUp();

		for ( $i = 0; $i < 10; $i ++ ) {
			wp_create_user( "testuser_{$i}", wp_generate_password( 12 ), "testuseremail_{$i}@gmail.com" );
		}

		$this->form_id = $this->factory->form->create();
	}

	/**
	 * After we make $choices as a static variable in `get_users_as_choices`,
	 * we want to make sure the `gravityflow_get_users_args` filter can still work as expected.
	 */
	public function test_filter_gravityflow_get_users_args() {
		$_GET['id'] = $this->form_id;

		$choices_1 = gravity_flow()->get_users_as_choices();
		add_filter( 'gravityflow_get_users_args', array( $this, 'filter_get_users_arg_five' ) );
		$choices_2 = gravity_flow()->get_users_as_choices();
		remove_filter( 'gravityflow_get_users_args', array( $this, 'filter_get_users_arg_five' ) );
		$choices_3 = gravity_flow()->get_users_as_choices();
		add_filter( 'gravityflow_get_users_args', array( $this, 'filter_get_users_arg_ten' ) );
		$choices_4 = gravity_flow()->get_users_as_choices();
		remove_filter( 'gravityflow_get_users_args', array( $this, 'filter_get_users_arg_ten' ) );
		$choices_5 = gravity_flow()->get_users_as_choices();

		$this->assertNotEquals( $choices_1, $choices_2 );
		$this->assertEquals( $choices_1, $choices_3 );
		$this->assertNotEquals( $choices_2, $choices_4 );
		$this->assertEquals( $choices_3, $choices_5 );
	}

	/**
	 * Filter `gravityflow_get_users_args`.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	public function filter_get_users_arg_five( $args ) {
		$args = wp_parse_args(
			array(
				'number' => 5,
			),
			$args
		);

		return $args;
	}

	/**
	 * Filter `gravityflow_get_users_args`.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	public function filter_get_users_arg_ten( $args ) {
		$args = wp_parse_args(
			array(
				'number' => 10,
			),
			$args
		);

		return $args;
	}

}
