<?php
//$scenario->skip();

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test assignee routing based on the value of a checkbox field' );

// Submit the form
$I->amOnPage( '/dynamic-assignee-routing' );

$I->scrollTo( [ 'css' => '.gform_title' ] );

$I->fillField( 'Single Line', 'Some Text' );
$I->fillField( 'Paragraph', 'Some Text' );
$I->checkOption( 'Publisher 1' );
$I->checkOption( 'Publisher 3' );
$I->scrollTo( [ 'css' => 'input[type=submit]' ] );
$I->click( 'Submit' );

$I->loginAsAdmin();

$I->amOnWorkflowPage( 'Inbox' );

$I->click( 'Dynamic assignee routing' );

$I->waitForText( 'User: admin1 admin1 (Pending)', 3 );
$I->see( 'User: admin3 admin3 (Pending)' );
$I->dontSee( 'User: admin2 admin2 (Pending)' );

// Add admin2 to the assignees (configured in the step conditional routing)
$I->checkOption( 'Publisher 2' );

$I->click( 'Update' );

$I->waitForText( 'User: admin1 admin1 (Pending)', 3 );
$I->see( 'User: admin2 admin2 (Pending)' );
$I->see( 'User: admin3 admin3 (Pending)' );
