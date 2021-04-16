<?php
/*
 * Purpose: Test validation of File Upload field for User Input Step
 */

// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test validation of File Upload field for User Input Step' );

// Submit the form
$I->amOnPage( '/0054-file-upload-validation' );

$I->see( '0054 File Upload Validation' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0054 File Upload Validation' );

$I->waitForText( '0054 File Upload Validation : Entry # ', 3 );
$I->see( '0054 File Upload Validation : Entry # ' );
$I->see( 'Enter Text' );
$I->see( 'Add File' );
$I->see( 'User Input (Pending Input)' );
$I->see( 'In progress' );
$I->see( 'Complete' );
$I->click( 'Update' );

$I->dontSee( 'This field is required.' );
$I->see( 'User Input (Pending Input)' );
$I->see( 'In progress' );
$I->selectOption( '//*[@id="gravityflow_complete"]', 'complete' );
$I->click( 'Update' );
$I->see( 'This field is required.' );
$I->see( 'User Input (Pending Input)' );
$I->see( 'In progress' );