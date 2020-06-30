<?php
/*
 * Purpose: Test the save progress types
 */

// @group merge-tags
// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the save progress types for user input step' );

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
$I->click( '#gravityflow_save_progress_button' );
$I->waitForText( 'Entry updated - in progress.' );
$I->see( 'Entry updated - in progress.' );
$I->click( '#gravityflow_submit_button' );
$I->waitForText( 'Entry updated and marked complete.', 10 );
$I->see( 'Entry updated and marked complete.' );

// Complete
$I->waitForText( 'Status: Complete' );
$I->see( 'The sky color is Yellow.' );
$I->see( 'Reason: Just an imagination.' );
