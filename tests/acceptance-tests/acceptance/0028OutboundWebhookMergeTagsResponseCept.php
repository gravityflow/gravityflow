<?php
/*
 * Purpose: Test that the outbound webhook step handles merge tag response mapping.
 */

// @group step-webhook
// @group step-approval

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the Outbound Webhook step handles response mapping.' );

// Submit the form
$I->amOnPage( '/0028-outgoing-webhook-merge-tags' );

$I->see( '0028 Outgoing Webhook Merge Tags' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_17.3"]', 'Joe' );
$I->fillField( 'input[name="input_17.6"]', 'Buck' );
$I->fillField( 'input[name="input_4"]', 'Buckyballs' );
$I->fillField( 'Tell us about your brand', 'Joe Buck has a long history of calling baseball and football games on television. Now he wants to diversify to sell the sports balls - Buckyballs!' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->loginAsAdmin();

$I->amOnWorkflowPage( 'Status' );
$I->waitForText( '0028 Outgoing Webhook Merge Tags', 3 );
$I->click( '0028 Outgoing Webhook Merge Tags' );

$I->waitForText( 'Status: Approved', 3 );
$I->see( '1200' );

$I->see( 'http://unit-test-webhook.com/0028-mapping-merge?currency=CAD. RESPONSE: 200 Accepted (Success)' );
