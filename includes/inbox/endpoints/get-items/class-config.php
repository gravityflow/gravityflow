<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Endpoints\Get_Items;

use Gravity_Flow\Gravity_Flow\Ajax\Argument;
use Gravity_Flow\Gravity_Flow\Ajax\Config as Ajax_Config;

class Config extends Ajax_Config {

	const POSTS_PER_PAGE = 'posts_per_page';
	const OFFSET         = 'offset';
	const ORDERBY        = 'orderby';
	const ORDER          = 'order';

	protected $name      = 'get_items';
	protected $is_public = true;

	public function args() {
		return array(
			$this->posts_per_page(),
			$this->offset(),
			$this->orderby(),
			$this->order(),
		);
	}

	public function posts_per_page() {
		return new Argument(
			self::POSTS_PER_PAGE,
			false,
			10,
			array( $this, 'sanitize_number' )
		);
	}

	public function offset() {
		return new Argument(
			self::OFFSET,
			false,
			0,
			array( $this, 'sanitize_number' )
		);
	}

	public function orderby() {
		return new Argument(
			self::ORDERBY,
			false,
			'date'
		);
	}

	public function order() {
		return new Argument(
			self::ORDER,
			true
		);
	}

	public function sanitize_number( $value ) {
		return filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
	}
}