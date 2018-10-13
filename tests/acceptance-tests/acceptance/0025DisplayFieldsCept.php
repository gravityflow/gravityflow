<?php
/*
 * Purpose: Test that the display fields setting displays the correct fields on the workflow detail page.
 */

// @group merge-tags
// @group step-approval
// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the display fields setting displays the correct fields on the workflow detail page.' );

// Submit the form
$I->amOnPage( '/0025-display-fields' );

$I->see( '0025 Display Fields' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->click( 'input[type=submit]' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0025 Display Fields' );

// Approval Step - Display: All Fields.
$I->waitForText( '0025 Display Fields : Entry #', 3 );
$I->see( 'Value 1' );
$I->see( 'Section 1' );
$I->see( 'Value 2' );
$I->see( 'Section 2' );
$I->see( 'Value 3' );
$I->click( 'Approve' );

// User Input Step - Display: All Fields Except
$I->waitForText( 'Entry Approved', 3 );
$I->see( 'Value 1' );
$I->see( 'Section 1' );
$I->seeElement( 'input[name=input_3]' );
$I->fillField( 'Two', 'Value 2 - Updated' );
$I->dontSee( 'Section 2' );
$I->dontSee( 'Value 3' );
$I->click( 'Submit' );

// Approval Step - Display: Selected Fields.
$I->waitForText( 'Entry updated and marked complete.', 3 );
$I->dontSee( 'Value 1' );
$I->dontSee( 'Section 1' );
$I->dontSee( 'Value 2 - Updated' );
$I->see( 'Section 2' );
$I->see( 'Value 3' );
$I->click( 'Approve' );

// Workflow Completed - Display: All Fields.
$I->waitForText( 'Entry Approved', 3 );
$I->see( 'Value 1' );
$I->see( 'Section 1' );
$I->see( 'Value 2 - Updated' );
$I->see( 'Section 2' );
$I->see( 'Value 3' );

// Test the output of {workflow_fields} in the page created from the approval step assignee email.
$I->amOnPage( '/0025-mt-workflow-fields' );
$I->waitForText( '0025-mt-workflow-fields', 3 );
$I->see( 'One' );
$I->see( 'Value 1' );
$I->see( 'Two' );
$I->see( 'Value 2' );
$I->see( 'Three' );
$I->see( 'Value 3' );

// Test the output of {workflow_fields: display=false} in the page created from the user input step assignee email.
$I->amOnPage( '/0025-mt-workflow-fields-editable' );
$I->waitForText( '0025-mt-workflow-fields-editable', 3 );
$I->dontSee( 'One' );
$I->dontSee( 'Value 1' );
$I->see( 'Two' );
$I->see( 'Value 2' );
$I->dontSee( 'Three' );
$I->dontSee( 'Value 3' );

// Test the output of {workflow_fields: editable=false} in the page created from the user input step complete email.
$I->amOnPage( '/0025-mt-workflow-fields-display' );
$I->waitForText( '0025-mt-workflow-fields-display', 3 );
$I->see( 'One' );
$I->see( 'Value 1' );
$I->see( 'Two' );
$I->see( 'Value 2 – Updated' );
$I->dontSee( 'Three' );
$I->dontSee( 'Value 3' );