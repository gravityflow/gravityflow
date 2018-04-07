<?php
/*
 * Purpose: Test revert user
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test Revert User' );

// Submit the form
$I->amOnPage( '/0012-test-revert-user' );

$I->see( 'Test Revert User' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->selectOption( 'select[name="input_2"]', 'Second Choice' );
// Next page
$I->click( '.gform_page_footer .gform_next_button' );
$I->fillField( 'input[name="input_4"]', '42' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0012 Test Revert User' );

// Approve
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );

// Verify number
$I->seeElement( 'input[name="input_4"]' );
$I->fillField( 'input[name="input_4"]', '40' );
$I->click( 'Submit' );

// Revert
$I->waitForElement( 'button[value=revert]', 3 );
$I->click( 'button[value=revert]' );

// Update number
$I->waitForElement( 'input[name="input_4"]', 3 );
$I->fillField( 'input[name="input_4"]', '41' );
$I->click( 'Submit' );

// Approve
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );
$I->see( 'Entry Approved' );
