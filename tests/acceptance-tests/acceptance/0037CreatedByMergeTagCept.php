<?php
/*
 * Purpose: Test that the created by merge tag is replaced in the notification step email.
 */

// @group merge-tags
// @group step-notification
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the token merge tags are replaced in the assignee email.' );

// Submit the form as an anonymous user.
$I->logOut();
$I->amOnPage( '/0037-created-by-merge-tag' );
$I->waitForText( '0037 Created By Merge Tag', 3 );
$I->see( '0037 Created By Merge Tag' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Go to the page created from the notification step email.
$I->amOnPage( '/0037-workflow-notification' );

// Confirm the merge tags are replaced by empty strings when an anonymous user submits the form
$I->dontSee( 'Created By: {created_by}' );
$I->see( 'Created By:' );
$created_by = $I->grabTextFrom( '.created-by' );
$I->assertEmpty( $created_by );

$I->dontSee( 'Created By ID: {created_by:ID}' );
$I->see( 'Created By ID:' );
$created_by_id = $I->grabTextFrom( '.created-by-id' );
$I->assertEmpty( $created_by_id );

$I->dontSee( 'Created By Login: {created_by:user_login}' );
$I->see( 'Created By Login:' );
$created_by_user_login = $I->grabTextFrom( '.created-by-user-login' );
$I->assertEmpty( $created_by_user_login );

$I->dontSee( 'Created By Roles: {created_by:roles}' );
$I->see( 'Created By Roles:' );
$created_by_roles = $I->grabTextFrom( '.created-by-roles' );
$I->assertEmpty( $created_by_roles );

// Login and submit the form again.
$I->loginAsAdmin();
$I->amOnPage( '/0037-created-by-merge-tag' );
$I->waitForText( '0037 Created By Merge Tag', 3 );
$I->see( '0037 Created By Merge Tag' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

// Go to the page created from the notification step email.
$I->amOnPage( '/0037-workflow-notification-2' );

// Confirm the merge tags are replaced with the properties of the logged in user who submitted the form.
$I->dontSee( 'Created By: {created_by}' );
$I->see( 'Created By: 1' );

$I->dontSee( 'Created By ID: {created_by:ID}' );
$I->see( 'Created By ID: 1' );

$I->dontSee( 'Created By Login: {created_by:user_login}' );
$I->see( 'Created By Login: admin' );

$I->dontSee( 'Created By Roles: {created_by:roles}' );
$I->see( 'Created By Roles: administrator' );
