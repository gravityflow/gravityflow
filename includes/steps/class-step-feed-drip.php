<?php
/**
 * Gravity Flow Step Feed Drip
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Drip
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Drip
 */
class Gravity_Flow_Step_Feed_Drip extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'drip';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFP_Drip';

	/**
	 * The current instance of the Drip add-on or null.
	 *
	 * @var null|GFP_Drip_Addon
	 */
	protected $_addon_instance = null;

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Drip';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/drip-icon.svg';
	}

	/**
	 * Returns the Drip add-on feeds for the current form.
	 *
	 * @return array
	 */
	public function get_feeds() {
		if ( is_object( $this->get_add_on_instance() ) ) {
			$form_id = $this->get_form_id();
			$feeds   = $this->get_add_on_instance()->get_feeds( $form_id );
		} else {
			$feeds = array();
		}

		return $feeds;
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @param array $feed The Drip add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
	public function process_feed( $feed ) {
		if ( is_object( $this->get_add_on_instance() ) ) {
			$form  = $this->get_form();
			$entry = $this->get_entry();
			$this->get_add_on_instance()->process_feed( $feed, $entry, $form );
		}

		return true;
	}

	/**
	 * Returns the current instance of the Drip add-on.
	 *
	 * @return GFP_Drip_Addon|null
	 */
	public function get_add_on_instance() {
		if ( ! is_object( $this->_add_on_instance ) && class_exists( $this->_class_name ) ) {
			$add_on = new GFP_Drip();
			$add_on->plugins_loaded();
			$this->_add_on_instance = $add_on->get_addon_object();
		}

		return $this->_add_on_instance;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Drip() );
