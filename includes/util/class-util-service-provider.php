<?php

namespace Gravity_Flow\Gravity_Flow\Util;

use Gravity_Flow\Gravity_Flow\Config\JS_Config;
use Gravity_Flow\Gravity_Flow\Service_Provider\Service_Provider;
use \GFAPI;
use \Gravity_Flow_API;

class Util_Service_Provider extends Service_Provider {

	const GF_API    = 'gf_api';
	const GFLOW_API = 'gflow_api';
	const JS_CONFIG = 'js_config';

	protected $provides = array(
		self::GF_API,
		self::GFLOW_API,
		self::JS_CONFIG,
	);


	public function register() {
		$container = $this->getContainer();

		$container->add( self::GFLOW_API, function () {
			return new Gravity_Flow_API( false );
		} );

		$container->add( self::GF_API, function () {
			return new GFAPI();
		} );

		$container->add( self::JS_CONFIG, function () {
			return new JS_Config();
		} );
	}

	public function hooks() {
		$container = $this->getContainer();

		add_action( 'admin_print_footer_scripts', function () use ( $container ) {
			$container->get( self::JS_CONFIG )->localize_admin_config();
		}, -10 );

		add_action( 'wp_print_footer_scripts', function () use ( $container ) {
			$container->get( self::JS_CONFIG )->localize_theme_config();
		}, -10 );
	}
}