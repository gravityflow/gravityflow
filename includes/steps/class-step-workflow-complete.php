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
class Gravity_Flow_Step_Workflow_Complete extends Gravity_Flow_Step {
	/**
	 * The step type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $_step_type = 'workflow_complete';

	/**
	 * Returns the step label.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Complete', 'gravityflow' );
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
		return '<i style="color:black;" class="fa fa-flag-checkered" aria-hidden="true"></i>';
	}

	/**
	 * Returns an array of statuses and their properties.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_status_config() {
		return array();
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
			'title'  => esc_html__( 'Complete', 'grvityflow' ),
			'fields' => array(
				array(
					'name'     => 'instructions',
					'label'    => __( 'Complete Message', 'gravityflow' ),
					'type'     => 'checkbox_and_textarea',
					'tooltip'  => esc_html__( 'Enable this setting to display a message to the form submitter when the workflow is complete.', 'gravityflow' ),
					'checkbox' => array(
						'label' => esc_html__( 'Display a message to the form submitter when the workflow is complete.', 'gravityflow' ),
					),
					'textarea' => array(
						'use_editor'    => true,
						'default_value' => '',
					),
				),
				array(
					'name'    => 'display_fields',
					'label'   => __( 'Display Fields', 'gravityflow' ),
					'tooltip' => __( 'Select the fields to display to the form submitter when the workflow is complete. Users with the "View All" capability can see all fields.', 'gravityflow' ),
					'type'    => 'display_fields',
				),
			),
		);

		return $settings;
	}

	/**
	 * This step can't set the final status.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function can_set_workflow_status() {
		return false;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Workflow_Complete() );
