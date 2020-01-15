<?php
/*
 * Purpose: Test that the entry link merge tag works in the assignee email.
 */

// @group merge-tags
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the entry link merge tag works in the assignee email.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0032-entry-link-merge-tag' );
$I->waitForText( '0032 Entry Link Merge Tag', 3 );
$I->see( '0032 Entry Link Merge Tag' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Test the output of {workflow_entry_link} in the page created from approval step assignee email.
$I->amOnPage( '/0032-assignee-email' );
$I->dontSee( 'Entry Link: {workflow_entry_link}' );
$I->see( 'Entry Link: Entry' );

// Test that the link token works for the email field assignee.
$I->click( 'Entry' );
$I->waitForText( '0032 Entry Link Merge Tag : Entry #', 3 );
$I->see( '0032 Entry Link Merge Tag : Entry #' );
$I->see( 'Email: entry@test.test (Pending)' );
