<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Refresh_Inbox_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Endpoint as Ajax_Endpoint;

class Endpoint extends Ajax_Endpoint {

	public function handle() {
		set_current_user( 1 );

		$tasks = $this->model->get_inbox_tasks( $this->data( Config::SEARCH_ARGS ) );
		$ids   = $this->data( config::CURRENT_IDS );

		$data = array(
			'add'    => $this->added( $tasks, $ids ),
			'remove' => $this->removed( $tasks, $ids ),
			'update' => $this->updated( $tasks, $ids ),
		);

		$response = $this->response_factory->create( $data, 200 );

		return $response->response();
	}

	private function added( $tasks, $ids ) {
		return array_filter( $tasks, function ( $item ) use ( $ids ) {
			return ! in_array( $item['id'], $ids );
		} );
	}

	private function updated( $tasks, $ids ) {
		return array_values( array_filter( $tasks, function ( $item ) use ( $ids ) {
			return in_array( $item['id'], $ids );
		} ) );
	}

	private function removed( $tasks, $ids ) {
		$task_ids = array();

		foreach ( $tasks as $task ) {
			$task_ids[] = $task['id'];
		}

		return array_values( array_map(
			function( $item ) {
				return array( 'id' => $item );
			},
			array_diff( $ids, $task_ids )
		) );
	}

}