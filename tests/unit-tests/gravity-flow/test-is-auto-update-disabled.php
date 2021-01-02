<?php

/**
 * Testing Gravity_Flow::is_auto_update_disabled()
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Is_Auto_Update_Disabled extends GF_UnitTestCase {

	/**
	 * @dataProvider data_provider_args
	 *
	 * @param $arg
	 * @param $filter_arg
	 * @param $expected
	 */
	public function test_arg( $arg, $filter_arg, $expected ) {
		global $wp_version;

		if ( version_compare( $wp_version, '5.5', '<' ) ) {
			$this->markTestSkipped();
		}

		$plugin = $this->_get_plugin();
		add_filter( 'gravityflow_disable_auto_update', array( $this->ma, 'filter' ) );
		$this->assertSame( $expected, $plugin->is_auto_update_disabled( $arg ) );

		$this->assertEquals( array(
			array(
				'filter' => 'filter',
				'tag'    => 'gravityflow_disable_auto_update',
				'args'   => array( $filter_arg ),
			),
		), $this->ma->get_events() );
	}

	public function data_provider_args() {
		return array(
			array( null, false, false ),
			array( true, false, false ),
			array( false, true, true ),
		);
	}

	/**
	 * @dataProvider data_provider_old_wp_args
	 *
	 * @param $enabled
	 * @param $filter_arg
	 * @param $expected
	 */
	public function test_old_wp_version( $enabled, $filter_arg, $expected ) {
		global $wp_version;

		if ( ! version_compare( $wp_version, '5.5', '<' ) ) {
			$this->markTestSkipped();
		}

		$plugin = $this->_get_plugin( $enabled );
		add_filter( 'gravityflow_disable_auto_update', array( $this->ma, 'filter' ) );
		$result = $plugin->is_auto_update_disabled();
		remove_filter( 'gravityflow_disable_auto_update', array( $this->ma, 'filter' ) );
		$this->assertSame( $expected, $result );

		$this->assertEquals( array(
			array(
				'filter' => 'filter',
				'tag'    => 'gravityflow_disable_auto_update',
				'args'   => array( $filter_arg ),
			),
		), $this->ma->get_events() );
	}

	public function data_provider_old_wp_args() {
		return array(
			array( true, false, false ),
			array( false, true, true ),
		);
	}

	public function test_constant() {
		$plugin = $this->_get_plugin();
		define( 'GRAVITYFLOW_DISABLE_AUTO_UPDATE', true );
		$this->assertTrue( $plugin->is_auto_update_disabled( false ) );
	}

	/**
	 * @param bool $updates_enabled
	 *
	 * @return PHPUnit_Framework_MockObject_MockObject|Gravity_Flow
	 */
	public function _get_plugin( $updates_enabled = true ) {
		$plugin = $this->getMockBuilder( 'Gravity_Flow' )
		               ->setMethods( array( 'get_app_setting' ) )
		               ->getMock();
		$plugin->method( 'get_app_setting' )->willReturn( $updates_enabled );

		return $plugin;
	}

}
