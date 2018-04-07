<?php
/*
 * Purpose: Test the two pages on the user input step
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the two pages on the user input step' );

// Submit the form
$I->amOnPage( '/0014-two-pages-rejected' );

$I->makeScreenshot( 'Form loaded.' );

$I->see( '0014 Two Pages Rejected' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->selectOption( 'input[name=input_7]', 'Third Choice' );
$I->scrollTo( [ 'css' => '.gform_page_footer .gform_next_button' ], 20, 50 ); // needed for chromedriver
// Next page
$I->click( '.gform_page_footer .gform_next_button' );
$I->selectOption( 'select[name=input_20]', 'Third Choice' );

$I->makeScreenshot( 'Before form submit.' );

$I->click( 'input[type=submit]' );

$I->makeScreenshot( 'Form submitted.' );

$I->see( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0014 Two Pages Rejected' );

// Reject
$I->click( 'Reject' );

// Complete
$I->waitForText( 'Rejected request (Pending Input)', 3 );

$I->click( 'Submit' );

$I->waitForText( 'Status: Rejected', 3 );
