<?php
/*
 * Purpose: Test the field conditional logic on the user input step
 */

// @group step-approval
// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the field conditional logic on the user input step' );


// Submit the form
$I->amOnPage( '/0002-conditional-logic' );

$I->see( '0002 Conditional Logic' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 );
$I->selectOption( 'input[name=input_7]', 'Second Choice' );
$I->dontSeeElement( 'textarea[name=input_15]' );
$I->checkOption( 'input[name=input_13\\.1]' );
$I->seeElement( 'textarea[name=input_15]' );
$I->fillField( 'textarea[name=input_15]', 'Some text' );
$I->scrollTo( [ 'css' => '.gform_page_footer .gform_next_button' ], 20, 50 );
// Next page
$I->click( '.gform_page_footer .gform_next_button' );
$I->waitForElement( 'input[type=submit]', 30, '.gform_page_footer' );
$I->scrollTo( [ 'css' => '.gform_page_footer' ], 20, 50 );
$I->click( '#gform_submit_button_2' );
$I->waitForText( 'Thanks for contacting us!' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( 'Conditional Logic', 3 );
$I->click( 'Conditional Logic' );

// Approve
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );

// Check field conditional logic on the user input step
$I->waitForText( 'Second Section - Second Choice', 3 );
$I->see( 'Second Section - Second Choice' );

$I->seeElement( 'textarea[name=input_15]' );
$I->uncheckOption( 'input[name=input_13\\.1]' );
$I->dontSeeElement( 'textarea[name=input_15]' );
