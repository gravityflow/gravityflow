<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Refresh_Inbox_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Endpoint as Ajax_Endpoint;

class Endpoint extends Ajax_Endpoint {

	public function handle( $request ) {
		$tasks = $this->model->get_inbox_tasks( $request->get_param( Config::SEARCH_ARGS ) );
		$ids   = $request->get_param( Config::CURRENT_IDS );

		$data = array(
			'add'    => $this->added( $tasks, $ids ),
			'remove' => $this->removed( $tasks, $ids ),
			'update' => $this->updated( $tasks, $ids ),
		);

		return $this->response_factory->create( $data, 200 );
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