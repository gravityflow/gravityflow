<?php
/*
 * Test summary: Job Apply Agency - test conditional routing, conditional field editing, revert
 *
 * Test details:
 * - Fill up the fields
 * - Submit form
 * - Log in as admin2
 * - 'Get approval' step - conditional routing to specific user based on filled fields
 * - 'Data validation' step - conditional execution of step if phone/email is empty
 * - Modify phone number (Condition editing of fields)
 * - Log in as admin1
 * - Modify email (Condition editing of fields)
 * - Log in as admin
 * - 'Admin approval' step
 * - Press Revert
 * - 'Data validation' step: Edit phone again and add note
 * - 'Admin approval' step: Final approval
 * - Send notification
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Job Apply Agency' );

// Submit the form
$I->amOnPage( '/job-apply-agency' );

$I->see( 'Job Apply Agency' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_3.3"]', 'First' );
$I->fillField( 'input[name="input_3.6"]', 'Last' );
$I->selectOption( 'select[name="input_2"]', 'Female' );
$I->selectOption( 'select[name="input_6"]', '65 or Above' );
$I->selectOption( 'select[name="input_7"]', 'Homemaker' );
$I->selectOption( 'select[name="input_8"]', 'Seasonal' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login as admin2
$I->loginAs( 'admin2', 'admin2' );
// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Job apply agency' );

// Approve
$I->fillField( 'textarea[name="gravityflow_note"]', 'Added to DB' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Check data
$I->fillField( 'input[name="input_4"]', '0210000000' );
$I->fillField( 'textarea[name="gravityflow_note"]', 'Phone added' );
$I->click( 'Update' );

// Log out
$I->amOnPage( '/specific-assign' );
$I->click( 'Log out' );

// Login as admin1
$I->loginAs( 'admin1', 'admin1' );
// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Job apply agency' );

// Check data
$I->fillField( 'input[name="input_5"]', 'example@example.com' );
$I->fillField( 'textarea[name="gravityflow_note"]', 'Email added' );
$I->click( 'Update' );

// Log out
$I->amOnPage( '/specific-assign' );
$I->click( 'Log out' );

// Login as Admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Job apply agency' );

// Revert
$I->fillField( 'textarea[name="gravityflow_note"]', 'Phone & email recheck' );
$I->seeElement( 'button[value=revert]' );
$I->click( 'button[value=revert]' );

// Log out
$I->amOnPage( '/specific-assign' );
$I->click( 'Log out' );

// Login as admin2
$I->loginAs( 'admin2', 'admin2' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Job apply agency' );

// Check data
$I->fillField( 'input[name="input_4"]', '0210000000' );
$I->fillField( 'textarea[name="gravityflow_note"]', 'Phone rechecked' );
$I->click( 'Update' );

// Log out
$I->amOnPage( '/specific-assign' );
$I->click( 'Log out' );

// Login as admin1
$I->loginAs( 'admin1', 'admin1' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Job apply agency' );

// Check data
$I->fillField( 'input[name="input_5"]', 'example@example.com' );
$I->fillField( 'textarea[name="gravityflow_note"]', 'Email rechecked' );
$I->click( 'Update' );

// Log out
$I->amOnPage( '/specific-assign' );
$I->click( 'Log out' );

// Login as Admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Job apply agency' );

// Approve
$I->fillField( 'textarea[name="gravityflow_note"]', 'Entry approved' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

$I->see( 'Status: Approved' );
