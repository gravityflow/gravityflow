<?php

/**
 * Testing the step expiration functionality.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Step_Update_User extends GF_UnitTestCase {

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
	 * @dataProvider data_provider_update_user
	 */
	public function test_update_user( $settings, $entry, $expected ) {
		$step = $this->_get_update_user_step( $entry, $settings );

		$user = get_user_by( 'ID', 1 );
		$step->set_user_properties( $user );
		// Refresh the user
		$user = get_user_by( 'ID', 1 );
		$this->assertEquals( $expected['value'], $user->{$expected['property']} );
	}

	/**
	 * The dataProvider for test_update_user().
	 *
	 * @return array
	 */
	function data_provider_update_user() {
		return array(
			'first_name' => array(
				'settings' => array(
					'user_source' => 'created_by',
					'first_name' => '5',
				),
				'entry' => array(
					'5' => 'Fred'
				),
				'expected' => array(
					'property' => 'first_name',
					'value' => 'Fred',
				)
			),
			'last_name' => array(
				'settings' => array(
					'user_source' => 'created_by',
					'last_name' => '5',
				),
				'entry' => array(
					'5' => 'Smith',
				),
				'expected' => array(
					'property' => 'last_name',
					'value' => 'Smith',
				)
			),
			'nickname' => array(
				'settings' => array(
					'user_source' => 'created_by',
					'nickname' => '5',
				),
				'entry' => array(
					'5' => 'Gravity Forms Expert',
				),
				'expected' => array(
					'property' => 'nickname',
					'value' => 'Gravity Forms Expert',
				)
			),
			'display_name' => array(
				'settings' => array(
					'user_source' => 'created_by',
					'display_name' => 'lastfirst',
					'first_name' => '5',
					'last_name' => '6',
				),
				'entry' => array(
					'5' => 'Fred',
					'6' => 'Smith',
				),
				'expected' => array(
					'property' => 'display_name',
					'value' => 'Smith Fred',
				)
			),
			'email' => array(
				'settings' => array(
					'user_source' => 'created_by',
					'email' => '5',
				),
				'entry' => array(
					'5' => 'exmple@test.com',
				),
				'expected' => array(
					'property' => 'user_email',
					'value' => 'exmple@test.com',
				)
			),
			'roles - replace' => array(
				'settings' => array(
					'user_source' => 'created_by',
					'roles_action' => 'replace',
					'roles' => array(
						'editor',
						'subscriber',
					)
				),
				'entry' => array(),
				'expected' => array(
					'property' => 'roles',
					'value' => array(
						'editor',
						'subscriber',
					)
				)
			),
			'roles - add' => array(
				'settings' => array(
					'user_source' => 'created_by',
					'roles_action' => 'add',
					'roles' => array(
						'editor',
						'subscriber',
					)
				),
				'entry' => array(),
				'expected' => array(
					'property' => 'roles',
					'value' => array(
						'administrator',
						'editor',
						'subscriber',
					)
				)
			),
			'user meta' => array(
				'settings' => array(
					'user_meta' => array(
						array(
							'key' => 'my_test_meta_key',
							'value' => '5',
							'custom_key' => '',
							'custom_value' => '',
						)
					),
				),
				'entry' => array(
					'5' => 'my test meta value'
				),
				'expected' => array(
					'property' => 'my_test_meta_key',
					'value' => 'my test meta value',
				)
			),
			'user meta - custom key' => array(
				'settings' => array(
					'user_meta' => array(
						array(
							'key' => 'gf_custom',
							'value' => '5',
							'custom_key' => 'my_custom_key',
							'custom_value' => '',
						)
					),
				),
				'entry' => array(
					'5' => 'my test meta value',
				),
				'expected' => array(
					'property' => 'my_custom_key',
					'value' => 'my test meta value',
				)
			),
			'user meta - custom value' => array(
				'settings' => array(
					'user_meta' => array(
						array(
							'key' => 'my_test_meta_key',
							'value' => 'gf_custom',
							'custom_key' => '',
							'custom_value' => 'custom value',
						)
					),
				),
				'entry' => array(),
				'expected' => array(
					'property' => 'my_test_meta_key',
					'value' => 'custom value',
				)
			),
			'user meta - custom value and key' => array(
				'settings' => array(
					'user_meta' => array(
						array(
							'key' => 'gf_custom',
							'value' => 'gf_custom',
							'custom_key' => 'custom_key',
							'custom_value' => 'custom value',
						)
					),
				),
				'entry' => array(),
				'expected' => array(
					'property' => 'custom_key',
					'value' => 'custom value'
				),
			),
			'user meta - custom value with merge tag' => array(
				'settings' => array(
					'user_meta' => array(
						array(
							'key' => 'gf_custom',
							'value' => 'gf_custom',
							'custom_key' => 'song',
							'custom_value' => '{:5}',
						)
					),
				),
				'entry' => array(
					'5' => 'perfect day'
				),
				'expected' => array(
					'property' => 'song',
					'value' => 'perfect day',
				)
			),
		);
	}

	function _get_update_user_step( $override_entry = array(), $override_settings = array() ){
		$default_settings = array(
			'step_name' => 'Update User',
			'description' => '',
			'step_type' => 'update_user',
			'feed_condition_logic_conditional_logic' => '0',
			'feed_condition_conditional_logic_object' => array(),
			'destination_complete' => 'next',
		);

		$settings = wp_parse_args( $override_settings, $default_settings );

		$feed = array(
			'id' => 1,
			'is_active' => true,
			'form_id' => $this->form_id,
			'meta' => $settings,
		);

		$entry = array( 'id' => 1, 'form_id' => $this->form_id, 'created_by' => 1, 'date_created' => '2016-06-18 11:00', '1' => 'Second Choice', '2.2' => 'Second Choice', '5' => 'test first name');

		$entry = $override_entry + $entry;

		$step = new Gravity_Flow_Step_Update_User( $feed, $entry );

		return $step;
	}

}
