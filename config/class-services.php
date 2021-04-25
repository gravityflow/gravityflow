<?php

namespace Gravity_Flow\Gravity_Flow\Config;

use Gravity_Flow\Gravity_Flow\Ajax\Ajax_Service_Provider;
use Gravity_Flow\Gravity_Flow\Inbox\Inbox_Service_Provider;
use Gravity_Flow\Gravity_Flow\Util\Util_Service_Provider;

class Services {

	public static function get() {
		return array(
			Util_Service_Provider::class,
			Ajax_Service_Provider::class,
			Inbox_Service_Provider::class,
		);
	}

}