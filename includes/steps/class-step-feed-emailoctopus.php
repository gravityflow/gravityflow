<?php
/**
 * Gravity Flow Step Feed EmailOctopus
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_EmailOctopus
 * @copyright   Copyright (c) 2015-2020, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.5.10
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_EmailOctopus
 *
 * @since 2.5.10
 */
class Gravity_Flow_Step_Feed_EmailOctopus extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @since 2.5.10
	 *
	 * @var string
	 */
	public $_step_type = 'emailoctopus';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @since 2.5.10
	 *
	 * @var string
	 */
	protected $_class_name = 'GF_EmailOctopus';

	/**
	 * Returns the step label.
	 *
	 * @since 2.5.10
	 *
	 * @return string
	 */
	public function get_label() {
		return 'EmailOctopus';
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_EmailOctopus() );
