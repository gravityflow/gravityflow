<?php
/*
 * Purpose: Test for Multi Step User Input
 */

// @group merge-tags
// @group step-approval
// @group emails
// @group new-tests

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test for Multi Step User Input' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

$I->loginAs( 'admin1', 'admin1' );

// Submit the form
$I->amOnPage( '/0051-multi-step-user-input' );
$I->waitForText( '0051 Multi Step User Input', 3 );
$I->see( '0051 Multi Step User Input' );
$I->executeJS( 'jQuery( ".hasDatepicker" ).datepicker( "show" );' );
$I->click( 'td.ui-datepicker-today a' );
$I->fillField( 'Number of Visitors', '2' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Open the assignee Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( '0051 Multi Step User Input' );
$I->see( '0051 Multi Step User Input' );
$I->click( '0051 Multi Step User Input' );
$I->fillField( 'Name', 'Tom' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ] );
$I->click( 'Submit' );

$I->waitForText( 'Entry updated and marked complete.', 3 );
$I->see( 'Entry updated and marked complete.' );
$I->see( 'Admin Approval (Pending Approval)' );
$I->see( 'User: admin (Pending)' );

$I->logOut();
$I->resetCookie( 'gflow_access_token' );

$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( '0051 Multi Step User Input' );
$I->see( '0051 Multi Step User Input' );
$I->click( '0051 Multi Step User Input' );
$I->see( 'Admin Approval (Pending Approval)' );
$I->see( 'User: admin (Pending)' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

$I->waitForText( 'Entry Approved' );
$I->see( 'Entry Approved' );