<?php
/*
 * Purpose: Test the file upload field in the user input step
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the file upload field in the user input step' );

// Submit the form
$I->amOnPage( '/0005-file-upload-user-input' );

$I->see( 'File Upload User Input' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->fillField( 'Single Line Text', 'testing' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 );
$I->click( 'Submit' );

$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'File Upload User Input' );
$I->attachFile( 'input[name=input_2]', 'gravityflow-logo.png' );
$I->click( 'Submit' );
$I->waitForElement( 'div.gravityflow_validation_error', 3 );

$I->selectOption( 'input[name=input_3]', 'Second Choice' );
$I->click( 'Submit' );
$I->waitForText( 'gravityflow-logo.png', 3 );
$I->seeLink( 'gravityflow-logo.png' );
