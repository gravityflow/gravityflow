<?php
/**
 * Gravity Flow OAuth1 Client
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/API
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public Licenses
 * @since       1.0
 */
 
if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * A minimal oauth1 client that will authenticate with the oauth server for the WP rest api
 * facilitated by this plugin
 *
 * Class Gravity_Flow_API
 *
 *
 * @since 1.0
 */
class Gravity_Flow_Oauth1_Client {
	
	public $data_store;
	
	public function __construct( $config, $connection_identifier, $url ) {
		
		$required_params = array(
			'consumer_key',
			'consumer_secret',
		);
		foreach( $required_params as $param ) {
			if ( !isset( $config[$param] ) ) {
				throw new InvalidArgumentException( sprintf( 'Missing OAuth1 client configuration: %s - see includes/class.oauth1-client.php', $param ) );
			}
		}
		$this->url = $url;
        $this->config = $config;
		$this->timestamp = time();
		
		$this->connection_identifier = $connection_identifier;
		$this->data_store = array(
			'auth_urls' => 'gravityflow_oauth_urls_'.base64_encode( $this->connection_identifier ),
			'progress' => 'gravity_flow_oauth_progress_'.base64_encode( $this->connection_identifier ),
			'full_credentials' => 'gravity_flow_oauth_full_credentials_'.base64_encode( $this->connection_identifier ),
		);
		$this->api_auth_urls = $this->get_auth_urls();
	}
	
	function get_auth_urls() {
		if ( get_option( $this->data_store['auth_urls'] ) !== false ) {
			return get_option( $this->data_store['auth_urls'] );
		}
		$url = parse_url( $this->url, PHP_URL_SCHEME) . '://' . parse_url( $this->url, PHP_URL_HOST );
		$page = wp_remote_get( $url );
		
		if ( !is_wp_error( $page ) ) {
			$headers = $page['headers'];
			$link_parts = explode( '; ',$headers['link'] );
			$this->api_base_url = str_replace( array( '<', '>' ), '', $link_parts[0] );
			$api_details = wp_remote_get( $this->api_base_url );
			if ( !is_wp_error( $api_details ) ) {
				$api_details = json_decode( $api_details['body'], true );
				if ( isset( $api_details['authentication'] ) ) {
					update_option( $this->data_store['auth_urls'], $api_details['authentication'] );
					return $api_details['authentication'];
				}
				else {
					throw new UnexpectValueException( sprintf( 'No authentication array in api details from %s', $this->api_base_url ) );
				}
			}
			else {
				throw new UnexpectValueException( sprintf( 'Problem with remote get call for %s. WP_Error: %s', $this->api_base_url, $api_details->get_error_message() ) );
			}
		}
		else {
			throw new Exception( sprintf( 'Broken request for %s', $url ) );
		}
		
	}
	
	//Flows
	function requestToken() {
		$response = wp_remote_post( $this->api_auth_urls['oauth1']['request'], [
            'headers' => $this->requestTokenHeaders(),
        ] );
		if ( !is_wp_error( $response) ) {
			parse_str( $response['body'], $temporary_credentials );
			return $temporary_credentials;
		}
		else {
			throw new UnexpectValueException( 'Problem with remote post for temporary credentials' );
		}
	}
	
	function getFullRequestHeader( $url, $httpVerb, $options = [] ) {
		$parameters = $this->full_request_params();
		if ( !empty( $options ) ) {
            $parameters = array_merge( $parameters, $options );
        }

        $parameters['oauth_signature'] = $this->hmac_sign( $url, $parameters, $httpVerb );

        return $this->authorizationHeaders( $parameters );
        
	}
	
	function requestAccessToken( $verifier ) {
		$response = wp_remote_post( $this->api_auth_urls['oauth1']['access'], [
            'headers' => $this->requestAccessTokenHeaders($verifier),
        ] );
		if ( !is_wp_error( $response ) ) {
			parse_str( $response['body'], $access_credentials );
			return $access_credentials;
		}
		else {
			throw new UnexpectValueException( 'Problem with remote post for access credentials' );
		}
	}
	
	function requestTokenHeaders() {
		$parameters = $this->request_token_params();

        $parameters['oauth_signature'] = $this->hmac_sign( $this->api_auth_urls['oauth1']['request'], $parameters );

        return [
            'Authorization' => $this->authorizationHeaders( $parameters ),
        ];
	}
	
	function requestAccessTokenHeaders( $verifier ) {
		$parameters = $this->request_access_token_params( $verifier );

        $parameters['oauth_signature'] = $this->hmac_sign( $this->api_auth_urls['oauth1']['access'], $parameters );

        return [
            'Authorization' => $this->authorizationHeaders( $parameters ),
        ];
	}
	
	public function nonce()
    {
        return md5( mt_rand() );
    }
		
	function request_token_params() {
		return array(
			'oauth_consumer_key' => $this->config['consumer_key'],
            'oauth_nonce' => $this->nonce(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $this->timestamp,
			'oauth_callback' => $this->config['callback_url'],
		);
	}
	
	function full_request_params() {
		return array(
			'oauth_consumer_key' => $this->config['consumer_key'],
			'oauth_token' => $this->config['token'],
            'oauth_nonce' => $this->nonce(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $this->timestamp,
		);
	}
	
	function request_access_token_params($verifier) {
		return array(
			'oauth_consumer_key' => $this->config['consumer_key'],
            'oauth_nonce' => $this->nonce(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $this->timestamp,
			'oauth_verifier' => $verifier,
			'oauth_token' => $this->config['token'],
		);
	}
	
	function hmac_sign( $uri, array $parameters = array(), $httpVerb = 'POST' ) {
		$baseString = $this->baseString( $uri, $parameters, $httpVerb );

        return base64_encode( $this->hash( $baseString ) );
	}

	public function baseString( $uri, array $parameters = [], $httpVerb = 'POST' ) {
        ksort( $parameters );

        $parameters = http_build_query( $parameters, '', '&', PHP_QUERY_RFC3986 );

        return sprintf( '%s&%s&%s', $httpVerb, rawurlencode($uri), rawurlencode($parameters) );
    }
	
	public function key() {
        $key = rawurlencode( $this->config['consumer_secret'] ) . '&';

        if ( !is_null( $this->config['token_secret'] ) ) {
            $key .= rawurlencode( $this->config['token_secret'] );
        }

        return $key;
    }
	
	public function hash( $data ) {
        return hash_hmac( 'sha1', $data, $this->key(), true );
    }
	
	public function authorizationHeaders( array $parameters ) {
        $parameters = http_build_query( $parameters, '', ', ', PHP_QUERY_RFC3986 );

        return "OAuth $parameters";
    }

	
}