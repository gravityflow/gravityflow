<?php
/*
 * Purpose: Test that the start and complete settings work
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the workflow start condition - condition is not met' );

// Login as subscriber
$I->loginAs( 'subscriber', 'subscriber' );

// Submit the form
$I->amOnPage( '/0040-start-condition' );


$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 );

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->fillField( 'textarea[name="input_2"]', 'Some paragraph text' );


$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->amOnPage( '/status' );
$I->waitForText( 'Status', 3 );
$I->click( '0040 Start Condition' );
$I->waitForText( 'Some text', 3 );
$I->see( 'Some paragraph text' );
$I->dontSee( 'Status: Pending' );
$I->see( 'Status: Complete' );


// Log out
$I->logOut();

$I->wantTo( 'Test the workflow start condition - condition is met' );

$I->loginAs( 'subscriber2', 'subscriber2' );

// Submit the form
$I->amOnPage( '/0040-start-condition' );


$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 );

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->fillField( 'textarea[name="input_2"]', 'all systems go!' );


$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->amOnPage( '/status' );
$I->waitForText( 'Status', 3 );
$I->click( '0040 Start Condition' );
$I->waitForText( 'Some text', 3 );
$I->see( 'all systems go!' );
$I->see( 'Status: Pending' );
$I->dontSee( 'Status: Complete' );


$I->logOut();

// Login to wp-admin
$I->loginAsAdmin();

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( '0040 Start Condition' );

// Complete Approval step
$I->waitForElement( 'button[value=approved]', 3 );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );


$I->logOut();

$I->loginAs( 'subscriber2', 'subscriber2', 3 );

$I->amOnPage( '/status' );
$I->waitForText( 'Status', 3 );
$I->click( '0040 Start Condition' );
$I->waitForText( 'Some text', 3 );
$I->see( 'all systems go!' );
$I->dontSee( 'Status: Pending' );
$I->see( 'Status: Approved' );