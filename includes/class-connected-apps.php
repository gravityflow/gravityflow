<?php
/**
 * Gravity Flow
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8.1
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Connected_Apps
 *
 * @since 1.8.1
 */
class Gravity_Flow_Connected_Apps {

	/**
	 * Holds connection status keys and details.
	 *
	 * @var array
	 */
	protected $oauth_connection_statuses = array();

	/**
	 * A holder for the instance.
	 *
	 * @var object
	 */
	public static $_instance;

	/**
	 * The Oauth1 Client.
	 *
	 * @var Gravity_Flow_Oauth1_Client
	 */
	protected $oauth1_client;

	/**
	 * The ID of the app currently being processed.
	 *
	 * @var string
	 */
	protected $current_app_id;

	/**
	 * The app config currently being processed.
	 *
	 * @var array
	 */
	protected $current_app;

	/**
	 * Add actions and set up connection statuses.
	 */
	function __construct() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'wp_ajax_gravity_flow_reauth_app', array( $this, 'reauthorize_app' ) );
		} else {
			add_action( 'admin_init', array( $this, 'maybe_process_auth_flow' ) );
		}
	}

	/**
	 * Wrap status messages in styled spans.
	 *
	 * @param string $message Incoming status message should be one-two word string.
	 *
	 * @return string
	 */
	public function wrap_status_message( $message ) {
		return '<span class="oauth-' . strtolower( str_replace( ' ', '-', $message ) ) . '">' . esc_html( $message ) . '</span>';
	}

	/**
	 * Clears the current credentials so that it can be reauthorized.
	 */
	function reauthorize_app() {
		check_admin_referer( 'gflow_settings_js', 'security' );
		$app_id  = sanitize_text_field( rgpost( 'app' ) );
		$app     = $this->get_app( $app_id );
		$new_app = array(
			'app_id'   => $app['app_id'],
			'app_name' => $app['app_name'],
			'api_url'  => $app['api_url'],
			'app_type' => $app['app_type'],
			'status'   => 'Not Verified',
		);
		$this->update_app( $app['app_id'], $new_app );
		wp_send_json( array(
			'success' => true,
			'app'     => 'ready for reauth',
		) );
	}

	/**
	 * If appropriate trigger processing of the auth settings or app authorization.
	 */
	function maybe_process_auth_flow() {
		if ( ( isset( $_POST['gflow_add_app'] )
		       || isset( $_POST['gflow_authorize_app'] )
		       || isset( $_GET['oauth_verifier'] ) )
		) {
			$this->process_auth_flow();
		}
	}

	/**
	 * Processes Auth settings, initial run creates unique_id and app
	 * subsequent run processes the authorization
	 */
	function process_auth_flow() {

		$adding_app      = rgpost( 'gflow_add_app' ) === 'Next';
		$authorizing_app = rgpost( 'gflow_authorize_app' ) === 'Authorize App';

		if ( $authorizing_app || isset( $_GET['oauth_verifier'] ) ) {

			$this->current_app_id = sanitize_text_field( rgget( 'app' ) );
			$this->current_app    = $this->get_app( $this->current_app_id );

			if ( $authorizing_app && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'nonce_authorize_app' ) ) {
				wp_die( 'Failed Security Check - refresh page and try again' );
			}

			if ( isset( $_POST['app_type'] ) ) {
				$process_func = sprintf( 'process_auth_%s', sanitize_text_field( $_POST['app_type'] ) );
			} else {
				$process_func = sprintf( 'process_auth_%s', $this->current_app['app_type'] );
			}
			if ( is_callable( array( $this, $process_func ) ) ) {
				$this->$process_func();
			} else {
				gravity_flow()->log_debug( __METHOD__ . '() - processing function ' . $process_func . ' not callable' );
			}
		} elseif ( $adding_app ) {
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'nonce_create_app' ) ) {
				wp_die( 'Failed Security Check. Refresh the page and try again', 'gravityflow' );
			}

			$app_name    = sanitize_text_field( $_POST['app_name'] );
			$app_type    = sanitize_text_field( $_POST['app_type'] );
			$app_api_url = esc_url_raw( $_POST['api_url'] );

			$new_app = array(
				'app_name' => $app_name,
				'api_url'  => $app_api_url,
				'app_type' => $app_type,
				'status'   => 'Not Verified',
			);

			$app_id = $this->add_app( $new_app );
			$url    = add_query_arg( 'app', esc_js( $app_id ) );
			$url    = esc_url_raw( $url );
			wp_safe_redirect( $url );
		}
	}

	/**
	 * Handles OAuth1 authentication.
	 *
	 * The callback_url in the client constructor must be registered in the WP-Api application callback_url
	 * So for the docs the current web address of the step setting form is taken and used to setup the application and
	 * put into the callback url field.
	 */
	public function process_auth_wp_oauth1() {

		if ( ! ( isset( $_POST['consumer_key'] )
		         || isset( $_POST['app_name'] )
		         || isset( $_GET['app'] )
		         || isset( $_GET['oauth_verifier'] ) )
		) {
			return;
		}

		require_once( 'class-oauth1-client.php' );
		$current_app_id = wp_unslash( sanitize_text_field( rgget( 'app' ) ) );

		if ( isset( $_POST['consumer_key'] ) || isset( $_POST['app_name'] ) ) {
			$reauth      = false;
			$current_app = $this->get_app( $current_app_id );
			if ( $_POST['app_name'] !== $current_app['app_name'] || $_POST['api_url'] !== $current_app['api_url'] ) {
				$current_app['app_name'] = sanitize_text_field( $_POST['app_name'] );
				$current_app['api_url']  = sanitize_text_field( $_POST['api_url'] );
			}
			if ( rgpost( 'consumer_key' ) !== rgar( $current_app, 'consumer_key' ) || rgpost( 'consumer_secret' ) !== rgar( $current_app, 'consumer_secret' ) ) {
				$current_app['consumer_key']    = sanitize_text_field( $_POST['consumer_key'] );
				$current_app['consumer_secret'] = sanitize_text_field( $_POST['consumer_secret'] );
				$reauth                         = true;
			}
			$this->update_app( $current_app_id, $current_app );
			if ( ! $reauth ) {
				wp_safe_redirect( esc_url_raw( remove_query_arg( '' ) ) );
			}
		}
		$this->current_app_id = $current_app_id;
		$this->setup_oauth1_client();
		$this->status_keys = array_keys( $this->get_connection_statuses() );
		if ( ! isset( $_GET['oauth_verifier'] ) ) {
			$this->process_oauth1_outward_leg();
		} else {
			$this->process_oauth1_return_legs();
		}

	}

	/**
	 * Handles OAuth1 authentication.
	 *
	 * The callback_url in the client constructor must be registered in the WP-Api application callback_url
	 * So for the docs the current web address of the step setting form is taken and used to setup the application and
	 * put into the callback url field.
	 */
	public function process_auth_gf_oauth1() {

		if ( ! ( isset( $_POST['consumer_key'] )
		         || isset( $_POST['app_name'] )
		         || isset( $_GET['app'] )
		         || isset( $_GET['oauth_verifier'] ) )
		) {
			return;
		}

		require_once( 'class-oauth1-client.php' );
		$current_app_id = wp_unslash( sanitize_text_field( rgget( 'app' ) ) );

		if ( isset( $_POST['consumer_key'] ) || isset( $_POST['app_name'] ) ) {
			$reauth      = false;
			$current_app = $this->get_app( $current_app_id );
			if ( $_POST['app_name'] !== $current_app['app_name'] || $_POST['api_url'] !== $current_app['api_url'] ) {
				$current_app['app_name'] = sanitize_text_field( $_POST['app_name'] );
				$current_app['api_url']  = sanitize_text_field( $_POST['api_url'] );
			}
			if ( rgpost( 'consumer_key' ) !== rgar( $current_app, 'consumer_key' ) || rgpost( 'consumer_secret' ) !== rgar( $current_app, 'consumer_secret' ) ) {
				$current_app['consumer_key']    = sanitize_text_field( $_POST['consumer_key'] );
				$current_app['consumer_secret'] = sanitize_text_field( $_POST['consumer_secret'] );
				$this->setup_oauth1_client( $current_app );
				$test_url                 = trailingslashit( $current_app['api_url'] ) . 'wp-json/gf/v2/forms/';
				$headers['Authorization'] = $this->oauth1_client->get_full_request_header( $test_url, 'OPTIONS' );
				$options                  = array(
					'headers' => $headers,
					'method'  => 'OPTIONS',
				);

				$response = wp_remote_request( $test_url, $options );

				if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && $response['response']['code'] == 200 ) {
					$current_app['status'] = 'Verified';
				} else {
					$current_app['status'] = 'Not Verified';
				}

				$reauth = true;
			}
			$this->update_app( $current_app_id, $current_app );
			if ( ! $reauth ) {
				wp_safe_redirect( esc_url_raw( remove_query_arg( '' ) ) );
			}
		}
	}

	/**
	 * Process oauth1 - authorize returning user
	 */
	function process_oauth1_return_legs() {
		$status    = $this->status_keys[1];
		$app_ident = $this->current_app_id;
		if ( empty( $_GET['oauth_verifier'] ) || ! isset( $_GET['oauth_token'] ) || empty( $_GET['oauth_token'] ) ) {
			update_option( "gflow_conn_app_status_{$app_ident}_{$status}", 'FAILED' );
		} elseif ( ! get_transient( $app_ident . '_temp_creds_secret_' . get_current_user_id() ) ) {
			update_option( "gflow_conn_app_status_{$app_ident}_{$status}", 'FAILED' );
		} else {
			update_option( "gflow_conn_app_status_{$app_ident}_{$status}", 'SUCCESS' );
			$status = $this->status_keys[2];
			try {
				$this->oauth1_client->config['token']        = $_GET['oauth_token'];
				$this->oauth1_client->config['token_secret'] = get_transient( $app_ident . '_temp_creds_secret_' . get_current_user_id() );
				$access_credentials                          = $this->oauth1_client->request_access_token( $_GET['oauth_verifier'] );
				update_option( "gflow_conn_app_status_{$app_ident}_{$status}", 'SUCCESS' );
				$this->current_app['access_creds'] = $access_credentials;
				$this->current_app['status']       = 'Verified';
				$this->update_app( $app_ident, $this->current_app );
			} catch ( Exception $e ) {
				update_option( "gflow_conn_app_status_{$app_ident}_{$status}", 'FAILED' );
				gravity_flow()->log_debug( __METHOD__ . '() - Exception caught ' . $e->getMessage() );
			}
		}
		$url = remove_query_arg( array( 'oauth_token', 'oauth_verifier', 'wp_scope' ) );
		wp_safe_redirect( $url );
	}

	/**
	 * Process oauth1 - sending user to site for authorization
	 */
	function process_oauth1_outward_leg() {
		$status     = $this->status_keys[0];
		$app_ident  = $this->current_app_id;
		$temp_creds = $this->get_temp_creds( $app_ident );

		if ( false === $temp_creds ) {
			update_option( "gflow_conn_app_status_{$app_ident}_{$status}", 'FAILED' );
			wp_safe_redirect( esc_url_raw( remove_query_arg( '' ) ) );
			exit;
		} else {
			set_transient( $app_ident . '_temp_creds_secret_' . get_current_user_id(), $temp_creds['oauth_token_secret'], HOUR_IN_SECONDS );
			update_option( "gflow_conn_app_status_{$app_ident}_{$status}", 'SUCCESS' );

			$auth_app_page = esc_url_raw( add_query_arg( $temp_creds, $this->oauth1_client->api_auth_urls['oauth1']['authorize'] ) );
			?>
			<script>
				window.onload = function () {
					window.location = <?php echo json_encode( $auth_app_page ); ?>;
				}
			</script>
			<?php
		}
	}

	/**
	 * Helper to use the consumer key and secret entered to get temporary credentials
	 * and then allow the user to authorize the webhook's connection using those credentials.
	 *
	 * @param string $app_ident the app getting creds for.
	 *
	 * @return string
	 */
	function get_temp_creds( $app_ident ) {
		try {
			$temporary_credentials = $this->oauth1_client->request_token();

			return $temporary_credentials;
		} catch ( Exception $e ) {
			gravity_flow()->log_debug( __METHOD__ . '() - Exception caught ' . $e->getMessage() );

			return false;
		}

		return false;
	}

	/**
	 * Configure and construct oauth1 client.
	 *
	 * @param array|null $app
	 */
	function setup_oauth1_client( $app = null ) {
		try {
			if ( ! $app ) {
				$app = $this->get_app( $this->current_app_id );
			}
			$this->oauth1_client = new Gravity_Flow_Oauth1_Client(
				array(
					'consumer_key'    => $app['consumer_key'],
					'consumer_secret' => $app['consumer_secret'],
					'callback_url'    => add_query_arg( array(
						'page' => 'gravityflow_settings',
						'view' => 'connected_apps',
						'app'  => $this->current_app_id,
					), esc_url( admin_url( 'admin.php' ) ) ),
				),
				'gravi_flow_' . $app['consumer_key'],
				$app['api_url']
			);

		} catch ( Exception $e ) {
			gravity_flow()->log_debug( __METHOD__ . '() - Exception caught ' . $e->getMessage() );
			$url = remove_query_arg( array( 'oauth_token', 'oauth_verifier', 'wp_scope' ) );
			$url = esc_url_raw( $url );
			wp_safe_redirect( $url );
		}
	}

	/**
	 * Instantiate class from outside.
	 *
	 * @return Gravity_Flow_Connected_Apps
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Returns an associative array of statuses plus labels.
	 *
	 * @return array
	 */
	function get_connection_statuses() {
		return array(
			'get_temporary_credentials' => __( 'Using Consumer Key and Secret to Get Temporary Credentials', 'gravityflow' ),
			'user_authorize_app'        => __( 'Redirecting for user authorization - you may need to login first', 'gravityflow' ),
			'get_access_credentials'    => __( 'Using credentials from user authorization to get permanent credentials', 'gravityflow' ),
		);
	}

	/**
	 * Returns an array of connected apps.
	 *
	 * @return array
	 */
	function get_connected_apps() {
		global $wpdb;

		$table = $wpdb->options;

		$key = 'gflow_conn_app_config_%';

		$results = $wpdb->get_results( $wpdb->prepare( "
			SELECT *
			FROM {$table}
			WHERE option_name LIKE %s
		", $key ) );

		$apps = array();

		foreach ( $results as $result ) {
			$app                    = maybe_unserialize( $result->option_value );
			$apps[ $app['app_id'] ] = $app;
		}

		return $apps;
	}

	/**
	 * Get the app settings.
	 *
	 * @param string $app_id The app ID.
	 *
	 * @return array
	 */
	function get_app( $app_id ) {
		return get_option( 'gflow_conn_app_config_' . $app_id, array() );
	}

	/**
	 * Delete the app settings.
	 *
	 * @param string $app_id The app ID.
	 *
	 * @return bool
	 */
	function delete_app( $app_id ) {

		if ( empty( $app_id ) || ! is_string( $app_id ) ) {
			return false;
		}

		// Delete the option with the app settings.
		delete_option( 'gflow_conn_app_config_' . $app_id );

		// Delete statuses.
		$statuses = $this->get_connection_statuses();
		foreach ( array_keys( $statuses ) as $key ) {
			delete_option( 'gflow_conn_app_status_' . $app_id . $key );
		}

		return true;
	}

	/**
	 * Adds an app.
	 *
	 * @param array $app_config The app settings.
	 *
	 * @return string
	 */
	function add_app( $app_config ) {
		$app_id               = uniqid();
		$app_config['app_id'] = $app_id;
		add_option( 'gflow_conn_app_config_' . $app_id, $app_config );

		return $app_id;
	}

	/**
	 * Updates an app.
	 *
	 * @param string $app_id     The app ID.
	 * @param array  $app_config The app settings.
	 *
	 * @return string
	 */
	function update_app( $app_id, $app_config ) {
		$app_config['app_id'] = $app_id;
		update_option( 'gflow_conn_app_config_' . $app_id, $app_config );

		return $app_id;
	}

	/**
	 * Output the HTML markup for the settings tab.
	 */
	public function settings_tab() {
		add_thickbox();

		$current_app = array();

		$current_app_id = wp_unslash( sanitize_text_field( rgget( 'app' ) ) );
		$connected_apps = $this->get_connected_apps();

		if ( isset( $_GET['delete'] ) && wp_verify_nonce( $_REQUEST['_nonce'], 'gflow_delete_app' ) ) {

			$this->delete_app( $current_app_id );
			$url = add_query_arg( array(
				'page' => 'gravityflow_settings',
				'view' => 'connected_apps',
			), esc_url( admin_url( 'admin.php ' ) ) );
			echo sprintf( esc_html__( 'App deleted. Redirecting...', 'gravityflow' ), $current_app_id );

			?>
			<script>window.location.href = '<?php echo $url; ?>'; </script>
			<?php
			exit;
		}

		?>
		<h3><span><i class="fa fa-cogs"></i> <?php esc_html_e( 'Connected Apps', 'gravityflow' ); ?></span>
			<?php

			if ( empty( $current_app_id ) ) {
				?>
				<a class="add-new-h2" id="new-app"><?php esc_html_e( 'Add New', 'gravityflow' ); ?></a>
				<?php
			}
			?>
		</h3>

		<div id='authorization_statuses_edit' style='display:none'>
			<h3><?php esc_html_e( 'Authorization Status Details', 'gravityflow' ); ?></h3>
			<ul id='auth_steps'>
				<?php
				$oauth_connection_statuses = $this->get_connection_statuses();
				foreach ( $oauth_connection_statuses as $status => $display ) {
					$option_key = 'gflow_conn_app_status_' . $current_app_id . '_' . sanitize_text_field( $status );
					if ( get_option( $option_key, '' ) !== '' ) {
						$display = get_option( $option_key );
					}
					?>
					<li>
						<span style="font-weight:bold"><?php echo esc_html( ucwords( str_replace( '_', ' ', $status ) ) ); ?></span>: <?php echo $this->wrap_status_message( $display ); ?>
					</li>
					<?php
				}
				?>
			</ul>
			<p><?php esc_html_e( 'If you don\'t see Success for all of the above steps, please check details and try again.', 'gravityflow' ); ?></p>
		</div>
		<?php

		if ( ! isset( $_GET['app'] ) ) {

			$table = new Gravity_Flow_Connected_Apps_Table();
			$table->prepare_items();

			?>
			<div id='connected_apps_table_container'>
				<p><?php esc_html_e( 'Note: Connected Apps can only be used in Outgoing Webhook steps.', 'gravityflow' ); ?></p>
				<?php
				$table->display();
				?>
			</div>
			<?php

		} else {
			$current_app = $connected_apps[ $current_app_id ];
		}

		$show_form = ( ! empty( $current_app_id ) ) ? '' : 'display:none';

		$status   = rgar( $current_app, 'status' );
		$app_name = rgar( $current_app, 'app_name' );
		$api_url  = rgar( $current_app, 'api_url' );
		$app_type = rgar( $current_app, 'app_type' );
		?>
		<div id='connected_app_form_container' style='<?php echo esc_attr( $show_form ); ?>'>

			<h2>
				<?php
				$is_edit = ! empty( $current_app_id );
				echo $is_edit ? esc_html__( 'Edit App', 'gravityflow' ) : esc_html__( 'Add an App', 'gravityflow' );
				?>
			</h2>

			<form class='oauth' id='add_connected_app' method="POST">
				<table class="form-table gforms_form_settings">
					<tbody>
					<tr id="gaddon-setting-row-step_name">
						<th>
							<label for='app_name'>
								<?php
								esc_html_e( 'App Name', 'gravityflow' );
								echo ' ';
								gform_tooltip( __( 'Enter a name for the app.', 'gravityflow' ) );
								?>
								<span class="required">*</span>
						</th>
						<td>
							<input class='required medium gaddon-setting gaddon-text' type='text' id="app_name"
							       name='app_name' value='<?php esc_attr_e( $app_name ); ?>'/>
						</td>
					</tr>
					<tr>
						<th>
							<label for='app_type'>
								<?php
								esc_html_e( 'App Type', 'gravityflow' );
								echo ' ';
								gform_tooltip( esc_html__( 'Currently only WordPress sites using the official WordPress REST API OAuth1 plugin or Gravity Forms 2.4 are supported.', 'gravityflow' ) ); ?>
						</th>
						</label>
						<td>
							<select id="app_type" name='app_type'>
								<option value='wp_oauth1' <?php selected( 'wp_oauth1', $app_type ); ?>><?php esc_html_e( 'WordPress OAuth1', 'gravityflow' ); ?></option>
								<option value='gf_oauth1' <?php selected( 'gf_oauth1', $app_type ); ?>><?php esc_html_e( 'Gravity Forms OAuth1', 'gravityflow' ); ?></option>
							</select>
						</td>
					</tr>
					<tr id="gaddon-setting-row-step_name">
						<th>
							<label for='api_url'>
								<?php
								esc_html_e( 'URL', 'gravityflow' );
								echo ' ';
								gform_tooltip( __( 'Enter the URL of the site.', 'gravityflow' ) );
								?>
								<span class="required">*</span>
							</label>
						</th>
						<td>
							<input id="api_url" class='required medium gaddon-setting gaddon-text' type='text'
							       name='api_url' value='<?php echo esc_url( $api_url ); ?>'/>
						</td>
					</tr>
					<?php if ( $is_edit ) : ?>
						<tr>
							<th id='status_row'>
								<?php esc_html_e( 'Authorization Status', 'gravityflow' ); ?>
							</th>
							<td>
								<a class='thickbox'
								   href='#TB_inline?width=500&height=300&inlineId=authorization_statuses_edit'><?php echo $this->wrap_status_message( $status ); ?></a>
							</td>
						</tr>
					<?php endif; ?>
					<?php
					if ( empty( $current_app_id ) ) {
						?>
						<tr>
							<td><?php wp_nonce_field( 'nonce_create_app' ); ?></td>
							<td><input type='submit' id='gflow_add_app' name='gflow_add_app' class='button primary'
							           value='Next'/>
								<button type='button' id='gflow_add_app_cancel'
								        class='button primary'><?php esc_html_e( 'Cancel', 'gravityflow' ); ?></td>
						</tr>
						<?php
					}
					if ( $is_edit && ! array_key_exists( 'consumer_key', $current_app ) && ! array_key_exists( 'consumer_secret', $current_app ) ) {
						if ( $current_app['app_type'] == 'wp_oauth1' ) {
							?>
							<tr>
								<td colspan="2">
									<h4><?php esc_html_e( 'Before continuing, copy and paste the current URL from your browser\'s address bar into the Callback setting of the registered application.', 'gravityflow' ); ?></h4>
									<input type='hidden' name='authorizing_app'
									       value='<?php echo esc_attr( $current_app_id ); ?>'/>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<th><?php esc_html_e( 'Client Key', 'gravityflow' );
								gform_tooltip( __( 'Enter the Client Key.', 'gravityflow' ) ); ?><span class="required">*</span>
							</th>
							<td>
								<input class='required medium gaddon-setting gaddon-text' type='text'
								       name='consumer_key'
								       value='<?php echo esc_attr( rgar( $current_app, 'consumer_key' ) ); ?>'/>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Client Secret', 'gravityflow' );
								gform_tooltip( __( 'Enter Client Secret.', 'gravityflow' ) ); ?><span
										class="required">*</span>
							</th>
							<td>
								<input class="required medium gaddon-setting gaddon-text" type="text"
								       name="consumer_secret"
								       value="<?php echo esc_attr( rgar( $current_app, 'consumer_secret' ) ); ?>"/>
							</td>
						</tr>
						<tr>
							<td><?php wp_nonce_field( 'nonce_authorize_app' ); ?></td>
							<td>
								<input type="hidden" id="gflow_authorize_app_hidden_action" name="gflow_authorize_app"
								       value="Authorize App"/>
								<input type="submit" id="gflow_authorize_app_button" name="gflow_authorize_app"
								       class="button primary"
								       value="<?php esc_html_e( 'Authorize App', 'gravityflow' ); ?>"/>

							</td>
						</tr>

						<?php
					}
					if ( $is_edit && array_key_exists( 'consumer_key', $current_app ) && array_key_exists( 'consumer_secret', $current_app ) ) {
						?>
						<tr>
							<td colspan="2">
								<input type="button" data-app="<?php echo $current_app_id; ?>"
								       id="gflow_reauthorize_app" class="button primary"
								       value="<?php esc_html_e( 'Re-authorize App', 'gravityflow' ); ?>"/>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
		<?php
	}
}

/**
 * Returns the instance of the Gravity_Flow_Connected_Apps class.
 *
 * @return Gravity_Flow_Connected_Apps
 */
function gravityflow_connected_apps() {
	return Gravity_Flow_Connected_Apps::instance();
}

gravityflow_connected_apps();

/**
 * Class Gravity_Flow_Connected_Apps_Table
 */
class Gravity_Flow_Connected_Apps_Table extends WP_List_Table {

	/**
	 * An array of connected apps.
	 *
	 * @var array
	 */
	protected $_apps = array();

	/**
	 * Gravity_Flow_Connected_Apps_Table constructor.
	 *
	 * @param array $args Array of arguments for the current table.
	 */
	function __construct( $args = array() ) {
		$cols                  = $this->get_columns();
		$this->_apps           = gravityflow_connected_apps()->get_connected_apps();
		$this->_column_headers = array(
			$cols,
			array(),
			array(),
		);
		parent::__construct(
			array(
				'singular' => esc_html__( 'App', 'gravityflow' ),
				'plural'   => esc_html__( 'Apps', 'gravityflow' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepares items for the table.
	 */
	function prepare_items() {
		$this->items = $this->_apps;
	}

	/**
	 * Returns the columns for the table.
	 *
	 * @return array
	 */
	function get_columns() {
		return array(
			'app_name' => esc_html__( 'App Name', 'gravityflow' ),
			'app_type' => esc_html__( 'Type', 'gravityflow' ),
			'status'   => esc_html__( 'Status', 'gravityflow' ),
		);
	}

	/**
	 * Outputs the no items message.
	 */
	function no_items() {
		esc_html_e( 'You don\'t have any Connected Apps configured.', 'gravityflow' );
	}

	/**
	 * Outputs the App Name.
	 *
	 * @param array $item The app settings.
	 */
	function column_app_name( $item ) {
		echo $item['app_name'];
	}

	/**
	 * Outputs the App Type.
	 *
	 * @param array $item The app settings.
	 */
	function column_app_type( $item ) {
		switch ( $item['app_type'] ) {
			case 'wp_oauth1' :
				echo '<span class="dashicons dashicons-wordpress"></span>&nbsp;';
				esc_html_e( 'WordPress OAuth1', 'gravityflow' );
				break;
			case 'gf_oauth1' :
				echo sprintf( '<img class="dashicons" src="%s" />', gravity_flow()->get_base_url() . '/images/gf-icon.svg' ) . '&nbsp;';
				esc_html_e( 'Gravity Forms OAuth1', 'gravityflow' );
				break;
			default :
				echo $item['app_type'];
		}
	}

	/**
	 * Outputs the status.
	 *
	 * @param array $item The app settings.
	 */
	function column_status( $item ) {
		echo gravityflow_connected_apps()->wrap_status_message( $item['status'] );
	}

	/**
	 * Returns the row actions.
	 *
	 * @param array  $item        The app settings.
	 * @param string $column_name The current column name.
	 * @param string $primary     The primary column name.
	 *
	 * @return string
	 */
	function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$edit_url          = esc_url( add_query_arg( array(
			'app' => $item['app_id'],
		), remove_query_arg( 'delete' ) ) );
		$delete_url        = esc_url( add_query_arg( array(
			'app'    => $item['app_id'],
			'delete' => true,
			'_nonce' => wp_create_nonce( 'gflow_delete_app' ),
		) ) );
		$actions           = array();
		$actions['edit']   = '<a href="' . $edit_url . '">' . esc_html__( 'Edit', 'gravityflow' ) . '</a>';
		$actions['delete'] = "<a class='submitdelete' href='" . $delete_url . "' onclick=\"if ( confirm( '" . esc_js( sprintf( __( "You are about to delete this app '%s'\n  'Cancel' to stop, 'OK' to delete." ), $item['app_name'] ) ) . "' ) ) { return true;}return false;\">" . __( 'Delete' ) . "</a>";

		return $this->row_actions( $actions );
	}
}
