<?php
/*
 * Purpose: Test that order summary does not display at Workflow end, if disabled on Complete Step Settings.
 */

// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that order summary does not display at Workflow end, if disabled on Complete Step Settings.' );

// Submit the form
$I->amOnPage( '/0052-summary-complete-step' );

$I->see( '0052 Summary Complete Step' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0052 Summary Complete Step' );

$I->waitForText( 'Checkbox', 3 );

$I->dontSee( 'First Number' );

$I->see( 'Order' );
$I->dontSee( 'First Product' );
$I->see( 'Second Product' );
$I->see( '$50.00' );

$I->click( 'Submit' );
$I->waitForText( 'Entry updated and marked complete.', 3 );

$I->see( 'Checkbox' );
$I->see( 'Second Choice' );
$I->dontSee( 'First Number' );

$I->dontSee( 'Order' );
$I->dontSee( 'First Product' );
$I->dontSee( 'Second Product' );
$I->dontSee( '$50.00' );