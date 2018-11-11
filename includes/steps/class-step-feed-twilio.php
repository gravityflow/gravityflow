<?php
/**
 * Gravity Flow Step Feed Twlio
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Twilio
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Twilio
 */
class Gravity_Flow_Step_Feed_Twilio extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'twilio';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFTwilio';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Twilio';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/twilio-icon-red.svg';
	}

	/**
	 * Processes this step.
	 *
	 * @since 2.3.2
	 *
	 * @return bool Is the step complete?
	 */
	function process() {
		// The Gravity Forms Twilio Add-On manipulates the merge tags when the URLs are shortened and prevents merge tag attributes from working.
		// So the Gravity Flow merge tags are replaced early.
		add_filter( 'gform_twilio_message', array( $this, 'filter_gform_twilio_message' ), 10, 4 );
		parent::process();
		remove_filter( 'gform_twilio_message', array( $this, 'filter_gform_twilio_message' ) );
		return true;
	}

	/**
	 * Callback for the gform_twilio_message filter.
	 *
	 * Replaces the merge tags in the SMS body before the Twilio Add-On mangles them.
	 *
	 * @since 2.3.2
	 *
	 * @return array
	 */
	function filter_gform_twilio_message( $args, $feed, $entry, $form ) {
		$args['body'] = gravity_flow()->replace_variables( $args['body'], $form, $entry, false, false, false, 'text' );
		return $args;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Twilio() );
