<?php
/*
 * Purpose: Test that the approval link merge tag works in the assignee email.
 */

// @group merge-tags
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the approval link merge tag works in the assignee email.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0029-approve-link-merge-tag' );
$I->see( '0029 Approve Link Merge Tag' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Test the output of {workflow_approve_link} in the page created from approval step assignee email.
$I->amOnPage( '/0029-assignee-email' );
$I->dontSee( 'Approval Link: {workflow_approve_link}' );
$I->see( 'Approval Link: Approve' );

// Test that the link token works for the email field assignee.
$I->click( 'Approve' );

//$I->see( 'Entry Approved' ); // Occurs when the test is run on its own.
//$I->see( "You don't have permission to view this entry." ); // Occurs when multiple tests run.

// Verify that the step was approved.
$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Status' );
$I->click( '0029 Approve Link Merge Tag' );
$I->waitForText( '0029 Approve Link Merge Tag : Entry #' );
$I->see( 'Status: Approved' );
