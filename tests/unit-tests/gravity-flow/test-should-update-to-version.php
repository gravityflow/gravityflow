<?php

/**
 * Testing Gravity_Flow::should_update_to_version()
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Should_Update_To_Version extends GF_UnitTestCase {

	/**
	 * @var null|Gravity_Flow
	 */
	private $plugin;

	public function setUp() {
		parent::setUp();
		$this->plugin = gravity_flow();
	}

	public function tearDown() {
		parent::tearDown();
		$this->plugin->_version = GRAVITY_FLOW_VERSION;
	}

	/**
	 * @dataProvider data_provider_no_downgrade
	 * @dataProvider data_provider_no_reinstall
	 *
	 * @param string $current_version
	 * @param string $new_version
	 * @param bool   $expected
	 */
	public function test_version_compare( $current_version, $new_version, $expected ) {
		$this->plugin->_version = $current_version;
		add_filter( 'gravityflow_major_version_auto_updates_allowed', array( $this->ma, 'filter' ) );
		$result = $this->plugin->should_update_to_version( $new_version );
		remove_filter( 'gravityflow_major_version_auto_updates_allowed', array( $this->ma, 'filter' ) );

		$this->assertSame( $expected, $result );
		$this->assertEmpty( $this->ma->get_events() );
	}

	/**
	 * @dataProvider data_provider_do_update_major
	 * @dataProvider data_provider_do_update_minor
	 * @dataProvider data_provider_do_update_pre_release
	 *
	 * @param string $current_version
	 * @param string $new_version
	 * @param bool   $expected
	 */
	public function test_no_filter( $current_version, $new_version, $expected ) {
		$this->plugin->_version = $current_version;
		$this->assertSame( $expected, $this->plugin->should_update_to_version( $new_version ) );
	}

	/**
	 * @dataProvider data_provider_no_update_major
	 * @dataProvider data_provider_do_update_minor
	 * @dataProvider data_provider_do_update_pre_release
	 *
	 * @param string $current_version
	 * @param string $new_version
	 * @param bool   $expected
	 */
	public function test_branch_check( $current_version, $new_version, $expected ) {
		$this->plugin->_version = $current_version;
		$this->ma->set_return_value( false );
		add_filter( 'gravityflow_major_version_auto_updates_allowed', array( $this->ma, 'return_value' ) );
		$result = $this->plugin->should_update_to_version( $new_version );
		remove_filter( 'gravityflow_major_version_auto_updates_allowed', array( $this->ma, 'return_value' ) );

		$this->assertSame( $expected, $result );
		$this->assertEquals( array(
			array(
				'filter' => 'return_value',
				'tag'    => 'gravityflow_major_version_auto_updates_allowed',
				'args'   => array( true ),
			),
		), $this->ma->get_events() );
	}

	public function data_provider_no_downgrade() {
		return array(
			array( '1.0', '0.9', false ),
			array( '1.2.3', '1.2.2', false ),
			array( '1.2.3.4', '1.2.3', false ),
			array( '2.0', '1.9.9', false ),
		);
	}

	public function data_provider_no_reinstall() {
		return array(
			array( '1.0', '1.0', false ),
			array( '1.1', '1.1', false ),
			array( '1.2.3', '1.2.3', false ),
			array( '1.2.3.4', '1.2.3.4', false ),
		);
	}

	public function data_provider_no_update_major() {
		return array(
			array( '1.0', '1.1', false ),
			array( '1.2.3', '1.3', false ),
			array( '1.2.3.4', '2.0', false ),
			array( '1.2.4-dev-abc123', '2.1', false ),
		);
	}

	public function data_provider_do_update_major() {
		return array(
			array( '1.0', '1.1', true ),
			array( '1.2.3', '1.3', true ),
			array( '1.2.3.4', '2.0', true ),
			array( '1.2.4-dev-abc123', '2.1', true ),
		);
	}

	public function data_provider_do_update_minor() {
		return array(
			array( '1.2.3', '1.2.4', true ),
			array( '1.2.3.4', '1.2.4', true ),
			array( '1.2.4', '1.2.40', true ),
		);
	}

	public function data_provider_do_update_pre_release() {
		return array(
			array( '1.2.4-dev-abc123', '1.2.4', true ),
			array( '1.2-dev-abc123', '1.2', true ),
			array( '2.5-beta-1', '2.5-beta-2', true ),
			array( '2.5-beta-1', '2.5', true ),
			array( '2.5-rc-1', '2.5-rc-2', true ),
			array( '2.5-rc-2', '2.5', true ),
		);
	}

}
