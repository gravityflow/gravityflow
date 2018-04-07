<?php
/**
 * Gravity Flow Step Feed Constant Contact
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Constant_Contact
 * @copyright   Copyright (c) 2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.1-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Constant_Contact
 */
class Gravity_Flow_Step_Feed_Constant_Contact extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'constant_contact';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GF_Constant_Contact';

	/**
	 * The slug used by the add-on.
	 *
	 * @var string
	 */
	protected $_slug = 'gravity-forms-constant-contact';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Constant Contact';
	}

	/**
	 * Returns the feed name.
	 *
	 * @param array $feed The Constant Contact feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		$label = $feed['meta']['feed_name'];

		return $label;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Constant_Contact() );
