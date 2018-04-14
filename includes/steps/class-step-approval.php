<?php
/**
 * Gravity Flow Step Approval
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Approval
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Approval
 */
class Gravity_Flow_Step_Approval extends Gravity_Flow_Step {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'approval';

	/**
	 * The resource slug for the REST API.
	 *
	 * @var string
	 */
	protected $_rest_base = 'approvals';

	/**
	 * Returns an array of statuses and their properties.
	 *
	 * @return array
	 */
	public function get_status_config() {
		return array(
			array(
				'status'                    => 'rejected',
				'status_label'              => __( 'Rejected', 'gravityflow' ),
				'destination_setting_label' => esc_html__( 'Next step if Rejected', 'gravityflow' ),
				'default_destination'       => 'complete',
			),
			array(
				'status'                    => 'approved',
				'status_label'              => __( 'Approved', 'gravityflow' ),
				'destination_setting_label' => __( 'Next Step if Approved', 'gravityflow' ),
				'default_destination'       => 'next',
			),
		);
	}

	/**
	 * Returns an array of quick actions to be displayed on the inbox.
	 *
	 * @return array
	 */
	public function get_actions() {
		return array(
			array(
				'key'             => 'approve',
				'icon'            => $this->get_approve_icon(),
				'label'           => __( 'Approve', 'gravityflow' ),
				'show_note_field' => in_array( $this->note_mode, array(
						'required_if_approved',
						'required_if_reverted_or_rejected',
						'required',
					)
				),
			),
			array(
				'key'             => 'reject',
				'icon'            => $this->get_reject_icon(),
				'label'           => __( 'Reject', 'gravityflow' ),
				'show_note_field' => in_array( $this->note_mode, array(
						'required_if_rejected',
						'required_if_reverted_or_rejected',
						'required',
					)
				),
			),
		);
	}

	/**
	 * Process the REST request for an entry.
	 *
	 * @since 1.7.1
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
	 *                                is already an instance, WP_HTTP_Response, otherwise
	 *                                returns a new WP_REST_Response instance.
	 */
	public function rest_callback( $request ) {
		if ( $request->get_method() !== 'POST' ) {
			return new WP_Error( 'invalid_request_method', __( 'Invalid request method' ) );
		}
		$action = $request['action'];
		$new_status = '';
		switch ( $action ) {
			case 'approve' :
				$new_status = 'approved';
				break;
			case 'reject' :
				$new_status = 'rejected';
		}

		if ( empty( $new_status ) ) {
			return new WP_Error( 'invalid_action', __( 'Action not supported.', 'gravityflow' ) );
		}

		$note = $request['gravityflow_note'];

		$valid_note = $this->validate_note_mode( $new_status, $note );

		if ( ! $valid_note ) {
			$response = array( 'status' => 'note_required', 'feedback' => __( 'A note is required.', 'gravityflow' ) );
			$response = rest_ensure_response( $response );
			return $response;
		}

		$assignees = $this->get_assignees();

		foreach ( $assignees as $assignee ) {
			if ( $assignee->is_current_user() ) {
				$feedback = $this->process_assignee_status( $assignee, $new_status, $this->get_form() );
				break;
			}
		}

		if ( empty( $assignee ) ) {
			return new WP_Error( 'not_supported', __( 'Action not supported.', 'gravityflow' ) );
		}

		$response = array( 'status' => 'success', 'feedback' => $feedback );

		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Indicates this step supports expiration.
	 *
	 * @return bool
	 */
	public function supports_expiration() {
		return true;
	}

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Approval', 'gravityflow' );
	}

