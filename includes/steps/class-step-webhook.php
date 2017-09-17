<?php
/**
 * Gravity Flow Step Webhook
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Webhook
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Gravity Flow Web Hook
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Webhook
 * @copyright   Copyright (c) 2015-2016, Steven Henty
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Gravity_Flow_Step_Webhook extends Gravity_Flow_Step {
	public $_step_type = 'webhook';
	protected $temporary_credentials = array();
	protected $oauth1_client;

	public function get_label() {
		return esc_html__( 'Outgoing Webhook', 'gravityflow' );
	}

	public function get_icon_url() {
		return '<i class="fa fa-external-link"></i>';
	}

	/**
	 * Handles OAuth1 authentication
	 * !Note - the callback_url in the client constructor must be registered in the WP-Api application callback_url
	 * So for the docs the current web address of the step setting form is taken and used to setup the application and put into
	 * the callback url field.
	 * @return void
	 */ 
	
	public function process_auth() {
		if ( $this->get_setting( 'authentication' ) != 'oauth1' ) {
			return;
		}
		session_start();
		$consumer_key = $this->get_setting( 'oauth1_consumer_key' );
		$consumer_secret = $this->get_setting( 'oauth1_consumer_secret' );
		$url = $this->get_setting( 'url' );
		require_once( trailingslashit( dirname(__DIR__) ) . '/class-oauth1-client.php' );
		try {
			$this->oauth1_client = new Gravity_Flow_Oauth1_Client(
				array(
					'consumer_key' => $consumer_key,
					'consumer_secret' => $consumer_secret,
					'callback_url' => "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
				),
				'gravi_flow_' . $this->get_setting( 'oauth1_consumer_key' ),
				$url
			);
			
		} catch ( Exception $e ) {
			error_log( 'Exception caught ' . $e->getMessage() );
			return;
		}
		
		$this->oauth_progress = get_user_meta( get_current_user_id(), $this->oauth1_client->data_store['progress'], true );
		if ( $this->oauth_progress === '' ) {
			if ( empty( $consumer_key ) || empty( $consumer_secret )) {
				?><h5>Please enter your consumer key and secret in the settings fields below</h5><?php
				return;
			}
			$this->get_temp_creds( $this->get_setting( 'oauth1_consumer_key' ), $this->get_setting( 'oauth1_consumer_secret' ) );
			
			$_SESSION['temp_secret'] = $this->temporary_credentials['oauth_token_secret'];
			if ( !isset($this->temporary_credentials['oauth_token']) ) {
				?><p class='oauth_failed'>Temporary credits request failed - check your settings and make sure to register this url as the callback url in your receiving app.</p><?php
			}
			$auth_creds = array( 'oauth_consumer_key' => $this->get_setting( 'oauth1_consumer_key' ), 'oauth_consumer_secret' => $this->get_setting( 'oauth1_consumer_secret' ) ) + $this->temporary_credentials;
			$auth_app_page = add_query_arg( $auth_creds, $this->oauth1_client->api_auth_urls['oauth1']['authorize'] );
			update_user_meta( get_current_user_id(), $this->oauth1_client->data_store['progress'], 'redirected_for_auth' );
			?><script>
					window.onload = function() {
						if ( confirm( 'You will now be redirected to the oauthserver to authorize the app - if you aren\'t logged in you will need to log in first. If you need to change any of the details for the webhook please hit NO/Cancel and resave the correct details.' ) ) {
							window.location = '<?php echo $auth_app_page; ?>';
						}
					}
			</script><?php
		} else if ( $this->oauth_progress == 'redirected_for_auth' ) {
			if ( empty( $_GET['oauth_verifier'] ) ) {
				?><p class='oauth_failed'>Something went wrong with the authorization please contact support</p><?php
				return;
			}
			else {
				try {
					$this->oauth1_client->config['token'] = $_GET['oauth_token'];
					$this->oauth1_client->config['token_secret'] = $_SESSION['temp_secret'];
					$access_credentials = $this->oauth1_client->requestAccessToken( $_GET['oauth_verifier'] );
					update_user_meta( get_current_user_id(), $this->oauth1_client->data_store['full_credentials'], $access_credentials );
					update_user_meta( get_current_user_id(), $this->oauth1_client->data_store['progress'], 'access_tokens_received' );
					?><p class='oauth_granted'>Your webhook is now authorized via OAuth and can make requests to <?php echo $this->get_setting( 'url' ); ?></p><?php
				} catch (Exception $e) {
					?><p class='oauth_failed'>Oauth verification failed. Please contact support</p><?php
					error_log( 'Exception caught ' . $e->getMessage() );
				}
			}
		}
		else if ( $this->oauth_progress == 'access_tokens_received' ) {
			?><p class='oauth_granted'>Your webhook is authorised via OAuth and can make requests to <?php echo $this->url; ?></p><?php
		}
	}

	public function get_settings() {
		$settings = array(
			'title'  => esc_html__( 'Outgoing Webhook', 'gravityflow' ),
			'fields' => array(
				array(
					'name'  => 'url',
					'class' => 'large',
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
							'label' => 'OAuth1',
							'value' => 'oauth1',
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
					'name'  => 'oauth1_consumer_key',
					'label' => esc_html__( 'Oauth Consumer Key - see docs for info', 'gravityflow' ),
					'type'  => 'text',
					'dependency' => array(
						'field' => 'authentication',
						'values' => array( 'oauth1' )
					),
				),
				array(
					'name'  => 'oauth1_consumer_secret',
					'label' => esc_html__( 'Oauth Consumer Secret - see docs for info', 'gravityflow' ),
					'type'  => 'text',
					'dependency' => array(
						'field' => 'authentication',
						'values' => array( 'oauth1' )
					),
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
					$raw_value = rgpost( '_gaddon_setting_raw_body' );

					if ( ! current_user_can( 'unfiltered_html' ) ) {
						$raw_value = wp_kses_post( $raw_value );
					}

					$_gaddon_posted_settings['raw_body'] = $raw_value;
				}

				$settings['fields'][] = array(
					'name'  => 'raw_body',
					'label' => esc_html__( 'Raw Body', 'gravityflow' ),
					'type'  => 'textarea',
					'class' => 'fieldwidth-3 fieldheight-2',
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
					'label'               => esc_html__( 'Field Values', 'gravityflow' ),
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
		
		return $settings;
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
	 * Process the step. For example, assign to a user, send to a service, send a notification or do nothing. Return (bool) $complete.
	 *
	 * @return bool Is the step complete?
	 */
	function process() {

		$this->send_webhook();

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

		if ( $this->body == 'raw' ) {
			$body = $this->raw_body;
			$body = GFCommon::replace_variables( $body, $this->get_form(), $entry, false, false, false, 'text' );
		} elseif ( in_array( $method, array( 'POST', 'PUT', 'PATCH' ) ) ) {
			$body = $this->get_request_body();
			if ( $this->format == 'json' ) {
				$headers['Content-type'] = 'application/json';
				$body    = json_encode( $body );
			} else {
				$headers = array();
			}
		}
		if ( $this->authentication == 'oauth1' ) {
			require_once( trailingslashit( dirname(__DIR__) ) . '/class-oauth1-client.php' );
			$this->oauth1_client = new Gravity_Flow_Oauth1_Client(
				array(
					'consumer_key' => $this->get_setting( 'oauth1_consumer_key' ),
					'consumer_secret' => $this->get_setting( 'oauth1_consumer_secret' ),
					'token' => '',
					'token_secret' => '',
				),
				'gravi_flow_' . $this->get_setting('oauth1_consumer_key'),
				$url
			);
			$access_credentials = get_user_meta( get_current_user_id(), $this->oauth1_client->data_store['full_credentials'], true);
			$this->oauth1_client->config['token'] = $access_credentials['oauth_token'];
			$this->oauth1_client->config['token_secret'] = $access_credentials['oauth_token_secret'];
			
			//Note we don't send the final $options[] parameter in here because our request is always sent in the body
			$headers['Authorization'] = $this->oauth1_client->getFullRequestHeader( $this->get_setting('url'), $method );
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

		$this->log_debug( __METHOD__ . '() - response: ' . print_r( $response, true ) );

		if ( is_wp_error( $response ) ) {
			$step_status = 'error';
		} else {
			$step_status = 'success';
		}

		$this->add_note( sprintf( esc_html__( 'Webhook sent. URL: %s', 'gravityflow' ), $url ) );

		do_action( 'gravityflow_post_webhook', $response, $args, $entry, $this );

		return $step_status;
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
	 * @param $form
	 * @param null|string $field_type
	 * @param null|array $exclude_field_types
	 *
	 * @return array
	 */
	public function get_field_map_choices( $form, $field_type = null, $exclude_field_types = null ) {

		$fields = array();

		// Setup first choice
		if ( rgblank( $field_type ) || ( is_array( $field_type ) && count( $field_type ) > 1 ) ) {

			$first_choice_label = __( 'Select a Field', 'gravityflow' );

		} else {

			$type = is_array( $field_type ) ? $field_type[0] : $field_type;
			$type = ucfirst( GF_Fields::get( $type )->get_form_editor_field_title() );

			$first_choice_label = sprintf( __( 'Select a %s Field', 'gravityflow' ), $type );

		}

		$fields[] = array( 'value' => '', 'label' => $first_choice_label );

		// if field types not restricted add the default fields and entry meta
		if ( is_null( $field_type ) ) {
			$fields[] = array( 'value' => 'id', 'label' => esc_html__( 'Entry ID', 'gravityflow' ) );
			$fields[] = array( 'value' => 'date_created', 'label' => esc_html__( 'Entry Date', 'gravityflow' ) );
			$fields[] = array( 'value' => 'ip', 'label' => esc_html__( 'User IP', 'gravityflow' ) );
			$fields[] = array( 'value' => 'source_url', 'label' => esc_html__( 'Source Url', 'gravityflow' ) );
			$fields[] = array( 'value' => 'created_by', 'label' => esc_html__( 'Created By', 'gravityflow' ) );

			$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );
			foreach ( $entry_meta as $meta_key => $meta ) {
				$fields[] = array( 'value' => $meta_key, 'label' => rgars( $entry_meta, "{$meta_key}/label" ) );
			}
		}

		// Populate form fields
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
					//not array, so should be single string
					if ( $input_type == $exclude_field_types ) {
						$exclude_field = true;
					} else {
						$exclude_field = false;
					}
				}

				if ( is_array( $inputs ) && $field_is_valid_type && ! $exclude_field ) {
					//If this is an address field, add full name to the list
					if ( $input_type == 'address' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
						);
					}
					//If this is a name field, add full name to the list
					if ( $input_type == 'name' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
						);
					}
					//If this is a checkbox field, add to the list
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
					$fields[] = array( 'value' => $field->id, 'label' => GFCommon::get_label( $field ) );
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
	 * @param array $body The body to sent.
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
	 * @param array $entry The entry being processed by this step.
	 * @param GF_Field $source_field The source field being processed.
	 * @param string $source_field_id The ID of the source field or input.
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
	 * @param string $selected_choice The choice value from the source field.
	 * @param GF_Field $source_field The source field being processed.
	 *
	 * @return string
	 */
	public function get_source_choice_text( $selected_choice, $source_field ) {
		return $this->get_choice_property( $selected_choice, $source_field->choices, 'value', 'text' );
	}

	/**
	 * Helper to get the specified choice property for the selected choice.
	 *
	 * @param string $selected_choice The selected choice value or text.
	 * @param array $choices The field choices.
	 * @param string $compare_property The choice property the $selected_choice is to be compared against.
	 * @param string $return_property The choice property to be returned.
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
	
	/**
	 * Helper to use the consumer key and secret entered to get temporary credentials
	 * and then allow the user to authorize the webhook's connection using those credentials.
	 * 
	 * 
     * @param string $fields the fields being validated.
	 * @param array $settings The settings
	 * 
	 * @return string
	 */
	function get_temp_creds( $consumer_key, $consumer_secret ) {
		try {
			$this->temporary_credentials = $this->oauth1_client->requestToken();
			update_user_meta( get_current_user_id(), $this->oauth1_client->data_store['progress'], 'temp_creds_received' );
		} catch (Exception $e) {
			error_log( 'Exception caught ' . $e->getMessage() );
		}
	}
	
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Webhook() );
