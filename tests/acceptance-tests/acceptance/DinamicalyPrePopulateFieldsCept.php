<?php
/*
 * Test summary: Dynamically pre populate fields
 *
 * Test details:
 * - Dynamically Fill up the fields
 * - Submit form
 * - log in as admin2 and update the quantity
 * - 'Validate inserted values' step: first approval depending on the User Condition (should not be admin)
 * - log in as admin and Approve
 * - Workflow complete
 * - Check if status is Approved
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Dynamically pre populate fields' );

// Submit the form
$I->amOnPage( '/dynamically-pre-populate-fields/?quant=10&prodname=Test product&prodprice=25' );

$I->see( 'Dynamically pre populate fields' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->selectOption( 'input[name="input_3.1"]', 'First Option' );
$I->selectOption( 'input[name="input_3.2"]', 'Second Option' );
$I->see( '$320.00' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver

// Next page
$I->click( '.gform_page_footer .gform_next_button' );
$I->fillField( 'textarea[name="input_6"]', 'Discussion text field.' );
$I->selectOption( 'select[name=input_8]', 'First admin2' );

$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login as admin2
$I->loginAs( 'admin2', 'admin2' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Dynamically pre populate fields' );
$I->fillField( 'input[name="input_2"]', '11' );
$I->fillField( 'textarea[name="gravityflow_note"]', 'Quantity updated!' );
$I->click( 'Update' );

// Log out
$I->amOnPage( '/dynamically-pre-populate-fields' );
$I->click( 'Log out' );

// Login as Admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Dynamically pre populate fields' );

// Approve
$I->fillField( 'textarea[name="gravityflow_note"]', 'Order approved' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );
$I->see( 'Status: Approved' );
