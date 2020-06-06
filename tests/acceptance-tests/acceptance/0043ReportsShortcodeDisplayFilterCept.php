<?php
/*
 * Purpose: Test that Reports shortcode can come with or without the filter.
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test that the Reports shortcode can come with or without the filter' );

$I->loginAsAdmin();

$I->amOnPage( '/reports' );
$I->amGoingTo( 'Test the filter is displayed by default' );
$I->seeElement( 'input', [ 'type' => 'submit', 'value' => 'Filter' ] );

$page = get_page_by_path( 'reports' );
wp_update_post( array( 'ID' => $page->ID, 'post_content' => '[gravityflow page="reports" display_filter="false"]' ) );

$I->amOnPage( '/reports' );
$I->amGoingTo( 'Test the filter isn\'t displayed when the display_filter attribute set to false' );
$I->dontSeeElement( 'input', [ 'type' => 'submit', 'value' => 'Filter' ] );
