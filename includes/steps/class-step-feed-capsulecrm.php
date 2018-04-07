<?php
/**
 * Gravity Flow Step Feed Capsule CRM
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_CapsuleCRM
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.1.6
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_CapsuleCRM
 */
class Gravity_Flow_Step_Feed_CapsuleCRM extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'capsulecrm';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFCapsuleCRM';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Capsule CRM';
	}

	/**
	 * Returns the feed name.
	 *
	 * @param array $feed The Capsule CRM feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		$label = $feed['meta']['feed_name'];

		return $label;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_CapsuleCRM() );
