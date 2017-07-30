<?php
/**
 * Gravity Flow Step Feed Campfire
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Campfire
 * @copyright   Copyright (c) 2016-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.1.4
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_Campfire extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'campfire';

	protected $_class_name = 'GFCampfire';

	public function get_label() {
		return 'Campfire';
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Campfire() );
