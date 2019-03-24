<?php
/*
 * Purpose: Test that the current step merge tag is replaced in the form confirmation and approval email.
 */

// @group merge-tags
// @group step-approval
// @group emails

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the current step merge tags are replaced in the form confirmation and approval email.' );

// Submit the form.
$I->logOut();
$I->amOnPage( '/0038-current-step-merge-tag' );
$I->waitForText( '0038 Current Step Merge Tag', 3 );
$I->see( '0038 Current Step Merge Tag' );
$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$timestamp = strtotime( '+1 week' );

// Confirm the merge tags are replaced.
$I->dontSee( 'Current Step: {current_step}' );
$I->see( 'Current Step: Approval' );

$I->dontSee( 'Current Step ID: {current_step:ID}' );
$I->see( 'Current Step ID:' );
$step_id = $I->grabTextFrom( '.current-step-id' );
$I->assertGreaterThan( 0, $step_id );

$I->dontSee( 'Current Step Type: {current_step:type}' );
$I->see( 'Current Step Type: approval' );

$I->dontSee( 'Current Step Schedule: {current_step:schedule}' );
$I->see( 'Current Step Schedule:' );
$schedule = $I->grabTextFrom( '.current-step-schedule' );
$I->assertStringStartsWith( date( 'F j, Y' ) . ' at', $schedule );

$I->dontSee( 'Current Step Expiration: {current_step:expiration}' );
$I->see( 'Current Step Expiration:' );
$expiration = $I->grabTextFrom( '.current-step-expiration' );
$I->assertStringStartsWith( date( 'F j, Y', $timestamp ) . ' at', $expiration );

$I->dontSee( 'Current Step Due Date: {current_step:due_date}' );
$I->see( 'Current Step Due Date:' );
$due_date = $I->grabTextFrom( '.current-step-due-date' );
$I->assertStringStartsWith( date( 'F j, Y', $timestamp ), $due_date );

$I->dontSee( 'Current Step Due Status: {current_step:due_status}' );
$I->see( 'Current Step Due Status:' );
$due_status = $I->grabTextFrom( '.current-step-due-status' );
$I->assertStringStartsWith( 'Pending', $due_status );

// Don't wait for the cron, start the step now.
$entry_id = $I->grabTextFrom( '.entry-id' );
$entry_id = intval( $entry_id );
$I->assertNotEmpty( $entry_id );
$entry = GFAPI::get_entry( $entry_id );
$I->assertArrayHasKey( 'form_id', $entry );
$form = GFAPI::get_form( $entry['form_id'] );
$I->assertArrayHasKey( 'id', $form );
$step = gravity_flow()->get_current_step( $form, $entry );

$step->scheduled = false;
$step->start();
gform_update_meta( $entry['id'], 'workflow_step_' . $step_id . '_timestamp', strtotime( '-1 hour' ) );

// Login and approve the entry.
$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0038 Current Step Merge Tag' );
$I->waitForText( '0038 Current Step Merge Tag : Entry #', 3 );
$I->see( 'Approval (Pending Approval)' );
$I->click( 'Approve' );
$I->waitForText( 'Entry Approved',3 );
// Go to the page created from the approval email.
$I->amOnPage( '/0038-approval-email/' );
$I->see( '0038 Approval Email' );

// Confirm the merge tags are replaced.
$I->dontSee( 'Current Step Start: {current_step:start}' );
$I->see( 'Current Step Start:' );
$start = $I->grabTextFrom( '.current-step-start' );
$I->assertNotEmpty( $start );

$I->dontSee( 'Current Step Duration: {current_step:duration}' );
$I->see( 'Current Step Duration:' );
$duration = $I->grabTextFrom( '.current-step-duration' );
$I->assertNotEmpty( $duration );

$I->dontSee( 'Current Step Duration Minutes: {current_step:duration_minutes}' );
$I->see( 'Current Step Duration Minutes:' );
$duration_minutes = $I->grabTextFrom( '.current-step-duration-minutes' );
$I->assertGreaterThanOrEqual( 60, $duration_minutes );

$I->dontSee( 'Current Step Duration Seconds: {current_step:duration_seconds}' );
$I->see( 'Current Step Duration Seconds:' );
$duration_minutes = $I->grabTextFrom( '.current-step-duration-seconds' );
$I->assertGreaterThanOrEqual( 3600, $duration_minutes );

$I->dontSee( 'Current Step Due Date: {current_step:due_date}' );
$I->see( 'Current Step Due Date:' );
$due_date = $I->grabTextFrom( '.current-step-due-date' );
$I->assertStringStartsWith( date( 'F j, Y', $timestamp ), $due_date );

$I->dontSee( 'Current Step Due Status: {current_step:due_status}' );
$I->see( 'Current Step Due Status:' );
$due_status = $I->grabTextFrom( '.current-step-due-status' );
$I->assertStringStartsWith( 'Pending', $due_status );
