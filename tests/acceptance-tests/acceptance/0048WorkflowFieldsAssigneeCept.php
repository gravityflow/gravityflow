<?php
/*
 * Purpose: Test workflow fields as assignees on Workflow Step
 */

// @group new-tests

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test workflow fields as assignees on Workflow Step' );

// Submit the form
$I->amOnPage( '/0048-workflow-fields-assignee' );
$I->see( '0048 Workflow Fields Assignee' );
$I->click( 'select.gfield_select > option:nth-child(1)' );
$I->click( 'select.gfield_select > option:nth-child(2)' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login as Admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( '0048 Workflow Fields Assignee', 3 );
$I->see( '0048 Workflow Fields Assignee' );
$I->click( '0048 Workflow Fields Assignee' );

// Assignee Field as Workflow Assignee
$I->waitForText( 'Assignee Field Approval (Pending Approval)', 3 );
$I->see( 'Assignee Field Approval (Pending Approval)' );
$I->see( 'User: admin (Pending)' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Multi User Field as Workflow Assignee
$I->waitForText( 'Multi User Field Approval (Pending Approval)', 3 );
$I->see( 'Multi User Field Approval (Pending Approval)' );
$I->see( 'User: admin (Pending)' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Multi User Field as Workflow Assignee (First assignee)
$I->waitForText( 'Multi User Field Approval (Pending Approval)', 3 );
$I->see( 'Multi User Field Approval (Pending Approval)' );
$I->see( 'User: admin (Approved)' );
$I->see( 'User: admin1 admin1 (Pending)' );
// Make sure we're logged out from first assignee
$I->logOut();

// Login with the second assignee
$I->loginAs( 'admin1', 'admin1' );
// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( '0048 Workflow Fields Assignee' );
$I->see( '0048 Workflow Fields Assignee' );
// Approve the entry
$I->click( '0048 Workflow Fields Assignee' );
$I->waitForText( 'Approval (Pending Approval)' );
$I->see( 'Approval (Pending Approval)' );
$I->see( 'User: admin (Approved)' );
$I->see( 'User: admin1 admin1 (Pending)' );
$I->click( 'Approve' );
$I->waitForText( 'Entry Approved' );
$I->see( 'Entry Approved' );
// Make sure we're logged out from second assignee
$I->logOut();

// Log back in to the first assignee
$I->loginAsAdmin();
// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( '0048 Workflow Fields Assignee' );
$I->see( '0048 Workflow Fields Assignee' );
$I->click( '0048 Workflow Fields Assignee' );

// User Field as Workflow Assignee
$I->waitForText( 'User Field Approval (Pending Approval)', 3 );
$I->see( 'User Field Approval (Pending Approval)' );
$I->see( 'User: admin (Pending)' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Role Field as Workflow Assignee
$I->waitForText( 'Role Field Approval (Pending Approval)', 3 );
$I->see( 'Role Field Approval (Pending Approval)' );
$I->see( 'Role: administrator (Pending)' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Workflow Complete
$I->waitForText( 'Entry Approved', 3 );
$I->see( 'Entry Approved' );
$I->see( 'Status: Approved' );
