<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Inbox_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Argument;
use Gravity_Flow\Gravity_Flow\Ajax\Config as Ajax_Config;

class Config extends Ajax_Config {

	const SEARCH_ARGS = 'search_args';
	const TOKEN       = 'gflow_access_token';

	protected $name = 'get_inbox_items';

	public function args() {
		return array(
			$this->search_args(),
			$this->token_args(),
		);
	}

	public function search_args() {
		return new Argument(
			self::SEARCH_ARGS,
			false,
			array(),
		);
	}

	public function token_args() {
		return new Argument(
			self::TOKEN,
			true,
		);
	}
}