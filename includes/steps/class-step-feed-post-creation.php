<?php
/**
 * Gravity Flow Step Feed Post Creation
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Breeze
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Post_Creation
 */
class Gravity_Flow_Step_Feed_Post_Creation extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'post_creation';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GF_Post_Creation';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Post Creation';
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Post_Creation() );
