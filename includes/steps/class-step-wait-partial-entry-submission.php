<?php
/**
 * Gravity Flow Step Wait For Partial Entry Submission
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Wait_Partial_Entry_Submission
 * @copyright   Copyright (c) 2015-2019, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.5
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Wait_Partial_Entry_Submission
 */
class Gravity_Flow_Step_Wait_Partial_Entry_Submission extends Gravity_Flow_Step {

	/**
	 * The step type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $_step_type = 'wait_partial_entry_submission';

	/**
	 * Returns the step label.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Wait For Partial Entry Submission', 'gravityflow' );
	}

	/**
	 * Returns the HTML for the step icon.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return '<i class="fa fa-pause"></i>';
	}

	/**
	 * Determines if this step can be used on this site.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function is_supported() {
		return Gravity_Flow_Partial_Entries::is_supported();
	}

	/**
	 * Ensure active steps are not processed if the step is not supported.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function is_active() {
		$is_active = parent::is_active();

		if ( $is_active && ! $this->is_supported() ) {
			$is_active = false;
		}

		return $is_active;
	}

	/**
	 * Allow this step to expire.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function supports_expiration() {
		return true;
	}

	/**
	 * Returns an array of settings for this step type.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings_api = $this->get_common_settings_api();
		$fields       = $settings_api->get_setting_notification( array(
			'name_prefix'      => 'workflow',
			'label'            => __( 'Workflow notification', 'gravityflow' ),
			'tooltip'          => __( 'Enable this setting to send an email.', 'gravityflow' ),
			'checkbox_label'   => __( 'Enabled', 'gravityflow' ),
			'checkbox_tooltip' => '',
			'send_to_fields'   => true,
			'resend_field'     => false,
		) );

		return array(
			'title'  => 'Notification',
			'fields' => $fields,
		);
	}

	/**
	 * Send the notification, if enabled.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function process() {
		$this->send_workflow_notification();

		return false;
	}

	/**
	 * Evaluates the status for the step.
	 *
	 * The step is only complete when the form has been submitted, deleting the Partial Entries entry meta.
	 *
	 * @since 2.5
	 *
	 * @return string 'pending' or 'complete'
	 */
	public function status_evaluation() {
		$partial_entry_id = gform_get_meta( $this->get_entry_id(), 'partial_entry_id' );

		return $partial_entry_id ? 'pending' : 'complete';
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Wait_Partial_Entry_Submission() );
