<?php
//$scenario->skip();
/*
 * Test summary: Specific assign
 *
 * Test details:
 * - Fill the fields
 * - Go to back-end to inbox and approve as User admin
 * - Logout
 * - Login as admin2
 * - Go to back-end to inbox and approve as User admin2
 * - Logout
 * - Login as admin3
 * - Go to back-end to inbox and approve as User admin3
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Specific assign' );

// Submit the form
$I->amOnPage( '/specific-assign' );

$I->see( 'Specific assign' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

$I->fillField( 'input[name="input_1"]', 'Some text' );
$I->fillField( 'textarea[name="input_2"]', 'Some paragraph text' );
$I->fillField( 'input[name="input_3"]', 'Tag 1, Tag 2, Tag 3' );
$I->selectOption( 'input[name="input_4.2"]', 'Second Choice' );
$I->selectOption( 'input[name="input_4.3"]', 'Third Choice' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ], 20, 50 ); // needed for chromedriver
$I->click( 'Submit' );
$I->see( 'Thanks for contacting us! We will get in touch with you shortly.' );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->click( 'Workflow' );
$I->click( 'Inbox' );
$I->see( 'Workflow Inbox' );
$I->click( 'Specific assign' );

// Approve
$I->seeElement( 'button[value=approved]' );
$I->click( 'button[value=approved]' );

// Log out
$I->logOut();

// Login as admin2
$I->loginAs( 'admin2', 'admin2' );
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'Specific assign' );

// Approve
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );

// Log out
$I->logOut();

// Login as admin3
$I->loginAs( 'admin3', 'admin3' );
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'Specific assign' );

// Approve
$I->waitForElement( 'button[value=approved]', 3 );
$I->click( 'button[value=approved]' );

$I->waitForText( 'Status: Approved', 3 );
