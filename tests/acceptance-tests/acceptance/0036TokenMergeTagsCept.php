<?php
/*
 * Purpose: Test that the token merge tags are replaced in the assignee email.
 */

// @group merge-tags
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the token merge tags are replaced in the assignee email.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0036-token-merge-tags' );
$I->waitForText( '0036 Token Merge Tags', 3 );
$I->see( '0036 Token Merge Tags' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Test the output of the URL merge tags in the page created from approval step assignee email.
$I->amOnPage( '/0036-assignee-email' );

$I->dontSee( 'Approve Token: {workflow_approve_token}' );
$I->see( 'Approve Token:' );
$approve_token = $I->grabTextFrom( '.approve-token' );
$I->assertNotEmpty( $approve_token );

$I->dontSee( 'Reject Token: {workflow_reject_token}' );
$I->see( 'Reject Token:' );
$reject_token = $I->grabTextFrom( '.reject-token' );
$I->assertNotEmpty( $reject_token );
