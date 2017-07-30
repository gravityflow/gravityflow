<?php
//$scenario->skip();
/*
 * Test summary: submit vacation request then approve or reject it
 *
 * Test details:
 * - Fill in the form fields: Firs and Last name, Department (drop down), Extra text field, Start and End dates, Comments
 * - Login to back-end, go to Inbox
 * - Click on the 'Vacation Request' Workflow
 * - Check if Approve button is shown
 * - Click on Approve button
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the vacation request form' );

// Submit the form
$I->amOnPage( '/vacation-request' );
$I->see( 'Vacation Request' );
$I->scrollTo( [ 'css' => '.gform_title' ] );
$I->fillField('First', 'Some');
$I->fillField('Last', 'Text');
$I->selectOption( 'Dep', 'Third Choice' );
$I->fillField( 'Third choice text', 'Third choice text' );
$I->fillField( 'Date from', '08/17/2016' );
$I->fillField( 'Date to', '08/18/2016' );
$I->fillField( 'Comments', 'Comments text' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ] );
$I->click( 'input[type=submit]' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'Vacation Request' );

// Approve
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );
