<?php
/*
 * Purpose: Test the vacation request form with rejected note
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the vacation request form with rejected note' );

// Submit the form
$I->amOnPage( '/0018-vacation-request-reject-note' );
$I->see( '0018 Vacation Request Reject Note' );
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
$I->click( 'Vacation Request' );

// Reject without note
$I->waitForElement( 'button[value=rejected]', 3 );
$I->click( 'button[value=rejected]' );

// Reject with note
$I->waitForElement( 'button[value=rejected]', 3 );
$I->see( 'A note is required' );
$I->fillField( ['name' => 'gravityflow_note'], 'Dates are expired.' );
$I->click( 'button[value=rejected]' );
$I->see( 'Entry Rejected' );