	/**
	 * Returns the HTML for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return '<i class="fa fa-check" style="color:darkgreen;"></i>';
	}

	/**
	 * Returns an array of settings for this step type.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings_api = $this->get_common_settings_api();

		$settings = array(
			'title'  => esc_html__( 'Approval', 'gravityflow' ),
			'fields' => array(
				$settings_api->get_setting_assignee_type(),
				$settings_api->get_setting_assignees(),
				$settings_api->get_setting_assignee_routing(),
				array(
					'name'          => 'assignee_policy',
					'label'         => __( 'Approval Policy', 'gravityflow' ),
					'tooltip'       => __( 'Define how approvals should be processed. If all assignees must approve then the entry will require unanimous approval before the step can be completed. If the step is assigned to a role only one user in that role needs to approve.', 'gravityflow' ),
					'type'          => 'radio',
					'default_value' => 'all',
					'choices'       => array(
						array(
							'label' => __( 'Only one assignee is required to approve', 'gravityflow' ),
							'value' => 'any',
						),
						array(
							'label' => __( 'All assignees must approve', 'gravityflow' ),
							'value' => 'all',
						),
					),
				),
				$settings_api->get_setting_instructions( esc_html__( 'Instructions: please review the values in the fields below and click on the Approve or Reject button', 'gravityflow' ) ),
				$settings_api->get_setting_display_fields(),
				$settings_api->get_setting_notification_tabs( array(
					array(
						'label'  => __( 'Assignee Email', 'gravityflow' ),
						'id'     => 'tab_assignee_notification',
						'fields' => $settings_api->get_setting_notification( array(
							'default_message' => __( 'A new entry is pending your approval. Please check your Workflow Inbox.', 'gravityflow' ),
						) ),
					),
					array(
						'label'  => __( 'Rejection Email', 'gravityflow' ),
						'id'     => 'tab_rejection_notification',
						'fields' => $settings_api->get_setting_notification( array(
							'name_prefix'      => 'rejection',
							'checkbox_label'   => __( 'Send email when the entry is rejected', 'gravityflow' ),
							'checkbox_tooltip' => __( 'Enable this setting to send an email when the entry is rejected.', 'gravityflow' ),
							'default_message'  => __( 'Entry {entry_id} has been rejected', 'gravityflow' ),
							'send_to_fields'   => true,
							'resend_field'     => false,
						) ),
					),
					array(
						'label'  => __( 'Approval Email', 'gravityflow' ),
						'id'     => 'tab_approval_notification',
						'fields' => $settings_api->get_setting_notification( array(
							'name_prefix'      => 'approval',
							'checkbox_label'   => __( 'Send email when the entry is approved', 'gravityflow' ),
							'checkbox_tooltip' => __( 'Enable this setting to send an email when the entry is approved.', 'gravityflow' ),
							'default_message'  => __( 'Entry {entry_id} has been approved', 'gravityflow' ),
							'send_to_fields'   => true,
							'resend_field'     => false,
						) ),
					),
				) ),
			),
		);

		$user_input_step_choices = array();
		$revert_field            = array();
		$form_id                 = $this->get_form_id();
		$steps                   = gravity_flow()->get_steps( $form_id );
		foreach ( $steps as $step ) {
			if ( $step->get_type() === 'user_input' ) {
				$user_input_step_choices[] = array(
					'label' => $step->get_name(),
					'value' => $step->get_id(),
				);
			}
		}

		if ( ! empty( $user_input_step_choices ) ) {
			$revert_field = array(
				'name'     => 'revert',
				'label'    => esc_html__( 'Revert to User Input step', 'gravityflow' ),
				'type'     => 'checkbox_and_select',
				'tooltip'  => esc_html__( 'The Revert setting enables a third option in addition to Approve and Reject which allows the assignee to send the entry directly to a User Input step without changing the status. Enable this setting to show the Revert button next to the Approve and Reject buttons and specify the User Input step the entry will be sent to.', 'gravityflow' ),
				'checkbox' => array(
					'label' => esc_html__( 'Enable', 'gravityflow' ),
				),
				'select'   => array(
					'choices' => $user_input_step_choices,
				),
			);
		}

		$note_mode_setting = array(
			'name'          => 'note_mode',
			'label'         => esc_html__( 'Workflow Note', 'gravityflow' ),
			'type'          => 'select',
			'tooltip'       => esc_html__( 'The text entered in the Note box will be added to the timeline. Use this setting to select the options for the Note box.', 'gravityflow' ),
			'default_value' => 'not_required',
			'choices'       => array(
				array( 'value' => 'hidden', 'label' => esc_html__( 'Hidden', 'gravityflow' ) ),
				array( 'value' => 'not_required', 'label' => esc_html__( 'Not required', 'gravityflow' ) ),
				array( 'value' => 'required', 'label' => esc_html__( 'Always required', 'gravityflow' ) ),
				array(
					'value' => 'required_if_approved',
					'label' => esc_html__( 'Required if approved', 'gravityflow' ),
				),
				array(
					'value' => 'required_if_rejected',
					'label' => esc_html__( 'Required if rejected', 'gravityflow' ),
				),
			),
		);

		if ( ! empty( $revert_field ) ) {
			$note_mode_setting['choices'][] = array(
				'value' => 'required_if_reverted',
				'label' => esc_html__( 'Required if reverted', 'gravityflow' )
			);
			$note_mode_setting['choices'][] = array(
				'value' => 'required_if_reverted_or_rejected',
				'label' => esc_html__( 'Required if reverted or rejected', 'gravityflow' )
			);
			$settings['fields'][]           = $revert_field;
		}

		$settings['fields'][] = $note_mode_setting;

		$form = gravity_flow()->get_current_form();
		if ( GFCommon::has_post_field( $form['fields'] ) ) {
			$settings['fields'][] = array(
				'name'    => 'post_action_on_rejection',
				'label'   => __( 'Post Action if Rejected:', 'gravityflow' ),
				'type'    => 'select',
				'choices' => array(
					array( 'label' => '' ),
					array( 'label' => __( 'Mark Post as Draft', 'gravityflow' ), 'value' => 'draft' ),
					array( 'label' => __( 'Trash Post', 'gravityflow' ), 'value' => 'trash' ),
					array( 'label' => __( 'Delete Post', 'gravityflow' ), 'value' => 'delete' ),

				),
			);

			$settings['fields'][] = array(
				'name'    => 'post_action_on_approval',
				'label'   => __( 'Post Action if Approved:', 'gravityflow' ),
				'type'    => 'checkbox',
				'choices' => array(
					array( 'label' => __( 'Publish Post', 'gravityflow' ), 'name' => 'publish_post_on_approval' ),

				),
			);
		}

		return $settings;
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
	 * Determines if the current step has been completed.
	 *
	 * @return bool
	 */
	public function is_complete() {
		$status = $this->evaluate_status();

		return ! in_array( $status, array( 'pending', 'queued' ) );
	}

