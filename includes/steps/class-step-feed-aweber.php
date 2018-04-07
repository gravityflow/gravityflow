<?php
/**
 * Gravity Flow Step Feed AWeber
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_AWeber
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.1.4
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_AWeber
 */
class Gravity_Flow_Step_Feed_AWeber extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'aweber';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFAWeber';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'AWeber';
	}
	
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_AWeber() );
