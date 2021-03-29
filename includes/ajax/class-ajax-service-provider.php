<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

use Gravity_Flow\Gravity_Flow\Service_Provider\Service_Provider;

class Ajax_Service_Provider extends Service_Provider {

	const STRATEGY         = 'ajax_response_strategy';
	const RESPONSE_FACTORY = 'ajax_response_factory';

	protected $provides = array(
		self::STRATEGY,
		self::RESPONSE_FACTORY,
	);

	public function register() {
		$container = $this->getContainer();

		$container->add( self::STRATEGY, function() {
			return new WP_Ajax_Return_Strategy();
		} );

		$container->add( self::RESPONSE_FACTORY, function() use ( $container ) {
			return new Response_Factory( $container->get( self::STRATEGY ) );
		} );
	}

}