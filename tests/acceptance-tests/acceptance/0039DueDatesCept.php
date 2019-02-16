<?php
/*
 * Purpose: Test the due date display
 */

// @group step-notification

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the due date highlighting on inbox' );

// Submit the form x 3
$I->amOnPage( '/0039-due-dates' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Validate Due Date Delay Setting
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( 'Due Date Delay 7', 3 );
$I->dontSeeElement( '#gravityflow-inbox tr[style="border-left-color: #dd3333;"]' );

// Proceed to Next Step
$I->click( 'Due Date Delay 7' );
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );
$I->waitForText( 'Entry Approved', 3 );

// Validate Due Date Delay Setting
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( 'Due Date Date Feb 1 2019', 3 );
$I->seeElement( '#gravityflow-inbox tr[style="border-left-color: #dd9933;"]' );

// Proceed to Next Step
$I->click( 'Due Date Date Feb 1 2019' );
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );
$I->waitForText( 'Entry Approved', 3 );

// Validate Due Date Delay Setting
$I->amOnWorkflowPage( 'Inbox' );
$I->waitForText( 'Due Date Date Field', 3 );
$I->seeElement( '#gravityflow-inbox tr[style="border-left-color: #81d742;"]' );

// Complete Workflow
$I->click( 'Due Date Date Field' );
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );
$I->waitForText( 'Entry Approved', 3 );
