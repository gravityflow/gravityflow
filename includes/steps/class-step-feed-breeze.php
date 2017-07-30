<?php
/**
 * Gravity Flow Step Feed Breeze
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Breeze
 * @copyright   Copyright (c) 2016-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_Breeze extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'breeze';

	protected $_class_name = 'GF_Breeze';

	public function get_label() {
		return 'Breeze';
	}

	public function get_icon_url() {
		return $this->get_base_url() . '/images/breeze-icon.svg';
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Breeze() );
