<?php
/*
 * Purpose: Test approval step reverts to user input and associated notifications
 */

// @group step-user_input
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test Approval Reverts' );

// Submit the form
$I->amOnPage( '/0034-approve-and-revert' );

$I->see( 'Approve and Revert' );
$I->scrollTo( [ 'css' => '.gform_title' ], 20, 50 ); // needed for chromedriver

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
$I->click( '0034 Approve and Revert' );

// User Input
$I->waitForText( 'Status: Pending', 3 );
$I->selectOption( 'input[name=input_7]', '10 - 20' );
$I->click( '#gravityflow_update_button' );

// Approve 1 - Revert
$I->waitForText( 'Status: Pending', 3 );
$I->see( 'No Revert Message' );
$I->click( 'button[value=revert]' );

// User Input
$I->waitForText( 'Status: Pending', 3 );
$I->selectOption( 'input[name=input_7]', '1 - 5' );
$I->click( '#gravityflow_update_button' );

// Approve 2 - Revert
$I->waitForText( 'Status: Pending', 3 );
$I->see( 'With Revert Message' );
$I->click( 'button[value=revert]' );

// User Input
$I->waitForText( 'Status: Pending', 3 );
$I->selectOption( 'input[name=input_7]', 'Scratch' );
$I->click( '#gravityflow_update_button' );

// Approve 2 - Accept
$I->waitForText( 'Status: Pending', 3 );
$I->see( 'With Revert Message' );
$I->click( 'button[value=approved]' );

$I->waitForText( 'Status: Approved', 3 );

//Validate number of notifications for the scenario by title

$query_assignee_notifications = new WP_Query( array(
	'post_type' => 'post',
	'title'     => '0034-70 - User Input - Assignee',
) );

if ( $query_assignee_notifications->have_posts() ) {
	$I->assertEquals( 2, $query_assignee_notifications->post_count, 'Incorrect number of assignee notifications sent for scenario' );
} else {
	$I->assertFalse( true, 'No assignee notifications sent for scenario' );
}

$query_revert_notifications = new WP_Query( array(
	'post_type' => 'post',
	'title'     => '0034-72 - Approval-Revert - Second Revert',
) );

if ( $query_revert_notifications->have_posts() ) {
	$I->assertEquals( 1, $query_revert_notifications->post_count, 'Incorrect number of revert notifications sent for scenario' );
} else {
	$I->assertFalse( true, 'No revert notifications sent for scenario' );
}

$I->logOut();
