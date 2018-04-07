<?php
/**
 * Gravity Flow Step Notification
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Notification
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Notification
 */
class Gravity_Flow_Step_Notification extends Gravity_Flow_Step {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'notification';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Notification', 'gravityflow' );
	}

	/**
	 * Returns the HTML for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return '<i class="fa fa-envelope-o"></i>';
	}

	/**
	 * Returns an array of settings for this step type.
	 *
	 * @return array
	 */
	public function get_settings() {
		$form    = $this->get_form();
		$choices = array();

		foreach ( $form['notifications'] as $notification ) {
			$choices[] = array(
				'label' => $notification['name'],
				'name'  => 'notification_id_' . $notification['id'],
			);
		}

		$fields = array(
			array(
				'name'     => 'notification',
				'label'    => esc_html__( 'Gravity Forms Notifications', 'gravityflow' ),
				'type'     => 'checkbox',
				'required' => false,
				'choices'  => $choices,
			),
		);

		$settings_api                 = $this->get_common_settings_api();
		$workflow_notification_fields = $settings_api->get_setting_notification( array(
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
			'fields' => array_merge( $fields, $workflow_notification_fields ),
		);
	}

	/**
	 * Triggers sending of the selected notifications.
	 *
	 * @return bool
	 */
	function process() {
		$this->log_debug( __METHOD__ . '(): starting' );

		/* Ensure compatibility with Gravity PDF 3.x */
		if ( defined( 'PDF_EXTENDED_VERSION' ) && version_compare( PDF_EXTENDED_VERSION, '4.0-beta1', '<' ) && class_exists( 'GFPDF_Core' ) ) {
			global $gfpdf;
			if ( empty( $gfpdf ) ) {
				$gfpdf = new GFPDF_Core();
			}
		}

		$entry = $this->get_entry();

		$form = $this->get_form();

		foreach ( $form['notifications'] as $notification ) {
			$notification_id = $notification['id'];
			$setting_key     = 'notification_id_' . $notification_id;
			if ( $this->{$setting_key} ) {
				if ( ! GFCommon::evaluate_conditional_logic( rgar( $notification, 'conditionalLogic' ), $form, $entry ) ) {
					$this->log_debug( __METHOD__ . "(): Notification conditional logic not met, not processing notification (#{$notification_id} - {$notification['name']})." );
					continue;
				}
				GFCommon::send_notification( $notification, $form, $entry );
				$this->log_debug( __METHOD__ . "(): Notification sent (#{$notification_id} - {$notification['name']})." );

				$this->add_note( sprintf( esc_html__( 'Sent Notification: %s', 'gravityflow' ), $notification['name'] ) );
			}
		}

		$this->send_workflow_notification();

		return true;
	}

	/**
	 * Sends the workflow notification, if enabled.
	 */
	public function send_workflow_notification() {

		if ( ! $this->workflow_notification_enabled ) {
			return;
		}

		$type      = 'workflow';
		$assignees = $this->get_notification_assignees( $type );

		if ( empty( $assignees ) ) {
			return;
		}

		$notification = $this->get_notification( $type );
		$this->send_notifications( $assignees, $notification );

		$note = esc_html__( 'Sent Notification: ', 'gravityflow' ) . $this->get_name();
		$this->add_note( $note );

	}

	/**
	 * Prevent the notifications assigned to the current step from being sent during form submission.
	 */
	public function intercept_submission() {
		$form_id = $this->get_form_id();
		add_filter( "gform_disable_notification_{$form_id}", array( $this, 'maybe_disable_notification' ), 10, 2 );
	}

	/**
	 * Prevents the current notification from being sent during form submission if it is selected for this step.
	 *
	 * @param bool  $is_disabled  Indicates if the current notification has already been disabled.
	 * @param array $notification The current notifications properties.
	 *
	 * @return bool
	 */
	public function maybe_disable_notification( $is_disabled, $notification ) {
		$setting_key = 'notification_id_' . $notification['id'];

		return $this->{$setting_key} ? true : $is_disabled;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Notification() );
