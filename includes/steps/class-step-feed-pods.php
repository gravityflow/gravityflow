<?php
/**
 * Gravity Flow Step Feed Pods
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Pods
 * @copyright   Copyright (c) 2020, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.5.10
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Pods
 *
 * @since 2.5.10
 */
class Gravity_Flow_Step_Feed_Pods extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @since 2.5.10
	 *
	 * @var string
	 */
	public $_step_type = 'pods';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @since 2.5.10
	 *
	 * @var string
	 */
	protected $_class_name = 'Pods_GF_Addon';

	/**
	 * Returns the step label.
	 *
	 * @since 2.5.10
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Pods';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @since 2.5.10
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/pods-circle-green.svg';
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @since 2.5.10
	 *
	 * @param array $feed The Pods add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
	public function process_feed( $feed ) {
		$pods = $this->get_add_on_instance();
		if ( is_object( $pods ) ) {
			$form  = $this->get_form();
			$entry = $this->get_entry();
			$pods->setup_pods_gf( $form, $feed );
			$pods->process_feed( $feed, $entry, $form );
		}

		return true;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Pods() );
