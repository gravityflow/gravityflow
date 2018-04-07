<?php
/*
 * Purpose: Test the vacation request form
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the vacation request form' );

// Submit the form
$I->amOnPage( '/0016-vacation-request' );
$I->see( '0016 Vacation Request' );
$I->scrollTo( [ 'css' => '.gform_title' ] );
$I->fillField('First', 'Some');
$I->fillField('Last', 'Text');
$I->selectOption( 'Dep', 'Third Choice' );
$I->fillField( 'Third choice text', 'Third choice text' );
$I->fillField( 'Date from', '08/17/2016' );
$I->fillField( 'Date to', '08/18/2016' );
$I->fillField( 'Comments', 'Comments text' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ] );
$I->click( 'input[type=submit]' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0016 Vacation Request' );

// Approve
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );
$I->see( 'Entry Approved' );
