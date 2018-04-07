<?php
/**
 * Gravity Flow Step Feed Zoho CRM
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_ZohoCRM
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.2-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_ZohoCRM
 */
class Gravity_Flow_Step_Feed_ZohoCRM extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'zohocrm';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFZohoCRM';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Zoho CRM';
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_ZohoCRM() );
