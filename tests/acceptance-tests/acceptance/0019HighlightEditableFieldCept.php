<?php
/*
 * Purpose: Test if editable fields highlighted on the user input step
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test if editable fields highlighted on the user input step' );

// Submit the form
$I->amOnPage( '/0019-user-input-highlight-editable-fields' );
$I->see( '0019 User Input Highlight Editable Fields' );
$I->scrollTo( [ 'css' => '.gform_title' ] );
$I->fillField( 'Paragraph', 'Some text' );
$I->selectOption( 'Dropdown', 'Third Choice' );
$I->fillField( 'Email', 'test@gmail.com' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ] );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us!', 3 );

// Login to wp-admin
$I->loginAsAdmin();
$I->seeInCurrentUrl( '/wp-admin/' );

// Go to Inbox
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0019 User Input Highlight Editable Fields' );

// Find 3 highlighted fields
$I->see( 'Paragraph', ['css' => '.green-background label'] );
$I->see( 'Dropdown', ['css' => '.green-background label'] );
$I->see( 'Admin only radio required', ['css' => '.green-background label'] );

// Can't find other fields highlighted
$I->cantsee( 'Email', ['css' => '.green-background label'] );
$I->cantsee( 'Calc', ['css' => '.green-background label'] );
$I->cantsee( 'Checkboxes', ['css' => '.green-background label'] );
