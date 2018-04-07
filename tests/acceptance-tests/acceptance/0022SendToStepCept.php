<?php
/*
 * Purpose: Test if "send to step" admin action can work after workflow completed
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test if "send to step" admin action can work after workflow completed' );

$I->amOnPage( '/0022-send-to-step' );

$I->amGoingTo( 'Submit the form.' );
$I->see( '0022 Send To Step' );
$I->scrollTo( [ 'css' => '.gform_title' ] );
$I->fillField( 'Text Field', 'Some text' );
$I->fillField( 'Email Field', 'test@gmail.com' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ] );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!', 3 );

$I->amGoingTo( 'Log into WP Dashboard.' );
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

$I->amGoingTo( 'View Workflow Inbox.' );
$I->amOnWorkflowPage( 'Inbox' );

$I->amGoingTo( 'Approve the entry to complete the Approval Step.' );
$I->click( '0022 Send To Step' );
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );
$I->waitForText( 'Entry Approved', 3 );

$I->amGoingTo( 'Submit the form to complete the User Input Step and also complete the workflow.' );
$I->see( 'User Input (Pending Input)' );
$I->click( 'input[value=Submit]' );
$I->waitForText( 'Entry updated and marked complete.', 3 );

$I->amGoingTo( 'View Workflow Status.' );
$I->amOnWorkflowPage( 'Status' );

$I->amGoingTo( 'Make sure the entry has been approved.' );
$I->click( '0022 Send To Step' );
$I->waitForText( 'Status: Approved', 3 );

$I->amGoingTo( 'Select and admin action that send this entry to Notification Step.' );
$I->selectOption( '//*[@id="gravityflow-admin-action"]', 'Notification Step' );
$I->click( 'input[name=_gravityflow_admin_action]' );
$I->waitForText( 'Sent to step: Notification Step', 3 );
$I->scrollTo( ['css' => '.gravityflow-note-notification'], 20, 50 );
$I->seeNumberOfElements( ['css' => '.gravityflow-note-notification'], 2 );
