<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

class WP_Ajax_Return_Strategy implements Ajax_Return_Strategy {

	public function success( $data ) {
		wp_send_json( $data );
	}

	public function error( $data, $code ) {
		wp_send_json_error( $data, $code );
	}

}