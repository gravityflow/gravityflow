<?php
/**
 * Gravity Flow Step Feed MailChimp
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_MailChimp
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_MailChimp
 */
class Gravity_Flow_Step_Feed_MailChimp extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'mailchimp';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFMailChimp';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'MailChimp';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/mailchimp.svg';
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_MailChimp() );
