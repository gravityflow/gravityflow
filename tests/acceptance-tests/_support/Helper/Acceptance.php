<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module {
	public function getPseudoContent( $identifier, $pseudo = 'after' ) {
		$webdriver = $this->getModule( 'WPWebDriver' );

		$command = sprintf( "return window.getComputedStyle(document.querySelector('%s'),':%s').getPropertyValue('content')", $identifier, $pseudo );
		$content = $webdriver->executeJS( $command );

		return $content;
	}

	public function seeSanitizedNumberInField( $identifier, $expected_value ) {
		$webdriver = $this->getModule( 'WPWebDriver' );
		$field     = $webdriver->_findElements( $identifier );
		$field     = $field[0];

		$value = $field->getAttribute( 'value' );

		$sanitized      = $this->convert_currency_to_float( $value );
		$expected_value = $this->convert_currency_to_float( $expected_value );

		$this->assertEquals( $expected_value, $sanitized );
	}

	public function convert_currency_to_float( $money ) {
		$cleanString       = preg_replace( '/([^0-9\.,])/i', '', $money );
		$onlyNumbersString = preg_replace( '/([^0-9])/i', '', $money );

		$separatorsCountToBeErased = strlen( $cleanString ) - strlen( $onlyNumbersString ) - 1;

		$stringWithCommaOrDot     = preg_replace( '/([,\.])/', '', $cleanString, $separatorsCountToBeErased );
		$removedThousandSeparator = preg_replace( '/(\.|,)(?=[0-9]{3,}$)/', '', $stringWithCommaOrDot );

		return (float) str_replace( ',', '.', $removedThousandSeparator );
	}
}