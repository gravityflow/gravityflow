<?php
/**
 * Purpose: Test that the Coupon field functions on the User Input step when other pricing fields are not present.
 */

if ( ! function_exists( 'gf_coupons' ) ) {
	$scenario->skip( 'Coupons Add-On not active.' );
}

$form_id = GFFormsModel::get_form_id( '0042 Coupon Field' );

gf_coupons()->insert_feed( $form_id, true, array(
	'gravityForm'      => $form_id,
	'couponName'       => '50 Percent Off',
	'couponCode'       => '50OFF',
	'couponAmountType' => 'percentage',
	'couponAmount'     => 50,
	'startDate'        => '',
	'endDate'          => '',
	'usageLimit'       => '',
	'isStackable'      => false,
	'usageCount'       => '',
) );

$I = new AcceptanceTester( $scenario );

$I->amGoingTo( 'Test that the Coupon field functions on the User Input step when other pricing fields are not present.' );

$entry_id = GFAPI::add_entry( array(
	'form_id' => $form_id,
	'1.1'     => 'Product',
	'1.2'     => '$100.00',
	'1.3'     => '1',
	'3'       => '',
	'2'       => '100',
) );

$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );

$I->click( '0042 Coupon Field' );
$I->waitForText( '0042 Coupon Field : Entry # ' . $entry_id, 3 );

$I->seeElement( 'input.gf_coupon_code' );
$I->fillField( 'input.gf_coupon_code', '50OFF' );
$I->click( 'Apply' );

$I->waitForElement( 'tr.gf_coupon_item', 3 );
$I->see( '50 Percent Off', 'span.gf_coupon_name' );
$I->see( '-$50.00', 'span.gf_coupon_discount' );

$I->click( 'Submit' );

$I->waitForText( 'Entry updated and marked complete.', 3 );
$I->see( '50OFF', 'td.entry-view-field-value' );
$I->see( '$50.00', 'td.entry-view-field-value' );

$I->scrollTo( 'table.entry-products' );
$I->see( 'Coupon Code: 50OFF', '.product_options li' );
$I->see( '$50.00', 'td.grandtotal_amount' );
