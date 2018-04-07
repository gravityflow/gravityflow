<?php
/**
 * Gravity Flow Step Feed iContact
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_iContact
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.2-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_iContact
 */
class Gravity_Flow_Step_Feed_iContact extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'icontact';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFiContact';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'iContact';
	}

	/**
	 * Returns the feed name.
	 *
	 * @param array $feed The iContact feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		$label = $feed['meta']['feed_name'];

		return $label;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_iContact() );
