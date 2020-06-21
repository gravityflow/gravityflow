<?php

/**
 * Testing Gravity_Flow::has_shortcode_or_block()
 *
 * @group testsuite
 */
class Tests_Gravity_Flow_Has_Shortcode_Or_Block extends GF_UnitTestCase {

	public function test_empty() {
		$this->assertFalse( gravity_flow()->has_shortcode_or_block( '' ) );
	}

	public function test_shortcode() {
		$this->assertFalse( gravity_flow()->has_shortcode_or_block( 'before [gravityforms id=1] after' ) );
		$this->assertTrue( gravity_flow()->has_shortcode_or_block( 'before [gravityflow page=inbox] after' ) );
		$this->assertTrue( gravity_flow()->has_shortcode_or_block( '[GRAVITYFLOW page=inbox]' ) );
	}

	public function test_block() {
		$this->assertFalse( gravity_flow()->has_shortcode_or_block( 'before <!-- wp:gravityforms/form {"formId":"1"} /--> after' ) );
		$this->assertTrue( gravity_flow()->has_shortcode_or_block( 'before <!-- wp:gravityflow/inbox {"actionsColumn":true} /--> after' ) );
	}

	public function test_reusable_block() {
		if ( ! function_exists( 'has_block' ) ) {
			$this->markTestSkipped();
		}

		$id = wp_insert_post( array(
			'post_type'    => 'wp_block',
			'post_content' => '<!-- WP:GRAVITYFLOW/INBOX /-->',
		) );
		$this->assertTrue( gravity_flow()->has_shortcode_or_block( sprintf( '<!-- wp:block {"ref":%d} /-->', $id ) ) );
		$this->assertFalse( gravity_flow()->has_shortcode_or_block( '<!-- wp:block /-->' ) );
		$this->assertFalse( gravity_flow()->has_shortcode_or_block( '<!-- wp:block {"ref":-1} /-->' ) );
	}

}
