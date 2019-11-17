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

$I->waitForText( '0026 Role Assignee Token', 3 );
$I->see( '0026 Role Assignee Token' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'Single Line Text', 'Test' );
$I->fillField( 'Paragraph', 'Test' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Tests the {workflow_entry_link} merge tag on the page created from the approval step assignee email.
$I->amOnPage( sanitize_title_with_dashes( '0026 Email - Role Assignee - Token - Approval' ) );
$I->see( 'Open Workflow Entry Detail' );
$I->click( 'Open Workflow Entry Detail' );
$I->waitForElement( '#gravityflow-status-box-container' );
$I->click( 'Approve', '.gravityflow-action-buttons' );
$I->waitForElement( '#gravityflow-status-box-container' );
$I->see( 'User Input (Pending Input)', '#gravityflow-status-box-container' );

if ( $scenario->current('env') !== 'win-edge') {
    // Edge current throws an error when attempting to set cookies.
    $I->resetCookie( 'gflow_access_token' );
}
$I->amOnPage( sanitize_title_with_dashes( '0026 Email - Role Assignee - Token - Approval' ) );
$I->see( 'Open Workflow Entry Detail' );
$I->click( 'Open Workflow Entry Detail' );

$I->waitForText( 'Single Line Text', 60, '.gfield_label' );
$I->fillField( 'Single Line Text', 'Test2' );
$I->fillField( 'Paragraph', 'Test2' );
$I->click( 'Submit' );
$I->waitForText( 'Status: Approved', 10 );
$I->see( 'Status: Approved' );
