<?php
/*
 * Purpose: Test Update User workflow step
 */

// @group new-tests

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the Update User Workflow Step' );

// Submit the form
$I->amOnPage( '/0049-update-user-step' );
$I->see( '0049 Update User Step' );
$I->click( 'select.gfield_select > option:nth-child(3) ' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login as Admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Status
$I->amOnWorkflowPage( 'Status' );
$I->waitForText( '0049 Update User Step', 3 );
$I->see( '0049 Update User Step' );
$I->click( '0049 Update User Step' );

$I->see( 'Update User: User has been updated.' );
$I->see( 'Status: Complete' );

$I->amOnPage( '/wp-admin/users.php' );

// Multi User Field as Workflow Assignee
$I->waitForText( 'account', 3 );
$I->see( 'account' );
