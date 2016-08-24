<?php
/*
 * Test summary: Schedule step
 *
 * Test details:
 * - Fill up fields
 * - Send notification after 2 minutes after the workflow step is triggered
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Schedule step' );

// Submit the form
$I->amOnPage( '/schedule-step' );

$I->see( 'Schedule step' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->fillField( 'textarea[name="input_2"]', 'Some paragraph text' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->amOnPage( '/wp-admin' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Status' );
$I->see( 'Workflow Status' );
$I->click( 'Schedule step' );
$I->see( 'Send schedule notification (Queued)' );