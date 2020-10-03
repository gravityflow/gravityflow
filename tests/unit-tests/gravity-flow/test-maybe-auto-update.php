<?php

/**
 * Testing Gravity_Flow::maybe_auto_update()
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Maybe_Auto_Update extends GF_UnitTestCase {

	public function test_no_slug() {
		$this->assertSame( 'test', $this->_get_plugin()->maybe_auto_update( 'test', $this->_get_item() ) );
	}

	public function test_wrong_slug() {
		$this->assertSame( 'test', $this->_get_plugin()->maybe_auto_update( 'test', $this->_get_item( array( 'slug' => 'wrong' ) ) ) );
	}

	public function test_updates_disabled() {
		$this->assertFalse( $this->_get_plugin()->maybe_auto_update( true, $this->_get_item( array( 'slug' => 'gravityflow' ) ) ) );
	}

	/**
	 * @dataProvider data_provider_version_compare
	 *
	 * @param $current_version
	 * @param $new_version
	 * @param $expected
	 */
	public function test_version_compare( $current_version, $new_version, $expected ) {
		$plugin           = $this->_get_plugin( false );
		$plugin->_version = $current_version;

		$item = $this->_get_item( array(
			'slug'        => 'gravityflow',
			'new_version' => $new_version,
		) );

		$this->assertSame( $expected, $plugin->maybe_auto_update( true, $item ) );
	}

	public function data_provider_version_compare() {
		return array(
			// Don't downgrade.
			array( '1.0', '0.9', false ),
			array( '1.2.3', '1.2.2', false ),
			array( '1.2.3.4', '1.2.3', false ),
			array( '2.0', '1.9.9', false ),
			// Don't reinstall.
			array( '1.0', '1.0', false ),
			array( '1.1', '1.1', false ),
			array( '1.2.3', '1.2.3', false ),
			array( '1.2.3.4', '1.2.3.4', false ),
			// Don't update to majors.
			array( '1.0', '1.1', false ),
			array( '1.2.3', '1.3', false ),
			array( '1.2.3.4', '2.0', false ),
			array( '1.2.4-dev-abc123', '2.1', false ),
			// Do update to minors.
			array( '1.2.3', '1.2.4', true ),
			array( '1.2.3.4', '1.2.4', true ),
			array( '1.2.4', '1.2.40', true ),
			// Do update pre-release versions to the final release.
			array( '1.2.4-dev-abc123', '1.2.4', true ),
			array( '1.2-dev-abc123', '1.2', true ),
			array( '2.5-beta-1', '2.5-beta-2', true ),
			array( '2.5-beta-1', '2.5', true ),
			array( '2.5-rc-1', '2.5-rc-2', true ),
			array( '2.5-rc-2', '2.5', true ),
		);
	}

	public function _get_item( $item = array() ) {
		return (object) $item;
	}

	/**
	 * @param bool $updates_disabled
	 *
	 * @return PHPUnit_Framework_MockObject_MockObject|Gravity_Flow
	 */
	public function _get_plugin( $updates_disabled = true ) {
		$plugin = $this->getMockBuilder( 'Gravity_Flow' )
		               ->disableOriginalConstructor()
		               ->setMethods( array( 'is_auto_update_disabled' ) )
		               ->getMock();
		$plugin->method( 'is_auto_update_disabled' )->willReturn( $updates_disabled );

		return $plugin;
	}

}
