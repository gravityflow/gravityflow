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
	 * @since 1.4.3
	 *
	 * @var string
	 */
	public $_step_type = 'post_creation';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @since 2.3.4 Changed from GF_Post_Creation.
	 * @since 1.4.3
	 *
	 * @var string
	 */
	protected $_class_name = 'GF_Advanced_Post_Creation';

	/**
	 * Returns the step label.
	 *
	 * @since 1.4.3
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Post Creation';
	}

	/**
	 * Returns the class name for the add-on.
	 *
	 * @since 2.3.4
	 *
	 * @return string
	 */
	public function get_feed_add_on_class_name() {
		if ( class_exists( 'GF_Post_Creation' ) ) {
			$this->_class_name = 'GF_Post_Creation';
		}

		return $this->_class_name;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Post_Creation() );
