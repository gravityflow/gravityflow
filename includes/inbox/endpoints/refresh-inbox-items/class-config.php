<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Refresh_Inbox_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Argument;
use Gravity_Flow\Gravity_Flow\Ajax\Config as Ajax_Config;

class Config extends Ajax_Config {

	const SEARCH_ARGS = 'search_args';
	const CURRENT_IDS = 'current_ids';

	protected $name      = 'refresh_inbox_items';
	protected $is_public = true;

	public function args() {
		return array(
			$this->search_args(),
			$this->current_ids(),
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
			false,
			array()
		);
	}
}