	/**
	 * Determines the current status of the step.
	 *
	 * @return string
	 */
	public function status_evaluation() {
		$approvers   = $this->get_assignees();
		$step_status = 'approved';

		foreach ( $approvers as $approver ) {

			$approver_status = $approver->get_status();

			if ( $approver_status == 'rejected' ) {
				$step_status = 'rejected';
				break;
			}
			if ( $this->assignee_policy == 'any' ) {
				if ( $approver_status == 'approved' ) {
					$step_status = 'approved';
					break;
				} else {
					$step_status = 'pending';
				}
			} else if ( empty( $approver_status ) || $approver_status == 'pending' ) {
				$step_status = 'pending';
			}
		}

		/**
		 * Allows the step status for the approval to be customized
		 *
		 * @since 2.1-dev 
		 *
		 * @param string                     $step_status   The status of the step
		 * @param Gravity_Flow_Assignee[]    $approvers     The array of Gravity_Flow_Assignee objects
		 * @param Gravity_Flow_Step          $step          The current step
		 */
		$step_status = apply_filters( 'gravityflow_step_status_evaluation_approval', $step_status, $approvers, $this );

		return $step_status;
	}

	/**
	 * Decodes and validates the supplied token.
	 *
	 * @param array $token The token properties.
	 *
	 * @return bool
	 */
	public function is_valid_token( $token ) {
		$token_json  = base64_decode( $token );
		$token_array = json_decode( $token_json, true );

		if ( empty( $token_array ) ) {
			return false;
		}

		$timestamp  = $token_array['timestamp'];
		$user_id    = $token_array['user_id'];
		$new_status = $token_array['new_status'];
		$entry_id   = $token_array['entry_id'];
		$sig        = $token_array['sig'];


		$expiration_days = apply_filters( 'gravityflow_approval_token_expiration_days', 1 );

		$i = wp_nonce_tick();

		$is_valid = false;

		for ( $n = 1; $n <= $expiration_days; $n ++ ) {
			$sig_key          = sprintf( '%s|%s|%s|%s|%s|%s', $i, $this->get_id(), $timestamp, $entry_id, $user_id, $new_status );
			$verification_sig = substr( wp_hash( $sig_key ), - 12, 10 );
			if ( hash_equals( $verification_sig, $sig ) ) {
				$is_valid = true;
				break;
			}
			$i --;
		}

		return $is_valid;
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
		$feedback        = false;
		$step_status_key = 'gravityflow_approval_new_status_step_' . $this->get_id();

		if ( isset( $_REQUEST[ $step_status_key ] ) || isset( $_GET['gflow_token'] ) || $token = gravity_flow()->decode_access_token() ) {
			if ( isset( $_POST['_wpnonce'] ) && check_admin_referer( 'gravityflow_approvals_' . $this->get_id() ) ) {
				$new_status = rgpost( $step_status_key );
				$validation = $this->validate_status_update( $new_status, $form );
				if ( is_wp_error( $validation ) ) {
					return $validation;
				}
			} else {

				$gflow_token = rgget( 'gflow_token' );
				$new_status  = rgget( 'new_status' );

				if ( ! $gflow_token ) {
					return false;
				}

				if ( $gflow_token ) {
					$token_json  = base64_decode( $gflow_token );
					$token_array = json_decode( $token_json, true );

					if ( empty( $token_array ) ) {
						return false;
					}

					$new_status = $token_array['new_status'];
					if ( empty( $new_status ) ) {
						return false;
					}
				}

				$valid_token = $this->is_valid_token( $gflow_token );

				if ( ! ( $valid_token ) ) {
					return false;
				}

			}

			$assignees = $this->get_assignees();

			foreach ( $assignees as $assignee ) {
				if ( $assignee->is_current_user() ) {
					$feedback = $this->process_assignee_status( $assignee, $new_status, $form );
					break;
				}
			}

			$entry = $this->refresh_entry();

			do_action( 'gravityflow_post_status_update_approval', $entry, $assignee, $new_status, $form );

			/**
			 * Allows the user feedback to be modified after processing the approval status update.
			 *
			 * @since 2.0.2 Added the current step
			 * @since 1.7.1
			 *
			 * @param string                $feedback   The feedback to send to the browser.
			 * @param array                 $entry      The current entry array.
			 * @param Gravity_Flow_Assignee $assignee   The assignee object.
			 * @param string                $new_status The new status
			 * @param array                 $form       The current form array.
			 * @param Gravity_Flow_Step     $step       The current step
			 */
			$feedback = apply_filters( 'gravityflow_feedback_approval', $feedback, $entry, $assignee, $new_status, $form, $this );

		}

		return $feedback;
	}

	/**
	 * Validates and performs the assignees status update.
	 *
	 * @param Gravity_Flow_Assignee $assignee   The assignee properties.
	 * @param string                $new_status The new status for this step.
	 * @param array                 $form       The current form.
	 *
	 * @return bool|string Return a success feedback message safe for page output or false.
	 */
	public function process_assignee_status( $assignee, $new_status, $form ) {
		if ( ! in_array( $new_status, array( 'pending', 'approved', 'rejected', 'revert' ) ) ) {
			return false;
		}

		if ( $new_status == 'revert' ) {
			return $this->process_revert_status();
		}

		$assignee->process_status( $new_status );

		$this->add_status_update_note( $new_status, $assignee );
		$status = $this->evaluate_status();
		$this->update_step_status( $status );
		$this->refresh_entry();

		return $this->get_status_update_feedback( $new_status );
	}

	/**
	 * If the revert settings are configured end the current step and start the specified step.
	 *
	 * @return bool|string
	 */
	public function process_revert_status() {
		$feedback = false;

		if ( $this->revertEnable ) {
			$step = gravity_flow()->get_step( $this->revertValue, $this->get_entry() );

			if ( $step ) {
				$this->end();

				$note = $this->get_name() . ': ' . esc_html__( 'Reverted to step', 'gravityflow' ) . ' - ' . $step->get_label();
				$this->add_note( $note . $this->maybe_add_user_note(), true );

				$step->start();
				$feedback = esc_html__( 'Reverted to step:', 'gravityflow' ) . ' ' . $step->get_label();
			}
		}

		return $feedback;
	}

	/**
	 * If applicable add a note to the current entry.
	 *
	 * @param string                $new_status The new status for the step.
	 * @param Gravity_Flow_Assignee $assignee   The step assignee.
	 */
	public function add_status_update_note( $new_status, $assignee ) {
		$note = '';

		if ( $new_status == 'approved' ) {
			$note = $this->get_name() . ': ' . __( 'Approved.', 'gravityflow' );
		} elseif ( $new_status == 'rejected' ) {
			$note = $this->get_name() . ': ' . __( 'Rejected.', 'gravityflow' );
		}

		if ( ! empty( $note ) ) {
			$this->add_note( $note . $this->maybe_add_user_note(), true );
		}
	}

	/**
	 * Get the feedback for this status update.
	 *
	 * @param string $new_status The new status for the step.
	 *
	 * @return bool|string
	 */
	public function get_status_update_feedback( $new_status ) {
		switch ( $new_status ) {
			case 'approved':
				return __( 'Entry Approved', 'gravityflow' );
			case 'rejected':
				return __( 'Entry Rejected', 'gravityflow' );
		}

		return false;
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

			case 'required_if_approved' :
				if ( $new_status == 'approved' && empty( $note ) ) {
					return false;
				}
				break;

			case 'required_if_rejected' :
				if ( $new_status == 'rejected' && empty( $note ) ) {
					return false;
				}
				break;

			case 'required_if_reverted' :
				if ( $new_status == 'revert' && empty( $note ) ) {
					return false;
				}
				break;

			case 'required_if_reverted_or_rejected' :
				if ( ( $new_status == 'revert' || $new_status == 'rejected' ) && empty( $note ) ) {
					return false;
				}
		}

