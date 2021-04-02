<?php
/*
 * Purpose: Test Conditional HTML Display on Workflow Detail
 */

// @group step-approval
// @group new-tests

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test Conditional HTML Display on Workflow Detail' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form (to display HTML Block)
$I->amOnPage( '/0053-conditional-html-workflow-detail' );
$I->waitForText( '0053 Conditional HTML Workflow Detail', 3 );
$I->see( '0053 Conditional HTML Workflow Detail' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox and process Workflow
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0053 Conditional HTML Workflow Detail' );

$I->waitForText( 'This is an HTML Block', 3 );
$I->see( 'This is an HTML Block' );
$I->see( 'Apple' );
$I->click( 'Approve' );

$I->waitForText( 'Entry Approved' );
$I->see( 'Entry Approved' );

$I->see( 'This is an HTML Block' );
$I->see( 'Apple' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form (to not display HTML Block)
$I->amOnPage( '/0053-conditional-html-workflow-detail' );
$I->waitForText( '0053 Conditional HTML Workflow Detail', 3 );
$I->see( '0053 Conditional HTML Workflow Detail' );
$I->fillField( 'Enter Text', 'Orange' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox and process Workflow
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0053 Conditional HTML Workflow Detail' );

$I->waitForText( 'Orange', 3 );
$I->see( 'Orange' );
$I->dontSee( 'This is an HTML Block' );
$I->click( 'Approve' );

$I->waitForText( 'Entry Approved' );
$I->see( 'Entry Approved' );

$I->dontSee( 'This is an HTML Block' );
$I->see( 'Orange' );