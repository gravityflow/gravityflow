<?php

namespace Gravity_Flow\Gravity_Flow\Service_Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

abstract class Service_Provider extends AbstractServiceProvider implements BootableServiceProviderInterface {

	/**
	 * No-op hooks method by default.
	 */
	public function hooks() {}

	/**
	 * Simply calls our custom hooks method.
	 */
	public function boot() {
		$this->hooks();
	}

}