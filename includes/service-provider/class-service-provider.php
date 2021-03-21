<?php

namespace Gravity_Flow\Gravity_Flow\Service_Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

abstract class Service_Provider extends AbstractServiceProvider implements BootableServiceProviderInterface {

	/**
	 * No-op boot method by default.
	 */
	public function boot() {}

}