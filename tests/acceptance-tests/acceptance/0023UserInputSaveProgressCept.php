<?php
/*
 * Purpose: Test the save progress types
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the save progress types for user input step' );

// Submit the form
$I->amOnPage( '/0023-user-input-save-progress' );

$I->see( '0023 - User Input Save Progress' );
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

// Test - Submit Buttons
$I->click( '0023 - User Input Save Progress' );
$I->waitForText( 'Save Progress - Submit Buttons (Pending Input)' );
$I->see( 'Save Progress - Submit Buttons (Pending Input)' );
$I->click( '#gravityflow_save_progress_button' );
$I->waitForText( 'Entry updated - in progress.' );
$I->see( 'Entry updated - in progress.' );
$I->click( '#gravityflow_submit_button' );
$I->waitForText( 'Entry updated and marked complete.' );
$I->see( 'Entry updated and marked complete.' );

// Test - Radio Buttons - In Progress
$I->waitForText( 'Save Progress - Radio Buttons - In Progress Default (Pending Input)' );
$I->see( 'Save Progress - Radio Buttons - In Progress Default (Pending Input)' );
$I->seeOptionIsSelected( 'input[name=gravityflow_status]', 'in_progress' );
$I->click( '#gravityflow_update_button' );
$I->waitForText( 'Entry updated - in progress.' );
$I->see( 'Entry updated - in progress.' );
$I->click( 'input#gravityflow_complete' );
$I->click( '#gravityflow_update_button' );
$I->waitForText( 'Entry updated and marked complete.' );
$I->see( 'Entry updated and marked complete.' );

// Test - Radio Buttons - Complete
$I->waitForText( 'Save Progress - Radio Buttons - Complete Default (Pending Input)' );
$I->see( 'Save Progress - Radio Buttons - Complete Default (Pending Input)' );
$I->seeOptionIsSelected( 'input[name=gravityflow_status]', 'complete' );
$I->click( '#gravityflow_update_button' );
$I->waitForText( 'Entry updated and marked complete.' );
$I->see( 'Entry updated and marked complete.' );

// Test - Disabled
$I->waitForText( 'Save Progress - Disabled (Pending Input)' );
$I->see( 'Save Progress - Disabled (Pending Input)' );
$I->dontSeeElement( 'input[name=gravityflow_status]' );
$I->click( '#gravityflow_update_button' );
$I->waitForText( 'Entry updated and marked complete.' );
$I->see( 'Entry updated and marked complete.' );
