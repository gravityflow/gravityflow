<?php
/*
 * Purpose: Tests that values of non-editable pricing fields can be accessed by GFMergeTag on the user input step.
 */

// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Tests that values of non-editable pricing fields can be accessed by GFMergeTag on the user input step.');

$I->amOnPage( '/0041-pricing-fields' );

$I->fillField( 'input[name="input_1.3"]', '2' );
$I->fillField( 'input[name="input_4"]', '100' );
$I->selectOption( 'select[name="input_10"]', 'Third Option' );

$I->scrollTo( [ 'css' => '.gform_footer' ] );
$I->assertSame( '340', $I->grabValueFrom( 'input[name="input_14"]' ) );

// Grab the form id, we'll need it for the merge tag tests.
$form_id = $I->grabValueFrom( 'input[name="gform_submit"]' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0041 Pricing Fields' );

$I->waitForText( '0041 Pricing Fields : Entry #', 3 );

$script = "return GFMergeTag.replaceMergeTags( {$form_id}, arguments[0] )";

// Single product.
$I->seeElementInDOM( "#field_{$form_id}_1 input" );
$I->dontSee( 'Price', "#field_{$form_id}_1" );
$I->assertSame( 'Product - Single, $10.00, 2', $I->executeJS( $script, [ '{:1}' ] ) );

// Drop down product.
$I->seeElementInDOM( "#field_{$form_id}_2 select" );
$I->dontSee( 'First Choice', "#field_{$form_id}_2" );
$I->assertSame( 'First Choice', $I->executeJS( $script, [ '{:2}' ] ) );

// Radio buttons product.
$I->seeElementInDOM( "#field_{$form_id}_3 input" );
$I->dontSee( 'First Choice', "#field_{$form_id}_3" );
$I->assertSame( 'First Choice', $I->executeJS( $script, [ '{:3}' ] ) );

// User defined price.
$I->dontSeeElement( "#field_{$form_id}_4 input" );
$I->seeElementInDOM( "#field_{$form_id}_4 input" );
$I->assertSame( '$100.00', $I->executeJS( $script, [ '{:4}' ] ) );

// Hidden product.
$I->seeElementInDOM( "#field_{$form_id}_5 input" );
$I->assertSame( '1, Product - Hidden, $10.00', $I->executeJS( $script, [ '{:5}' ] ) );

// Calculated product.
$I->seeElementInDOM( "#field_{$form_id}_6 input" );
$I->dontSee( 'Price', "#field_{$form_id}_6" );
$I->assertSame( 'Product - Calculation, $100.00, 1', $I->executeJS( $script, [ '{:6}' ] ) );

// Number quantity.
$I->dontSeeElement( "#field_{$form_id}_7 input" );
$I->seeElementInDOM( "#field_{$form_id}_7 input" );
$I->assertSame( '1', $I->executeJS( $script, [ '{:7}' ] ) );

// Drop down quantity.
$I->dontSeeElement( "#field_{$form_id}_8 select" );
$I->seeElementInDOM( "#field_{$form_id}_8 select" );
$I->assertSame( '1', $I->executeJS( $script, [ '{:8}' ] ) );

// Hidden quantity.
$I->seeElementInDOM( "#field_{$form_id}_9 input" );
$I->assertSame( '1', $I->executeJS( $script, [ '{:9}' ] ) );

// Drop down option.
$I->dontSeeElement( "#field_{$form_id}_10 select" );
$I->seeElementInDOM( "#field_{$form_id}_10 select" );
$I->assertSame( 'Third Option', trim( $I->executeJS( $script, [ '{:10}' ] ) ) );

// Checkboxes option.
$I->seeElementInDOM( "#field_{$form_id}_11 input" );
$I->dontSee( 'First Option', "#field_{$form_id}_11" );
$I->assertSame( 'First Option', $I->executeJS( $script, [ '{:11}' ] ) );

// Radio buttons option.
$I->seeElementInDOM( "#field_{$form_id}_12 input" );
$I->dontSee( 'First Option', "#field_{$form_id}_12" );
$I->assertSame( 'First Option', $I->executeJS( $script, [ '{:12}' ] ) );

// Shipping.
$I->seeElementInDOM( "#field_{$form_id}_13 input" );
$I->dontSee( '$10.00', "#field_{$form_id}_13" );
$I->assertSame( '$10.00', $I->executeJS( $script, [ '{:13}' ] ) );

// Total.
$I->seeElementInDOM( "#field_{$form_id}_14 input" );
$I->See( 'Total', "#field_{$form_id}_14" );
$I->assertSame( '340', $I->executeJS( $script, [ '{:14}' ] ) );
