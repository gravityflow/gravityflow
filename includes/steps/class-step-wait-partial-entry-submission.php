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

		$settings = array(
			'title'  => $this->get_label(),
			'fields' => array(
				$settings_api->get_setting_assignee_type(),
				$settings_api->get_setting_assignees(),
				$settings_api->get_setting_assignee_routing(),
				$settings_api->get_setting_notification_tabs( array(
					array(
						'label'  => __( 'Assignee email', 'gravityflow' ),
						'id'     => 'tab_assignee_notification',
						'fields' => $settings_api->get_setting_notification( array(
							'checkbox_default_value' => true,
							'default_message'        => __( 'Please resume the following form: {workflow_resume_partial_entry_link}', 'gravityflow' ),
						) ),
					),
				) ),
				array(
					'name'          => 'submit_page',
					'tooltip'       => __( 'Select the page to be used for the form submission. This can be the Workflow Submit Page in the WordPress Admin Dashboard or you can choose a page with either a Gravity Flow submit shortcode or a Gravity Forms shortcode.', 'gravityflow' ),
					'label'         => __( 'Submission Page', 'gravityflow' ),
					'type'          => 'select',
					'default_value' => 'admin',
					'choices'       => $this->get_page_choices(),
				),
			),
		);

		return $settings;
	}

	/**
	 * Send the notification, if enabled.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function process() {
		$this->assign();

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

	/**
	 * Returns the choices for the Submit Page setting.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_page_choices() {
		$choices = array(
			array(
				'label' => __( 'Default - WordPress Admin Dashboard: Workflow Submit Page', 'gravityflow' ),
				'value' => 'admin',
			),
		);

		$pages = get_pages();

		foreach ( $pages as $page ) {
			$choices[] = array(
				'label' => $page->post_title,
				'value' => $page->ID,
			);
		}

		return $choices;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Wait_Partial_Entry_Submission() );
