<?php
/*
 * Purpose: Test that merge tag on User Input Step evaluates with the updated values
 */

// @group merge-tags
// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that merge tag on User Input Step evaluates with the updated values' );

// Submit the form
$I->amOnPage( '/0045-user-input-confirmation-message' );

$I->see( '0045 - User Input Confirmation Message' );
$I->scrollTo( [ 'css' => '.gform_title' ] ); // needed for chromedriver
$I->selectOption( 'input[name=input_1]', 'Blue' );
$I->fillField( 'textarea[name="input_2"]', 'Ozone tints the light of the sun' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ] ); // needed for chromedriver
$I->click( 'Submit' );

$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );

// Test - Radio Button Update
$I->click( '0045 - User Input Confirmation Message' );
$I->waitForText( 'User Input (Pending Input)' );
$I->see( 'User Input (Pending Input)' );
$I->selectOption( 'input[name=input_1]', 'Yellow' );
$I->fillField( 'textarea[name="input_2"]', 'Just an imagination' );
$I->click( '#gravityflow_update_button' );
$I->waitForText( 'Entry updated and marked complete.', 10 );
$I->see( 'Entry updated and marked complete.' );

// Complete, check result of Merge Tag
$I->waitForText( 'Status: Complete' );
$I->dontSee( 'The sky color is Blue.' );
$I->see( 'The sky color is Yellow.' );
$I->dontSee( 'Reason: Ozone tints the light of the sun' );
$I->see( 'Reason: Just an imagination.' );  