<?php
/**
 * Gravity Flow Step Feed Capsule CRM
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_CapsuleCRM
 * @copyright   Copyright (c) 2016-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.1.6
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_CapsuleCRM extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'capsulecrm';

	protected $_class_name = 'GFCapsuleCRM';

	public function get_label() {
		return 'Capsule CRM';
	}

	public function get_feed_label( $feed ) {
		$label = $feed['meta']['feed_name'];

		return $label;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_CapsuleCRM() );
