<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

class Argument {

	private $name;

	private $required;

	private $default;

	private $sanitization;

	private $validation;

	public function __construct( $name, $required = false, $default = null, callable $sanitization = null, callable $validation = null ) {
		$this->name         = $name;
		$this->required     = $required;
		$this->default      = $default;
		$this->sanitization = $sanitization;
		$this->validation   = $validation;
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

	public function to_array() {
		$data = array(
			'default'  => $this->default_value(),
			'required' => $this->is_required(),
		);

		if ( ! empty( $this->sanitization ) ) {
			$data['sanitize_callback'] = $this->sanitization;
		}

		if ( ! empty( $this->validation ) ) {
			$data['validate_callback'] = $this->validation;
		}

		return $data;
	}
}