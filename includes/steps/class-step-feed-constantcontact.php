<?php
/**
 * Gravity Flow Step Feed Constant Contact
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_ConstantContact
 * @copyright   Copyright (c) 2015-2019, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.4.5
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_ConstantContact
 */
class Gravity_Flow_Step_Feed_ConstantContact extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @since 2.5
	 * @var string
	 */
	public $_step_type = 'constantcontact';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @since 2.5
	 * @var string
	 */
	protected $_class_name = 'GF_ConstantContact';

	/**
	 * Returns the step label.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Constant Contact';
	}

	/**
	 * Returns the URL for the step icon.
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/constant-contact.svg';
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_ConstantContact() );
