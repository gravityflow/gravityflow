<?php
//$scenario->skip();
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

$I->waitForText( 'Schedule step', 3 );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->fillField( 'textarea[name="input_2"]', 'Some paragraph text' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Status
$I->amOnWorkflowPage( 'Status' );
$I->waitForText( 'Schedule step', 3 );
$I->click( 'Schedule step' );
$I->waitForText( 'Send schedule notification (Queued)', 3 );
