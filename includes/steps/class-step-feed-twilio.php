<?php
/**
 * Gravity Flow Step Feed Twlio
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Twilio
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_Twilio extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'twilio';

	protected $_class_name = 'GFTwilio';

	public function get_label() {
		return 'Twilio';
	}

	public function get_icon_url() {
		return $this->get_base_url() . '/images/twilio-icon-red.svg';
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Twilio() );
