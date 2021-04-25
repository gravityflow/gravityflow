<?php
/*
 * Purpose: Test Workflow Note Validation
 */

// @group step-approval
// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test Workflow Note Validation' );

// Submit the form
$I->amOnPage( '/0055-workflow-note-validation' );

$I->see( '0055 Workflow Note Validation' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0055 Workflow Note Validation' );

//Approval - Attempt with an empty note.
$I->waitForText( '0055 Workflow Note Validation : Entry # ', 3 );
$I->see( '0055 Workflow Note Validation : Entry # ' );
$I->see( 'Approval (Pending Approval)' );
$I->fillField( 'Note', '' );
$I->click( 'Approve' );
$I->waitForText( 'There was a problem while updating your form.', 3 );
$I->see( 'There was a problem while updating your form.' );
$I->see( 'A note is required' );

//Approval - Attempt with a single space. 
$I->fillField( 'Note', ' ' );
$I->click( 'Approve' );
$I->waitForText( 'There was a problem while updating your form.', 3 );
$I->see( 'There was a problem while updating your form.' );
$I->see( 'A note is required' );

//Approval - Attempt with valid note.
$I->fillField( 'Note', 'Approval note' );
$I->click( 'Approve' );
$I->waitForText( 'Entry Approved', 3 );
$I->see( 'Entry Approved' );
$I->see( 'Note: Approval note' );

//User Input - Attempt with an empty note.
$I->see( 'User Input (Pending Input)' );
$I->fillField( 'Note', '' );
$I->click( 'Submit' );
$I->waitForText( 'There was a problem while updating your form.', 3 );
$I->see( 'There was a problem while updating your form.' );
$I->see( 'A note is required' );

//User Input - Attempt with a single space.
$I->fillField( 'Note', ' ' );
$I->click( 'Submit' );
$I->waitForText( 'There was a problem while updating your form.', 3 );
$I->see( 'There was a problem while updating your form.' );
$I->see( 'A note is required' );

//User Input - Attempt with valid note.
$I->fillField( 'Note', 'User Input note' );
$I->click( 'Submit' );
$I->waitForText( 'Entry updated and marked complete.', 3 );
$I->see( 'Entry updated and marked complete.' );
$I->see( 'Note: User Input note' );
$I->see( 'Status: Approved' );
