<?php
/*
 * Purpose: Test that the outbound webhook step handles response mapping.
 */

// @group step-user_input
// @group step-webhook

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the Outbound Webhook step handles response mapping.' );

// Submit the form
$I->amOnPage( '/0027-outgoing-webhook' );

$I->see( '0027 Outgoing Webhook' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'Question', 'Codeception Question' );
$I->fillField( 'Answer', '42' );
$I->fillField( 'Rationale', 'Why it is the answer to the Ultimate Question of Life, the Universe, and Everything' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->loginAsAdmin();

$I->amGoingTo( 'View Workflow Inbox.' );
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'StepCheck - Update' );


$I->waitForText( 'Status: Pending', 3 );
$I->seeInField('#input_27_1', 'Codeception Question');
$I->seeInField('#input_27_3', '42');
$I->seeInField('#input_27_2', 'Why it is the answer to the Ultimate Question of Life, the Universe, and Everything');
$I->click( '#gravityflow_update_button' );

$I->waitForText( 'Status: Pending', 5 );
$I->scrollTo( ['css' => '.gravityflow-timeline'], 20, 50 );
$I->see( 'https://unit-test-webhook.com/200-empty. RESPONSE: 202 Accepted (Success)' );
$I->seeInField('#input_27_1', 'Codeception Question');
$I->seeInField('#input_27_3', '42');
$I->seeInField('#input_27_2', 'Why it is the answer to the Ultimate Question of Life, the Universe, and Everything');
$I->scrollTo( [ 'css' => '.gf_admin_page_title' ] );
$I->click( 'Submit' );

$I->waitForText( 'https://unit-test-webhook.com/200-0027. RESPONSE: 202 Accepted (Success)', 5 );
$I->see( 'https://unit-test-webhook.com/200-0027. RESPONSE: 202 Accepted (Success)' );
$I->seeInField('#input_27_1', 'customQuestion200');
$I->seeInField('#input_27_3', 'customAnswer200');
$I->seeInField('#input_27_2', 'customRationale200' );
$I->fillField( 'Question', 'Codeception Question' );
$I->fillField( 'Answer', '42' );
$I->fillField( 'Rationale', 'Why it is the answer to the Ultimate Question of Life, the Universe, and Everything' );
$I->scrollTo( [ 'css' => '.gf_admin_page_title' ] );
$I->click( 'Submit' );

$I->waitForText( 'Status: Complete', 3 );
$I->dontSee( 'https://unit-test-webhook.com/200-nojson' );

