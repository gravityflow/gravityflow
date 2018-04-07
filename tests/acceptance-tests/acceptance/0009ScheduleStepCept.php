<?php
/*
 * Purpose: Test scheduling a step
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test scheduling a step' );

// Submit the form
$I->amOnPage( '/0009-schedule-step' );

$I->waitForText( 'Schedule Step', 3 );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->fillField( 'textarea[name="input_2"]', 'Some paragraph text' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Status
$I->amOnWorkflowPage( 'Status' );
$I->waitForText( 'Schedule Step', 3 );
$I->click( '0009 Schedule Step' );
$I->waitForText( 'Send schedule notification (Queued)', 3 );
