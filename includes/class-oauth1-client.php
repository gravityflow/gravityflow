<?php
/**
 * Gravity Flow OAuth1 Client
 *
 * @package     GravityFlow
 * @subpackage  Classes/API
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public Licenses
 * @since       1.0
 **/

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * A minimal oauth1 client. Currently only supports the official WordPress oauth server plugin for the WP REST API.
 *
 * Class Gravity_Flow_Oauth1_Client
 *
 * @since 1.8.1
 */
class Gravity_Flow_Oauth1_Client {

	/**
	 * Gives unique name to the application for storage.
	 *
	 * @var array
	 */
	public $data_store;

	/**
	 * Class constructor
	 *
	 * @param array  $config                sent in to build the client.
	 * @param string $connection_identifier a unique identifier for the instance.
	 * @param string $url                   the app url used for collecting the auth urls.
	 *
	 * @throws InvalidArgumentException If missing keys in config.
	 */
	public function __construct( $config, $connection_identifier, $url ) {

		$required_params = array(
			'consumer_key',
			'consumer_secret',
		);
		foreach ( $required_params as $param ) {
			if ( ! isset( $config[ $param ] ) ) {
				throw new InvalidArgumentException( sprintf( 'Missing OAuth1 client configuration: %s - see includes/class-oauth1-client.php', $param ) );
			}
		}
		$this->url       = $url;
		$this->config    = $config;
		$this->timestamp = time();

		$this->connection_identifier = $connection_identifier;
		$this->data_store            = array(
			'auth_urls'        => 'gravityflow_oauth_urls_' . base64_encode( $this->connection_identifier ),
			'progress'         => 'gravity_flow_oauth_progress_' . base64_encode( $this->connection_identifier ),
			'full_credentials' => 'gravity_flow_oauth_full_credentials_' . base64_encode( $this->connection_identifier ),
		);
		$this->api_auth_urls         = $this->get_auth_urls();
	}

	/**
	 * Hits the app url and collects authorization endpoint urls.
	 *
	 * @throws Exception If collection of api auth urls fails.
	 * @return array
	 */
	function get_auth_urls() {
		if ( get_option( $this->data_store['auth_urls'] ) !== false ) {
			return get_option( $this->data_store['auth_urls'] );
		}
		$url  = wp_parse_url( $this->url, PHP_URL_SCHEME ) . '://' . wp_parse_url( $this->url, PHP_URL_HOST );
		$page = wp_remote_get( $url );

		if ( ! is_wp_error( $page ) ) {
			$headers            = $page['headers'];
			$link               = rgar( $headers['link'], 0, $headers['link'] );
			$link_parts         = explode( '; ', $link );
			$this->api_base_url = str_replace( array( '<', '>' ), '', $link_parts[0] );
			$api_details        = wp_remote_get( $this->api_base_url );
			if ( ! is_wp_error( $api_details ) ) {
				$api_details = json_decode( $api_details['body'], true );
				if ( isset( $api_details['authentication'] ) ) {
					update_option( $this->data_store['auth_urls'], $api_details['authentication'] );

					return $api_details['authentication'];
				} else {
					throw new Exception( sprintf( 'No authentication array in api details from %s', $this->api_base_url ) );
				}
			} else {
				throw new Exception( sprintf( 'Problem with remote get call for %s. WP_Error: %s', $this->api_base_url, $api_details->get_error_message() ) );
			}
		} else {
			throw new Exception( sprintf( 'Broken request for %s', $url ) );
		}

	}

	/**
	 * Request token i.e. temporary credentials using consumer key and secret.
	 *
	 * @throws Exception If request for temporary credentials fails.
	 *
	 * @return array
	 */
	function request_token() {
		$response = wp_remote_post( $this->api_auth_urls['oauth1']['request'], array(
			'headers' => $this->request_token_headers(),
		) );
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {
			parse_str( $response['body'], $temporary_credentials );

			return $temporary_credentials;
		} else {
			gravity_flow()->log_debug( __METHOD__ . '() - response: ' . print_r( $response, true ) );
			throw new Exception( 'Problem with remote post for temporary credentials' );
		}
	}

	/**
	 * Gets request header for final access request.
	 *
	 * @param string $url       Url for the request.
	 * @param string $http_verb Request type (GET, POST etc).
	 * @param array  $options   Optional array of options.
	 *
	 * @return string
	 */
	function get_full_request_header( $url, $http_verb, $options = array() ) {
		$parameters = $this->full_request_params();
		if ( ! empty( $options ) ) {
			$parameters = array_merge( $parameters, $options );
		}

		$parameters['oauth_signature'] = $this->hmac_sign( $url, $parameters, $http_verb );

		return $this->authorization_headers( $parameters );

	}

