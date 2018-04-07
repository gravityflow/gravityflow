<?php
/*
 * Purpose: Test the field conditional logic on the user input step
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the field conditional logic on the user input step' );


// Submit the form
$I->amOnPage( '/0002-conditional-logic' );

$I->see( 'Conditional Logic' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->selectOption( 'input[name=input_7]', 'Second Choice' );
$I->dontSeeElement( 'textarea[name=input_15]' );
$I->checkOption( 'input[name=input_13\\.1]' );
$I->seeElement( 'textarea[name=input_15]' );
$I->fillField( 'textarea[name=input_15]', 'Some text' );
$I->scrollTo( [ 'css' => '.gform_page_footer .gform_next_button' ], 20, 50 ); // needed for chromedriver
// Next page
$I->click( '.gform_page_footer .gform_next_button' );
$I->click( 'input[type=submit]' );
$I->waitForText( 'Thanks for contacting us!', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'Conditional Logic' );

// Approve
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );

// Check field conditional logic on the user input step
$I->waitForText( 'Some text', 3 );
$I->see( 'Second Section - Second Choice' );

$I->seeElement( 'textarea[name=input_15]' );
$I->uncheckOption( 'input[name=input_13\\.1]' );
$I->dontSeeElement( 'textarea[name=input_15]' );
