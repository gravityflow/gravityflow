<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

abstract class Config {

	protected $name = '';

	protected $method = 'GET';

	public function __construct() {
		if ( empty( $this->name ) ) {
			throw new \LogicException( __( 'AJAX Endpoint Configs must have a unique $name property.', 'gravityflow' ) );
		}
	}

	public function name() {
		return $this->name;
	}

	public function method() {
		return array( $this->method );
	}

	/**
	 * @return Argument[]
	 */
	abstract public function args();

}