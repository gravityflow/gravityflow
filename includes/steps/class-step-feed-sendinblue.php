<?php
/**
 * Gravity Flow Step Feed SendinBlue
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Sendinblue
 * @copyright   Copyright (c) 2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.1-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Sendinblue
 */
class Gravity_Flow_Step_Feed_Sendinblue extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'sendinblue';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFSIB_Manager';

	/**
	 * The slug used by the add-on.
	 *
	 * @var string
	 */
	protected $_slug = 'gravityformssendinblue';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'SendinBlue';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/sendinblue-icon.png';
	}

	/**
	 * Returns the SendinBlue add-on feeds for the current form.
	 *
	 * @return array
	 */
	public function get_feeds() {
		if ( class_exists( 'GFSendinBlueData' ) ) {
			$form_id = $this->get_form_id();
			$feeds   = GFSendinBlueData::get_feed_by_form( $form_id, true );
		} else {
			$feeds = array();
		}

		return $feeds;
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @param array $feed The SendinBlue add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
	public function process_feed( $feed ) {
		$form  = $this->get_form();
		$entry = $this->get_entry();

		GFSIB_Manager::export_feed( $entry, $form, $feed );

		return true;
	}

	/**
	 * Prevent the feeds assigned to the current step from being processed by the associated add-on.
	 */
	public function intercept_submission() {
		remove_action( 'gform_post_submission', array( 'GFSIB_Manager', 'export' ) );
	}

	/**
	 * Returns the feed name.
	 *
	 * @param array $feed The SendinBlue feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		return $feed['meta']['contact_list_name'];
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
		if ( ! rgars( $feed, 'meta/optin_enabled' ) ) {
			return true;
		}

		$feed['meta']['feed_condition_conditional_logic'] = true;
		$feed['meta']['feed_condition_conditional_logic_object']['conditionalLogic'] = array(
			'logicType' => 'all',
			'rules'     => array(
				array(
					'fieldId'  => rgars( $feed, 'meta/optin_field_id' ),
					'operator' => rgars( $feed, 'meta/optin_operator' ),
					'value'    => rgars( $feed, 'meta/optin_value' ),
				),
			),
		);

		return parent::is_feed_condition_met( $feed, $form, $entry );
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Sendinblue() );
