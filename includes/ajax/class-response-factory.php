<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

class Response_Factory {

	private $strategy;

	public function __construct( Ajax_Return_Strategy $strategy ) {
		$this->strategy = $strategy;
	}

	public function create( $data, $code = 200 ) {
		return new Response( $data, $code, $this->strategy );
	}

}