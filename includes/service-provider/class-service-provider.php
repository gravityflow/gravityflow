<?php

namespace Gravity_Flow\Gravity_Flow\Service_Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

abstract class Service_Provider extends AbstractServiceProvider implements BootableServiceProviderInterface {

	/**
	 * No-op hooks method by default.
	 */
	public function hooks() {
	}

	protected function has_hooks() {
		$reflector = new \ReflectionMethod( $this, 'hooks' );

		return $reflector->getDeclaringClass()->getName() !== self::class;
	}

	/**
	 * Simply calls our custom hooks method.
	 */
	public function boot() {
		if ( ! $this->has_hooks() ) {
			return;
		}

		$this->register();
		$this->hooks();
	}

}