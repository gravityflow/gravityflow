<?php
/**
 * Gravity Flow Step Feed Agile CRM
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_AgileCRM
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.1.4
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_AgileCRM
 */
class Gravity_Flow_Step_Feed_AgileCRM extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'agilecrm';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFAgileCRM';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Agile CRM';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/agilecrm-icon.svg';
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_AgileCRM() );