	/**
	 * Request access token from oauth server.
	 *
	 * @param string $verifier The code sent back after authorizing at remote site.
	 *
	 * @throws Exception If remote request fails.
	 *
	 * @return array
	 */
	function request_access_token( $verifier ) {
		$response = wp_remote_post( $this->api_auth_urls['oauth1']['access'], array(
			'headers' => $this->request_access_token_headers( $verifier ),
		) );
		if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] ) {
			parse_str( $response['body'], $access_credentials );

			return $access_credentials;
		} else {
			gravity_flow()->log_debug( __METHOD__ . '() - response: ' . print_r( $response, true ) );
			throw new Exception( 'Problem with remote post for access credentials' );
		}
	}

	/**
	 * Get headers for token request.
	 *
	 * @return array
	 */
	function request_token_headers() {
		$parameters = $this->request_token_params();

		$parameters['oauth_signature'] = $this->hmac_sign( $this->api_auth_urls['oauth1']['request'], $parameters );

		return array(
			'Authorization' => $this->authorization_headers( $parameters ),
		);
	}

	/**
	 * Get headers for access token request.
	 *
	 * @param string $verifier Code sent back after authorizing app on remote site.
	 *
	 * @return array
	 */
	function request_access_token_headers( $verifier ) {
		$parameters = $this->request_access_token_params( $verifier );

		$parameters['oauth_signature'] = $this->hmac_sign( $this->api_auth_urls['oauth1']['access'], $parameters );

		return array(
			'Authorization' => $this->authorization_headers( $parameters ),
		);
	}

	/**
	 * Generates nonce for oauth requests.
	 *
	 * @return string
	 */
	public function nonce() {
		return md5( mt_rand() );
	}

	/**
	 * Sets up params for request token request.
	 *
	 * @return array
	 */
	function request_token_params() {
		return array(
			'oauth_consumer_key'     => $this->config['consumer_key'],
			'oauth_nonce'            => $this->nonce(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => $this->timestamp,
			'oauth_callback'         => $this->config['callback_url'],
		);
	}

	/**
	 * Sets up params for full request.
	 *
	 * @return array
	 */
	function full_request_params() {
		return array(
			'oauth_consumer_key'     => $this->config['consumer_key'],
			'oauth_token'            => rgar( $this->config, 'token' ),
			'oauth_nonce'            => $this->nonce(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => $this->timestamp,
		);
	}

	/**
	 * Sets up params for request access token request.
	 *
	 * @param string $verifier Verification code sent back after authorizing app at remote site.
	 *
	 * @return array
	 */
	function request_access_token_params( $verifier ) {
		return array(
			'oauth_consumer_key'     => $this->config['consumer_key'],
			'oauth_nonce'            => $this->nonce(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => $this->timestamp,
			'oauth_verifier'         => $verifier,
			'oauth_token'            => $this->config['token'],
		);
	}

	/**
	 * Signs oauth request.
	 *
	 * @param string $uri        Url used to sign the request.
	 * @param array  $parameters Parameters to build into signature.
	 * @param string $http_verb  Request type.
	 *
	 * @return string
	 */
	function hmac_sign( $uri, array $parameters = array(), $http_verb = 'POST' ) {
		$base_string = $this->base_string( $uri, $parameters, $http_verb );

		return base64_encode( $this->hash( $base_string ) );
	}

	/**
	 * Builds base string for request signing.
	 *
	 * @param string $uri        Url in the request.
	 * @param array  $parameters Params from the request.
	 * @param string $http_verb  Request method.
	 *
	 * @return string
	 */
	public function base_string( $uri, array $parameters = array(), $http_verb = 'POST' ) {
		ksort( $parameters );

		$parameters = http_build_query( $parameters, '', '&', PHP_QUERY_RFC3986 );

		return sprintf( '%s&%s&%s', $http_verb, rawurlencode( $uri ), rawurlencode( $parameters ) );
	}

	/**
	 * Get key:secret pair for request.
	 *
	 * @return string
	 */
	public function key() {
		$key = rawurlencode( $this->config['consumer_secret'] ) . '&';

		if ( array_key_exists( 'token_secret', $this->config ) && ! is_null( $this->config['token_secret'] ) ) {
			$key .= rawurlencode( $this->config['token_secret'] );
		}

		return $key;
	}

	/**
	 * Hash request
	 *
	 * @param array $data Data to be included in the hash.
	 *
	 * @return array
	 */
	public function hash( $data ) {
		return hash_hmac( 'sha1', $data, $this->key(), true );
	}

	/**
	 * Build header for request.
	 *
	 * @param array $parameters Oauth parameters to be sent.
	 *
	 * @return string
	 */
	public function authorization_headers( array $parameters ) {
		$parameters = http_build_query( $parameters, '', ', ', PHP_QUERY_RFC3986 );

		return "OAuth $parameters";
	}
}
