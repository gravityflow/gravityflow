<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

interface Ajax_Return_Strategy {

	public function success( $data );

	public function error( $data, $code );

}