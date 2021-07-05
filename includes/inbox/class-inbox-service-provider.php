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

	const TASK_MODEL   = 'task_model';
	const DEFAULTS_MAP = 'defaults_map';
	const PREF_FETCHER = 'pref_fetcher';

	const FETCH_ENABLED    = 'fetch_enabled';
	const FETCH_INTERVAL   = 'fetch_interval';
	const ITEMS_PER_PAGE   = 'items_per_page';
	const DEFAULT_SORT_COL = 'default_sort_col';
	const DEFAULT_SORT_DIR = 'default_sort_dir';

	protected $provides = array(
		self::GET_ITEMS_ENDPOINT,
		self::TASK_MODEL,
		self::DEFAULTS_MAP,
		self::PREF_FETCHER,
	);

	protected $endpoints = array(
		self::GET_ITEMS_ENDPOINT,
		self::REFRESH_ITEMS_ENDPOINT,
	);

	public function register() {
		$container = $this->getContainer();

		$container->add( self::DEFAULTS_MAP, function () {
			return array(
				self::FETCH_ENABLED    => 1,
				self::FETCH_INTERVAL   => 30,
				self::ITEMS_PER_PAGE   => 20,
				self::DEFAULT_SORT_COL => 'id',
				self::DEFAULT_SORT_DIR => 'asc',
			);
		} );

		$container->add( self::PREF_FETCHER, function () {
			return new Preferences();
		} );

		$container->share( self::TASK_MODEL, function () use ( $container ) {
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
		$container = $this->getContainer();
		$endpoints = $this->endpoints;

		add_action( 'rest_api_init', function () use ( $container, $endpoints ) {
			foreach ( $endpoints as $ep_name ) {
				/**
				 * @var \Gravity_Flow\Gravity_Flow\Ajax\Endpoint $endpoint
				 */
				$endpoint = $container->get( $ep_name );

				$endpoint->register_routes();
			}
		} );

		add_filter( 'gravityflow_js_config_shared', function ( $config ) use ( $container ) {
			/**
			 * @var Task $tasks
			 */
			$tasks   = $container->get( self::TASK_MODEL );
			$sc_args = $tasks->get_args_for_shortcode( 'inbox_default' );

			$grid_config['grid_options'] = array(
				'columnDefs'         => $tasks->get_table_header_defs( $sc_args),
				'rowData'            => $tasks->get_inbox_tasks( $sc_args ),
				'pagination'         => true,
				'paginationPageSize' => (int) $this->get_pref( self::ITEMS_PER_PAGE ),
			);

			$grid_config['current_user_token']     = $this->get_user_token();
			$grid_config[ self::FETCH_ENABLED ]    = (bool) $this->get_pref( self::FETCH_ENABLED );
			$grid_config[ self::FETCH_INTERVAL ]   = (int) $this->get_pref( self::FETCH_INTERVAL );
			$grid_config[ self::DEFAULT_SORT_COL ] = $this->get_pref( self::DEFAULT_SORT_COL );
			$grid_config[ self::DEFAULT_SORT_DIR ] = $this->get_pref( self::DEFAULT_SORT_DIR );

			$config['grids']['inbox_default'] = $grid_config;

			return $config;
		}, 10, 1 );
	}

	protected function get_pref( $pref_name ) {
		$container = $this->getContainer();
		$user_id   = get_current_user_id();
		$view      = 'inbox';

		/**
		 * @var array $defaults_map
		 */
		$defaults_map = $container->get( self::DEFAULTS_MAP );

		/**
		 * @var Preferences $prefs
		 */
		$prefs = $container->get( self::PREF_FETCHER );

		return $prefs->get_setting(
			$pref_name,
			$user_id,
			$view,
			$defaults_map[ $pref_name ]
		);
	}

	protected function get_user_token() {
		$user = wp_get_current_user();

		$assignee = new \Gravity_Flow_Assignee(
			array(
				'id'   => get_current_user_id(),
				'user' => $user,
				'type' => 'user_id'
			) );

		return gravity_flow()->generate_access_token( $assignee );
	}
}