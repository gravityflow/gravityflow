<?php
/**
 * Gravity Flow Step Webhook
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Webhook
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Webhook
 */
class Gravity_Flow_Step_Webhook extends Gravity_Flow_Step {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'webhook';

	/**
	 * The temporary credentials.
	 *
	 * @var array
	 */
	protected $temporary_credentials = array();

	/**
	 * The OAuth1 Client
	 *
	 * @var Gravity_Flow_Oauth1_Client
	 */
	protected $oauth1_client;

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Outgoing Webhook', 'gravityflow' );
	}

	/**
	 * Returns the HTML for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return '<i class="fa fa-external-link"></i>';
	}

	/**
	 * Returns an array of settings for this step type.
	 *
	 * @return array
	 */
	public function get_settings() {
		$connected_apps = gravityflow_connected_apps()->get_connected_apps();
		$connected_apps_options = array(
			array(
				'label' => esc_html__( 'Select a Connected App', 'gravityflow' ),
				'value' => '',
			),
		);
		foreach ( $connected_apps as $key => $app ) {
			$connected_apps_options[ $key ] = array(
				'label' => $app['app_name'],
				'value' => $app['app_id'],
			);
		}

		$settings = array(
			'title'  => esc_html__( 'Outgoing Webhook', 'gravityflow' ),
			'fields' => array(
				array(
					'name'  => 'url',
					'class' => 'large merge-tag-support',
					'label' => esc_html__( 'Outgoing Webhook URL', 'gravityflow' ),
					'type'  => 'text',
				),
				array(
					'name'          => 'method',
					'label'         => esc_html__( 'Request Method', 'gravityflow' ),
					'type'          => 'select',
					'default_value' => 'post',
					'onchange'    => "jQuery(this).closest('form').submit();",
					'choices'       => array(
						array(
							'label' => 'POST',
							'value' => 'post',
						),
						array(
							'label' => 'GET',
							'value' => 'get',
						),
						array(
							'label' => 'PUT',
							'value' => 'put',
						),
						array(
							'label' => 'DELETE',
							'value' => 'delete',
						),
						array(
							'label' => 'PATCH',
							'value' => 'patch',
						),
					),
				),
				array(
					'name'          => 'authentication',
					'label'         => esc_html__( 'Request Authentication Type', 'gravityflow' ),
					'type'          => 'select',
					'onchange'    => "jQuery(this).closest('form').submit();",
					'default_value' => '',
					'choices'       => array(
						array(
							'label' => 'None',
							'value' => '',
						),
						array(
							'label' => 'Basic',
							'value' => 'basic',
						),
						array(
							'label' => 'Connected App',
							'value' => 'connected_app',
						),
					),
				),
				array(
					'name'  => 'basic_username',
					'label' => esc_html__( 'Username', 'gravityflow' ),
					'type'  => 'text',
					'dependency' => array(
						'field' => 'authentication',
						'values' => array( 'basic' ),
					),
				),
				array(
					'name'  => 'basic_password',
					'label' => esc_html__( 'Password', 'gravityflow' ),
					'type'  => 'text',
					'dependency' => array(
						'field' => 'authentication',
						'values' => array( 'basic' ),
					),
				),
				array(
					'name'  => 'connected_app',
					'label' => esc_html__( 'Connected App', 'gravityflow' ),
					'type'  => 'select',
					'tooltip' => esc_html__( 'Manage your Connected Apps in the Workflow->Settings->Connected Apps page. ', 'gravityflow' ),
					'dependency' => array(
						'field' => 'authentication',
						'values' => array(
							'connected_app',
						),
					),
					'choices' => $connected_apps_options,
				),
				array(
					'label'          => esc_html__( 'Request Headers', 'gravityflow' ),
					'name'           => 'requestHeaders',
					'type'           => 'generic_map',
					'required'       => false,
					'merge_tags'     => true,
					'tooltip'        => sprintf(
						'<h6>%s</h6>%s',
						esc_html__( 'Request Headers', 'gravityflow' ),
						esc_html__( 'Setup the HTTP headers to be sent with the webhook request.', 'gravityflow' )
					),
					'key_choices' => $this->get_header_choices(),
					// The Add-On Framework now contains the generic map field but with a slight difference
					// The key_field and value_field elements are included here for when Gravity Flow removes the generic map field.
					'key_field'      => array(
						'choices'      => $this->get_header_choices(),
						'custom_value' => true,
						'title'        => esc_html__( 'Name', 'gravityflow' ),
					),
					'value_field'    => array(
						'choices'      => 'form_fields',
						'custom_value' => true,
					),
				),
				array(
					'name' => 'body',
					'label' => esc_html__( 'Request Body', 'gravityflow' ),
					'type' => 'radio',
					'default_value' => 'select',
					'horizontal' => true,
					'onchange'    => "jQuery(this).closest('form').submit();",
					'choices' => array(
						array(
							'label' => __( 'Select Fields', 'gravityflow' ),
							'value' => 'select',
						),
						array(
							'label' => __( 'Raw request', 'gravityflow' ),
							'value' => 'raw',
						),
					),
					'dependency' => array(
						'field'  => 'method',
						'values' => array( '', 'post', 'put', 'patch' ),
					),
				),
			),
		);
		if ( in_array( $this->get_setting( 'method' ), array( 'post', 'put', 'patch', '' ) ) ) {

			if ( $this->get_setting( 'body' ) == 'raw' ) {
				global $_gaddon_posted_settings;

				if ( ! empty( $_gaddon_posted_settings ) ) {
					$this->set_posted_raw_body_value();
				}

				$settings['fields'][] = array(
					'name'  => 'raw_body',
					'label' => esc_html__( 'Raw Body', 'gravityflow' ),
					'type'  => 'textarea',
					'class' => 'fieldwidth-3 fieldheight-2',
					'save_callback' => array( $this, 'save_callback_raw_body' ),
				);
			} else {

				$settings['fields'][] = array(
					'name'          => 'format',
					'label'         => esc_html__( 'Format', 'gravityflow' ),
					'type'          => 'select',
					'tooltip'       => esc_html__( 'If JSON is selected then the Content-Type header will be set to application/json', 'gravityflow' ),
					'onchange'      => "jQuery(this).closest('form').submit();",
					'default_value' => 'json',
					'choices'       => array(
						array(
							'label' => 'JSON',
							'value' => 'json',
						),
						array(
							'label' => 'FORM',
							'value' => 'form',
						),
					),
				);
				$settings['fields'][] = array(
					'name'          => 'body_type',
					'label'         => esc_html__( 'Body Content', 'gravityflow' ),
					'type'          => 'radio',
					'default_value' => 'all_fields',
					'horizontal'    => true,
					'onchange'      => "jQuery(this).closest('form').submit();",
					'choices'       => array(
						array(
							'label' => __( 'All Fields', 'gravityflow' ),
							'value' => 'all_fields',
						),
						array(
							'label' => __( 'Select Fields', 'gravityflow' ),
							'value' => 'select_fields',
						),
					),
					'dependency'    => array(
						'field'  => 'body',
						'values' => array( 'select', '' ),
					),
				);
				$settings['fields'][] = array(
					'name'                => 'mappings',
					'label'               => esc_html__( 'Request Field Values', 'gravityflow' ),
					'type'                => 'generic_map',
					'enable_custom_key'   => false,
					'enable_custom_value' => true,
					'key_field_title'     => esc_html__( 'Key', 'gravityflow' ),
					'value_field_title'   => esc_html__( 'Value', 'gravityflow' ),
					'value_choices'       => $this->value_mappings(),
					'tooltip'             => '<h6>' . esc_html__( 'Mapping', 'gravityflow' ) . '</h6>' . esc_html__( 'Map the fields of this form to the selected form. Values from this form will be saved in the entry in the selected form', 'gravityflow' ),
					'dependency'          => array(
						'field'  => 'body_type',
						'values' => array( 'select_fields' ),
					),
				);
			}
		}

		$settings['fields'][] = array(
			'name'          => 'response_body',
			'label'         => esc_html__( 'Response Mapping', 'gravityflow' ),
			'type'          => 'radio',
			'default_value' => '',
			'horizontal'    => true,
			'onchange'      => "jQuery(this).closest('form').submit();",
			'tooltip'       => '<h6>' . esc_html__( 'Mapping', 'gravityflow' ) . '</h6>' . esc_html__( 'Whether to map selected values from the request response to the form entry.', 'gravityflow' ),
			'choices'       => array(
				array(
					'label' => __( 'None', 'gravityflow' ),
					'value' => '',
				),
				array(
					'label' => __( 'Select Fields', 'gravityflow' ),
					'value' => 'select_fields',
				),
			),
		);

		if ( in_array( $this->get_setting( 'response_body' ), array( 'select_fields' ) ) ) {
			$settings['fields'][] = array(
				'name'                => 'response_mappings',
				'label'               => esc_html__( 'Response Field Values', 'gravityflow' ),
				'type'                => 'generic_map',
				'enable_custom_key'   => false,
				'enable_custom_value' => true,
				'key_field_title'     => esc_html__( 'Key', 'gravityflow' ),
				'value_field_title'   => esc_html__( 'Field', 'gravityflow' ),
				'value_choices'       => $this->value_mappings(),
				'tooltip'             => '<h6>' . esc_html__( 'Response Mapping', 'gravityflow' ) . '</h6>' . esc_html__( 'Map selected field values from the request response to the form. Values from the response will be saved in the entry in the selected form.', 'gravityflow' ),
				'dependency'          => array(
					'field'  => 'response_body',
					'values' => array( 'select_fields' ),
				),
			);
		}

		return $settings;
	}

	/**
	 * Returns an array of statuses and their properties.
	 *
	 * @return array
	 */
	public function get_status_config() {
		return array(
			array(
				'status'                    => 'complete',
				'status_label'              => __( 'Success', 'gravityflow' ),
				'destination_setting_label' => esc_html__( 'Next Step if Success', 'gravityflow' ),
				'default_destination'       => 'next',
			),
			array(
				'status'                    => 'error_client',
				'status_label'              => __( 'Error - Client', 'gravityflow' ),
				'destination_setting_label' => esc_html__( 'Next Step if Client Error', 'gravityflow' ),
				'default_destination'       => 'complete',
			),
			array(
				'status'                    => 'error_server',
				'status_label'              => __( 'Error - Server', 'gravityflow' ),
				'destination_setting_label' => esc_html__( 'Next Step if Server Error', 'gravityflow' ),
				'default_destination'       => 'complete',
			),
			array(
				'status'                    => 'error',
				'status_label'              => __( 'Error - Other', 'gravityflow' ),
				'destination_setting_label' => esc_html__( 'Next step if Other Error', 'gravityflow' ),
				'default_destination'       => 'complete',
			),
		);
	}

	/**
	 * Prepares common HTTP header names as choices.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_header_choices() {

		return array(
			array(
				'label' => esc_html__( 'Select a Name', 'gravityformswebhooks' ),
				'value' => '',
			),
			array(
				'label' => 'Accept',
				'value' => 'Accept',
			),
			array(
				'label' => 'Accept-Charset',
				'value' => 'Accept-Charset',
			),
			array(
				'label' => 'Accept-Encoding',
				'value' => 'Accept-Encoding',
			),
			array(
				'label' => 'Accept-Language',
				'value' => 'Accept-Language',
			),
			array(
				'label' => 'Accept-Datetime',
				'value' => 'Accept-Datetime',
			),
			array(
				'label' => 'Cache-Control',
				'value' => 'Cache-Control',
			),
			array(
				'label' => 'Connection',
				'value' => 'Connection',
			),
			array(
				'label' => 'Cookie',
				'value' => 'Cookie',
			),
			array(
				'label' => 'Content-Length',
				'value' => 'Content-Length',
			),
			array(
				'label' => 'Content-Type',
				'value' => 'Content-Type',
			),
			array(
				'label' => 'Date',
				'value' => 'Date',
			),
			array(
				'label' => 'Expect',
				'value' => 'Expect',
			),
			array(
				'label' => 'Forwarded',
				'value' => 'Forwarded',
			),
			array(
				'label' => 'From',
				'value' => 'From',
			),
			array(
				'label' => 'Host',
				'value' => 'Host',
			),
			array(
				'label' => 'If-Match',
				'value' => 'If-Match',
			),
			array(
				'label' => 'If-Modified-Since',
				'value' => 'If-Modified-Since',
			),
			array(
				'label' => 'If-None-Match',
				'value' => 'If-None-Match',
			),
			array(
				'label' => 'If-Range',
				'value' => 'If-Range',
			),
			array(
				'label' => 'If-Unmodified-Since',
				'value' => 'If-Unmodified-Since',
			),
			array(
				'label' => 'Max-Forwards',
				'value' => 'Max-Forwards',
			),
			array(
				'label' => 'Origin',
				'value' => 'Origin',
			),
			array(
				'label' => 'Pragma',
				'value' => 'Pragma',
			),
			array(
				'label' => 'Proxy-Authorization',
				'value' => 'Proxy-Authorization',
			),
			array(
				'label' => 'Range',
				'value' => 'Range',
			),
			array(
				'label' => 'Referer',
				'value' => 'Referer',
			),
			array(
				'label' => 'TE',
				'value' => 'TE',
			),
			array(
				'label' => 'User-Agent',
				'value' => 'User-Agent',
			),
			array(
				'label' => 'Upgrade',
				'value' => 'Upgrade',
			),
			array(
				'label' => 'Via',
				'value' => 'Via',
			),
			array(
				'label' => 'Warning',
				'value' => 'Warning',
			),
		);

	}

	/**
	 * Settings are JSON decoded so this callback resets the value to the raw value and strips scripts if the current
	 * user cannot unfiltered_html. This circumvents the automatic parsing of JSON values by the add-on framework.
	 *
	 * @since 1.8.1
	 *
	 * @param array $field         The setting properties.
	 * @param mixed $field_setting The setting value.
	 *
	 * @return string
	 */
	function save_callback_raw_body( $field, $field_setting ) {
		return $this->set_posted_raw_body_value();
	}


	/**
	 * Sets the value of the raw_body setting in the $_gaddon_posted_settings global and strips scripts if the current
	 * user cannot unfiltered_html. This circumvents the automatic parsing of JSON values by the add-on framework.
	 *
	 * @since 1.8.1
	 *
	 * @return string the raw value
	 */
	protected function set_posted_raw_body_value() {
		global $_gaddon_posted_settings;

		$raw_value = rgpost( '_gaddon_setting_raw_body' );

		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$raw_value = wp_kses_post( $raw_value );
		}

		$_gaddon_posted_settings['raw_body'] = $raw_value;

		return $raw_value;
	}

	/**
	 * Process the step. For example, assign to a user, send to a service, send a notification or do nothing. Return (bool) $complete.
	 *
	 * @return bool Is the step complete?
	 */
	function process() {

		$step_status = $this->send_webhook();

		//Ensure webhook steps defined / last updated prior to v2.0.2 (w/o 4xx, 5xx, other response code config) continue to process
		$destination_status_key = 'destination_' . $step_status;
		if ( ! isset( $this->{$destination_status_key} ) ) {
			$step_status = 'complete';
		}

		$this->update_step_status( $step_status );
		return true;
	}

	/**
	 * Processes the webhook request.
	 *
	 * @return string The step status.
	 */
	function send_webhook() {

		$entry = $this->get_entry();

		$url = $this->url;

		$this->log_debug( __METHOD__ . '() - url before replacing variables: ' . $url );

		$url = GFCommon::replace_variables( $url, $this->get_form(), $entry, true, false, false, 'text' );

		$this->log_debug( __METHOD__ . '() - url after replacing variables: ' . $url );

		$method = strtoupper( $this->method );

		$body = null;

		// Get request headers.
		$headers = gravity_flow()->get_generic_map_fields( $this->get_feed_meta(), 'requestHeaders', $this->get_form(), $entry );

		// Remove request headers with undefined name.
		unset( $headers[ null ] );

		if ( $this->authentication == 'basic' ) {
			$auth_string = sprintf( '%s:%s', $this->basic_username, $this->basic_password );
			$headers['Authorization'] = sprintf( 'Basic %s', base64_encode( $auth_string ) );
		}
		$this->log_debug( __METHOD__ . '() - log body setting ' . $this->body . ' :: ' . $this->raw_body );
		if ( $this->body == 'raw' ) {
			$body = $this->raw_body;
			$body = GFCommon::replace_variables( $body, $this->get_form(), $entry, false, false, false, 'text' );
			$this->log_debug( __METHOD__ . '() - got body after replace vars: ' . $body );
		} elseif ( in_array( $method, array( 'POST', 'PUT', 'PATCH' ) ) ) {
			$body = $this->get_request_body();
			if ( $this->format == 'json' ) {
				$headers['Content-type'] = 'application/json';
				$body    = json_encode( $body );
			} else {
				$headers = array();
			}
		}

		if ( $this->authentication == 'connected_app' ) {

			$app_id = $this->get_setting( 'connected_app' );

			$connected_app = gravityflow_connected_apps()->get_app( $app_id );

			if ( empty( $connected_app ) ) {
				$this->log_debug( __METHOD__ . '() - Connected app not found: ' . $app_id );
			}

			$access_credentials = rgar( $connected_app, 'access_creds' );

			require_once( dirname( __FILE__ ) . '/../class-oauth1-client.php' );
			$this->oauth1_client = new Gravity_Flow_Oauth1_Client(
				array(
					'consumer_key'    => $connected_app['consumer_key'],
					'consumer_secret' => $connected_app['consumer_secret'],
					'token'           => rgar( $access_credentials, 'oauth_token' ),
					'token_secret'    => rgar( $access_credentials, 'oauth_token_secret' ),
				),
				'gravi_flow_' . $connected_app['consumer_key'],
				$this->get_setting( 'url' )
			);

			if ( ! is_array( $access_credentials ) ) {
				$this->log_debug( __METHOD__ . '() - No access credentials: ' . print_r( $access_credentials, true ) );
			} else {
				$this->oauth1_client->config['token']        = $access_credentials['oauth_token'];
				$this->oauth1_client->config['token_secret'] = $access_credentials['oauth_token_secret'];
			}

			// Note we don't send the final $options[] parameter in here because our request is always sent in the body.
			$headers['Authorization'] = $this->oauth1_client->get_full_request_header( $this->get_setting( 'url' ), $method );
		}

		$args = array(
			'method'      => $method,
			'timeout'     => 45,
			'redirection' => 3,
			'blocking'    => true,
			'headers'     => $headers,
			'body'        => $body,
			'cookies'     => array(),
		);

		$args = apply_filters( 'gravityflow_webhook_args', $args, $entry, $this );
		$args = apply_filters( 'gravityflow_webhook_args_' . $this->get_form_id(), $args, $entry, $this );

		$response = wp_remote_request( $url, $args );
		$this->log_debug( __METHOD__ . '() - request: ' . print_r( $args, true ) );
		$this->log_debug( __METHOD__ . '() - response: ' . print_r( $response, true ) );

		if ( is_wp_error( $response ) ) {
			$step_status = 'error';
			$http_response_message = ' (WP Error)';
		} else {

			if ( $this->get_setting( 'response_body' ) == 'select_fields' ) {

				$this->log_debug( __METHOD__ . '(): Attempting to map response data into $entry' );

				if ( ! is_array( $this->response_mappings ) ) {
					$this->log_debug( __METHOD__ . '(): Step has no response mappings defined' );
					return;
				}

				$data = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( ! is_array( $data ) ) {
					$this->log_debug( __METHOD__ . '(): Response body does not include properly formatted JSON' );
					return;
				}

				foreach ( $this->response_mappings as $mapping ) {
					if ( rgblank( $mapping['key'] ) ) {
						continue;
					}

					$entry = $this->add_mapping_to_entry( $entry, $mapping, $data, array( 'subtype' => 'json' ) );

				}

				$result = GFAPI::update_entry( $entry );

				if ( is_wp_error( $result ) ) {
					$this->log_debug( __METHOD__ . '(): There was an issue with updating the entry.' );
				} else {
					$this->log_debug( __METHOD__ . '(): Updated entry: ' . print_r( $entry, true ) );
				}

			}

			if ( isset( $response['response']['code'] ) ) {
				$http_response_code = intval( $response['response']['code'] );
				switch ( true ) {
					case in_array( $http_response_code, range( 200,299 ) ):
						$http_response_message = $response['response']['code'] . ' ' . $response['response']['message'] . ' (Success)';
						$step_status = 'complete';
						break;
					case in_array( $http_response_code, range( 400,499 ) ):
						$step_status = 'error_client';
						$http_response_message = $response['response']['code'] . ' ' . $response['response']['message'] . ' (Client Error)';
						break;
					case in_array( $http_response_code, range( 500,599 ) ):
						$step_status = 'error_server';
						$http_response_message = $response['response']['code'] . ' ' . $response['response']['message'] . ' (Server Error)';
						break;
					default:
						$step_status = 'error';
						$http_response_message = $response['response']['code'] . ' ' . $response['response']['message'] . ' (Error)';
				}
			} else {
				$step_status = 'error';
				$http_response_message = ' (Error)';
			}
		}

		/**
		 * Allow the step status to be modified on the webhook step.
		 *
		 * @param string              $step_status The step status derived from webhook response.
		 * @param array               $response    The response returned from webhook.
		 * @param array               $args        The arguments used for executing the webhook request.
		 * @param array               $entry       The current entry.
		 * @param Gravity_Flow_Step   $this        The current step.
		 *
		 * @return string
		 */
		$step_status = apply_filters( 'gravityflow_step_status_webhook', $step_status, $response, $args, $entry, $this );

		/**
		 * Allow the message logged to the timeline following webhook step to be modified
		 *
		 * @param string              $http_response_message The status message derived from webhook response.
		 * @param string              $step_status           The step status derived from webhook response.
		 * @param array               $response              The response returned from webhook.
		 * @param array               $args                  The arguments used for executing the webhook request.
		 * @param array               $entry                 The current entry.
		 * @param Gravity_Flow_Step   $this                  The current step.
		 *
		 * @return string
		 */
		$custom_response_message = apply_filters( 'gravityflow_response_message_webhook', $http_response_message, $step_status, $response, $args, $entry, $this );

		if ( $custom_response_message == $http_response_message ) {

			$show_url_in_note = true;

			/**
			 * Allows the URL to be hidden in the notes. Useful if API keys are in the URL params.
			 *
			 * @since 2.3.1
			 *
			 * @param bool $show_url_in_note
			 */
			$show_url_in_note = gf_apply_filters( array( 'gravityflow_webhook_url_in_note', $this->get_form_id() ), $show_url_in_note );

			$url_in_note = $show_url_in_note ? sprintf( 'URL: %s.', $url ) : '';

			/* Translators: 1st placeholders is URL provided by user in step settings, 2nd placeholder is response codes from webhook execution */
			$this->add_note( sprintf( esc_html__( 'Webhook sent. %1$s RESPONSE: %2$s', 'gravityflow' ), $url_in_note, $http_response_message ) );

			$this->log_debug( __METHOD__ . '() - result: ' . $http_response_message );
		} else {

			$this->add_note( esc_html( $custom_response_message ) );

			$this->log_debug( __METHOD__ . '() - result: ' . $custom_response_message );
		}

		do_action( 'gravityflow_post_webhook', $response, $args, $entry, $this );

		return $step_status;
	}

	/**
	 * Add the mapped value of the response to the entry.
	 *
	 * @since 2.2.4-dev
	 *
	 * @param array $entry        The entry to update.
	 * @param array $mapping      The properties for the mapping being processed.
	 * @param array $data         The response params.
	 * @param array $content_type The content type of incoming request.
	 *
	 * @return array
	 */
	public function add_mapping_to_entry( $entry, $mapping, $data, $content_type ) {

		$skip_mapping = false;

		$source_field_id = trim( $mapping['custom_key'] );

		$target_field_id = (string) $mapping['value'];

		$value = $this->parse_response_value( $data, $source_field_id );

		if ( is_wp_error( $value ) ) {
			$this->log_debug( $value->get_error_message() );
			return $entry;
		}

		$form = $this->get_form();

		$target_field = GFFormsModel::get_field( $form, $target_field_id );

		if ( $target_field instanceof GF_Field ) {

			if ( in_array( $target_field->type, array( 'fileupload', 'post_title', 'post_content', 'post_excerpt', 'post_tags', 'post_category', 'post_image', 'post_custom_field', 'product', 'singleproduct', 'quantity', 'option', 'shipping', 'singleshipping', 'total' ) ) ) {
				$skip_mapping = true;
			} else {
				$this->log_debug( __METHOD__ . '(): Mapping into Field #' . $target_field->id . ' / Type: ' . $target_field->type );

				$is_full_target      = $target_field_id === (string) intval( $target_field_id );
				$target_field_inputs = $target_field->get_entry_inputs();

				// Non-Choice Field Type.
				if ( $is_full_target && ! is_array( $target_field_inputs ) ) {

					if ( rgar( $content_type, 'subtype' ) == 'json' && in_array( $target_field->type, array( 'multiselect', 'workflow_multi_user' ) ) ) {

						if ( ! is_array( $value ) ) {
							$value = json_decode( $value );
						}

						$entry[ $target_field_id ] = $target_field->get_value_save_entry( $value, $form, false, $entry['id'], $entry );

					} elseif ( in_array( $target_field->type, array( 'workflow_discussion' ) ) ) {
						$entry[ $target_field_id ] = $target_field->get_value_save_entry( $value, $form, false, $entry['id'], $entry );
					} else {
						$entry[ $target_field_id ] = $target_field->sanitize_entry_value( $value, $form['id'] );
					}

					// Choice Field Types.
				} elseif ( is_array( $target_field_inputs ) ) {

					// Received Parent Input ID.
					if ( $target_field_id == $target_field['id'] ) {

						if ( is_array( $value ) ) {
							$choices = $value;
						} else {
							$choices = json_decode( $value, true );
						}

						foreach ( $choices as $source_field ) {

							$entry[ $source_field['id'] ] = $target_field->sanitize_entry_value( $source_field['value'], $form['id'] );
						}

						// Received Direct Input ID.
					} else {
						foreach ( $target_field_inputs as $input ) {
							if ( $target_field_id === $input['id'] ) {
								$entry[ $target_field_id ] = $target_field->sanitize_entry_value( $value, $form['id'] );
								break;
							}
						}
					}
				} else {
					$skip_mapping = true;
				}
			}
		} elseif ( $target_field_id == 'gf_custom' ) {
			$this->log_debug( __METHOD__ . '(): Mapping into gf_custom' );
			$entry[ $target_field_id ] = GFCommon::replace_variables( $value, $form, $entry, false, true, false, 'text' );
		} else {
			$skip_mapping = true;
		}

		if ( $skip_mapping ) {
			if ( is_object( $target_field ) ) {
				$this->log_debug( __METHOD__ . '(): Field Type ' . $target_field->type . ' - Not available for import yet.' );
			} else {
				$this->log_debug( __METHOD__ . '(): Incoming field mapping error.' );
			}
		}

		/**
		 * Allow the entry to be modified during the response mapping of the webhook step.
		 *
		 * @since 2.2.4-dev
		 *
		 * @param array               $entry        The entry
		 * @param array               $mapping      The response field mappings for outgoing webhook step.
		 * @param array               $data         The response data
		 * @param Gravity_Flow_Step   $this         The current step.
		 *
		 * @return array
		 */
		$entry = apply_filters( 'gravityflow_entry_webhook_response_mapping', $entry, $mapping, $data, $this );

		return $entry;
	}

	/**
	 * Parses the response value. Uses the backslash to drill down into arrays.
	 *
	 * @since 2.3.1
	 *
	 * @param array  $value     The response values.
	 * @param string $key       The key used to lookup the value.
	 * @param string $default   The defaeult return value.
	 *
	 * @return array|string|WP_Error
	 */
	public function parse_response_value( $value, $key, $default = '' ) {

		if ( ! is_array( $value ) && ! ( is_object( $value ) && $value instanceof ArrayAccess ) ) {
			return $default;
		}

		/* translators: %s is the key used to lookup the value in the REST API response */
		$error_message = sprintf( __( 'The key %s does not match any element in the response.', 'gravityflow' ), $key ) ;

		$this->log_debug( __METHOD__ . '() - key before replacing variables: ' . $key );
		$key = GFCommon::replace_variables( $key, $this->get_form(), $this->get_entry(), true, false, false, 'text' );
		$this->log_debug( __METHOD__ . '() - key after replacing variables: ' . $key );

		if ( strpos( $key, '\\' ) === false ) {
			return isset( $value[ $key ] ) ? $value[ $key ] : new WP_Error( 'invalid_key', $error_message );
		}

		$names = explode( '\\', $key );
		if ( $names === false ) {
			return new WP_Error( 'invalid_key_parsing', $error_message );
		}
		$val = $value;
		foreach ( $names as $current_name ) {
			$val = rgar( $val, $current_name, $default );
		}

		return $val;
	}

	/**
	 * Determines the current status of the step.
	 *
	 * @return string
	 */
	public function status_evaluation() {
		$step_status = $this->get_status();

		return $step_status;
	}

	/**
	 * Determines if the current step has been completed.
	 *
	 * @return bool
	 */
	public function is_complete() {
		$status = $this->evaluate_status();

		return ! in_array( $status, array( 'pending', 'queued' ) );
	}

	/**
	 * Prepare value map.
	 *
	 * @return array
	 */
	public function value_mappings() {

		$form = $this->get_form();

		$fields = $this->get_field_map_choices( $form );
		return $fields;
	}

	/**
	 * Returns the choices for the source field mappings.
	 *
	 * @param array       $form                The current form.
	 * @param null|string $field_type          Field types to include as choices. Defaults to null.
	 * @param null|array  $exclude_field_types Field types to exclude as choices. Defaults to null.
	 *
	 * @return array
	 */
	public function get_field_map_choices( $form, $field_type = null, $exclude_field_types = null ) {

		$fields = array();

		// Setup first choice.
		if ( rgblank( $field_type ) || ( is_array( $field_type ) && count( $field_type ) > 1 ) ) {

			$first_choice_label = __( 'Select a Field', 'gravityflow' );

		} else {

			$type = is_array( $field_type ) ? $field_type[0] : $field_type;
			$type = ucfirst( GF_Fields::get( $type )->get_form_editor_field_title() );

			/* Translators: Placeholder is for the field type which should be selected */
			$first_choice_label = sprintf( __( 'Select a %s Field', 'gravityflow' ), $type );

		}

		$fields[] = array(
			'value' => '',
			'label' => $first_choice_label,
		);

		// If field types not restricted add the default fields and entry meta.
		if ( is_null( $field_type ) ) {
			$fields[] = array( 'value' => 'id', 'label' => esc_html__( 'Entry ID', 'gravityflow' ) );
			$fields[] = array( 'value' => 'date_created', 'label' => esc_html__( 'Entry Date', 'gravityflow' ) );
			$fields[] = array( 'value' => 'ip', 'label' => esc_html__( 'User IP', 'gravityflow' ) );
			$fields[] = array( 'value' => 'source_url', 'label' => esc_html__( 'Source Url', 'gravityflow' ) );
			$fields[] = array( 'value' => 'created_by', 'label' => esc_html__( 'Created By', 'gravityflow' ) );

			$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );
			foreach ( $entry_meta as $meta_key => $meta ) {
				$fields[] = array(
					'value' => $meta_key,
					'label' => rgars( $entry_meta, "{$meta_key}/label" ),
				);
			}
		}

		// Populate form fields.
		if ( is_array( $form['fields'] ) ) {
			foreach ( $form['fields'] as $field ) {
				$input_type = $field->get_input_type();
				$inputs     = $field->get_entry_inputs();
				$field_is_valid_type = ( empty( $field_type ) || ( is_array( $field_type ) && in_array( $input_type, $field_type ) ) || ( ! empty( $field_type ) && $input_type == $field_type ) );

				if ( is_null( $exclude_field_types ) ) {
					$exclude_field = false;
				} elseif ( is_array( $exclude_field_types ) ) {
					if ( in_array( $input_type, $exclude_field_types ) ) {
						$exclude_field = true;
					} else {
						$exclude_field = false;
					}
				} else {
					// Not array, so should be single string.
					if ( $input_type == $exclude_field_types ) {
						$exclude_field = true;
					} else {
						$exclude_field = false;
					}
				}

				if ( is_array( $inputs ) && $field_is_valid_type && ! $exclude_field ) {
					// If this is an address field, add full name to the list.
					if ( $input_type == 'address' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
						);
					}
					// If this is a name field, add full name to the list.
					if ( $input_type == 'name' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
						);
					}
					// If this is a checkbox field, add to the list.
					if ( $input_type == 'checkbox' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Selected', 'gravityflow' ) . ')',
						);
					}

					foreach ( $inputs as $input ) {
						$fields[] = array(
							'value' => $input['id'],
							'label' => GFCommon::get_label( $field, $input['id'] ),
						);
					}
				} elseif ( $input_type == 'list' && $field->enableColumns && $field_is_valid_type && ! $exclude_field ) {
					$fields[] = array(
						'value' => $field->id,
						'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
					);
					$col_index = 0;
					foreach ( $field->choices as $column ) {
						$fields[] = array(
							'value' => $field->id . '.' . $col_index,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html( rgar( $column, 'text' ) ) . ')',
						);
						$col_index ++;
					}
				} elseif ( ! rgar( $field, 'displayOnly' ) && $field_is_valid_type && ! $exclude_field ) {
					$fields[] = array(
						'value' => $field->id,
						'label' => GFCommon::get_label( $field ),
					);
				}
			}
		}

		return $fields;
	}

	/**
	 * Returns the request body.
	 *
	 * @return array|null The request body.
	 */
	public function get_request_body() {
		$entry = $this->get_entry();
		if ( empty( $this->body_type ) || $this->body_type == 'all_fields' ) {
			return $entry;
		}

		return $this->do_request_body_mapping();
	}

	/**
	 * Performs the body's response mappings.
	 */
	public function do_request_body_mapping() {
		$body = array();

		if ( ! is_array( $this->mappings ) ) {

			return $body;
		}

		foreach ( $this->mappings as $mapping ) {
			if ( rgblank( $mapping['key'] ) ) {
				continue;
			}

			$body = $this->add_mapping_to_body( $mapping, $body );
		}

		return $body;
	}

	/**
	 * Add the mapped value to the body.
	 *
	 * @param array $mapping The properties for the mapping being processed.
	 * @param array $body    The body to sent.
	 *
	 * @return array
	 */
	public function add_mapping_to_body( $mapping, $body ) {
		$target_field_id = trim( $mapping['custom_key'] );

		$source_field_id = (string) $mapping['value'];

		$entry = $this->get_entry();

		$form = $this->get_form();

		$source_field = GFFormsModel::get_field( $form, $source_field_id );

		if ( is_object( $source_field ) ) {
			$is_full_source      = $source_field_id === (string) intval( $source_field_id );
			$source_field_inputs = $source_field->get_entry_inputs();

			if ( $is_full_source && is_array( $source_field_inputs ) ) {
				$body[ $target_field_id ] = $source_field->get_value_export( $entry, $source_field_id, true );
			} else {
				$body[ $target_field_id ] = $this->get_source_field_value( $entry, $source_field, $source_field_id );
			}
		} elseif ( $source_field_id == 'gf_custom' ) {
			$body[ $target_field_id ] = GFCommon::replace_variables( $mapping['custom_value'], $form, $entry, false, false, false, 'text' );
		} else {
			$body[ $target_field_id ] = $entry[ $source_field_id ];
		}

		return $body;
	}

	/**
	 * Get the source field value.
	 *
	 * Returns the choice text instead of the unique value for choice based poll, quiz and survey fields.
	 *
	 * The source field choice unique value will not match the target field unique value.
	 *
	 * @param array    $entry           The entry being processed by this step.
	 * @param GF_Field $source_field    The source field being processed.
	 * @param string   $source_field_id The ID of the source field or input.
	 *
	 * @return string
	 */
	public function get_source_field_value( $entry, $source_field, $source_field_id ) {
		$field_value = $entry[ $source_field_id ];

		if ( in_array( $source_field->type, array( 'poll', 'quiz', 'survey' ) ) ) {
			if ( $source_field->inputType == 'rank' ) {
				$values = explode( ',', $field_value );
				foreach ( $values as &$value ) {
					$value = $this->get_source_choice_text( $value, $source_field );
				}

				return implode( ',', $values );
			}

			if ( $source_field->inputType == 'likert' && $source_field->gsurveyLikertEnableMultipleRows ) {
				list( $row_value, $field_value ) = rgexplode( ':', $field_value, 2 );
			}

			return $this->get_source_choice_text( $field_value, $source_field );
		}

		return $field_value;
	}

	/**
	 * Gets the choice text for the supplied choice value.
	 *
	 * @param string   $selected_choice The choice value from the source field.
	 * @param GF_Field $source_field    The source field being processed.
	 *
	 * @return string
	 */
	public function get_source_choice_text( $selected_choice, $source_field ) {
		return $this->get_choice_property( $selected_choice, $source_field->choices, 'value', 'text' );
	}

	/**
	 * Helper to get the specified choice property for the selected choice.
	 *
	 * @param string $selected_choice  The selected choice value or text.
	 * @param array  $choices          The field choices.
	 * @param string $compare_property The choice property the $selected_choice is to be compared against.
	 * @param string $return_property  The choice property to be returned.
	 *
	 * @return string
	 */
	public function get_choice_property( $selected_choice, $choices, $compare_property, $return_property ) {
		if ( $selected_choice && is_array( $choices ) ) {
			foreach ( $choices as $choice ) {
				if ( $choice[ $compare_property ] == $selected_choice ) {
					return $choice[ $return_property ];
				}
			}
		}

		return $selected_choice;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Webhook() );
