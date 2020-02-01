<?php
/*
 * Purpose: Test that the inbox link merge tag works in the assignee email.
 */

// @group merge-tags
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the inbox link merge tag works in the assignee email.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0033-inbox-link-merge-tag' );
$I->see( '0033 Inbox Link Merge Tag' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Test the output of {workflow_inbox_link} in the page created from approval step assignee email.
$I->amOnPage( '/0033-assignee-email' );
$I->dontSee( 'Inbox Link: {workflow_inbox_link}' );
$I->see( 'Inbox Link: Inbox' );

// Test that the link token works for the email field assignee.
$I->click( 'Inbox' );
$I->waitForElement( '#gravityflow-inbox', 10 );
$I->scrollTo( [ 'css' => '#gravityflow-inbox' ] );
$I->see( '0033 Inbox Link Merge Tag', "//table[@id='gravityflow-inbox']/tbody/tr[1]/td[2]" );
$I->see( 'Approval', "//table[@id='gravityflow-inbox']/tbody/tr[1]/td[4]" );
$I->click( '0033 Inbox Link Merge Tag' );
$I->waitForText( '0033 Inbox Link Merge Tag : Entry #' );
$I->see( 'Email: inbox@test.test (Pending)' );
