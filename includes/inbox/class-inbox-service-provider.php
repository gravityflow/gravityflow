<?php

namespace Gravity_Flow\Gravity_Flow\Inbox;

use Gravity_Flow\Gravity_Flow\Ajax\Ajax_Service_Provider;
use Gravity_Flow\Gravity_Flow\Ajax\Response_Factory;
use Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Inbox_Items\Config as Get_Items_Config;
use Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Inbox_Items\Endpoint as Get_Items_Endpoint;
use Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Refresh_Inbox_Items\Config as Refresh_Items_Config;
use Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Refresh_Inbox_Items\Endpoint as Refresh_Items_Endpoint;
use Gravity_Flow\Gravity_Flow\Inbox\Models\Task;
use Gravity_Flow\Gravity_Flow\Service_Provider\Service_Provider;
use Gravity_Flow\Gravity_Flow\Util\Util_Service_Provider;
use \Gravity_Flow_API;

class Inbox_Service_Provider extends Service_Provider {

	const AJAX_PREFIX = 'gflow_inbox_';

	const GET_ITEMS_ENDPOINT     = 'get_items_endpoint';
	const REFRESH_ITEMS_ENDPOINT = 'refresh_items_endpoint';

	const TASK_MODEL = 'task_model';

	protected $provides = array(
		self::GET_ITEMS_ENDPOINT,
		self::TASK_MODEL,
	);

	protected $endpoints = array(
		self::GET_ITEMS_ENDPOINT,
		self::REFRESH_ITEMS_ENDPOINT,
	);

	public function register() {
		$container = $this->getContainer();

		$container->add( self::TASK_MODEL, function () use ( $container ) {
			return new Task( $container->get( Util_Service_Provider::GFLOW_API ), $container->get( Util_Service_Provider::GF_API ) );
		} );

		$container->add( self::GET_ITEMS_ENDPOINT, function () use ( $container ) {
			$config = new Get_Items_Config();

			return new Get_Items_Endpoint( $config, $container->get( Ajax_Service_Provider::RESPONSE_FACTORY ), $container->get( self::TASK_MODEL ) );
		} );

		$container->add( self::REFRESH_ITEMS_ENDPOINT, function () use ( $container ) {
			$config = new Refresh_Items_Config();

			return new Refresh_Items_Endpoint( $config, $container->get( Ajax_Service_Provider::RESPONSE_FACTORY ), $container->get( self::TASK_MODEL ) );
		} );
	}

	public function hooks() {
		foreach ( $this->endpoints as $ep_name ) {
			/**
			 * @var \Gravity_Flow\Gravity_Flow\Ajax\Endpoint $endpoint
			 */
			$endpoint = $this->getContainer()->get( $ep_name );
			add_action( 'wp_ajax_' . self::AJAX_PREFIX . $endpoint->name(), array( $endpoint, 'delegate' ) );

			if ( $endpoint->is_public() ) {
				add_action( 'wp_ajax_nopriv_' . self::AJAX_PREFIX . $endpoint->name(), array( $endpoint, 'delegate' ) );
			}
		}

		$container = $this->getContainer();

		add_filter( 'gravityflow_js_config_shared', function ( $config ) use ( $container ) {
			/**
			 * @var Task $tasks
			 */
			$tasks = $container->get( self::TASK_MODEL );

			$config['grid_options'] = array(
				'columnDefs' => $tasks->get_table_header_defs(),
				'rowData'    => $tasks->get_inbox_tasks( array() ),
			);

			return $config;
		}, 10, 1 );
	}
}