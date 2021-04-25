<?php

namespace Gravity_Flow\Gravity_Flow\Inbox;

use Gravity_Flow\Gravity_Flow\Ajax\Ajax_Service_Provider;
use Gravity_Flow\Gravity_Flow\Ajax\Response_Factory;
use Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Items\Config as Get_Items_Config;
use Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Items\Endpoint as Get_Items_Endpoint;
use Gravity_Flow\Gravity_Flow\Service_Provider\Service_Provider;

class Inbox_Service_Provider extends Service_Provider {

	const AJAX_PREFIX = 'gflow_inbox_';

	const GET_ITEMS_ENDPOINT = 'get_items_endpoint';

	protected $provides = array(
		self::GET_ITEMS_ENDPOINT,
	);

	protected $endpoints = array(
		self::GET_ITEMS_ENDPOINT,
	);

	public function register() {
		$container = $this->getContainer();

		$container->add( self::GET_ITEMS_ENDPOINT, function () use ( $container ) {
			$config = new Get_Items_Config();

			return new Get_Items_Endpoint( $config, $container->get( Ajax_Service_Provider::RESPONSE_FACTORY ) );
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
	}
}