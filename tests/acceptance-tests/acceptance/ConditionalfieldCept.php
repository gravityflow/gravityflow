<?php
/*
 * Test summary: conditional field & numeric validation
 *
 * Test details:
 * - Select Second Choice radio button
 * - Check for Select box
 * - Select Second Choice select box
 * - Check for Number field
 * - Number field appears
 * - Fill Number input with wrong value
 * - If validation error appears, entry numeric only
 * - Submit
 * - Login to back-end, go to Status
 * - Click on the 'Conditional field' Workflow
 * - See if Status is Completed
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the field conditional logic and the numeric input validation' );

// Submit the form
$I->amOnPage( '/conditional-field' );

$I->see( 'Conditional field' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->selectOption( 'input[name=input_1]', 'Second Choice' );
$I->seeElement('select[name="input_3"]');
$I->selectOption( 'select[name=input_3]', 'Second Choice' );
$I->seeElement('input[name="input_4"]');
$I->fillField( 'input[name=input_4]', '123NAN' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );

// trow number field error
$I->seeElement( 'div.validation_error' );

// fill number field with correct values
$I->fillField( 'input[name=input_4]', '1234' );
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Go to Status
$I->click( 'Workflow' );
$I->click( 'Status' );
$I->see( 'Workflow Status' );
$I->click( 'Conditional field' );
$I->see( 'Status: Complete' );
