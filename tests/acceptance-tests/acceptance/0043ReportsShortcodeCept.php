<?php
/*
 * Purpose: Test that Reports shortcode.
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test non-admin users cannot view the reports' );

$I->amOnPage( '/reports' );
$I->amGoingTo( 'Test no reports is displayed for non-admin users' );
$I->dontSeeElement( 'input', [ 'type' => 'submit', 'value' => 'Filter' ] );

$I->wantTo( 'Test that the Reports shortcode can come with or without the filter for admin users' );

$I->amGoingTo( 'Login as the admin' );
$I->loginAsAdmin();

$I->amOnPage( '/reports' );
$I->amGoingTo( 'Test the filter is displayed by default' );
$I->seeElement( 'input', [ 'type' => 'submit', 'value' => 'Filter' ] );

$I->amGoingTo( 'Test if the Assignees dropdown will show up when the category is step and a step is selected' );
$I->selectOption( 'select[name=form-id]', '1' );
$I->selectOption( 'select[name=category]', 'step' );
$I->selectOption( 'select[name=step-id]', '1' );
$I->seeOptionIsSelected( 'select[name=assignee]', 'All Assignees' );

$I->amOnWorkflowPage( 'Reports' );

$I->amGoingTo( 'Test if the Assignees dropdown will show up when the category is step and a step is selected in the Workflow Reports Page' );
$I->selectOption( 'select[name=form-id]', '1' );
$I->selectOption( 'select[name=category]', 'step' );
$I->selectOption( 'select[name=step-id]', '1' );
$I->seeOptionIsSelected( 'select[name=assignee]', 'All Assignees' );

$page = get_page_by_path( 'reports' );
wp_update_post( array( 'ID' => $page->ID, 'post_content' => '[gravityflow page="reports" display_filter="false"]' ) );

$I->amOnPage( '/reports' );
$I->amGoingTo( 'Test the filter isn\'t displayed when the display_filter attribute set to false' );
$I->dontSeeElement( 'input', [ 'type' => 'submit', 'value' => 'Filter' ] );
