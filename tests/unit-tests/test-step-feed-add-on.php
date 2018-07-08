<?php

/**
 * Testing the common functionality of Feed Add-On based steps.
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Step_Feed_Add_On extends GF_UnitTestCase {

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
	 * Tests that the Gravity_Flow_Step_Feed_Test_Add_On class was successfully registered.
	 */
	public function test_steps_get_instance() {

		$step = Gravity_Flow_Steps::get( 'test_feed' );
		$this->assertInstanceOf( 'Gravity_Flow_Step_Feed_Test_Add_On', $step );

	}

	/**
	 * Tests that Gravity_Flow_Step_Feed_Add_On::is_active() is working.
	 */
	public function test_is_active() {

		// Test that the step is inactive when the feed add-on is not available.
		$step = $this->_get_step();
		$this->assertFalse( $step->is_active() );

		// Test that the step is active now that the feed add-on is available.
		$this->_include_test_add_on();
		$this->assertTrue( $step->is_active() );

		// Test that the step is inactive when set to inactive in the database.
		$step_id = $step->get_id();
		gravity_flow()->update_feed_active( $step_id, false );
		$updated_step = $this->api->get_step( $step_id );
		$this->assertFalse( $updated_step->is_active() );

	}

	/**
	 * Tests that getting an instance of the add-on for the step works.
	 */
	public function test_get_add_on_instance() {

		$this->_include_test_add_on();
		$step   = $this->_get_step();
		$add_on = $step->get_add_on_instance();
		$this->assertInstanceOf( 'Test_Feed_Addon', $add_on );

	}

	/**
	 * Tests that Gravity_Flow_Step_Feed_Add_On::get_feeds() can get the feeds for the add-on.
	 */
	public function test_get_feeds() {

		$this->_include_test_add_on();

		// Test that the add-on has no feeds for the current form.
		$step  = $this->_get_step();
		$feeds = $step->get_feeds();
		$this->assertEmpty( $feeds );

		// Create a feed for the form.
		$feed_id = $this->_create_test_add_on_feed();

		// Test that the feed was successfully created.
		$feeds = $step->get_feeds();
		$this->assertEquals( 1, count( $feeds ) );
		$this->assertEquals( $feed_id, $feeds[0]['id'] );

	}

	/**
	 * Tests that Gravity_Flow_Step_Feed_Add_On::intercept_submission() removes the selected feed from the array to be processed by the add-on.
	 */
	public function test_intercept_submission() {

		// Test that the add-ons feeds array contains one feed.
		$step  = $this->_get_step_with_entry();
		$feeds = $step->get_feeds();
		$this->assertEquals( 1, count( $feeds ) );

		// Test that the feed is removed from the array by the step.
		$step->intercept_submission();
		$entry          = $step->get_entry();
		$form           = $step->get_form();
		$filtered_feeds = gravityforms_test_feed_addon()->pre_process_feeds( $feeds, $entry, $form );
		$this->assertEmpty( $filtered_feeds );

	}

	/**
	 * Tests that Gravity_Flow_Step_Feed_Add_On::status_evaluation() is able to use the processed_feeds entry meta to determine the step status.
	 */
	public function test_status_evaluation_post_intercept() {

		$step = $this->_get_step_with_entry();

		// Simulate submission time feed processing.
		$step->intercept_submission();
		$entry = $step->get_entry();
		$form  = $step->get_form();
		gravityforms_test_feed_addon()->maybe_process_feed( $entry, $form );

		// Test that the step prevented the feed being processed. The feed ID should not be included in the processed_feeds entry meta.
		$status = $step->status_evaluation();
		$this->assertEquals( 'pending', $status );

	}

	/**
	 * Tests that Gravity_Flow_Step_Feed_Add_On::process() is able to process a feed and immediately complete the step.
	 */
	public function test_process() {

		$step     = $this->_get_step_with_entry();
		$complete = $step->process();
		$this->assertTrue( $complete );

		$status = $step->status_evaluation();
		$this->assertEquals( 'complete', $status );

	}

	/**
	 * Tests that Gravity_Flow_Step_Feed_Add_On::process() is able to trigger processing of a feed which will complete at a later date/time (i.e. Dropbox).
	 */
	public function test_process_completion_delayed() {

		$step = $this->_get_step_with_entry( array( 'completion_delayed' => true ) );

		$complete = $step->process();
		$this->assertFalse( $complete );

		$status = $step->status_evaluation();
		$this->assertEquals( 'pending', $status );

	}

	/* HELPERS */

	/**
	 * Include the Test feed based add-on which is included with Gravity Forms.
	 */
	public function _include_test_add_on() {
		require_once GFCommon::get_base_path() . '/tests/testfeedaddon.php';
	}

	/**
	 * Creates a feed for the Test feed based add-on.
	 *
	 * @return int
	 */
	public function _create_test_add_on_feed() {
		$this->_include_test_add_on();

		return gravityforms_test_feed_addon()->insert_feed( $this->form_id, true, array( 'feedName' => 'Test Feed' ) );
	}

	/**
	 * Creates a Gravity Flow Step for the feed add-on.
	 *
	 * @param array $override_settings The additional step settings.
	 *
	 * @return int The Step ID.
	 */
	public function _create_test_feed_step( $override_settings = array() ) {
		$default_settings = array(
			'step_name'                               => 'Test Feed Add-On',
			'description'                             => '',
			'step_type'                               => 'test_feed',
			'feed_condition_logic_conditional_logic'  => false,
			'feed_condition_conditional_logic_object' => array(),
			'destination_complete'                    => 'next',
		);

		$settings = wp_parse_args( $override_settings, $default_settings );

		return $this->api->add_step( $settings );
	}

	/**
	 * Creates and returns a Gravity Flow step for the feed add-on.
	 *
	 * @param array $override_settings The additional step settings.
	 * @param null|array $entry Null or a Gravity Forms entry.
	 *
	 * @return bool|Gravity_Flow_Step_Feed_Test_Add_On
	 */
	public function _get_step( $override_settings = array(), $entry = null ) {
		$step_id = $this->_create_test_feed_step( $override_settings );

		return $this->api->get_step( $step_id, $entry );
	}

	/**
	 * Creates and returns a Gravity Flow step, initialised with an entry, for the feed add-on.
	 *
	 * @param array $override_settings The additional step settings.
	 *
	 * @return bool|Gravity_Flow_Step_Feed_Test_Add_On
	 */
	public function _get_step_with_entry( $override_settings = array() ) {
		$feed_id = $this->_create_test_add_on_feed();

		$override_settings[ 'feed_' . $feed_id ] = '1';

		return $this->_get_step( $override_settings, $this->_create_entry() );
	}

	/**
	 * Creates and returns a random entry.
	 *
	 * @return array|WP_Error
	 */
	function _create_entry() {
		$form                           = $this->factory->form->get_form_by_id( $this->form_id );
		$random_entry_object            = $this->factory->entry->generate_random_entry_object( $form );
		$random_entry_object['form_id'] = $form['id'];
		$entry_id                       = $this->factory->entry->create( $random_entry_object );

		return $this->factory->entry->get_entry_by_id( $entry_id );
	}

}

/**
 * Class Gravity_Flow_Step_Feed_Test_Add_On
 *
 * The step for the Gravity Forms Test Feed Add-On.
 */
class Gravity_Flow_Step_Feed_Test_Add_On extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'test_feed';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'Test_Feed_Addon';

	/**
	 * Process the feed; remove the feed from the processed feeds list;
	 *
	 * @param array $feed The feed to be processed.
	 *
	 * @return bool Returning false to ensure the next step is not processed until after the files are uploaded.
	 */
	public function process_feed( $feed ) {
		parent::process_feed( $feed );

		return $this->completion_delayed ? false : true;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Test_Add_On() );
