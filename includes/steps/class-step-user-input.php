<?php
/**
 * Gravity Flow Step User Input
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_User_Input
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_User_Input extends Gravity_Flow_Step {

	public $_step_type = 'user_input';

	protected $_editable_fields = array();

	protected $_update_post_fields = array(
		'fields' => array(),
		'images' => array(),
	);

	public function get_label() {
		return esc_html__( 'User Input', 'gravityflow' );
	}

	public function supports_expiration() {
		return true;
	}

	public function get_icon_url() {
		return '<i class="fa fa-pencil" ></i>';
	}

	public function get_settings() {
		$form         = $this->get_form();
		$settings_api = $this->get_common_settings_api();

		$settings = array(
			'title'  => esc_html__( 'User Input', 'gravityflow' ),
			'fields' => array(
				$settings_api->get_setting_assignee_type(),
				$settings_api->get_setting_assignees(),
				array(
					'id'       => 'editable_fields',
					'name'     => 'editable_fields[]',
					'label'    => __( 'Editable fields', 'gravityflow' ),
					'multiple' => 'multiple',
					'type'     => 'editable_fields',
				),
				$settings_api->get_setting_assignee_routing(),
				array(
					'id'            => 'assignee_policy',
					'name'          => 'assignee_policy',
					'label'         => __( 'Assignee Policy', 'gravityflow' ),
					'tooltip'       => __( 'Define how this step should be processed. If all assignees must complete this step then the entry will require input from every assignee before the step can be completed. If the step is assigned to a role only one user in that role needs to complete the step.', 'gravityflow' ),
					'type'          => 'radio',
					'default_value' => 'all',
					'choices'       => array(
						array(
							'label' => __( 'Only one assignee is required to complete the step', 'gravityflow' ),
							'value' => 'any',
						),
						array(
							'label' => __( 'All assignees must complete this step', 'gravityflow' ),
							'value' => 'all',
						),
					),
				),
			),
		);

		if ( $this->fields_have_conditional_logic( $form ) ) {
			$display_page_load_logic_setting = apply_filters( 'gravityflow_page_load_logic_setting', false );
			if ( $display_page_load_logic_setting && GFCommon::has_pages( $form ) && $this->pages_have_conditional_logic( $form ) ) {
				$settings['fields'][] = array(
					'name'     => 'conditional_logic_editable_fields_enabled',
					'label'    => esc_html__( 'Conditional Logic', 'gravityflow' ),
					'type'     => 'checkbox_and_select',
					'checkbox' => array(
						'label'          => esc_html__( 'Enable field conditional logic', 'gravityflow' ),
						'name'           => 'conditional_logic_editable_fields_enabled',
						'default_value' => '1',
					),
					'select'   => array(
						'name'    => 'conditional_logic_editable_fields_mode',
						'choices' => array(
							array(
								'value' => 'dynamic',
								'label' => esc_html__( 'Dynamic', 'gravityflow' ),
							),
							array(
								'value' => 'page_load',
								'label' => esc_html__( 'Only when the page loads', 'gravityflow' ),
							),
						),
						'tooltip' => esc_html__( 'Fields and Sections support dynamic conditional logic. Pages do not support dynamic conditional logic so they will only be shown or hidden when the page loads.', 'gravityflow' ),
					),
				);
			} else {
				$settings['fields'][] = array(
					'name'    => 'conditional_logic_editable_fields_enabled',
					'label'   => esc_html__( 'Conditional Logic', 'gravityflow' ),
					'type'    => 'checkbox',
					'choices' => array(
						array(
							'label'          => esc_html__( 'Enable field conditional logic', 'gravityflow' ),
							'name'           => 'conditional_logic_editable_fields_enabled',
							'default_value' => '1',
						),
					),
				);
			}
		}

		$settings2 = array(
			array(
				'name'     => 'highlight_editable_fields',
				'label'    => esc_html__( 'Highlight Editable Fields', 'gravityflow' ),
				'type'     => 'checkbox_and_select',
				'checkbox' => array(
					'label'          => esc_html__( 'Enable', 'gravityflow' ),
					'name'           => 'highlight_editable_fields_enabled',
					'defeault_value' => '0',
				),
				'select'   => array(
					'name'    => 'highlight_editable_fields_class',
					'choices' => array(
						array(
							'value' => 'green-triangle',
							'label' => esc_html__( 'Green triangle', 'gravityflow' ),
						),
						array(
							'value' => 'green-background',
							'label' => esc_html__( 'Green Background', 'gravityflow' ),
						),
					),
					'tooltip' => esc_html__( 'Fields and Sections support dynamic conditional logic. Pages do not support dynamic conditional logic so they will only be shown or hidden when the page loads.', 'gravityflow' ),
				),
			),
			$settings_api->get_setting_instructions(),
			$settings_api->get_setting_display_fields(),
			array(
				'name'          => 'default_status',
				'type'          => 'select',
				'label'         => __( 'Save Progress Option', 'gravityflow' ),
				'tooltip'       => __( 'This setting allows the assignee to save the field values without submitting the form as complete. Select Disabled to hide the "in progress" option or select the default value for the radio buttons.', 'gravityflow' ),
				'default_value' => 'hidden',
				'choices'       => array(
					array( 'label' => __( 'Disabled', 'gravityflow' ), 'value' => 'hidden' ),
					array( 'label' => __( 'Radio buttons (default: In progress)', 'gravityflow' ), 'value' => 'in_progress' ),
					array( 'label' => __( 'Radio buttons (default: Complete)', 'gravityflow' ), 'value' => 'complete' ),
				),
			),
			array(
				'name'          => 'note_mode',
				'label'         => esc_html__( 'Workflow Note', 'gravityflow' ),
				'type'          => 'select',
				'tooltip'       => esc_html__( 'The text entered in the Note box will be added to the timeline. Use this setting to select the options for the Note box.', 'gravityflow' ),
				'default_value' => 'not_required',
				'choices'       => array(
					array( 'value' => 'hidden', 'label' => esc_html__( 'Hidden', 'gravityflow' ) ),
					array( 'value' => 'not_required', 'label' => esc_html__( 'Not required', 'gravityflow' ) ),
					array( 'value' => 'required', 'label' => esc_html__( 'Always Required', 'gravityflow' ) ),
					array(
						'value' => 'required_if_in_progress',
						'label' => esc_html__( 'Required if in progress', 'gravityflow' ),
					),
					array(
						'value' => 'required_if_complete',
						'label' => esc_html__( 'Required if complete', 'gravityflow' ),
					),
				),
			),
			$settings_api->get_setting_notification_tabs( array(
				array(
					'label'  => __( 'Assignee Email', 'gravityflow' ),
					'id'     => 'tab_assignee_notification',
					'fields' => $settings_api->get_setting_notification( array(
						'checkbox_default_value' => true,
						'default_message'        => __( 'A new entry requires your input.', 'gravityflow' ),
					) ),
				),
				array(
					'label'  => __( 'In Progress Email', 'gravityflow' ),
					'id'     => 'tab_in_progress_notification',
					'fields' => $settings_api->get_setting_notification( array(
						'name_prefix'      => 'in_progress',
						'checkbox_label'   => __( 'Send email when the step is in progress.', 'gravityflow' ),
						'checkbox_tooltip' => __( 'Enable this setting to send an email when the entry is updated but the step is not completed.', 'gravityflow' ),
						'default_message'  => __( 'Entry {entry_id} has been updated and remains in progress.', 'gravityflow' ),
						'send_to_fields'   => true,
						'resend_field'     => false,
					) ),
				),
				array(
					'label'  => __( 'Complete Email', 'gravityflow' ),
					'id'     => 'tab_complete_notification',
					'fields' => $settings_api->get_setting_notification( array(
						'name_prefix'      => 'complete',
						'checkbox_label'   => __( 'Send email when the step is complete.', 'gravityflow' ),
						'checkbox_tooltip' => __( 'Enable this setting to send an email when the entry is updated completing the step.', 'gravityflow' ),
						'default_message'  => __( 'Entry {entry_id} has been updated completing the step.', 'gravityflow' ),
						'send_to_fields'   => true,
						'resend_field'     => false,
					) ),
				),
			) ),
			$settings_api->get_setting_confirmation_messasge( esc_html__( 'Thank you.', 'gravityflow' ) ),
		);

		$settings['fields'] = array_merge( $settings['fields'], $settings2 );

		return $settings;
	}

	public function fields_have_conditional_logic( $form ) {
		return gravity_flow()->fields_have_conditional_logic( $form );
	}

	public function pages_have_conditional_logic( $form ) {
		return gravity_flow()->pages_have_conditional_logic( $form );
	}

	public function process() {
		return $this->assign();
	}

	public function status_evaluation() {
		$assignee_details = $this->get_assignees();
		$step_status      = 'complete';

		foreach ( $assignee_details as $assignee ) {
			$user_status = $assignee->get_status();

			if ( $this->assignee_policy == 'any' ) {
				if ( $user_status == 'complete' ) {
					$step_status = 'complete';
					break;
				} else {
					$step_status = 'pending';
				}
			} else if ( empty( $user_status ) || $user_status == 'pending' ) {
				$step_status = 'pending';
			}
		}

		return $step_status;
	}

	public function fields_empty( $entry, $editable_fields ) {

		foreach ( $editable_fields as $editable_field ) {
			if ( isset( $entry[ $editable_field ] ) && ! empty( $entry[ $editable_field ] ) ) {
				return false;
			}
		}

		return true;
	}

	public function get_editable_fields() {
		if ( ! empty( $this->_editable_fields ) ) {
			return $this->_editable_fields;
		}

		$current_user_key = $this->get_current_assignee_key();
		$editable_fields  = array();
		$assignee_details = $this->get_assignees();

		foreach ( $assignee_details as $assignee ) {

			$assignee_key  = $assignee->get_key();
			$assignee_type = $assignee->get_type();

			$match = false;
			if ( $assignee_type == 'role' && gravity_flow()->check_user_role( $assignee->get_id() ) ) {
				$match = true;
			} elseif ( $assignee_key == $current_user_key ) {
				$match = true;
			}
			if ( $match && is_array( $assignee->get_editable_fields() ) ) {
				$assignee_editable_fields = $assignee->get_editable_fields();
				$editable_fields          = array_merge( $editable_fields, $assignee_editable_fields );
			}
		}

		$editable_fields        = apply_filters( 'gravityflow_editable_fields_user_input', $editable_fields, $this );
		$this->_editable_fields = $editable_fields;

		return $editable_fields;
	}


	public function maybe_process_status_update( $form, $entry ) {

		$new_status = rgpost( 'gravityflow_status' );
		if ( ! in_array( $new_status, array( 'in_progress', 'complete' ) ) ) {
			return false;
		}

		$entry_updater = $this->get_entry_updater( $form );
		$result        = $entry_updater->process( $new_status );

		if ( $result !== true ) {
			return $result;
		}

		$assignee_key = $this->get_current_assignee_key();
		$assignee     = $this->get_assignee( $assignee_key );
		$feedback     = $this->process_assignee_status( $assignee, $new_status, $form );

		$this->maybe_send_notification( $new_status );

		return $feedback;
	}

	/**
	 * @param Gravity_Flow_Assignee $assignee
	 * @param string $new_status
	 * @param array $form
	 *
	 * @return string|bool If processed return a message to be displayed to the user.
	 */
	public function process_assignee_status( $assignee, $new_status, $form ) {
		if ( $new_status == 'complete' ) {
			$current_user_status = $assignee->get_status();

			list( $role, $current_role_status ) = $this->get_current_role_status();

			if ( $current_user_status == 'pending' ) {
				$assignee->update_status( 'complete' );
			}

			if ( $current_role_status == 'pending' ) {
				$this->update_role_status( $role, 'complete' );
			}
			$this->refresh_entry();
		}

		if ( $new_status == 'complete' ) {
			$note_message = __( 'Entry updated and marked complete.', 'gravityflow' );
			if ( $this->confirmation_messageEnable ) {
				$feedback = $this->confirmation_messageValue;
				$feedback = $this->replace_variables( $feedback, $assignee );
				$feedback = GFCommon::replace_variables( $feedback, $form, $this->get_entry(), false, true, true, 'html' );
				$feedback = do_shortcode( $feedback );
				$feedback = wp_kses_post( $feedback );
			} else {
				$feedback = $note_message;
			}
		} else {
			$feedback     = esc_html__( 'Entry updated - in progress.', 'gravityflow' );
			$note_message = $feedback;
		}

		/**
		 * Allow the feedback message to be modified on the user input step.
		 *
		 * @param string $feedback
		 * @param string $new_status
		 * @param Gravity_Flow_Assignee $assignee
		 * @param array $form
		 * @param Gravity_Flow_Step $this
		 */
		$feedback = apply_filters( 'gravityflow_feedback_message_user_input', $feedback, $new_status, $assignee, $form, $this );

		$note = sprintf( '%s: %s', $this->get_name(), $note_message );
		$this->add_note( $note . $this->maybe_add_user_note(), true );

		$status = $this->evaluate_status();
		$this->update_step_status( $status );
		$entry = $this->refresh_entry();

		GFAPI::send_notifications( $form, $entry, 'workflow_user_input' );

		return $feedback;
	}

	/**
	 * Determine if the note is valid.
	 *
	 * @param string $new_status The new status for the current step.
	 * @param string $note The submitted note.
	 *
	 * @return bool
	 */
	public function validate_note_mode( $new_status, $note ) {
		switch ( $this->note_mode ) {
			case 'required' :
				return ! empty( $note );

			case 'required_if_in_progress' :
				if ( $new_status == 'in_progress' && empty( $note ) ) {
					return false;
				};
				break;

			case 'required_if_complete' :
				if ( $new_status == 'complete' && empty( $note ) ) {
					return false;
				};
		}

		return true;
	}

	/**
	 * Allow the validation result to be overridden using the gravityflow_validation_user_input filter.
	 *
	 * @param array $validation_result The validation result and form currently being processed.
	 * @param string $new_status The new status for the current step.
	 *
	 * @return array
	 */
	public function maybe_filter_validation_result( $validation_result, $new_status ) {

		return apply_filters( 'gravityflow_validation_user_input', $validation_result, $this, $new_status );

	}

	/**
	 * Display the workflow detail box for this step.
	 *
	 * @param array $form The current form.
	 * @param array $args The page arguments.
	 */
	public function workflow_detail_box( $form, $args ) {
		?>
		<div>
			<?php

			$this->maybe_display_assignee_status_list( $args, $form );

			$assignee_status = $this->get_current_assignee_status();
			list( $role, $role_status ) = $this->get_current_role_status();
			$can_update = $assignee_status == 'pending' || $role_status == 'pending';

			$this->maybe_enable_update_button( $can_update );

			/**
			 * Allows content to be added in the workflow box below the status list.
			 *
			 * @param Gravity_Flow_Step $this
			 * @param array $form
			 */
			do_action( 'gravityflow_below_status_list_user_input', $this, $form );

			if ( $can_update ) {
				$this->maybe_display_note_box( $form );
				$this->display_status_inputs();
				$this->display_update_button( $form );
			}

			?>
		</div>
		<?php
	}

	/**
	 * Get the status string, including icon (if complete).
	 *
	 * @return string
	 */
	public function get_status_string() {
		$input_step_status = $this->get_status();
		$status_str        = __( 'Pending Input', 'gravityflow' );

		if ( $input_step_status == 'complete' ) {
			$approve_icon = '<i class="fa fa-check" style="color:green"></i>';
			$status_str   = $approve_icon . __( 'Complete', 'gravityflow' );
		} elseif ( $input_step_status == 'queued' ) {
			$status_str = __( 'Queued', 'gravityflow' );
		}

		return $status_str;
	}

	/**
	 * If applicable display the assignee status list.
	 *
	 * @param array $args The page arguments.
	 * @param array $form The current form.
	 */
	public function maybe_display_assignee_status_list( $args, $form ) {
		$display_step_status = (bool) $args['step_status'];

		/**
		 * Allows the assignee status list to be hidden.
		 *
		 * @param array $form
		 * @param array $entry
		 * @param Gravity_Flow_Step $current_step
		 */
		$display_assignee_status_list = apply_filters( 'gravityflow_assignee_status_list_user_input', $display_step_status, $form, $this );
		if ( ! $display_assignee_status_list ) {
			return;
		}

		echo sprintf( '<h4 style="margin-bottom:10px;">%s (%s)</h4>', $this->get_name(), $this->get_status_string() );

		echo '<ul>';

		$assignees = $this->get_assignees();

		$this->log_debug( __METHOD__ . '(): assignee details: ' . print_r( $assignees, true ) );

		foreach ( $assignees as $assignee ) {
			$assignee_status = $assignee->get_status();

			$this->log_debug( __METHOD__ . '(): showing status for: ' . $assignee->get_key() );
			$this->log_debug( __METHOD__ . '(): assignee status: ' . $assignee_status );

			if ( ! empty( $assignee_status ) ) {

				$assignee_type = $assignee->get_type();
				$assignee_id   = $assignee->get_id();

				if ( $assignee_type == 'user_id' ) {
					$user_info    = get_user_by( 'id', $assignee_id );
					$status_label = $this->get_status_label( $assignee_status );
					echo sprintf( '<li>%s: %s (%s)</li>', esc_html__( 'User', 'gravityflow' ), $user_info->display_name, $status_label );
				} elseif ( $assignee_type == 'email' ) {
					$email        = $assignee_id;
					$status_label = $this->get_status_label( $assignee_status );
					echo sprintf( '<li>%s: %s (%s)</li>', esc_html__( 'Email', 'gravityflow' ), $email, $status_label );
				} elseif ( $assignee_type == 'role' ) {
					$status_label = $this->get_status_label( $assignee_status );
					$role_name    = translate_user_role( $assignee_id );
					echo sprintf( '<li>%s: (%s)</li>', esc_html__( 'Role', 'gravityflow' ), $role_name, $status_label );
					echo '<li>' . $role_name . ': ' . $assignee_status . '</li>';
				}
			}
		}

		echo '</ul>';

	}

	/**
	 * If the user can update the step enable the update button.
	 *
	 * @param bool $can_update Indicates if the assignee or role status is pending.
	 */
	public function maybe_enable_update_button( $can_update ) {
		if ( ! $can_update ) {
			return;
		}

		?>
		<script>
			(function (GFFlowInput, $) {
				$(document).ready(function () {
					$('#gravityflow_update_button').prop('disabled', false);
				});
			}(window.GFFlowInput = window.GFFlowInput || {}, jQuery));
		</script>
		<?php
	}

	/**
	 * Output the status inputs and associated labels.
	 */
	public function display_status_inputs() {
		$default_status = $this->default_status ? $this->default_status : 'complete';

		if ( $default_status == 'hidden' ) {
			?>
			<input type="hidden" id="gravityflow_status_hidden" name="gravityflow_status" value="complete"/>
			<?php
		} else {

			$in_progress_label = esc_html__( 'In progress', 'gravityflow' );

			/**
			 * Allows the 'in progress' label to be modified on the User Input step.
			 *
			 * @params string $in_progress_label
			 * @params Gravity_Flow_Step $this The current step.
			 */
			$in_progress_label = apply_filters( 'gravityflow_in_progress_label_user_input', $in_progress_label, $this );

			$complete_label = esc_html__( 'Complete', 'gravityflow' );

			/**
			 * Allows the 'complete' label to be modified on the User Input step.
			 *
			 * @params string $complete_label
			 * @params Gravity_Flow_Step $this The current step.
			 */
			$complete_label = apply_filters( 'gravityflow_complete_label_user_input', $complete_label, $this )
			?>
			<br/><br/>
			<div>
				<label for="gravityflow_in_progress">
					<input type="radio" id="gravityflow_in_progress" name="gravityflow_status" <?php checked( $default_status, 'in_progress' ); ?> value="in_progress"/><?php echo $in_progress_label; ?>
				</label>&nbsp;&nbsp;
				<label for="gravityflow_complete">
					<input type="radio" id="gravityflow_complete" name="gravityflow_status" value="complete" <?php checked( $default_status, 'complete' ); ?>/><?php echo $complete_label; ?>
				</label>
			</div>
			<?php
		}
	}

	/**
	 * Display the update button for this step.
	 *
	 * @param array $form The form for the current entry.
	 */
	public function display_update_button( $form ) {
		?>
		<br/>
		<div class="gravityflow-action-buttons">
			<?php
			$button_text = $this->default_status == 'hidden' ? esc_html__( 'Submit', 'gravityflow' ) : esc_html__( 'Update', 'gravityflow' );
			$button_text = apply_filters( 'gravityflow_update_button_text_user_input', $button_text, $form, $this );

			$form_id          = absint( $form['id'] );
			$button_click     = "jQuery('#action').val('update'); jQuery('#gform_{$form_id}').submit(); return false;";
			$update_button_id = 'gravityflow_update_button';

			$update_button    = '<input id="' . $update_button_id . '" disabled="disabled" class="button button-large button-primary" type="submit" tabindex="4" value="' . $button_text . '" name="save" onclick="' . $button_click . '"/>';
			echo apply_filters( 'gravityflow_update_button_user_input', $update_button );
			?>
		</div>
		<?php
	}

	/**
	 * If applicable display the note section of the workflow detail box.
	 *
	 * @param array $form The form for the current entry.
	 */
	public function maybe_display_note_box( $form ) {
		if ( $this->note_mode === 'hidden' ) {
			return;
		}
		$invalid_note = ( isset( $form['workflow_note'] ) && is_array( $form['workflow_note'] ) && $form['workflow_note']['failed_validation'] );
		$posted_note  = '';
		if ( rgar( $form, 'failed_validation' ) ) {
			$posted_note = rgpost( 'gravityflow_note' );
		}
		?>

		<div>
			<label id="gravityflow-notes-label" for="gravityflow-note">
				<?php
				esc_html_e( 'Note', 'gravityflow' );
				$required_indicator = ( $this->note_mode == 'required' ) ? '*' : '';
				printf( "<span class='gfield_required'>%s</span>", $required_indicator );
				?>
			</label>
		</div>

		<textarea id="gravityflow-note" rows="4" class="wide" name="gravityflow_note"><?php echo esc_textarea( $posted_note ) ?></textarea>
		<?php

		if ( $invalid_note ) {
			printf( "<div class='gfield_description validation_message'>%s</div>", $form['workflow_note']['validation_message'] );
		}
	}

	public function entry_detail_status_box( $form ) {
		$status = $this->evaluate_status();
		?>
		<h4 style="padding:10px;"><?php echo $this->get_name() . ': ' . $status ?></h4>

		<div style="padding:10px;">
			<ul>
				<?php

				$assignees = $this->get_assignees();

				foreach ( $assignees as $assignee ) {

					$assignee_type = $this->get_type();

					$status = $assignee->get_status();

					if ( ! empty( $user_status ) ) {
						$status_label = $this->get_status_label( $status );
						switch ( $assignee_type ) {
							case 'email':
								echo sprintf( '<li>%s: %s (%s)</li>', esc_html__( 'Email', 'gravityflow' ), $this->get_id(), $status_label );
								break;
							case 'user_id' :
								$user_info = get_user_by( 'id', $assignee->get_id() );
								echo '<li>' . esc_html__( 'User', 'gravityflow' ) . ': ' . $user_info->display_name . '<br />' . esc_html__( 'Status', 'gravityflow' ) . ': ' . esc_html( $status_label ) . '</li>';
								break;
							case 'role' :

								$role_name = translate_user_role( $assignee->get_id() );
								echo '<li>' . $role_name . ': ' . esc_html( $status_label ) . '</li>';
								break;
						}
					}
				}

				?>
			</ul>
		</div>
		<?php
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_User_Input() );
