<?php
/*
 * Purpose: Test that the start and complete settings work
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the start settings' );

// Login as subscriber
$I->loginAs( 'subscriber', 'subscriber' );

// Submit the form
$I->amOnPage( '/0039-start-complete' );


$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 );

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->fillField( 'textarea[name="input_2"]', 'Some paragraph text' );


$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->amOnPage( '/status' );
$I->waitForText( 'Status', 3 );
$I->click( '0039 Start Complete' );
$I->waitForText( 'Some text', 3 );
$I->see( 'Some paragraph text' );
$I->see( 'Your application is currently being processed. You will be notified when the process is complete.' );
$I->dontSee( 'Administrator notes' );


$I->logOut();

// Login to wp-admin
$I->loginAsAdmin();

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( '0039 Start Complete' );
$I->waitForText( 'Approver notes', 3 );

// Complete User Input step
$I->fillField( 'Approver notes', 'Private note that the form submitter must not see.' );
$I->click( 'Submit' );

// Complete Approval step
$I->waitForElement( 'button[value=approved]', 3 );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Log out
$I->logOut();

$I->wantTo( 'Test the complete settings' );

$I->loginAs( 'subscriber', 'subscriber' );

$I->amOnPage( '/status' );
$I->click( '0039 Start Complete' );
$I->waitForText( 'Some text', 3 );
$I->see( 'Some paragraph text' );
$I->see( 'Your application process is complete.' );
$I->dontSee( 'Administrator notes' );
$I->dontSee( 'Private note that the form submitter must not see.' );