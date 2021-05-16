<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Refresh_Inbox_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Argument;
use Gravity_Flow\Gravity_Flow\Ajax\Config as Ajax_Config;

class Config extends Ajax_Config {

	const SEARCH_ARGS = 'search_args';
	const CURRENT_IDS = 'current_ids';
	const TOKEN       = 'gflow_access_token';

	protected $name = 'refresh_inbox_items';

	public function method() {
		return 'POST';
	}

	public function args() {
		return array(
			$this->search_args(),
			$this->current_ids(),
			$this->token_args(),
		);
	}

	public function search_args() {
		return new Argument(
			self::SEARCH_ARGS,
			false,
			array()
		);
	}

	public function current_ids() {
		return new Argument(
			self::CURRENT_IDS,
			true,
		);
	}

	public function token_args() {
		return new Argument(
			self::TOKEN,
			true,
		);
	}
}