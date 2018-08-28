<?php
/**
Plugin Name: Webhook Response Stubs
Plugin URI: https://gravityflow.io
Description: Stub outgoing webhook requests and provide expected response code/body for tests
Version: 1.0
Author: Gravity Flow
Author URI: https://gravityflow.io
License: GPL-2.0+
*/

function webhook_response_test_results( $preempt, $request, $url ) {
	if ( strpos( $url, 'unit-test-webhook.com' ) == false ) {
		return false;
	}

	$url_pieces = explode( '/', $url );
	switch ( end( $url_pieces ) ) :
		case '200':
		case '200-empty':
			$response = array(
				'headers'  => array(),
				'body'     => '',
				'response' => array(
					'code'    => 202,
					'message' => 'Accepted',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case '200-0027':
			$response = array(
				'headers'  => array( 'content_type' => 'application/json'),
				'body'     => json_encode( array(
								'fvQuestion'  => 'customQuestion200',
								'fvAnswer'    => 'customAnswer200',
								'fvRationale' => 'customRationale200',
								'fvPreference' => 'Very Important'
				)),
				'response' => array(
					'code'    => 202,
					'message' => 'Accepted',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case '400':
		case '400-empty':
			$response = array(
				'headers'  => array(),
				'body'     => '',
				'response' => array(
					'code'    => 402,
					'message' => 'Payment Required',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case '400-0027':
			$response = array(
				'headers'  => array( 'content_type' => 'application/json'),
				'body'     => json_encode( array(
					'fvQuestion'  => 'customQuestion400',
					'fvAnswer'    => 'customAnswer400',
					'fvRationale' => 'customRationale400',
					'fvPreference' => 'Very Important'
				)),
				'response' => array(
					'code'    => 402,
					'message' => 'Payment Required',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case '500':
		case '500-empty':
			$response = array(
				'headers'  => array(),
				'body'     => '',
				'response' => array(
					'code'    => 502,
					'message' => 'Bad Gateway',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case '500-0027':
			$response = array(
				'headers'  => array( 'content_type' => 'application/json'),
				'body'     => json_encode( array(
					'fvQuestion'  => 'customQuestion500',
					'fvAnswer'    => 'customAnswer500',
					'fvRationale' => 'customRationale500',
					'fvPreference' => 'Very Important'
				)),
				'response' => array(
					'code'    => 502,
					'message' => 'Bad Gateway',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		case 'other':
			$response = array(
				'headers'  => array(),
				'body'     => '{"test":"OTHERPOST"}',
				'response' => array(
					'code'    => 302,
					'message' => 'Found',
				),
				'cookies'  => array(),
				'filename' => '',
			);
			break;
		default:
			$response = false;

	endswitch;
	return $response;
}
add_filter( 'pre_http_request', 'webhook_response_test_results', 10, 3 );
