<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

abstract class Config {

	protected $name = '';

	protected $is_public = false;

	public function __construct() {
		if ( empty( $this->name ) ) {
			throw new \LogicException( __( 'AJAX Endpoint Configs must have a unique $name property.', 'gravityflow' ) );
		}
	}

	public function name() {
		return $this->name;
	}

	public function is_public() {
		return $this->is_public;
	}

	/**
	 * @return Argument[]
	 */
	abstract protected function args();

}