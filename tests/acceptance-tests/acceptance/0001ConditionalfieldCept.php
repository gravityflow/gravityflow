<?php
/*
 * Purpose: Test the field conditional logic and the numeric input validation
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the field conditional logic and the numeric input validation' );

// Submit the form
$I->amOnPage( '/0001-conditional-field' );

$I->see( 'Conditional Field' );
$I->scrollTo( [ 'css' => '.gform_title' ] ); // needed for chromedriver
$I->selectOption( 'input[name=input_1]', 'Second Choice' );
$I->seeElement('select[name="input_3"]');
$I->selectOption( 'select[name=input_3]', 'Second Choice' );
$I->seeElement('input[name="input_4"]');
$I->fillField( 'input[name=input_4]', '123NAN' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ] ); // needed for chromedriver
$I->click( 'Submit' );

// trow number field error
$I->waitForElement( 'div.validation_error', 3 );

// fill number field with correct values
$I->fillField( 'input[name=input_4]', '1234' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();

// Go to Status
$I->amOnWorkflowPage( 'Status' );
$I->click( 'Conditional Field' );
$I->waitForText( 'Status: Complete', 3 );
