<?php
/*
 * Purpose: Test the section field conditional logic on the user input step
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the section field conditional logic on the user input step' );

// Submit the form
$I->amOnPage( '/0021-hs4878-user-input-section-logic' );

$I->see( '0021 HS4878 User Input Section Logic' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->checkOption( 'input[name=input_1\\.2]' );
$I->click( 'input[type=submit]' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'HS4878 User Input Section Logic' );

$I->waitForText( 'Second Choice', 3 );

$I->dontSee( 'Hidden Section' );
$I->dontSeeElement( 'input[name=input_3]' );

$I->see( 'Visible Section' );
$I->seeElement( 'input[name=input_5]' );
$I->fillField( 'Text Two', 'Value Two' );

$I->click( 'Submit' );
$I->waitForText( 'Entry updated and marked complete.', 3 );

$I->dontSee( 'Hidden Section' );
$I->dontSee( 'Value One' );
$I->see( 'Visible Section' );
$I->see( 'Value Two' );