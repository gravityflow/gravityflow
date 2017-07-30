<?php
/**
 * Gravity Flow Step Feed MailChimp
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_MailChimp
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_MailChimp extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'mailchimp';

	protected $_class_name = 'GFMailChimp';

	public function get_label() {
		return 'MailChimp';
	}

	public function get_icon_url() {
		return $this->get_base_url() . '/images/mailchimp.svg';
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_MailChimp() );
