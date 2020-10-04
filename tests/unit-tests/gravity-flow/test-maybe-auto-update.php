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

	public function test_null() {
		$this->assertNull( $this->_get_plugin()->maybe_auto_update( null, $this->_get_item( array( 'slug' => 'gravityflow-gravityflow' ) ) ) );
	}

	public function test_updates_disabled() {
		$this->assertFalse( $this->_get_plugin()->maybe_auto_update( true, $this->_get_item( array( 'slug' => 'gravityflow-gravityflow' ) ) ) );
	}

	public function test_should_update_true() {
		$plugin = $this->_get_plugin( false );
		$plugin->method( 'should_update_to_version' )->willReturn( true );

		$item = $this->_get_item( array(
			'slug'        => 'gravityflow-gravityflow',
			'new_version' => 'the number',
		) );

		$this->assertTrue( $plugin->maybe_auto_update( true, $item ) );
	}

	public function test_should_update_false() {
		$plugin = $this->_get_plugin( false );
		$plugin->method( 'should_update_to_version' )->willReturn( false );

		$item = $this->_get_item( array(
			'slug'        => 'gravityflow-gravityflow',
			'new_version' => 'number',
		) );

		$this->assertFalse( $plugin->maybe_auto_update( true, $item ) );
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
		               ->setMethods( array( 'is_auto_update_disabled', 'should_update_to_version' ) )
		               ->getMock();
		$plugin->method( 'is_auto_update_disabled' )->willReturn( $updates_disabled );

		return $plugin;
	}

}
