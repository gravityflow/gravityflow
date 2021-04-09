<?php
/*
 * Purpose: Test that hidden fields work on Workflow Completion
 */

// @group new-tests

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that hidden fields work on Workflow Completion' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0046-hidden-fields-complete-step' );
$I->waitForText( '0046 Hidden Fields Complete Step', 3 );
$I->see( '0046 Hidden Fields Complete Step' );
$I->fillField( 'What is your request for?', 'Step Completion' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Process email assignee Workflow Step
$I->amOnPage( '/0046-assignee-email' );
$I->click( 'Entry' );
$I->waitForText( '0046 Hidden Fields Complete Step : Entry #', 3 );
$I->see( '0046 Hidden Fields Complete Step : Entry #' );
$I->see( 'Email: entry@test.test (Pending)' );
$I->click( 'Approve' );

// Check for the fields display at this step (Step Completion fields)
$I->waitForText( 'Entry Approved', 3 );
$I->see( 'Entry Approved' );
$I->see( 'What is your request for?' );
$I->see( 'Email' );
// Important to ensure the hidden Admin field is not displayed at Completion Step
$I->dontSee( 'Admin Feedback' );
