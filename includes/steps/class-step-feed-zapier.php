<?php
/**
 * Gravity Flow Step Feed Zapier
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Zapier
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}


class Gravity_Flow_Step_Feed_Zapier extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'zapier';

	protected $_class_name = 'GFZapier';
	protected $_slug = 'gravityformszapier';

	public function get_label() {
		return 'Zapier';
	}

	public function get_icon_url() {
		return $this->get_base_url() . '/images/zapier-icon.svg';
	}

	public function get_feeds() {
		if ( class_exists( 'GFZapierData' ) ) {
			$form_id = $this->get_form_id();
			$feeds   = GFZapierData::get_feed_by_form( $form_id );
		} else {
			$feeds = array();
		}

		return $feeds;
	}

	public function process_feed( $feed ) {
		$form  = $this->get_form();
		$entry = $this->get_entry();

		if ( method_exists( 'GFZapier', 'process_feed' ) ) {
			GFZapier::process_feed( $feed, $entry, $form );
		} else {
			GFZapier::send_form_data_to_zapier( $entry, $form );
		}

		return true;
	}

	public function intercept_submission() {
		remove_action( 'gform_after_submission', array( 'GFZapier', 'send_form_data_to_zapier' ) );
	}

	public function get_feed_label( $feed ) {
		return $feed['name'];
	}

	public function is_feed_condition_met( $feed, $form, $entry ) {

		return GFZapier::conditions_met( $form, $feed, $entry );
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Zapier() );
