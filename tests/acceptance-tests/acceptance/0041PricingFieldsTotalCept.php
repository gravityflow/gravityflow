<?php
/*
 * Purpose: Tests that values of non-editable pricing fields can be accessed by the GF Total field calculations on the user input step.
 */

// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Tests that values of non-editable pricing fields can be accessed by the GF Total field calculations on the user input step.');

$I->amOnPage( '/0041-pricing-fields' );

$I->fillField( 'input[name="input_1.3"]', '1' );
$I->fillField( 'input[name="input_4"]', '100' );
$I->selectOption( 'select[name="input_10"]', 'Second Option' );

$I->scrollTo( [ 'css' => '.gform_footer' ] );
$I->assertSame( '290', $I->grabValueFrom( 'input[name="input_14"]' ) );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0041 Pricing Fields' );

// Confirm that the GF pricing scripts can access the field values by testing the total field value has been populated.
$I->waitForText( '0041 Pricing Fields : Entry #', 3 );
$I->see( '$290.00' );
$I->assertSame( '290', $I->grabValueFrom( 'input[name="input_14"]' ) );
