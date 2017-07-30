<?php
/**
 * Gravity Flow Step Feed Drip
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Drip
 * @copyright   Copyright (c) 2016-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_Drip extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'drip';

	protected $_class_name = 'GFP_Drip';
	protected $_addon_instance = null;

	public function get_label() {
		return 'Drip';
	}

	public function get_icon_url() {
		return $this->get_base_url() . '/images/drip-icon.svg';
	}

	public function get_feeds() {
		if ( is_object( $this->get_add_on_instance() ) ) {
			$form_id = $this->get_form_id();
			$feeds   = $this->get_add_on_instance()->get_feeds( $form_id );
		} else {
			$feeds = array();
		}

		return $feeds;
	}

	public function process_feed( $feed ) {
		if ( is_object( $this->get_add_on_instance() ) ) {
			$form  = $this->get_form();
			$entry = $this->get_entry();
			$this->get_add_on_instance()->process_feed( $feed, $entry, $form );
		}

		return true;
	}

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
