<?php
/*
 * Purpose: Test that the cancel link merge tag works in the assignee email.
 */

// @group merge-tags
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the cancel link merge tag works in the assignee email.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0030-cancel-link-merge-tag' );
$I->see( '0030 Cancel Link Merge Tag' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Test the output of {workflow_cancel_link} in the page created from approval step assignee email.
$I->amOnPage( '/0030-assignee-email' );
$I->dontSee( 'Cancel Link: {workflow_cancel_link}' );
$I->see( 'Cancel Link: Cancel Workflow' );

// Test that the link token works for the email field assignee.
$I->click( 'Cancel Workflow' );

//$I->see( 'Workflow Cancelled' ); // Occurs when the test is run on its own.
//$I->see( "You don't have permission to view this entry." ); // Occurs when multiple tests run.

// Verify that the workflow was cancelled.
$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Status' );
$I->waitForText( '0030 Cancel Link Merge Tag', 3 );

$I->click( '0030 Cancel Link Merge Tag' );
$I->waitForText( '0030 Cancel Link Merge Tag : Entry #', 3 );
$I->see( 'Status: Cancelled' );
