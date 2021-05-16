<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

use \WP_HTTP_Response;

class Response_Factory {

	public function create( $data, $code = 200 ) {
		return new WP_HTTP_Response( $data, $code );
	}

}