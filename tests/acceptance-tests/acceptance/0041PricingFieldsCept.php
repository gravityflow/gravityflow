<?php
/*
 * Purpose: Tests that values of non-editable pricing fields can be accessed on the user input step.
 */

// @group step-user_input

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Tests that values of non-editable pricing fields can be accessed.');

$I->amOnPage( '/0041-pricing-fields' );

$I->fillField( 'input[name="input_1.3"]', '1' );
$I->fillField( 'input[name="input_4"]', '100' );
$I->selectOption( 'select[name="input_10"]', 'Second Option' );

$I->scrollTo( [ 'css' => '.gform_footer' ] );
$I->assertSame( '290', $I->grabValueFrom( 'input[name="input_14"]' ) );

// Grab the form id, we'll need it for the merge tag tests.
$form_id = $I->grabValueFrom( 'input[name="gform_submit"]' );

$I->click( 'Submit' );
$I->waitForText( 'Thanks for contacting us! We will get in touch with you shortly.', 3 );

$I->loginAsAdmin();
$I->amOnWorkflowPage( 'Inbox' );
$I->click( '0041 Pricing Fields' );

// Confirm that the GF pricing scripts can access the field values by testing the total field value has been populated.
$I->waitForText( '0041 Pricing Fields : Entry #', 3 );
$I->see( '$290.00' );
$I->assertSame( '290', $I->grabValueFrom( 'input[name="input_14"]' ) );

// Confirm that the GF merge tag scripts can access the field values.
$format = "return GFMergeTag.replaceMergeTags( {$form_id}, '%s' )";
$I->assertSame( 'Product - Single, $10.00, 1', $I->executeJS( sprintf( $format, '{:1}' ) ) ); // Single product.
$I->assertSame( 'First Choice', $I->executeJS( sprintf( $format, '{:2}' ) ) ); // Drop down product.
$I->assertSame( 'First Choice', $I->executeJS( sprintf( $format, '{:3}' ) ) ); // Radio buttons product.
$I->assertSame( '$100.00', $I->executeJS( sprintf( $format, '{:4}' ) ) ); // User defined price.
$I->assertSame( '1, Product - Hidden, $10.00', $I->executeJS( sprintf( $format, '{:5}' ) ) ); // Hidden product.
$I->assertSame( 'Product - Calculation, $100.00, 1', $I->executeJS( sprintf( $format, '{:6}' ) ) ); // Calculated product.
$I->assertSame( '1', $I->executeJS( sprintf( $format, '{:7}' ) ) ); // Number quantity.
$I->assertSame( '1', $I->executeJS( sprintf( $format, '{:8}' ) ) ); // Drop down quantity.
$I->assertSame( '1', $I->executeJS( sprintf( $format, '{:9}' ) ) ); // Hidden quantity.
$I->assertSame( 'Second Option', trim( $I->executeJS( sprintf( $format, '{:10}' ) ) ) ); // Drop down option.
$I->assertSame( 'First Option', $I->executeJS( sprintf( $format, '{:11}' ) ) ); // Checkboxes option.
$I->assertSame( 'First Option', $I->executeJS( sprintf( $format, '{:12}' ) ) ); // Radio buttons option.
$I->assertSame( '$10.00', $I->executeJS( sprintf( $format, '{:13}' ) ) ); // Shipping.
