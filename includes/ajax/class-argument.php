<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

class Argument {

	private $name;

	private $required;

	private $default;

	private $sanitization;

	public function __construct( $name, $required = false, $default = null, callable $sanitization = null ) {
		$this->name         = $name;
		$this->required     = $required;
		$this->default      = $default;
		$this->sanitization = $sanitization;
	}

	public function name() {
		return $this->name;
	}

	public function is_required() {
		return $this->required;
	}

	public function default_value() {
		return $this->default;
	}

	public function sanitize( $value ) {
		if ( ! $this->should_sanitize() ) {
			return $value;
		}

		return call_user_func( $this->sanitization, $value );
	}

	private function should_sanitize() {
		return ! is_null( $this->sanitization );
	}
}