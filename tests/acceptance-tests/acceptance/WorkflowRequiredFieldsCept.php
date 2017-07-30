<?php
//$scenario->skip();
/*
 * Test summary: Workflow required fields
 *
 * Test details:
 * - Fill up the fields
 * - Submit form
 * - First approval force adding note
 * - User input note require if In progress
 * - Add second note
 * - Select Complete
 * - Outgoing JSON Webhook
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Workflow required fields' );

// Submit the form
$I->amOnPage( '/workflow-required-fields' );

$I->see( 'Workflow required fields' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_2"]', 'Text' );
$I->fillField( 'textarea[name="input_1"]', 'Pharagraph text' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login as Admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'Workflow required fields' );
$I->see( 'Instructions: please review the values in the fields below and click on the Approve or Reject button' );

$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

$I->waitForText( 'A note is required', 3 );
$I->fillField( 'textarea[name="gravityflow_note"]', 'First note added' );
$I->click( 'button[value=approved]' );

$I->waitForText( 'Editable instructions.', 3 );
$I->seeElement( 'input[name=gravityflow_status]' );
$I->click( 'Update' );

$I->waitForText( 'A note is required', 3 );
$I->fillField( 'textarea[name="gravityflow_note"]', 'Second note added' );
$I->click( 'Update' );
$I->selectOption( 'input[name="gravityflow_status"]', 'Complete' );
$I->click( 'Update' );
$I->waitForText( 'Status: Approved', 3 );
