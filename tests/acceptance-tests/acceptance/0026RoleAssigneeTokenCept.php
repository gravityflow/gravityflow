<?php
/*
 * Purpose: Test that the display fields setting displays the correct fields on the workflow detail page.
 */

// @group merge-tags
// @group step-approval
// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the Role Assignee Token works.' );

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0026-role-assignee-token' );

$I->see( '0026 Role Assignee Token' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'Single Line Text', 'Test' );
$I->fillField( 'Paragraph', 'Test' );
$I->click( 'input[type=submit]' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Tests the {workflow_entry_link} merge tag on the page created from the approval step assignee email.
$I->amOnPage( sanitize_title_with_dashes( '0026 Email - Role Assignee - Token - Approval' ) );
$I->see( 'Open Workflow Entry Detail' );
$I->click( 'Open Workflow Entry Detail' );
$I->waitForText( 'Approve', 3 );
$I->click( 'Approve' );

$I->waitForText( 'User Input (Pending Input)', 3 );

$I->resetCookie( 'gflow_access_token' );
$I->amOnPage( sanitize_title_with_dashes( '0026 Email - Role Assignee - Token - Approval' ) );
$I->see( 'Open Workflow Entry Detail' );
$I->click( 'Open Workflow Entry Detail' );

$I->fillField( 'Single Line Text', 'Test2' );
$I->fillField( 'Paragraph', 'Test2' );
$I->click( 'Submit' );

$I->waitForText( 'Status: Approved', 3 );
$I->see( 'Status: Approved' );
