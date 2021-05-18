<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Inbox_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Endpoint as Ajax_Endpoint;

class Endpoint extends Ajax_Endpoint {

	public function handle( $request ) {
		$args  = $request->get_param( Config::SEARCH_ARGS );

		$data = $this->model->get_inbox_tasks( $args );

		return $this->response_factory->create( $data, 200 );
	}

}