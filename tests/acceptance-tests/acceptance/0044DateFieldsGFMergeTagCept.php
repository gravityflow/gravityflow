<?php
/*
 * Purpose: Tests that values of non-editable date fields can be accessed by GFMergeTag on the user input step.
 */

// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Tests that values of non-editable date fields can be accessed by GFMergeTag on the user input step.');

$I->amOnPage( '/0044-date-fields' );

$I->scrollTo( [ 'css' => '.gform_footer' ] );

// Grab the form id, we'll need it for the merge tag tests.
$form_id = $I->grabValueFrom( 'input[name="gform_submit"]' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0044 Date Fields' );

$I->waitForText( '0044 Date Fields : Entry #', 3 );

$script = "return GFMergeTag.replaceMergeTags( {$form_id}, arguments[0] )";

$I->seeElementInDOM( "#field_{$form_id}_1 input.datepicker" );
$I->dontSee( 'Date 1', "#field_{$form_id}_1" );
$I->assertSame( '06/20/2020', $I->executeJS( $script, [ '{:1}' ] ) );

$I->seeElementInDOM( "#field_{$form_id}_2 input.datepicker" );
$I->dontSee( 'Date 2', "#field_{$form_id}_2" );
$I->assertSame( '07/20/2020', $I->executeJS( $script, [ '{:2}' ] ) );

$I->See( 'Date 3', "#field_{$form_id}_3" );
