<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Endpoint as Ajax_Endpoint;

class Endpoint extends Ajax_Endpoint {

	public function handle() {
		$data = array(
			Config::POSTS_PER_PAGE => $this->data( Config::POSTS_PER_PAGE ),
			Config::OFFSET         => $this->data( Config::OFFSET ),
			Config::ORDERBY        => $this->data( Config::ORDERBY ),
			Config::ORDER          => $this->data( Config::ORDER ),
		);

		$response = $this->response_factory->create( $data, 200 );

		return $response->response();
	}

}