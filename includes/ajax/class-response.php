<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

class Response {

	private $data;
	private $code;
	private $return_strategy;

	public function __construct( $data, $code, $return_strategy ) {
		$this->data            = $data;
		$this->code            = $code;
		$this->return_strategy = $return_strategy;
	}

	public function response() {
		if ( $this->code === 200 ) {
			$this->return_strategy->success( $this->data );
			return;
		}

		$this->return_strategy->error( $this->data, $this->code );
	}

}