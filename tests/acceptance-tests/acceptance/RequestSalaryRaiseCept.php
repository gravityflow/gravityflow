<?php

/*
 * Test summary: Test chained (multiple) approvals through a salary rise document
 *
 * Test details:
 * - Enter number
 * - Select department
 * - Fill Number input with wrong value
 * - If validation error appears, entry numeric only
 * - Submit
 * - Login to back-end, go to Inbox
 * - Click on the 'Request salary raise' Workflow
 * - Aprove request
 * - Send request to CEO
 * - Aprove request by CEO
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the approval steps' );

// Submit the form
$I->amOnPage( '/request-salary-raise' );

$I->see( 'Request salary raise' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->fillField( 'input[name=input_1]', '20%' );
$I->selectOption( 'select[name=input_2]', 'Third Choice' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );

// trow number field error
$I->seeElement( 'div.validation_error' );

// fill number field with correct values
$I->fillField( 'input[name=input_1]', '1234' );
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Request salary raise' );

// Approve
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Send to CEO
$I->selectOption( 'select[name=gravityflow_admin_action]', 'send_to_step|7' );
$I->click( 'Apply' );

// Approve
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );
