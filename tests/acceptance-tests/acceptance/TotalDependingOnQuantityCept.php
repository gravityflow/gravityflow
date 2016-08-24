<?php

/*
 * Test summary: Display Total depending on Quantity
 *
 * Test details:
 * - Fill up fields
 * - Enter Quantity equal 1
 * - Select Shipping method
 * - Go to back-end and deny
 * - Send back for rectification
 * - Aprove by all
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Total depending on Quantity' );

// Submit the form
$I->amOnPage( '/total-depending-on-quantity' );

$I->see( 'Total depending on Quantity' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->fillField( 'textarea[name="input_2"]', 'Some paragraph text' );
$I->selectOption( 'input[name=input_4]', 'Second Choice' );
$I->selectOption( 'select[name="input_5"]', 'Medium' );
$I->fillField( 'input[name="input_6"]', '1' );
$I->selectOption( 'input[name="input_7"]', 'Third Choice' );
$I->see( '$24.00' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Total depending on Quantity' );

// Approve
$I->fillField( 'textarea[name="gravityflow_note"]', 'Change quantity to 2' );
$I->seeElement( 'button[value=rejected]' );
$I->click( 'button[value=rejected]' );

// Change quantity
$I->seeElement( 'input[name="input_6"]' );
$I->fillField( 'input[name="input_6"]', '2' );
$I->click( 'Update' );

// Approve
$I->see( '$44.00' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );