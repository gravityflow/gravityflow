<?php
/**
 * Gravity Flow Step User Input
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_User_Input
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_User_Input
 */
class Gravity_Flow_Step_User_Input extends Gravity_Flow_Step {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'user_input';

	/**
	 * An array of field IDs which the user can edit.
	 *
	 * @var array
	 */
	protected $_editable_fields = array();

	/**
	 * An array of field IDs to be processed when updating the post created from the current entry.
	 *
	 * @var array
	 */
	protected $_update_post_fields = array(
		'fields' => array(),
		'images' => array(),
	);

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'User Input', 'gravityflow' );
	}

	/**
	 * Indicates this step can expire without user input.
	 *
	 * @return bool
	 */
	public function supports_expiration() {
		return true;
	}

	/**
	 * Returns the HTML for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return '<i class="fa fa-pencil" ></i>';
	}

	/**
	 * Returns an array of settings for this step type.
	 *
	 * @return array
	 */
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
				'label'         => __( 'Save Progress', 'gravityflow' ),
				'tooltip'       => __( 'This setting allows the assignee to save the field values without submitting the form as complete. Select Disabled to hide the "in progress" option or select the default value for the radio buttons.', 'gravityflow' ),
				'default_value' => 'hidden',
				'choices'       => array(
					array( 'label' => __( 'Disabled', 'gravityflow' ), 'value' => 'hidden' ),
					array( 'label' => __( 'Radio buttons (default: In progress)', 'gravityflow' ), 'value' => 'in_progress' ),
					array( 'label' => __( 'Radio buttons (default: Complete)', 'gravityflow' ), 'value' => 'complete' ),
					array( 'label' => __( 'Submit buttons (Save and Submit)', 'gravityflow' ), 'value' => 'submit_buttons' ),
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

	/**
	 * Determines if this forms fields have conditional logic configured.
	 *
	 * @param array $form The current form.
	 *
	 * @return bool
	 */
	public function fields_have_conditional_logic( $form ) {
		return gravity_flow()->fields_have_conditional_logic( $form );
	}

	/**
	 * Determines if this forms page fields have conditional logic configured.
	 *
	 * @param array $form The current form.
	 *
	 * @return bool
	 */
	public function pages_have_conditional_logic( $form ) {
		return gravity_flow()->pages_have_conditional_logic( $form );
	}

	/**
	 * Set the assignees for this step.
	 *
	 * @return bool
	 */
	public function process() {
		return $this->assign();
	}

	/**
	 * Determines the current status of the step.
	 *
	 * @return string
	 */
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

	/**
	 * Determines if all the editable fields are empty.
	 *
	 * @param array $entry           The current entry.
	 * @param array $editable_fields An array of field IDs which the user can edit.
	 *
	 * @return bool
	 */
	public function fields_empty( $entry, $editable_fields ) {

		foreach ( $editable_fields as $editable_field ) {
			if ( isset( $entry[ $editable_field ] ) && ! empty( $entry[ $editable_field ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns an array of editable fields for the current user.
	 *
	 * @return array
	 */
	public function get_editable_fields() {
		if ( ! empty( $this->_editable_fields ) ) {
			return $this->_editable_fields;
		}

		$editable_fields  = array();
		$assignee_details = $this->get_assignees();

		foreach ( $assignee_details as $assignee ) {
			if ( $assignee->is_current_user() && is_array( $assignee->get_editable_fields() ) ) {
				$assignee_editable_fields = $assignee->get_editable_fields();
				$editable_fields          = array_merge( $editable_fields, $assignee_editable_fields );
			}
		}

		$editable_fields        = apply_filters( 'gravityflow_editable_fields_user_input', $editable_fields, $this );
		$this->_editable_fields = $editable_fields;

		return $editable_fields;
	}

	/**
	 * Handles POSTed values from the workflow detail page.
	 *
	 * @param array $form  The current form.
	 * @param array $entry The current entry.
	 *
	 * @return string|bool|WP_Error Return a success feedback message safe for page output or a WP_Error instance with an error.
	 */
	public function maybe_process_status_update( $form, $entry ) {

		$feedback = false;

		$form_id = $form['id'];

		if ( isset( $_POST['gforms_save_entry'] ) && rgpost( 'step_id' ) == $this->get_id() && check_admin_referer( 'gforms_save_entry', 'gforms_save_entry' ) ) {

			$new_status = rgpost( 'gravityflow_status' );

			if ( ! in_array( $new_status, array( 'in_progress', 'complete' ) ) ) {
				return false;
			}

			// Loading files that have been uploaded to temp folder.
			$files = GFCommon::json_decode( rgpost( 'gform_uploaded_files' ) );
			if ( ! is_array( $files ) ) {
				$files = array();
			}

			GFFormsModel::$uploaded_files[ $form_id ] = $files;

			$validation = $this->validate_status_update( $new_status, $form );
			if ( is_wp_error( $validation ) ) {
				$this->log_debug( __METHOD__ . '(): Failed validation.' );

				// Upload valid temp single files.
				$this->maybe_upload_files( $form, $files );

				return $validation;
			}

			$editable_fields = $this->get_editable_fields();

			$previous_assignees = $this->get_assignees();

			foreach ( $previous_assignees as $assignee ) {
				if ( $assignee->is_current_user() ) {
					$feedback = $this->process_assignee_status( $assignee, $new_status, $form );
					break;
				}
			}

			$original_entry = $entry = $this->refresh_entry();

			$this->save_entry( $form, $entry, $editable_fields );

			remove_action( 'gform_after_update_entry', array( gravity_flow(), 'filter_after_update_entry' ) );

			do_action( 'gform_after_update_entry', $form, $entry['id'], $original_entry );
			do_action( "gform_after_update_entry_{$form['id']}", $form, $entry['id'], $original_entry );

			$entry = GFFormsModel::get_lead( $entry['id'] );
			GFFormsModel::set_entry_meta( $entry, $form );

			$this->refresh_entry();

			$this->maybe_process_post_fields( $form, $entry['post_id'] );

			GFCache::flush();

			$this->maybe_adjust_assignment( $previous_assignees );

			if ( ! $feedback ) {
				$feedback = new WP_Error( 'assignee_not_found', esc_html__( 'There was a problem while updating the assignee status.' ) );
			}

			$this->maybe_send_notification( $new_status );
			GFAPI::send_notifications( $form, $entry, 'workflow_user_input' );
		}

		return $feedback;
	}

	/**
	 * Get the temporary file path and create the folder if it does not already exist.
	 *
	 * @param int $form_id The ID of the form currently being processed.
	 *
	 * @return string
	 */
	public function get_temp_files_path( $form_id ) {
		$form_upload_path = GFFormsModel::get_upload_path( $form_id );
		$target_path      = $form_upload_path . '/tmp/';

		wp_mkdir_p( $target_path );
		GFCommon::recursive_add_index_file( $form_upload_path );

		return $target_path;
	}

	/**
	 * Determines if there are any fields which need files uploading to the temporary folder.
	 *
	 * @param array $form The form currently being processed.
	 * @param array $files An array of files which have already been uploaded.
	 */
	public function maybe_upload_files( $form, $files ) {
		if ( empty( $_FILES ) ) {
			return;
		}

		$this->log_debug( __METHOD__ . '(): Checking for fields to process.' );

		$target_path     = $this->get_temp_files_path( $form['id'] );
		$editable_fields = $this->get_editable_fields();

		foreach ( $form['fields'] as $field ) {
			if ( ! in_array( $field->id, $editable_fields )
			     || ! in_array( $field->get_input_type(), array( 'fileupload', 'post_image' ) )
			     || $field->multipleFiles
			     || $field->failed_validation
			) {
				// Skip fields which are not editable, are the wrong type, or have failed validation.
				continue;
			}

			$files = $this->maybe_upload_temp_file( $field, $files, $target_path );
		}

		GFFormsModel::$uploaded_files[ $form['id'] ] = $files;
	}

	/**
	 * Upload the file to the temporary folder for the current field.
	 *
	 * @param GF_Field $field       The field properties.
	 * @param array    $files       An array of files which have already been uploaded.
	 * @param string   $target_path The path to the tmp folder the file should be moved to.
	 *
	 * @return array
	 */
	public function maybe_upload_temp_file( $field, $files, $target_path ) {
		$input_name = "input_{$field->id}";

		if ( empty( $_FILES[ $input_name ]['name'] ) ) {
			return $files;
		}

		$file_info = GFFormsModel::get_temp_filename( $field->formId, $input_name );
		$this->log_debug( __METHOD__ . "(): Uploading temporary file for field: {$field->label}({$field->id} - {$field->type}). File info => " . print_r( $file_info, true ) );

		if ( $file_info && move_uploaded_file( $_FILES[ $input_name ]['tmp_name'], $target_path . $file_info['temp_filename'] ) ) {
			GFFormsModel::set_permissions( $target_path . $file_info['temp_filename'] );
			$files[ $input_name ] = $file_info['uploaded_filename'];
			$this->log_debug( __METHOD__ . '(): File uploaded successfully.' );
		} else {
			$this->log_debug( __METHOD__ . "(): File could not be uploaded: tmp_name: {$_FILES[ $input_name ]['tmp_name']} - target location: " . $target_path . $file_info['temp_filename'] );
		}

		return $files;
	}

	/**
	 * Validates and performs the assignees status update.
	 *
	 * @param Gravity_Flow_Assignee $assignee The assignee properties.
	 * @param string                $new_status The new status.
	 * @param array                 $form The current form.
	 *
	 * @return string|bool If processed return a message to be displayed to the user.
	 */
	public function process_assignee_status( $assignee, $new_status, $form ) {

		if ( $new_status == 'complete' ) {
			$success = $assignee->process_status( $new_status );
			if ( is_wp_error( $success ) ) {
				return $success;
			}
			$note_message = __( 'Entry updated and marked complete.', 'gravityflow' );
			if ( $this->confirmation_messageEnable ) {
				$feedback = $this->confirmation_messageValue;
				$feedback = $assignee->replace_variables( $feedback );
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
		 * @param string                $feedback   The message to be displayed to the assignee when the detail page is redisplayed.
		 * @param string                $new_status The new status.
		 * @param Gravity_Flow_Assignee $assignee   The assignee properties.
		 * @param array                 $form       The current form.
		 * @param Gravity_Flow_Step     $this       The current step.
		 */
		$feedback = apply_filters( 'gravityflow_feedback_message_user_input', $feedback, $new_status, $assignee, $form, $this );

		$note = sprintf( '%s: %s', $this->get_name(), $note_message );
		$this->add_note( $note . $this->maybe_add_user_note(), true );

		$status = $this->evaluate_status();
		$this->update_step_status( $status );

		return $feedback;
	}

	/**
	 * Determine if this step is valid.
	 *
	 * @param string $new_status The new status for the current step.
	 * @param array  $form       The form currently being processed.
	 *
	 * @return bool
	 */
	public function validate_status_update( $new_status, $form ) {
		$valid = $this->validate_note( $new_status, $form );
		$valid = $this->validate_editable_fields( $valid, $form );

		return $this->get_validation_result( $valid, $form, $new_status );
	}

	/**
	 * Determine if the note is valid.
	 *
	 * @param string $new_status The new status for the current step.
	 * @param string $note       The submitted note.
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
	 * Determine if the editable fields for this step are valid.
	 *
	 * @param bool  $valid The steps current validation state.
	 * @param array $form  The form currently being processed.
	 *
	 * @return bool
	 */
	public function validate_editable_fields( $valid, &$form ) {
		$editable_fields = $this->get_editable_fields();

		$conditional_logic_enabled           = gravity_flow()->fields_have_conditional_logic( $form ) && $this->conditional_logic_editable_fields_enabled;
		$page_load_conditional_logic_enabled = $conditional_logic_enabled && $this->conditional_logic_editable_fields_mode == 'page_load';
		$dynamic_conditional_logic_enabled   = $conditional_logic_enabled && $this->conditional_logic_editable_fields_mode != 'page_load';

		$saved_entry = $this->get_entry();

		if ( ! $conditional_logic_enabled || $page_load_conditional_logic_enabled ) {
			$entry = $saved_entry;
		} else {
			$entry = GFFormsModel::create_lead( $form );
		}

		foreach ( $form['fields'] as $field ) {
			/* @var GF_Field $field */
			if ( in_array( $field->id, $editable_fields ) ) {
				if ( ( $dynamic_conditional_logic_enabled && GFFormsModel::is_field_hidden( $form, $field, array() ) ) ) {
					continue;
				}

				$submission_is_empty = $field->is_value_submission_empty( $form['id'] );

				if ( $field->get_input_type() == 'fileupload' ) {

					if ( $field->isRequired && $submission_is_empty && rgempty( $field->id, $saved_entry ) ) {
						$field->failed_validation  = true;
						$field->validation_message = empty( $field->errorMessage ) ? esc_html__( 'This field is required.', 'gravityflow' ) : $field->errorMessage;
						$valid                     = false;

						continue;
					}

					$field->validate( '', $form );
					if ( $field->failed_validation ) {
						$valid = false;
					}

					continue;
				}

				if ( $page_load_conditional_logic_enabled ) {
					$field_is_hidden = GFFormsModel::is_field_hidden( $form, $field, array(), $entry );
				} elseif ( $dynamic_conditional_logic_enabled ) {
					$field_is_hidden = GFFormsModel::is_field_hidden( $form, $field, array() );
				} else {
					$field_is_hidden = false;
				}

				if ( ! $field_is_hidden && $submission_is_empty && $field->isRequired && rgpost( 'gravityflow_status' ) == 'complete' ) {
					$field->failed_validation  = true;
					$field->validation_message = empty( $field->errorMessage ) ? esc_html__( 'This field is required.', 'gravityflow' ) : $field->errorMessage;
					$valid                     = false;
				} elseif ( ! $field_is_hidden && ! $submission_is_empty ) {
					$value = GFFormsModel::get_field_value( $field );

					if ( ! empty( $field->fields ) &&  rgpost( 'gravityflow_status' ) == 'in_progress' ) {
						// Temporarily set isRequired for all sub-fields to false to allow required fields to be saved when saving progress.
						$this->set_field_property( $field, 'isRequired', false );
						$field->validate( $value, $form );
						$this->restore_field_property( $field, 'isRequired' );
					} else {
						$field->validate( $value, $form );
					}

					$custom_validation_result = gf_apply_filters( array( 'gform_field_validation', $form['id'], $field->id ), array(
						'is_valid' => $field->failed_validation ? false : true,
						'message'  => $field->validation_message,
					), $value, $form, $field );

					$field->failed_validation  = rgar( $custom_validation_result, 'is_valid' ) ? false : true;
					$field->validation_message = rgar( $custom_validation_result, 'message' );

					if ( $field->failed_validation ) {
						$valid = false;
					}
				}
			}
		}

		return $valid;
	}

	/**
	 * Set the value of a field property and optionally stash the current value to be restored later in
	 * $this->restore_field_property().
	 *
	 * Sets the value of sub-fields recursively.
	 *
	 * @since 2.4
	 *
	 * @param GF_Field $field
	 * @param string   $property
	 * @param mixed    $new_value
	 * @param bool     $stash_previous_value
	 */
	public function set_field_property( $field, $property, $new_value, $stash_previous_value = true ) {
		$value = $field->{$property};
		if ( $stash_previous_value ) {
			$field->set_context_property( 'gravityflow_stash_' . $property, $value );
		}
		$field->{$property} = $new_value;
		if ( ! empty( $field->fields ) && is_array( $field->fields ) ) {
			foreach ( $field->fields as $sub_field ) {
				$this->set_field_property( $sub_field, $property, $new_value, $stash_previous_value );
			}
		}
	}

	/**
	 * Set the value of a field property and optionally stash the current value to be restored later in
	 * $this->restore_field_property().
	 *
	 * Sets the value of sub-fields recursively.
	 *
	 * @since 2.4
	 *
	 * @param GF_Field $field
	 * @param string   $property
	 */
	public function restore_field_property( $field, $property ) {
		$value = $field->get_context_property( 'gravityflow_stash_' . $property );

		$field->{$property} = $value;
		if ( ! empty( $field->fields ) && is_array( $field->fields ) ) {
			foreach ( $field->fields as $sub_field ) {
				$this->restore_field_property( $sub_field, $property );
			}
		}
	}

	/**
	 * Allow the validation result to be overridden using the gravityflow_validation_user_input filter.
	 *
	 * @param array  $validation_result The validation result and form currently being processed.
	 * @param string $new_status        The new status for the current step.
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

			$assignees = $this->get_assignees();

			$can_update = false;
			foreach ( $assignees as $assignee ) {
				if ( $assignee->is_current_user() ) {
					$can_update = true;
					break;
				}
			}

			$this->maybe_enable_update_button( $can_update );

			/**
			 * Allows content to be added in the workflow box below the status list.
			 *
			 * @param Gravity_Flow_Step $this The current step.
			 * @param array             $form The current form.
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
		 * @param array             $form         The current form.
		 * @param array             $entry        The current entry.
		 * @param Gravity_Flow_Step $current_step The current step.
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
			$assignee_status_label = $assignee->get_status_label();
			$assignee_status_li    = sprintf( '<li>%s</li>', $assignee_status_label );

			echo $assignee_status_li;
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
			(function (GravityFlowUserInput, $) {
				$(document).ready(function () {
					<?php if ( $this->default_status == 'submit_buttons' ) { ?>
						$('#gravityflow_save_progress_button').prop('disabled', false);
						$('#gravityflow_submit_button').prop('disabled', false);
					<?php } else { ?>
						$('#gravityflow_update_button').prop('disabled', false);
					<?php } ?>
				});
			}(window.GravityFlowUserInput = window.GravityFlowUserInput || {}, jQuery));
		</script>

		<?php
	}

	/**
	 * Output the status inputs and associated labels.
	 */
	public function display_status_inputs() {
		$default_status = $this->default_status ? $this->default_status : 'complete';

		if ( in_array( $default_status, array( 'hidden', 'submit_buttons' ), true ) ) {
			?>
			<input type="hidden" id="gravityflow_status_hidden" name="gravityflow_status" value="complete"/>
			<?php
		} else {

			$in_progress_label = esc_html__( 'In progress', 'gravityflow' );

			/**
			 * Allows the 'in progress' label to be modified on the User Input step.
			 *
			 * @params string $in_progress_label The "In progress" label.
			 * @params Gravity_Flow_Step $this The current step.
			 */
			$in_progress_label = apply_filters( 'gravityflow_in_progress_label_user_input', $in_progress_label, $this );

			$complete_label = esc_html__( 'Complete', 'gravityflow' );

			/**
			 * Allows the 'complete' label to be modified on the User Input step.
			 *
			 * @params string $complete_label The "Complete" label.
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
			if ( $this->default_status == 'submit_buttons' ) {

				$form_id          = absint( $form['id'] );

				$save_progress_button_text   = esc_html__( 'Save', 'gravityflow' );

				/**
				 * Allows the save_progress button label to be modified on the User Input step when the Save Progress option is set to 'Submit Buttons'.
				 *
				 * @since  1.9.2
				 *
				 * @params string $save_progress_label. The "Save" label.
				 * @params array  $form The form for the current entry.
				 * @params Gravity_Flow_Step $this The current step.
				 */
				$save_progress_button_text   = apply_filters( 'gravityflow_save_progress_button_text_user_input', $save_progress_button_text, $form, $this );
				$save_progress_button_click  = "jQuery('#action').val('update'); jQuery('#gravityflow_status_hidden').val('in_progress'); jQuery('#gform_{$form_id}').submit(); return false;";
				$save_progress_button        = '<input id="gravityflow_save_progress_button" disabled="disabled" class="button button-large button-secondary" type="submit" tabindex="4" value="' . $save_progress_button_text . '" name="in_progress" onclick="' . $save_progress_button_click . '" />';

				/**
				 * Allows the save_progress button to be modified on the User Input step when the Save Progress option is set to 'Submit Buttons'.
				 *
				 * @since  1.9.2
				 *
				 * @params string $save_progress_button The HTML for the "Save" button.
				 */
				echo apply_filters( 'gravityflow_save_progress_button_user_input', $save_progress_button );

				$submit_button_text   = esc_html__( 'Submit', 'gravityflow' );

				/**
				 * Allows the submit button label to be modified on the User Input step when the Save Progress option is set to 'Submit Buttons'.
				 *
				 * @since  1.9.2
				 *
				 * @params string $submit_label The "Submit" label.
				 * @params array  $form The form for the current entry.
				 * @params Gravity_Flow_Step $this The current step.
				 */
				$submit_button_text   = apply_filters( 'gravityflow_submit_button_text_user_input', $submit_button_text, $form, $this );
				$submit_button_click  = "jQuery('#action').val('update'); jQuery('#gravityflow_status_hidden').val('complete'); jQuery('#gform_{$form_id}').submit(); return false;";
				$submit_button        = '<input id="gravityflow_submit_button" disabled="disabled" class="button button-large button-primary" type="submit" tabindex="5" value="' . $submit_button_text . '" name="save" onclick="' . $submit_button_click . '"/>';

				/**
				* Allows the submit button to be modified on the User Input step when the Save Progress option is set to 'Submit Buttons'
				*
				* @since 1.9.2
				*
				* @params string $submit_button The HTML for the "Submit" button.
				*/
				echo apply_filters( 'gravityflow_submit_button_user_input', $submit_button );
			} else {

				$button_text = $this->default_status == 'hidden' ? esc_html__( 'Submit', 'gravityflow' ) : esc_html__( 'Update', 'gravityflow' );

				/**
				 * Allows the update button label to be modified on the User Input step when the Save Progress option is set to hidden or either radio button setting.
				 *
				 * @since  unknown
				 *
				 * @params string $update_label The "Update" label.
				 * @params array  $form The form for the current entry.
				 * @params Gravity_Flow_Step $this The current step.
				 */
				$button_text = apply_filters( 'gravityflow_update_button_text_user_input', $button_text, $form, $this );

				$form_id          = absint( $form['id'] );
				$button_click     = "jQuery('#action').val('update'); jQuery('#gform_{$form_id}').submit(); return false;";
				$update_button    = '<input id="gravityflow_update_button" disabled="disabled" class="button button-large button-primary" type="submit" tabindex="4" value="' . $button_text . '" name="save" onclick="' . $button_click . '"/>';

				/**
				* Allows the update button to be modified on the User Input step when the Save Progress option is set to hidden or either radio button setting.
				*
				* @since unknown
				*
				* @params string $update_button The HTML for the "Update" button.
				*/
				echo apply_filters( 'gravityflow_update_button_user_input', $update_button );

			}
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

	/**
	 * Displays content inside the Workflow metabox on the Gravity Forms Entry Detail page.
	 *
	 * @param array $form The current form.
	 */
	public function entry_detail_status_box( $form ) {
		$status = $this->evaluate_status();
		?>
		<h4 style="padding:10px;"><?php echo $this->get_name() . ': ' . $status ?></h4>

		<div style="padding:10px;">
			<ul>
				<?php

				$assignees = $this->get_assignees();

				foreach ( $assignees as $assignee ) {
					$assignee_status_label = $assignee->get_status_label();
					$assignee_status_li    = sprintf( '<li>%s</li>', $assignee_status_label );

					echo $assignee_status_li;
				}

				?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Updates the entry.
	 *
	 * @param array $form            The current form.
	 * @param array $entry           The current entry.
	 * @param array $editable_fields The editable fields for the current user.
	 */
	public function save_entry( $form, &$entry, $editable_fields ) {
	    global $wpdb;

		$this->log_debug( __METHOD__ . '(): Saving entry.' );

		$is_new_lead = $entry == null;

		// Bailing if null.
		if ( $is_new_lead ) {
			return;
		}

		$entry_meta_table = GFFormsModel::get_entry_meta_table_name();

		$sql = gravity_flow()->is_gravityforms_supported( '2.4-rc-1' ) ? "SELECT id, meta_key, item_index FROM $entry_meta_table WHERE entry_id=%d" : "SELECT id, meta_key FROM $entry_meta_table WHERE entry_id=%d";

		$current_fields = $wpdb->get_results( $wpdb->prepare( $sql, $entry['id'] ) );

		$total_fields       = array();
		$calculation_fields = array();

		GFFormsModel::begin_batch_field_operations();

		/**
		 * The field properties.
		 *
		 * @var GF_Field $field
		 */
		foreach ( $form['fields'] as &$field ) {

			// Ignore fields that are marked as display only.
			if ( $field->displayOnly && $field->type != 'password' ) {
				continue;
			}

			// Process total field after all fields have been saved.
			if ( $field->type == 'total' ) {
				$total_fields[] = $field;
				continue;
			}

			// Process calculation fields after all fields have been saved (moved after the is hidden check).
			if ( $field->has_calculation() ) {
				$calculation_fields[] = $field;
				continue;
			}

			if ( ! in_array( $field->id, $editable_fields ) ) {
				continue;
			}

			if ( ! $this->conditional_logic_editable_fields_enabled ) {
				$field->conditionalLogic = null;
			}

			if ( in_array( $field->get_input_type(), array( 'fileupload', 'post_image' ) ) ) {
				$this->maybe_save_field_files( $field, $form, $entry );
				continue;
			}

			if ( $field->type == 'post_category' ) {
				$field = GFCommon::add_categories_as_choices( $field, '' );
			}

			$inputs = $field->get_entry_inputs();

			if ( is_array( $inputs ) ) {
				foreach ( $inputs as $input ) {
					$this->save_input( $form, $field, $entry, $current_fields, $input['id'] );
				}
			} else {
				$this->save_input( $form, $field, $entry, $current_fields, $field->id );
			}
		}

		$results = GFFormsModel::commit_batch_field_operations();

		if ( ! empty( $calculation_fields ) ) {
			GFFormsModel::begin_batch_field_operations();
			$this->log_debug( __METHOD__ . '(): Saving calculation fields.' );

			/**
			 * The calculation field properties.
			 *
			 * @var GF_Field $calculation_field
			 */
			foreach ( $calculation_fields as $calculation_field ) {
				// Make sure that the value gets recalculated.
				if ( ! $this->conditional_logic_editable_fields_enabled ) {
					$calculation_field->conditionalLogic = null;
				}

				$inputs = $calculation_field->get_entry_inputs();

				if ( is_array( $inputs ) ) {

					if ( ! in_array( $calculation_field->id, $editable_fields ) ) {
						// Make sure calculated product names and quantities are saved as if they're submitted.
						$value                                             = array( $calculation_field->id . '.1' => $entry[ $calculation_field->id . '.1' ] );
						$_POST[ 'input_' . $calculation_field->id . '_1' ] = $calculation_field->get_field_label( false, $value );
						$quantity                                          = trim( $entry[ $calculation_field->id . '.3' ] );
						if ( $calculation_field->disableQuantity && empty( $quantity ) ) {
							$_POST[ 'input_' . $calculation_field->id . '_3' ] = 1;
						} else {
							$_POST[ 'input_' . $calculation_field->id . '_3' ] = $quantity;
						}
					}
					foreach ( $inputs as $input ) {
						$this->save_input( $form, $calculation_field, $entry, $current_fields, $input['id'] );
					}
				} else {
					$this->save_input( $form, $calculation_field, $entry, $current_fields, $calculation_field->id );
				}

				$results = GFFormsModel::commit_batch_field_operations();
			}
		}

		GFFormsModel::refresh_product_cache( $form, $entry = RGFormsModel::get_lead( $entry['id'] ) );

		// Saving total field as the last field of the form.
		if ( ! empty( $total_fields ) ) {
			GFFormsModel::begin_batch_field_operations();
			$this->log_debug( __METHOD__ . '(): Saving total fields.' );

			/**
			 * The total field properties.
			 *
			 * @var GF_Field $total_field
			 */
			foreach ( $total_fields as $total_field ) {
				$this->save_input( $form, $total_field, $entry, $current_fields, $total_field->id );
			}

			$results = GFFormsModel::commit_batch_field_operations();
		}

		if ( gravity_flow()->is_gravityforms_supported( '2.4-rc-1' ) ) {
			GFFormsModel::hydrate_repeaters( $entry, $form );
		}
	}

	/**
	 * Update the input value in the entry.
	 *
     * @since 2.4 added the $current_fields parameter
	 * @since 1.5.1-dev
	 *
	 * @param array      $form           The form currently being processed.
	 * @param GF_Field   $field          The current fields properties.
	 * @param array      $entry          The entry currently being processed.
	 * @param array      $current_fields The array of current field values in the database.
	 * @param int|string $input_id       The ID of the field or input currently being processed.
	 */
	public function save_input( $form, $field, &$entry, $current_fields, $input_id ) {

		if ( gravity_flow()->is_gravityforms_supported( '2.4-rc-1' ) && isset( $field->fields ) && is_array( $field->fields ) ) {
			foreach( $field->fields as $sub_field ) {
				$inputs = $sub_field->get_entry_inputs();
				if ( is_array( $inputs ) ) {
					foreach ( $inputs as $input ) {
						$this->save_input( $form, $sub_field, $entry, $current_fields, $input['id'] );
					}
				} else {
					$this->save_input( $form, $sub_field, $entry, $current_fields, $sub_field->id );
				}
				foreach ( $current_fields as $current_field ) {
					if ( intval( $current_field->meta_key ) == $sub_field->id && ! isset( $current_field->update ) ) {
						$current_field->delete = true;
						$result = GFFormsModel::queue_batch_field_operation( $form, $entry, $sub_field, $current_field->id, $current_field->meta_key, '', $current_field->item_index );
						$this->log_debug( __METHOD__ . "(): Deleting: {$field->label}(#{$sub_field->id}{$current_field->item_index} - {$field->type}). Result: " . var_export( $result, 1 ) );
					}
				}
			}
			return;
		}

		$input_name = 'input_' . str_replace( '.', '_', $input_id );

		if ( $field->enableCopyValuesOption && rgpost( 'input_' . $field->id . '_copy_values_activated' ) ) {
			$source_field_id   = $field->copyValuesOptionField;
			$source_input_name = str_replace( 'input_' . $field->id, 'input_' . $source_field_id, $input_name );
			$value             = rgpost( $source_input_name );
		} else {
			$value = rgpost( $input_name );
		}
		if ( gravity_flow()->is_gravityforms_supported( '2.4-rc-1' ) ) {
			GFFormsModel::queue_save_input_value( $value, $form, $field, $entry, $current_fields, $input_id );
		} else {
			$existing_value = rgar( $entry, $input_id );
			$value          = GFFormsModel::maybe_trim_input( $value, $form['id'], $field );
			$value          = GFFormsModel::prepare_value( $form, $field, $value, $input_name, $entry['id'], $entry );
			if ( $existing_value != $value ) {
				$entry_meta_id = GFFormsModel::get_lead_detail_id( $current_fields, $input_id );
				$result = GFFormsModel::queue_batch_field_operation( $form, $entry, $field, $entry_meta_id, $input_id, $value );
				$this->log_debug( __METHOD__ . "(): Updating: {$field->label}(#{$field->id} - {$field->type}). Result: " . var_export( $result, 1 ) );
			}
		}

		if ( GFCommon::is_post_field( $field ) && ! in_array( $field->id, $this->_update_post_fields['fields'] ) ) {
			$this->_update_post_fields['fields'][] = $field->id;
		}
	}

	/**
	 * If any new files where uploaded save them to the entry.
	 *
	 * @param GF_Field $field The current fields properties.
	 * @param array    $form  The form currently being processed.
	 * @param array    $entry The entry currently being processed.
	 */
	public function maybe_save_field_files( $field, $form, $entry ) {
		$input_name = 'input_' . $field->id;
		if ( $field->multipleFiles && ! isset( GFFormsModel::$uploaded_files[ $form['id'] ][ $input_name ] ) ) {
			// No new files uploaded, abort.
			return;
		}

		$existing_value = rgar( $entry, $field->id );
		$this->maybe_pre_process_post_image_field( $field, $existing_value, $input_name );
		$value = $field->get_value_save_entry( $existing_value, $form, $input_name, $entry['id'], $entry );

		if ( ! empty( $value ) && $existing_value != $value ) {
			$result = GFAPI::update_entry_field( $entry['id'], $field->id, $value );
			$this->log_debug( __METHOD__ . "(): Saving: {$field->label}(#{$field->id} - {$field->type}). Result: " . var_export( $result, 1 ) );

			if ( GFCommon::is_post_field( $field ) && ! in_array( $field->id, $this->_update_post_fields['images'] ) ) {
				$this->_update_post_fields['images'][] = $field->id;

				$post_images = gform_get_meta( $entry['id'], '_post_images' );
				if ( $post_images && isset( $post_images[ $field->id ] ) ) {
					wp_delete_attachment( $post_images[ $field->id ] );
					unset( $post_images[ $field->id ] );
					gform_update_meta( $entry['id'], '_post_images', $post_images, $form['id'] );
				}
			}
		}
	}

	/**
	 * Add the existing post image URL to the $_gf_uploaded_files global so the image title, caption, and description can be updated.
	 *
	 * @since 2.1.2-dev
	 *
	 * @param GF_Field $field          The current field object.
	 * @param string   $existing_value The current fields existing entry value.
	 * @param string   $input_name     The input name to use when accessing the current fields values in the submission.
	 */
	public function maybe_pre_process_post_image_field( $field, $existing_value, $input_name ) {
		if ( $existing_value && $field->type === 'post_image' && empty( $_FILES[ $input_name ]['name'] ) ) {
			$parts             = explode( '|:|', $existing_value );
			$existing_filename = basename( rgar( $parts, 0 ) );
			$new_filename      = rgar( GFFormsModel::$uploaded_files[ $field->formId ], $input_name );

			if ( ! empty( $new_filename ) && $new_filename === $existing_filename ) {
				global $_gf_uploaded_files;
				$_gf_uploaded_files[ $input_name ] = $parts[0];
			}
		}
	}

	/**
	 * If a post exists for this entry initiate the update.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $form    The form currently being processed.
	 * @param int   $post_id The ID of the post created from the current entry.
	 */
	public function maybe_process_post_fields( $form, $post_id ) {
		$this->log_debug( __METHOD__ . '(): running.' );

		if ( empty( $post_id ) ) {
			$this->log_debug( __METHOD__ . '(): aborting; no post id' );

			return;
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			$this->log_debug( __METHOD__ . '(): aborting; unable to get post.' );

			return;
		}

		$result = $this->process_post_fields( $form, $post );
		$this->log_debug( __METHOD__ . '(): wp_update_post result => ' . print_r( $result, 1 ) );
	}

	/**
	 * Update the post with the field values which have changed.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array   $form The form currently being processed.
	 * @param WP_Post $post The post to be updated.
	 *
	 * @return int|WP_Error
	 */
	public function process_post_fields( $form, $post ) {
		$entry                = $this->get_entry();
		$post_images          = $this->process_post_images( $form, $entry );
		$has_content_template = rgar( $form, 'postContentTemplateEnabled' );

		foreach ( $this->_update_post_fields['fields'] as $field_id ) {

			$field = GFFormsModel::get_field( $form, $field_id );
			$value = GFFormsModel::get_lead_field_value( $entry, $field );

			switch ( $field->type ) {
				case 'post_title' :
					$post_title       = $this->get_post_title( $value, $form, $entry, $post_images );
					$post->post_title = $post_title;
					$post->post_name  = $post_title;
					break;

				case 'post_content' :
					if ( ! $has_content_template ) {
						$post->post_content = GFCommon::encode_shortcodes( $value );
					}
					break;

				case 'post_excerpt' :
					$post->post_excerpt = GFCommon::encode_shortcodes( $value );
					break;

				case 'post_tags' :
					$this->set_post_tags( $value, $post->ID );
					break;

				case 'post_category' :
					$this->set_post_categories( $value, $post->ID );
					break;

				case 'post_custom_field' :
					$this->set_post_meta( $field, $value, $form, $entry, $post_images );
					break;
			}
		}

		if ( $has_content_template ) {
			$post->post_content = GFFormsModel::process_post_template( $form['postContentTemplate'], 'post_content', $post_images, array(), $form, $entry );
		}

		return wp_update_post( $post, true );
	}

	/**
	 * Attach any new images to the post and set the featured image.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $form  The form currently being processed.
	 * @param array $entry The entry currently being processed.
	 *
	 * @return array
	 */
	public function process_post_images( $form, $entry ) {
		$post_id     = $entry['post_id'];
		$post_images = gform_get_meta( $entry['id'], '_post_images' );
		if ( ! $post_images ) {
			$post_images = array();
		}

		foreach ( $this->_update_post_fields['images'] as $field_id ) {
			$value = rgar( $entry, $field_id );
			list( $url, $title, $caption, $description ) = rgexplode( '|:|', $value, 4 );

			if ( empty( $url ) ) {
				continue;
			}

			$image_meta = array(
				'post_excerpt' => $caption,
				'post_content' => $description,
			);

			// Adding title only if it is not empty. It will default to the file name if it is not in the array.
			if ( ! empty( $title ) ) {
				$image_meta['post_title'] = $title;
			}

			$media_id = GFFormsModel::media_handle_upload( $url, $post_id, $image_meta );

			if ( $media_id ) {
				$post_images[ $field_id ] = $media_id;

				// Setting the featured image.
				$field = RGFormsModel::get_field( $form, $field_id );
				if ( $field && $field->postFeaturedImage ) {
					$result = set_post_thumbnail( $post_id, $media_id );
				}
			}

		}

		if ( ! empty( $post_images ) ) {
			gform_update_meta( $entry['id'], '_post_images', $post_images, $form['id'] );
		}

		return $post_images;
	}

	/**
	 * Get the post title.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param string $value       The entry field value.
	 * @param array  $form        The form currently being processed.
	 * @param array  $entry       The entry currently being processed.
	 * @param array  $post_images The images which have been attached to the post.
	 *
	 * @return string
	 */
	public function get_post_title( $value, $form, $entry, $post_images ) {
		if ( rgar( $form, 'postTitleTemplateEnabled' ) ) {
			return GFFormsModel::process_post_template( $form['postTitleTemplate'], 'post_title', $post_images, array(), $form, $entry );
		}

		return GFCommon::encode_shortcodes( $value );
	}

	/**
	 * Set the post tags.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param string|array $value   The entry field value.
	 * @param int          $post_id The ID of the post created from the current entry.
	 */
	public function set_post_tags( $value, $post_id ) {
		$post_tags = array( $value ) ? array_values( $value ) : explode( ',', $value );

		wp_set_post_tags( $post_id, $post_tags, false );
	}

	/**
	 * Set the post categories.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param string|array $value   The entry field value.
	 * @param int          $post_id The ID of the post created from the current entry.
	 */
	public function set_post_categories( $value, $post_id ) {
		$post_categories = array();

		foreach ( explode( ',', $value ) as $cat_string ) {
			$cat_array = explode( ':', $cat_string );
			// The category id is the last item in the array, access it using end() in case the category name includes colons.
			array_push( $post_categories, end( $cat_array ) );
		}

		wp_set_post_categories( $post_id, $post_categories, false );
	}

	/**
	 * Set the post meta.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param GF_Field     $field       The Post Custom Field.
	 * @param string|array $value       The entry field value.
	 * @param array        $form        The form currently being processed.
	 * @param array        $entry       The entry currently being processed.
	 * @param array        $post_images The images which have been attached to the post.
	 */
	public function set_post_meta( $field, $value, $form, $entry, $post_images ) {
		$post_id = $entry['post_id'];

		delete_post_meta( $post_id, $field->postCustomFieldName );

		if ( ! empty( $field->customFieldTemplateEnabled ) ) {
			$value = GFFormsModel::process_post_template( $field->customFieldTemplate, 'post_custom_field', $post_images, array(), $form, $entry );
		}

		switch ( $field->inputType ) {
			case 'list' :
				$value = maybe_unserialize( $value );
				if ( is_array( $value ) ) {
					foreach ( $value as $item ) {
						if ( is_array( $item ) ) {
							$item = implode( '|', $item );
						}

						if ( ! rgblank( $item ) ) {
							add_post_meta( $post_id, $field->postCustomFieldName, $item );
						}
					}
				}
				break;

			case 'multiselect' :
			case 'checkbox' :
				$value = ! is_array( $value ) ? explode( ',', $value ) : $value;
				foreach ( $value as $item ) {
					if ( ! rgblank( $item ) ) {
						add_post_meta( $post_id, $field->postCustomFieldName, $item );
					}
				}
				break;

			case 'date' :
				$value = GFCommon::date_display( $value, $field->dateFormat );
				if ( ! rgblank( $value ) ) {
					add_post_meta( $post_id, $field->postCustomFieldName, $value );
				}
				break;

			default :
				if ( ! rgblank( $value ) ) {
					add_post_meta( $post_id, $field->postCustomFieldName, $value );
				}
				break;
		}
	}

	/**
	 * Add the gform_after_create_post filter.
	 *
	 * @since 1.5.1-dev
	 */
	public function intercept_submission() {
		$form_id = $this->get_form_id();
		add_filter( "gform_after_create_post_{$form_id}", array( $this, 'action_after_create_post' ), 10, 3 );
	}

	/**
	 * Store the media IDs for the processed post images in the entry meta.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param int   $post_id The ID of the post created from the current entry.
	 * @param array $entry   The entry currently being processed.
	 * @param array $form    The form currently being processed.
	 */
	public function action_after_create_post( $post_id, $entry, $form ) {
		$post_images = gform_get_meta( $entry['id'], '_post_images' );

		if ( $post_images ) {
			return;
		}

		$post_images = array();

		foreach ( $form['fields'] as $field ) {
			if ( $field->type !== 'post_image' || rgempty( $field->id, $entry ) ) {
				continue;
			}

			$props = rgexplode( '|:|', $entry[ $field->id ], 5 );

			if ( ! empty( $props[4] ) ) {
				$post_images[ $field->id ] = $props[4];
			}
		}

		if ( ! empty( $post_images ) ) {
			gform_add_meta( $entry['id'], '_post_images', $post_images );
		}
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_User_Input() );
