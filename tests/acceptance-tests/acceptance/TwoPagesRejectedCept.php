<?php
/*
 * Test summary: two pages form, with rejected approval
 *
 * Test details:
 * - Select Third Choise radio button
 * - Go to step 2
 * - Select Third Choise from select box
 * - Login to back-end, go to Inbox
 * - Click on the 'Two pages rejected' Workflow
 * - Check if Reject button is shown
 * - Click on Reject button
 * -
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the two pages on the user input step' );

// Submit the form
$I->amOnPage( '/two-pages-rejected' );

$I->makeScreenshot( 'Form loaded.' );

$I->see( 'Two pages rejected' );
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
$I->amOnPage( '/wp-admin' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Two pages rejected' );

// Reject
$I->click( 'Reject' );

// Complete
$I->see( 'Status: Rejected' );
