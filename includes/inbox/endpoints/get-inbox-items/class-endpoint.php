<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Inbox_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Endpoint as Ajax_Endpoint;

class Endpoint extends Ajax_Endpoint {

	public function handle() {
		$data = $this->model->get_inbox_tasks( $this->data( Config::SEARCH_ARGS ) );

		$response = $this->response_factory->create( $data, 200 );

		return $response->response();
	}

}