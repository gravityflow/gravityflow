<?php
/**
 * Purpose: Test that the Coupon usage count is updated by the User Input step.
 */

if ( ! function_exists( 'gf_coupons' ) ) {
	$scenario->skip( 'Coupons Add-On not active.' );
}

$form_id = GFFormsModel::get_form_id( '0042 Coupon Field' );

$I = new AcceptanceTester( $scenario );

$I->amGoingTo( 'Test that the Coupon usage count is updated by the User Input step' );

$feed_id_1 = gf_coupons()->insert_feed( $form_id, true, array(
	'gravityForm'      => $form_id,
	'couponName'       => 'First',
	'couponCode'       => 'ONE',
	'couponAmountType' => 'percentage',
	'couponAmount'     => 25,
	'startDate'        => '',
	'endDate'          => '',
	'usageLimit'       => '',
	'isStackable'      => false,
	'usageCount'       => '1',
) );

$I->comment( 'Coupon ONE created: #' . $feed_id_1 );

$feed_id_2 = gf_coupons()->insert_feed( $form_id, true, array(
	'gravityForm'      => $form_id,
	'couponName'       => 'Second',
	'couponCode'       => 'TWO',
	'couponAmountType' => 'percentage',
	'couponAmount'     => 50,
	'startDate'        => '',
	'endDate'          => '',
	'usageLimit'       => '',
	'isStackable'      => false,
	'usageCount'       => '',
) );

$I->comment( 'Coupon TWO created: #' . $feed_id_2 );

$entry_id = GFAPI::add_entry( array(
	'form_id' => $form_id,
	'1.1'     => 'Product',
	'1.2'     => '$100.00',
	'1.3'     => '1',
	'3'       => 'ONE',
	'2'       => '75',
) );

$I->comment( 'Entry created: #' . $entry_id );

$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );

$I->see( '0042 Coupon Field' );
$I->see( $entry_id, 'table.gravityflow-inbox td[data-label="ID"]' );
$I->click( $entry_id, 'table.gravityflow-inbox td[data-label="ID"]' );
$I->waitForText( '0042 Coupon Field : Entry # ' . $entry_id, 3 );

$I->seeElement( 'input.gf_coupon_code' );
$I->seeElement( 'tr.gf_coupon_item' );
$I->see( 'First', 'span.gf_coupon_name' );
$I->see( '-$25.00', 'span.gf_coupon_discount' );

$I->click( '(x)', '#gf_coupon_ONE' );
$I->waitForElementNotVisible( '#gf_coupon_ONE', 3 );

$I->fillField( 'input.gf_coupon_code', 'TWO' );
$I->click( 'Apply' );

$I->waitForElement( 'tr.gf_coupon_item', 3 );
$I->see( 'Second', 'span.gf_coupon_name' );
$I->see( '-$50.00', 'span.gf_coupon_discount' );

$I->click( 'Submit' );
$I->waitForText( 'Entry updated and marked complete.', 3 );

$I->comment( 'Checking coupon ONE usageCount.' );
$feed_1 = gf_coupons()->get_feed( $feed_id_1 );
$I->assertSame( 0, $feed_1['meta']['usageCount'] );

$I->comment( 'Checking coupon TWO usageCount.' );
$feed_2 = gf_coupons()->get_feed( $feed_id_2 );
$I->assertSame( 1, $feed_2['meta']['usageCount'] );
