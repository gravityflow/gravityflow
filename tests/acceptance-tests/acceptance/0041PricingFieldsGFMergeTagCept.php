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
$I->assertSame( 'Product - Single, $10.00, 2', $I->executeJS( $script, [ '{:1}' ] ) ); // Single product.
$I->assertSame( 'First Choice', $I->executeJS( $script, [ '{:2}' ] ) ); // Drop down product.
$I->assertSame( 'First Choice', $I->executeJS( $script, [ '{:3}' ] ) ); // Radio buttons product.
$I->assertSame( '$100.00', $I->executeJS( $script, [ '{:4}' ] ) ); // User defined price.
$I->assertSame( '1, Product - Hidden, $10.00', $I->executeJS( $script, [ '{:5}' ] ) ); // Hidden product.
$I->assertSame( 'Product - Calculation, $100.00, 1', $I->executeJS( $script, [ '{:6}' ] ) ); // Calculated product.
$I->assertSame( '1', $I->executeJS( $script, [ '{:7}' ] ) ); // Number quantity.
$I->assertSame( '1', $I->executeJS( $script, [ '{:8}' ] ) ); // Drop down quantity.
$I->assertSame( '1', $I->executeJS( $script, [ '{:9}' ] ) ); // Hidden quantity.
$I->assertSame( 'Third Option', trim( $I->executeJS( $script, [ '{:10}' ] ) ) ); // Drop down option.
$I->assertSame( 'First Option', $I->executeJS( $script, [ '{:11}' ] ) ); // Checkboxes option.
$I->assertSame( 'First Option', $I->executeJS( $script, [ '{:12}' ] ) ); // Radio buttons option.
$I->assertSame( '$10.00', $I->executeJS( $script, [ '{:13}' ] ) ); // Shipping.
$I->assertSame( '340', $I->executeJS( $script, [ '{:14}' ] ) ); // Total.
