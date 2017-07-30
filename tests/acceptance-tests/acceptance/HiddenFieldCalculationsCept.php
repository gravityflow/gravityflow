<?php
$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test calculations that use fields that are not editable or displayed.');

$I->amOnPage( '/hidden-field-calculations' );

$I->fillField( 'Single Line', 'Test' );
$I->fillField( 'Number', 2 );
$I->click( 'Submit' );

$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'Hidden Field Calculations' );
$I->waitForText( 'Single Line', 3 );
$I->seeInField( 'Number 2', '4' );
$I->seeInField( 'Number 3', '8' );
