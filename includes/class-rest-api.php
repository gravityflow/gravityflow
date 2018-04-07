<?php
/**
 * Gravity Flow REST API
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_REST_API
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public Licenses
 * @since       1.4.3
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * This is a partial implementation of the REST API version 2 and may be subject to change.
 *
 * @todo cover all functionality to provide complete headless access.
 *
 * @beta
 *
 * @since 1.4.3
 *
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 * Class Gravity_Flow_REST_API
 */
class Gravity_Flow_REST_API {

	/**
	 * Gravity_Flow_REST_API constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'action_rest_api_init' ) );
	}

	/**
	 * Register the workflow route.
	 */
	public function action_rest_api_init() {
		register_rest_route( 'gf/v2', '/entries/(?P<id>\d+)/workflow/(?P<base>[\S]+)', array(
			'methods' => 'POST',
			'callback' => array( $this, 'handle_rest_request' ),
			'permission_callback' => array( $this, 'post_items_permissions_check' ),
		) );
	}

	/**
	 * Process the request.
	 *
	 * @param WP_REST_Request $request The request instance.
	 *
	 * @return bool|Gravity_Flow_Step|mixed|WP_Error|WP_REST_Response
	 */
	public function handle_rest_request( $request ) {
		$step = $this->get_current_step( $request );

		if ( ! $step || is_wp_error( $step ) ) {
			return $step;
		}

		$entry = $step->get_entry();

		$entry_id = $entry['id'];

		$api = new Gravity_Flow_API( $entry['form_id'] );

		$response = $step->rest_callback( $request );

		$api->process_workflow( $entry_id );

		return $response;
	}

	/**
	 * Check if a REST request has permission.
	 *
	 * @param WP_REST_Request $request The request instance.
	 *
	 * @return Gravity_Flow_Step|bool|WP_Error
	 */
	public function post_items_permissions_check( $request ) {

		$step = $this->get_current_step( $request );

		if ( ! $step || is_wp_error( $step ) ) {
			return $step;
		}

		return $step->rest_permission_callback( $request );
	}

	/**
	 * Get the current step for the entry specified in the request.
	 *
	 * @param WP_REST_Request $request The request instance.
	 *
	 * @return bool|Gravity_Flow_Step|WP_Error
	 */
	public function get_current_step( $request ) {
		$entry_id = $request['id'];

		if ( empty( $entry_id ) ) {
			return new WP_Error( 'entry_missing', __( 'Entry ID missing', 'gravityflow', array( 'status' => 404 ) ) );
		}

		$rest_base = $request['base'];

		if ( empty( $rest_base ) ) {
			return new WP_Error( 'base_missing', __( 'Workflow base missing', 'gravityflow', array( 'status' => 404 ) ) );
		}

		$entry = GFAPI::get_entry( $entry_id );

		if ( empty( $entry_id ) ) {
			return new WP_Error( 'not_found', __( 'Entry not found', 'gravityflow', array( 'status' => 404 ) ) );
		}

		$api = new Gravity_Flow_API( $entry['form_id'] );

		$step = $api->get_current_step( $entry );

		if ( empty( $step ) ) {
			return new WP_Error( 'not_found', __( 'Entry not found', 'gravityflow', array( 'status' => 404 ) ) );
		}

		if ( $step->get_rest_base() != $rest_base ) {
			return new WP_Error( 'base_incorrect', __( 'The entry is not on the expected step.', 'gravityflow' ) );
		}

		return $step;
	}
}

new Gravity_Flow_REST_API();
