<?php
/**
 * Purpose: Test that the Coupon field value is populated on the User Input step.
 */

if ( ! function_exists( 'gf_coupons' ) ) {
	$scenario->skip( 'Coupons Add-On not active.' );
}

$form_id = GFFormsModel::get_form_id( '0042 Coupon Field' );

$I = new AcceptanceTester( $scenario );

$I->amGoingTo( 'Test that the Coupon field value is populated on the User Input step.' );

$feed_id = gf_coupons()->insert_feed( $form_id, true, array(
	'gravityForm'      => $form_id,
	'couponName'       => '25 Percent Off',
	'couponCode'       => '25OFF',
	'couponAmountType' => 'percentage',
	'couponAmount'     => 25,
	'startDate'        => '',
	'endDate'          => '',
	'usageLimit'       => '',
	'isStackable'      => false,
	'usageCount'       => '',
) );

$I->comment( 'Coupon 25OFF created: #' . $feed_id );

$entry_id = GFAPI::add_entry( array(
	'form_id' => $form_id,
	'1.1'     => 'Product',
	'1.2'     => '$100.00',
	'1.3'     => '1',
	'3'       => '25OFF',
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

$I->assertSame( '25OFF', $I->grabValueFrom( "#gf_coupon_codes_{$form_id}" ) );
$I->assertSame( '100', $I->grabValueFrom( "#gf_total_no_discount_{$form_id}" ) );
$I->assertSame( '{"25OFF":{"amount":25,"name":"25 Percent Off","type":"percentage","code":"25OFF","can_stack":false,"usage_count":0}}', $I->grabValueFrom( "#gf_coupons_{$form_id}" ) );

$I->seeElement( 'tr.gf_coupon_item' );
$I->see( '25 Percent Off', 'span.gf_coupon_name' );
$I->see( '-$25.00', 'span.gf_coupon_discount' );
