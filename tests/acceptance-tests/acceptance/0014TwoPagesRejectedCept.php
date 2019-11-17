<?php
/*
 * Purpose: Test the two pages on the user input step
 */

// @group merge-tags
// @group step-approval
// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the two pages on the user input step' );

// Submit the form
$I->amOnPage( '/0014-two-pages-rejected' );

//$I->makeScreenshot( 'Form loaded.' );

$I->see( '0014 Two Pages Rejected' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->selectOption( 'input[name=input_7]', 'Third Choice' );
$I->scrollTo( [ 'css' => '.gform_page_footer .gform_next_button' ], 20, 50 ); // needed for chromedriver
// Next page
$I->click( '.gform_page_footer .gform_next_button' );
$I->waitForElement( 'select[name=input_20]', 3 );
$I->selectOption( 'select[name=input_20]', 'Third Choice' );

//$I->makeScreenshot( 'Before form submit.' );

$I->click( 'Submit' );

//$I->makeScreenshot( 'Form submitted.' );

$I->waitForText( 'Thanks for contacting us!', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0014 Two Pages Rejected' );

// Reject
$I->waitForText( 'Reject', 3 );
$I->click( 'Reject' );

// Complete
$I->waitForText( 'Rejected request (Pending Input)', 3 );

$I->click( 'Submit' );

$I->waitForText( 'Status: Rejected', 3 );

// Test the output of {workflow_timeline} in the page created from the approval step rejection email.
$I->amOnPage( '/0014-rejection-email-mt-timeline' );
$I->waitForText( '0014-rejection-email-mt-timeline', 3 );
$I->see( 'Entry has been rejected. Timeline merge tag test.' );
$I->dontSee( '{workflow_timeline}' );
$I->see( 'admin:' );
$I->see( 'Approval for two step form: Rejected.' );
$I->see( 'Workflow:' );
$I->see( 'Workflow submitted' );
