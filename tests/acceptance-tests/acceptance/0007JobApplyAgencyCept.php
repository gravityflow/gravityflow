<?php
/*
 * Purpose: Test the job apply workflow
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the job apply workflow' );

// Submit the form
$I->amOnPage( '/0007-job-apply-agency' );

$I->see( 'Job Apply Agency' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_3.3"]', 'First' );
$I->fillField( 'input[name="input_3.6"]', 'Last' );
$I->selectOption( 'select[name="input_2"]', 'Female' );
$I->selectOption( 'select[name="input_6"]', '65 or Above' );
$I->selectOption( 'select[name="input_7"]', 'Homemaker' );
$I->selectOption( 'select[name="input_8"]', 'Seasonal' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login as admin2
$I->loginAs( 'admin2', 'admin2' );
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0007 Job Apply Agency' );

// Approve
$I->fillField( 'textarea[name="gravityflow_note"]', 'Added to DB' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Check data
$I->wait(1);
$I->fillField( 'input[name="input_4"]', '0210000000' );
$I->fillField( 'textarea[name="gravityflow_note"]', 'Phone added' );
$I->click( 'Submit' );

// Log out
$I->logOut();

// Login as admin1
$I->loginAs( 'admin1', 'admin1' );
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0007 Job Apply Agency' );

// Check data
$I->fillField( 'input[name="input_5"]', 'example@example.com' );
$I->fillField( 'textarea[name="gravityflow_note"]', 'Email added' );
$I->click( 'Submit' );

// Log out
$I->logOut();

// Login as Admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0007 Job Apply Agency' );

// Approve
$I->fillField( 'textarea[name="gravityflow_note"]', 'Entry approved' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

$I->waitForText( 'Status: Approved', 3 );
