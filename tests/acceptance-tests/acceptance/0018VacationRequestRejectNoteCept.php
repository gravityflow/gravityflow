<?php
/*
 * Purpose: Test the vacation request form with rejected note
 */

// @group merge-tags
// @group step-approval
// @group step-user_input
// @group step-notification

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the vacation request form with rejected note' );

// Submit the form
$I->amOnPage( '/0018-vacation-request-reject-note' );
$I->see( '0018 Vacation Request Reject Note' );
$I->scrollTo( [ 'css' => '.gform_title' ] );
$I->fillField('First', 'Some');
$I->fillField('Last', 'Text');
$I->selectOption( 'Dep', 'Third Choice' );
$I->fillField( 'Third choice text', 'Third choice text' );
$I->appendField( 'Date from', '08/17/2016' );
$I->appendField( 'Date to', '08/18/2016' );
$I->executeJS( 'return jQuery( ".datepicker" ).datepicker( "hide" );' );
$I->fillField( 'Comments', 'Comments text' );
$I->scrollTo( [ 'css' => '.gform_footer' ] );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( ['link' => '0018 Vacation Request Reject Note' ] );

// Reject without note
$I->waitForElement( 'button[value=rejected]', 3 );
$I->click( 'button[value=rejected]' );

// Reject with note
$I->waitForText( 'A note is required', 3 );
$I->see( 'A note is required' );
$I->fillField( ['name' => 'gravityflow_note'], 'Dates are expired.' );
$I->click( 'button[value=rejected]' );
$I->waitForText( 'Entry Rejected', 3 );

// Test the output of {workflow_note} in the page created from the user input step assignee email.
$I->amOnPage( '/0018-assignee-email-mt-note' );
$I->waitForText( '0018-assignee-email-mt-note', 3 );
$I->see( 'Please review the dates.' );
$I->see( 'Notes from manager:' );
$I->dontSee( '{workflow_note}' );
$I->see( 'Dates are expired.' );
