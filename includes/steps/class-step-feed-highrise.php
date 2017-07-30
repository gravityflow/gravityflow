<?php
/**
 * Gravity Flow Step Feed Highrise
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Highrise
 * @copyright   Copyright (c) 2016-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.2-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_Highrise extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'highrise';

	protected $_class_name = 'GFHighrise';

	public function get_label() {
		return 'Highrise';
	}

	public function get_feed_label( $feed ) {
		$label = $feed['meta']['feed_name'];

		return $label;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Highrise() );