		return true;
	}

	/**
	 * Allow the validation result to be overridden using the gravityflow_validation_approval filter.
	 *
	 * @param array  $validation_result The validation result and form currently being processed.
	 * @param string $new_status        The new status for the current step.
	 *
	 * @return array
	 */
	public function maybe_filter_validation_result( $validation_result, $new_status ) {

		return apply_filters( 'gravityflow_validation_approval', $validation_result, $this );

	}

	/**
	 * Displays content inside the Workflow metabox on the workflow detail page.
	 *
	 * @param array $form The Form array which may contain validation details.
	 * @param array $args Additional args which may affect the display.
	 */
	public function workflow_detail_box( $form, $args ) {
		$status               = esc_html__( 'Pending Approval', 'gravityflow' );
		$approve_icon         = '<i class="fa fa-check" style="color:green"></i>';
		$reject_icon          = '<i class="fa fa-times" style="color:red"></i>';
		$approval_step_status = $this->get_status();
		if ( $approval_step_status == 'approved' ) {
			$status = $approve_icon . ' ' . esc_html__( 'Approved', 'gravityflow' );
		} elseif ( $approval_step_status == 'rejected' ) {
			$status = $reject_icon . ' ' . esc_html__( 'Rejected', 'gravityflow' );
		} elseif ( $approval_step_status == 'queued' ) {
			$status = esc_html__( 'Queued', 'gravityflow' );
		}
		$display_step_status = (bool) $args['step_status'];
		if ( $display_step_status ) : ?>
			<h4>
				<?php printf( '%s (%s)', $this->get_name(), $status ); ?>
			</h4>
			<div>
				<?php $this->workflow_detail_status_box_status() ?>
			</div>
		<?php endif; ?>
		<?php $this->workflow_detail_status_box_actions( $form ) ?>

		<?php

	}

	/**
	 * Display the assignee status in the workflow detail status box.
	 */
	public function workflow_detail_status_box_status() {
		?>
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
		<?php
	}

	/**
	 * Displays the note input and step action buttons in the workflow detail status box.
	 *
	 * @param array $form The current form.
	 */
	public function workflow_detail_status_box_actions( $form ) {
		$approve_icon = $this->get_approve_icon();
		$reject_icon  = $this->get_reject_icon();
		$revert_icon  = $this->get_revert_icon();

		$assignees = $this->get_assignees();

		$can_update = false;
		foreach ( $assignees as $assignee ) {
			if ( $assignee->is_current_user() ) {
				$can_update = true;
				break;
			}
		}

		if ( $can_update ) {
			wp_nonce_field( 'gravityflow_approvals_' . $this->get_id() );

			if ( $this->note_mode !== 'hidden' ) { ?>
				<br/>
				<div>
					<label for="gravityflow-note">
						<?php
						$note_label = esc_html__( 'Note', 'gravityflow' );
						/**
						 * Allows the label for the Note field on the approval step to be modified.
						 *
						 * @since 1.8.1
						 *
						 * @param string @note_label the label
						 * @param Gravity_Flow_Step_Approval $this The current Approval Step.
						 */
						$note_label = apply_filters( 'gravityflow_approval_note_label_workflow_detail', $note_label, $this );
						echo $note_label;
						$required_indicator = ( $this->note_mode == 'required' ) ? '*' : '';
						printf( "<span class='gfield_required'>%s</span>", $required_indicator );
						?>
					</label>
				</div>
				<textarea id="gravityflow-note" style="width:100%;" rows="4" class="wide" name="gravityflow_note"><?php
					echo rgar( $form, 'failed_validation' ) ? esc_textarea( rgpost( 'gravityflow_note' ) ) : '';
					?></textarea>
				<?php
				$invalid_note = ( isset( $form['workflow_note'] ) && is_array( $form['workflow_note'] ) && $form['workflow_note']['failed_validation'] );
				if ( $invalid_note ) {
					printf( "<div class='gfield_description validation_message'>%s</div>", $form['workflow_note']['validation_message'] );
				}
			}

			do_action( 'gravityflow_above_approval_buttons', $this, $form );
			?>
			<br/><br/>
			<div class="gravityflow-action-buttons">
				<button name="gravityflow_approval_new_status_step_<?php echo $this->get_id() ?>" value="approved"
				        type="submit"
				        class="button">
					<?php
					$approve_label = esc_html__( 'Approve', 'gravityflow' );

					/**
					 * Allows the 'Approve' label to be modified on the Approval step.
					 *
					 * @params string $approve_label The label to be modified.
					 * @params Gravity_Flow_Step_Approval $this The current step.
					 */
					$approve_label = apply_filters( 'gravityflow_approve_label_workflow_detail', $approve_label, $this );

					echo $approve_icon . ' ' . $approve_label; ?>
				</button>
				<button name="gravityflow_approval_new_status_step_<?php echo $this->get_id() ?>" value="rejected"
				        type="submit"
				        class="button">
					<?php
					$reject_label = esc_html__( 'Reject', 'gravityflow' );

					/**
					 * Allows the 'Reject' label to be modified on the Approval step.
					 *
					 * @params string $reject_label The label to be modified.
					 * @params Gravity_Flow_Step_Approval $this The current step.
					 */
					$reject_label = apply_filters( 'gravityflow_reject_label_workflow_detail', $reject_label, $this );

					echo $reject_icon . ' ' . $reject_label; ?>
				</button>
				<?php if ( $this->revertEnable ) : ?>
					<button name="gravityflow_approval_new_status_step_<?php echo $this->get_id() ?>" value="revert"
					        type="submit"
					        class="button">
						<?php
						$revert_label = esc_html__( 'Revert', 'gravityflow' );

						/**
						 * Allows the 'Revert' label to be modified on the Approval step.
						 *
						 * @params string $revert_label The label to be modified.
						 * @params Gravity_Flow_Step_Approval $this The current step.
						 */
						$revert_label = apply_filters( 'gravityflow_revert_label_workflow_detail', $revert_label, $this );

						echo $revert_icon . ' ' . $revert_label; ?>
					</button>
					<?php
				endif;
				?>
			</div>
			<?php
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
	 * Triggers sending of the approval notification.
	 */
	public function send_approval_notification() {
		$this->maybe_send_notification( 'approval' );
	}

	/**
	 * Triggers sending of the rejection notification.
	 */
	public function send_rejection_notification() {
		$this->maybe_send_notification( 'rejection' );
	}

	/**
	 * Provides a way for a step to process a token action before anything else. If feedback is returned it is displayed and nothing else with be rendered.
	 *
	 * @param array $action The action properties.
	 * @param array $token  The assignee token properties.
	 * @param array $form   The current form.
	 * @param array $entry  The current entry.
	 *
	 * @return bool|string|WP_Error
	 */
	public function maybe_process_token_action( $action, $token, $form, $entry ) {
		$feedback = parent::maybe_process_token_action( $action, $token, $form, $entry );

		if ( $feedback ) {
			return $feedback;
		}

		if ( ! in_array( $action, array( 'approve', 'reject' ) ) ) {
			return false;
		}

		$entry_id = rgars( $token, 'scopes/entry_id' );
		if ( empty( $entry_id ) || $entry_id != $entry['id'] ) {
			return new WP_Error( 'incorrect_entry_id', esc_html__( 'Error: incorrect entry.', 'gravityflow' ) );
		}

		$step_id = rgars( $token, 'scopes/step_id' );
		if ( empty( $step_id ) || $step_id != $this->get_id() ) {
			return new WP_Error( 'step_already_processed', esc_html__( 'Error: step already processed.', 'gravityflow' ) );
		}

		$assignee_key = sanitize_text_field( $token['sub'] );
		$assignee     = $this->get_assignee( $assignee_key );
		$new_status   = false;
		switch ( $token['scopes']['action'] ) {
			case 'approve' :
				$new_status = 'approved';
				break;
			case 'reject' :
				$new_status = 'rejected';
				break;
		}
		$feedback = $this->process_assignee_status( $assignee, $new_status, $form );

		/**
		 * Allows the user feedback to be modified after processing the token action.
		 *
		 * @since 2.0.2 Added the current step
		 * @since 1.7.1
		 *
		 * @param string                $feedback   The feedback to send to the browser.
		 * @param array                 $entry      The current entry array.
		 * @param Gravity_Flow_Assignee $assignee   The assignee object.
		 * @param string                $new_status The new status
		 * @param array                 $form       The current form array.
		 * @param Gravity_Flow_Step     $step       The current step
		 */
		$feedback = apply_filters( 'gravityflow_feedback_approval_token', $feedback, $entry, $assignee, $new_status, $form, $this );

		return $feedback;
	}

	/**
	 * Triggers actions to be performed when this step ends.
	 */
	public function end() {
		$status = $this->evaluate_status();
		$entry  = $this->get_entry();
		if ( $status == 'approved' ) {
			$this->send_approval_notification();
			$this->maybe_perform_post_action( $entry, $this->publish_post_on_approval ? 'publish' : '' );
		} elseif ( $status == 'rejected' ) {
			$this->send_rejection_notification();
			$this->maybe_perform_post_action( $entry, $this->post_action_on_rejection );
		}
		if ( $status == 'approved' || $status == 'rejected' ) {
			GFAPI::send_notifications( $this->get_form(), $entry, 'workflow_approval' );
		}
		parent::end();
	}

	/**
	 * If a post exists for the entry perform the configured approval or rejection action.
	 *
	 * @param array  $entry  The current entry.
	 * @param string $action The action to perform.
	 */
	public function maybe_perform_post_action( $entry, $action ) {
		$post_id = rgar( $entry, 'post_id' );
		if ( $post_id && $action ) {
			$post = get_post( $post_id );
			if ( $post instanceof WP_Post ) {
				$result = '';
				switch ( $action ) {
					case 'publish' :
					case 'draft' :
						$post->post_status = $action;
						$result            = wp_update_post( $post );
						break;

					case 'trash' :
						$result = wp_delete_post( $post_id );
						break;

					case 'delete' :
						$result = wp_delete_post( $post_id, true );
						break;
				}

				gravity_flow()->log_debug( __METHOD__ . "() - Post: {$post_id}. Action: {$action}. Result: " . var_export( (bool) $result, 1 ) );
			}
		}
	}

	/**
	 * Returns the HTML for the approve icon.
	 *
	 * @return string
	 */
	public function get_approve_icon() {
		$approve_icon = '<i class="fa fa-check" style="color:green"></i>';
		return $approve_icon;
	}

	/**
	 * Returns the HTML for the reject icon.
	 *
	 * @return string
	 */
	public function get_reject_icon() {
		$reject_icon  = '<i class="fa fa-times" style="color:red"></i>';
		return $reject_icon;
	}

	/**
	 * Returns the HTML for the revert icon.
	 *
	 * @return string
	 */
	public function get_revert_icon() {
		$revert_icon  = '<i class="fa fa-undo" style="color:blue"></i>';
		return $revert_icon;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Approval() );
