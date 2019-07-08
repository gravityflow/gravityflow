<?php
/**
 * Gravity Flow Step Feed Zapier
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Zapier
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Zapier
 */
class Gravity_Flow_Step_Feed_Zapier extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'zapier';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFZapier';

	/**
	 * The slug used by the add-on.
	 *
	 * @var string
	 */
	protected $_slug = 'gravityformszapier';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Zapier';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/zapier-icon.svg';
	}

	/**
	 * Returns the feeds for the add-on.
	 *
	 * @return array
	 */
	public function get_feeds() {
		if ( class_exists( 'GFZapierData' ) ) {
			$form_id = $this->get_form_id();
			$feeds   = GFZapierData::get_feed_by_form( $form_id );
		} else {
			$feeds = array();
		}

		return $feeds;
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @param array $feed The add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
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

	/**
	 * Prevent the feeds assigned to the current step from being processed by the associated add-on.
	 */
	public function intercept_submission() {
		remove_action( 'gform_after_submission', array( 'GFZapier', 'send_form_data_to_zapier' ) );
	}

	/**
	 * Returns the feed name.
	 *
	 * @param array $feed The feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		return $feed['name'];
	}

	/**
	 * Determines if the supplied feed should be processed.
	 *
	 * @param array $feed  The current feed.
	 * @param array $form  The current form.
	 * @param array $entry The current entry.
	 *
	 * @return bool
	 */
	public function is_feed_condition_met( $feed, $form, $entry ) {

		return GFZapier::conditions_met( $form, $feed, $entry );
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Zapier() );
