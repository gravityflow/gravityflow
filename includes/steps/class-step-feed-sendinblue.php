<?php
/**
 * Gravity Flow Step Feed SendinBlue
 *
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


class Gravity_Flow_Step_Feed_Sendinblue extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'sendinblue';

	protected $_class_name = 'GFSIB_Manager';
	protected $_slug = 'gravityformssendinblue';

	public function get_label() {
		return 'SendinBlue';
	}

	public function get_icon_url() {
		return $this->get_base_url() . '/images/sendinblue-icon.png';
	}

	public function get_feeds() {
		if ( class_exists( 'GFSendinBlueData' ) ) {
			$form_id = $this->get_form_id();
			$feeds   = GFSendinBlueData::get_feed_by_form( $form_id, true );
		} else {
			$feeds = array();
		}

		return $feeds;
	}

	public function process_feed( $feed ) {
		$form  = $this->get_form();
		$entry = $this->get_entry();

		GFSIB_Manager::export_feed( $entry, $form, $feed );

		return true;
	}

	public function intercept_submission() {
		remove_action( 'gform_post_submission', array( 'GFSIB_Manager', 'export' ) );
	}

	public function get_feed_label( $feed ) {
		return $feed['meta']['contact_list_name'];
	}

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
