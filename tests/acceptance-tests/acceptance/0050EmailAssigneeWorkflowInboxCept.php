<?php
/*
 * Purpose: Test Workflow Inbox for Email Assignee
 */

// @group merge-tags
// @group step-approval
// @group emails
// @group new-tests

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test Workflow Inbox for Email Assignee' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0050-email-assignee-workflow-inbox' );
$I->waitForText( '0050 Email Assignee Workflow Inbox', 3 );
$I->see( '0050 Email Assignee Workflow Inbox' );
$I->fillField( 'Say something', 'Testing the workflow inbox for the assignee email' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Open the assignee Inbox
$I->amOnPage( '/0050-assignee-email' );
$I->waitForText( '0050 Assignee Email', 3 );
$I->see( '0050 Assignee Email' );
$I->click( 'Inbox' );

// Check and open the entry
$I->waitForText( '0050 Email Assignee Workflow Inbox', 3 );
$I->see( '0050 Email Assignee Workflow Inbox' );
$I->click( '0050 Email Assignee Workflow Inbox' );

// Approve the entry
$I->waitForText( 'Email Assignee Approval (Pending Approval)', 3 );
$I->see( 'Email Assignee Approval (Pending Approval)' );
$I->see( 'Email: url@test.test (Pending)' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );
$I->waitForText( 'Entry Approved' );
$I->see( 'Entry Approved' );
$I->see( 'Status: Approved' );
$I->see( 'Email Assignee Approval: Approved.' );

// Open the assignee Inbox again
$I->amOnPage( '/0050-assignee-email' );
$I->waitForText( '0050 Assignee Email', 3 );
$I->see( '0050 Assignee Email' );
$I->click( 'Inbox' );

// confirm that the entry is no longer listed
$I->dontSee( '0050 Email Assignee Workflow Inbox' );