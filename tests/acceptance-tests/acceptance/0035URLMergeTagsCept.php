<?php
/*
 * Purpose: Test that the URL merge tags are replaced in the assignee email.
 */

// @group merge-tags
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the URL merge tags are replaced in the assignee email.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0035-url-merge-tags' );
$I->waitForText( '0035 URL Merge Tags', 3 );
$I->see( '0035 URL Merge Tags' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Test the output of the URL merge tags in the page created from approval step assignee email.
$I->amOnPage( '/0035-assignee-email' );

$page_id  = gravity_flow()->get_app_setting( 'inbox_page' );
$base_url = add_query_arg( array( 'page' => 'gravityflow-inbox' ), get_permalink( $page_id ) );

$I->dontSee( 'Approval URL: {workflow_approve_url}' );
$I->see( 'Approval URL: ' . $base_url );

$I->dontSee( 'Cancel URL: {workflow_cancel_url}' );
$I->see( 'Cancel URL: ' . $base_url );

$I->dontSee( 'Reject URL: {workflow_reject_url}' );
$I->see( 'Reject URL: ' . $base_url );

$I->dontSee( 'Entry URL: {workflow_entry_url}' );
$I->see( 'Entry URL: ' . $base_url );

$I->dontSee( 'Inbox URL: {workflow_inbox_url}' );
$I->see( 'Inbox URL: ' . $base_url );
