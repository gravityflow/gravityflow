<?php
/**
 * Gravity Flow Step Workflow Start
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Complete
 * @copyright   Copyright (c) 2015-2019, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.5
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Complete
 *
 * @since 2.5
 */
class Gravity_Flow_Step_Workflow_Start extends Gravity_Flow_Step {
	/**
	 * The step type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $_step_type = 'workflow_start';

	/**
	 * Returns the step label.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Start', 'gravityflow' );
	}

	/**
	 * Indicates this step can expire without user input.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function supports_expiration() {
		return false;
	}

	/**
	 * Returns the HTML for the step icon.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return '<i style="color:darkgreen;" class="fa fa-play" aria-hidden="true"></i>';
	}

	/**
	 * Returns an array of statuses and their properties.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_status_config() {
		return array(
			array(
				'status'                    => 'complete',
				'status_label'              => __( 'Complete', 'gravityflow' ),
				'destination_setting_label' => __( 'First Step in the workflow', 'gravityflow' ),
				'default_destination'       => 'next',
			),
		);
	}

	/**
	 * Add settings to the step.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_settings() {

		$settings = array(
			'title'  => esc_html__( 'Start', 'grvityflow' ),
			'fields' => array(
				array(
					'name'     => 'instructions',
					'label'    => __( 'Pending Message', 'gravityflow' ),
					'type'     => 'checkbox_and_textarea',
					'tooltip'  => esc_html__( 'Enable this setting to display a message to the form submitter while the workflow is pending.', 'gravityflow' ),
					'checkbox' => array(
						'label' => esc_html__( 'Display a message to the form submitter while the workflow is pending.', 'gravityflow' ),
					),
					'textarea' => array(
						'use_editor'    => true,
						'default_value' => '',
						'before' => sprintf( '<div id="instructions-settings-description" class="gravityflow-instructions-setting-description">%s</div>', esc_html__( "Note: this message will not be displayed when the form submitter is an assignee of a workflow step. Use the step's instructions setting instead.", 'gravityflow' ) ),
					),
				),
				array(
					'name'    => 'display_fields',
					'label'   => __( 'Default Display Fields', 'gravityflow' ),
					'tooltip' => __( 'Select the fields to hide or display for users who are not assignees while the workflow is in progress. Users with the "View All" capability can see all fields.', 'gravityflow' ),
					'type'    => 'display_fields',
				),
			),
		);

		return $settings;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Workflow_Start() );
