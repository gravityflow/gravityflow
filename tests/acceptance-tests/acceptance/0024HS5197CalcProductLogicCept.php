<?php
/*
 * Purpose: Test that calculated product fields hidden by conditional logic do not reappear in the order summary after the user input step saves the entry.
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that calculated product fields hidden by conditional logic do not reappear in the order summary after the user input step saves the entry.' );

// Submit the form
$I->amOnPage( '/0024-hs5197-calculated-product-logic' );

$I->see( '0024 HS5197 Calculated Product Logic' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->click( 'input[type=submit]' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0024 HS5197 Calculated Product Logic' );

$I->waitForText( 'Checkbox', 3 );

$I->dontSee( 'First Number' );

$I->see( 'Order' );
$I->dontSee( 'First Product' );
$I->see( 'Second Product' );
$I->see( '$50.00' );

$I->click( 'Submit' );
$I->waitForText( 'Entry updated and marked complete.', 3 );

$I->see( 'Checkbox' );
$I->see( 'Second Choice' );
$I->dontSee( 'First Number' );

$I->see( 'Order' );
$I->dontSee( 'First Product' );
$I->dontSee( '$0.00' );
$I->see( 'Second Product' );
$I->see( '$50.00' );