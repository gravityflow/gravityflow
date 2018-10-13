<?php
/*
 * Purpose: Test that the display fields setting displays the correct fields on the workflow detail page.
 */

// @group merge-tags

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the approval link merge tag works in the assignee email.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0028-to-0032-link-merge-tags' );
$I->see( '0028 to 0032 Link Merge Tags' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->click( 'View email merge tags' );

// Test the output of {workflow_approve_link} in the page created from approval step assignee email.
$I->waitForText( '0028 to 0032 MT Links', 3 );
$I->dontSee( 'Approval Link: {workflow_approve_link}' );
$I->see( 'Approval Link: Approve' );

// Test that the link token works for the email field assignee.
$I->click( 'Approve' );
$I->waitForText( 'INBOX', 3 );
//$I->see( 'Entry Approved' ); // Occurs when the test is run on its own.
//$I->see( "You don't have permission to view this entry." ); // Occurs when multiple tests run.

// Verify that the step was approved.
$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Status' );
$I->click( '0028 to 0032 Link Merge Tags' );
$I->waitForText( '0028 to 0032 Link Merge Tags : Entry #' );
$I->see( 'Status: Approved' );
