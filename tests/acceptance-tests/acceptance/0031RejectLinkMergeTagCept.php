<?php
/*
 * Purpose: Test that the reject link merge tag works in the assignee email.
 */

// @group merge-tags
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the reject link merge tag works in the assignee email.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0031-reject-link-merge-tag' );
$I->waitForText( '0031 Reject Link Merge Tag', 3 );
$I->see( '0031 Reject Link Merge Tag' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Test the output of {workflow_reject_link} in the page created from approval step assignee email.
$I->amOnPage( '/0031-assignee-email' );
$I->dontSee( 'Reject Link: {workflow_reject_link}' );
$I->see( 'Reject Link: Reject' );

// Test that the link token works for the email field assignee.
$I->click( 'Reject' );

//$I->see( 'Entry Rejected' ); // Occurs when the test is run on its own.
//$I->see( "You don't have permission to view this entry." ); // Occurs when multiple tests run.

// Verify that the workflow was cancelled.
$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Status' );
$I->click( '0031 Reject Link Merge Tag' );
$I->waitForText( '0031 Reject Link Merge Tag : Entry #' );
$I->see( 'Status: Rejected' );
