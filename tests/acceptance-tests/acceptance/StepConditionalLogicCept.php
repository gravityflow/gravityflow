<?php
$I = new AcceptanceTester( $scenario );

$I->loginAsAdmin();

$I->amOnPage( '/step-conditional-logic' );
$I->fillField( 'Single Line', 'test' );
$I->fillField( 'Paragraph', 'test' );
$I->selectOption( 'User', '1' );
$I->click( 'Submit' );


$I->amOnWorkflowPage( 'Inbox' );
$I->click( 'Step Conditional Logic' );

$I->waitforText( 'Approval if Created By ID1' );
$I->see( 'Approval if Created By ID1', '.gravityflow-status-box' );
$I->dontSee( 'Approval if Created By ID2', '.gravityflow-status-box' );
$I->click( 'Approve' );

$I->waitforText( 'Approval if User Field ID1' );
$I->see( 'Approval if User Field ID1', '.gravityflow-status-box' );
$I->dontSee( 'Approval if User Field ID2', '.gravityflow-status-box' );
$I->click( 'Approve' );



