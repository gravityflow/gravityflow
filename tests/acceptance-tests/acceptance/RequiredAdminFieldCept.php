<?php
//$scenario->skip();


$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the field conditional logic on the user input step' );


// Submit the form
$I->amOnPage( '/user-input' );

$I->see( 'User Input' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->fillField( 'Paragraph', 'Some text' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
// Next page
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'User Input' );

$I->selectOption( 'input[name=gravityflow_status]', 'complete' );
$I->click( 'Update' );
// Approve
$I->waitForElement( 'div.validation_message', 3 );

// Check field conditional logic on the user input step
$I->see( 'This field is required' );
$I->selectOption( 'input[name=input_13]', 'Second Choice' );
$I->scrollTo( '#gravityflow-note' );
$I->selectOption( 'input[name=gravityflow_status]', 'complete' );
$I->click( 'Update' );
$I->waitForElement( 'div.notice-success', 3 );
$I->waitForText( 'Entry updated and marked complete', 3 );
