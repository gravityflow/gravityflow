<?php
//$scenario->skip();
/*
 * Test summary: perform step if previous step status is rejected
 *
 * Test details:
 * - Fill in the form fields.
 * - Login to back-end, go to Inbox
 * - Click on the 'Step Status Conditional Logic' Workflow
 * - Click on the 'Reject' button
 * - Go to 'Approval if Previous Approval Step Rejected' step
 * - Click on the 'Approve' button
 */

$I = new AcceptanceTester( $scenario );

$I->loginAsAdmin();

$I->amOnPage( '/step-status-conditional-logic' );
$I->fillField( 'Single Line', 'test' );
$I->fillField( 'Paragraph', 'test' );
$I->selectOption( 'User', '1' );
$I->click( 'Submit' );


$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'Step Status Conditional Logic' );

$I->waitforText( 'Approval Step' );
$I->see( 'Approval Step', '.gravityflow-status-box' );
$I->click( 'Reject' );

$I->waitforText( 'Approval if Previous Approval Step Rejected' );
$I->see( 'Approval if Previous Approval Step Rejected', '.gravityflow-status-box' );
$I->dontSee( 'Approval if Previous Approval Step Approved', '.gravityflow-status-box' );
