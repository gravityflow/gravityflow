<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

abstract class Config {

	protected $name;

	protected $is_public;

	/**
	 * @return array
	 */
	abstract protected function args();

}