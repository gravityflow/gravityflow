<?php
/*
 * Purpose: Test for Multiple Assignees on a Workflow Step
 */

// @group new-tests

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test for Multiple Assignees on a Workflow Step' );

// Submit the form
$I->amOnPage( '/0047-multiple-assignees' );
$I->waitForText( '0047 - Multiple Assignees', 3 );
$I->see( '0047 - Multiple Assignees' );
$I->fillField( 'Text', 'Some input data' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin (first assignee)
$I->loginAsAdmin();
// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( '0047 - Multiple Assignees' );
$I->see( '0047 - Multiple Assignees' );
// Approve the entry
$I->click( '0047 - Multiple Assignees' );
$I->waitForText( 'Approval (Pending Approval)' );
$I->see( 'Approval (Pending Approval)' );
$I->see( 'User: admin (Pending)' );
$I->see( 'User: admin1 admin1 (Pending)' );
$I->click( 'Approve' );
$I->waitForText( 'Entry Approved' );
$I->see( 'Entry Approved' );

// Make sure we're logged out from first assignee
$I->logOut();

// Login with the second assignee
$I->loginAs( 'admin1', 'admin1' );
// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( '0047 - Multiple Assignees' );
$I->see( '0047 - Multiple Assignees' );
// Approve the entry
$I->click( '0047 - Multiple Assignees' );
$I->waitForText( 'Approval (Pending Approval)' );
$I->see( 'Approval (Pending Approval)' );
$I->see( 'User: admin (Approved)' );
$I->see( 'User: admin1 admin1 (Pending)' );
$I->click( 'Approve' );
$I->waitForText( 'Entry Approved' );
$I->see( 'Entry Approved' );
$I->see( 'Status: Approved' );

// Make sure we're logged out from first assignee
$I->logOut();

// Log back in to the first assignee
$I->loginAsAdmin();

// Check on Workflow Status that entry has been Approved
$I->amOnPage( '/status' );
$I->waitForText( 'Status', 3 );
$I->click( '0047 - Multiple Assignees' );
$I->waitForText( 'Text', 3 );
$I->see( 'Some input data' );
$I->see( 'Status: Approved' );
