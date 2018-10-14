<?php
/**
 * Gravity Flow API
 *
 * @package     GravityFlow
 * @subpackage  Classes/API
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public Licenses
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * The future-proof way to interact with the high level functions in Gravity Flow.
 *
 * Class Gravity_Flow_API
 *
 * @since 1.0
 */
class Gravity_Flow_API {

	/**
	 * The ID of the Form to be used throughout the Gravity Flow API.
	 *
	 * @var int
	 */
	public $form_id = null;

	/**
	 * The constructor for the API. Requires a Form ID.
	 *
	 * @param int $form_id The current form ID.
	 */
	public function __construct( $form_id ) {
		$this->form_id = $form_id;
	}

	/**
	 * Adds a Workflow step to the form with the given settings. The following settings are required:
	 * - step_name (string)
	 * - step_type (string)
	 * - description (string)
	 *
	 * @param array $step_settings The step settings (aka feed meta).
	 *
	 * @return mixed
	 */
	public function add_step( $step_settings ) {
		return GFAPI::add_feed( $this->form_id, $step_settings, 'gravityflow' );
	}

	/**
	 * Returns the step with the given step ID. Optionally pass an Entry object to perform entry-specific functions.
	 *
	 * @param int        $step_id The current step ID.
	 * @param null|array $entry   The current entry.
	 *
	 * @return Gravity_Flow_Step|bool Returns the Step. False if not found.
	 */
	public function get_step( $step_id, $entry = null ) {
		return gravity_flow()->get_step( $step_id, $entry );
	}

	/**
	 * Returns all the steps for current form.
	 *
	 * @return Gravity_Flow_Step[]
	 */
	public function get_steps() {
		return gravity_flow()->get_steps( $this->form_id );
	}

	/**
	 * Returns the current step for the given entry.
	 *
	 * @param array $entry The current entry.
	 *
	 * @return Gravity_Flow_Step|bool
	 */
	public function get_current_step( $entry ) {
		$form = GFAPI::get_form( $this->form_id );

		return gravity_flow()->get_current_step( $form, $entry );
	}

	/**
	 * Processes the workflow for the given Entry ID. Handles the step orchestration - moving the workflow through the steps and ending the workflow.
	 * Not generally required unless there's been a change to the entry outside the usual workflow orchestration.
	 *
	 * @param int $entry_id The ID of the current entry.
	 */
	public function process_workflow( $entry_id ) {
		$form = GFAPI::get_form( $this->form_id );
		gravity_flow()->process_workflow( $form, $entry_id );
	}

	/**
	 * Cancels the workflow for the given Entry ID. Removes the assignees, adds a note in the entry's timeline and logs the event.
	 *
	 * @param array $entry The current entry.
	 *
	 * @return bool True for success. False if not currently in a workflow.
	 */
	public function cancel_workflow( $entry ) {
		$entry_id = absint( $entry['id'] );
		$form     = GFAPI::get_form( $this->form_id );
		$step     = $this->get_current_step( $entry );
		if ( ! $step ) {
			return false;
		}

		/**
		 * Fires before a workflow is cancelled.
		 *
		 * @param array             $entry The current entry.
		 * @param array             $form  The current form.
		 * @param Gravity_Flow_Step $step  The current step object.
		 */
		do_action( 'gravityflow_pre_cancel_workflow', $entry, $form, $step );

		$step->purge_assignees();

		gform_update_meta( $entry_id, 'workflow_final_status', 'cancelled' );
		gform_delete_meta( $entry_id, 'workflow_step' );
		$feedback = esc_html__( 'Workflow cancelled.', 'gravityflow' );
		gravity_flow()->add_timeline_note( $entry_id, $feedback );
		gravity_flow()->log_event( 'workflow', 'cancelled', $form['id'], $entry_id );
		GFAPI::send_notifications( $form, $entry, 'workflow_cancelled' );

		return true;
	}

	/**
	 * Restarts the current step for the given entry, adds a note in the entry's timeline and logs the activity.
	 *
	 * @param array $entry The current entry.
	 *
	 * @return bool True for success. False if the entry doesn't have a current step.
	 */
	public function restart_step( $entry ) {
		$step = $this->get_current_step( $entry );
		if ( ! $step ) {
			return false;
		}
		$entry_id = $entry['id'];
		$this->log_activity( 'step', 'restarted', $this->form_id, $entry_id );
		$step->purge_assignees();
		$step->restart_action();
		$step->start();
		$feedback = esc_html__( 'Workflow Step restarted.', 'gravityflow' );
		$this->add_timeline_note( $entry_id, $feedback );

		return true;
	}

	/**
	 * Restarts the workflow for an entry, adds a note in the entry's timeline and logs the activity.
	 *
	 * @param array $entry The current entry.
	 */
	public function restart_workflow( $entry ) {

		$current_step = $this->get_current_step( $entry );
		$entry_id     = absint( $entry['id'] );
		$form         = GFAPI::get_form( $this->form_id );

		/**
		 * Fires just before the workflow restarts for an entry.
		 *
		 * @since 1.4.3
		 *
		 * @param array $entry The current entry.
		 * @param array $form The current form.
		 */
		do_action( 'gravityflow_pre_restart_workflow', $entry, $form );

		if ( $current_step ) {
			$current_step->purge_assignees();
		}

		$steps = $this->get_steps();
		foreach ( $steps as $step ) {
			// Create a step based on the entry and use it to reset the status.
			$step_for_entry = $this->get_step( $step->get_id(), $entry );
			$step_for_entry->update_step_status( 'pending' );
			$step_for_entry->restart_action();
		}
		$feedback = esc_html__( 'Workflow restarted.', 'gravityflow' );
		$this->add_timeline_note( $entry_id, $feedback );
		gform_update_meta( $entry_id, 'workflow_final_status', 'pending' );
		gform_update_meta( $entry_id, 'workflow_step', false );
		$this->log_activity( 'workflow', 'restarted', $form['id'], $entry_id );
		$this->process_workflow( $entry_id );
	}

	/**
	 * Returns the workflow status for the current entry.
	 *
	 * @param array $entry The current entry.
	 *
	 * @return string|bool The status.
	 */
	public function get_status( $entry ) {
		$current_step = $this->get_current_step( $entry );

		if ( false === $current_step ) {
			$status = gform_get_meta( $entry['id'], 'workflow_final_status' );
		} else {
			$status = $current_step->evaluate_status();
		}

		return $status;
	}

	/**
	 * Sends an entry to the specified step.
	 *
	 * @param array $entry   The current entry.
	 * @param int   $step_id The ID of the step the entry is to be sent to.
	 */
	public function send_to_step( $entry, $step_id ) {
		$current_step = $this->get_current_step( $entry );
		if ( $current_step ) {
			$current_step->purge_assignees();
			$current_step->update_step_status( 'cancelled' );
		}
		$entry_id = $entry['id'];
		$new_step = $this->get_step( $step_id, $entry );
		$feedback = sprintf( esc_html__( 'Sent to step: %s', 'gravityflow' ), $new_step->get_name() );
		$this->add_timeline_note( $entry_id, $feedback );
		$this->log_activity( 'workflow', 'sent_to_step', $this->form_id, $entry_id, $step_id );
		gform_update_meta( $entry_id, 'workflow_final_status', 'pending' );
		$new_step->start();
		$this->process_workflow( $entry_id );
	}

	/**
	 * Add a note to the timeline of the specified entry.
	 *
	 * @param int    $entry_id The ID of the current entry.
	 * @param string $note     The note to be added to the timeline.
	 */
	public function add_timeline_note( $entry_id, $note ) {
		gravity_flow()->add_timeline_note( $entry_id, $note );
	}

	/**
	 * Registers activity event in the activity log. The activity log is used to generate reports.
	 *
	 * @param string $log_type      The object of the event: 'workflow', 'step', 'assignee'.
	 * @param string $event         The event which occurred: 'started', 'ended', 'status'.
	 * @param int    $form_id       The form ID.
	 * @param int    $entry_id      The Entry ID.
	 * @param string $log_value     The value to log.
	 * @param int    $step_id       The Step ID.
	 * @param int    $duration      The duration in seconds - if applicable.
	 * @param int    $assignee_id   The assignee ID - if applicable.
	 * @param string $assignee_type The Assignee type - if applicable.
	 * @param string $display_name  The display name of the User.
	 */
	public function log_activity( $log_type, $event, $form_id = 0, $entry_id = 0, $log_value = '', $step_id = 0, $duration = 0, $assignee_id = 0, $assignee_type = '', $display_name = '' ) {
		gravity_flow()->log_event( $log_type, $event, $form_id, $entry_id, $log_value, $step_id, $duration, $assignee_id, $assignee_type, $display_name );
	}

	/**
	 * Returns the timeline for the specified entry with simple formatting.
	 *
	 * @param array $entry The current entry.
	 *
	 * @return string
	 */
	public function get_timeline( $entry ) {
		return gravity_flow()->get_timeline( $entry );
	}

	/**
	 * Returns an array of entries to be displayed in the inbox.
	 *
	 * @since 2.3.2
	 *
	 * @param array $args        The inbox configuration arguments.
	 * @param int   $total_count The total number of entries for the current assignee.
	 *
	 * @return array
	 */
	public static function get_inbox_entries( $args = array(), &$total_count = 0 ) {
		$entries = array();

		timer_start();

		$search_criteria = self::get_inbox_search_criteria( $args );

		if ( ! empty( $search_criteria ) ) {
			$form_ids = self::get_inbox_form_ids( $args, $search_criteria );

			if ( ! empty( $form_ids ) ) {
				$entries = GFAPI::get_entries( $form_ids, $search_criteria, self::get_inbox_sorting( $args ), self::get_inbox_paging( $args ), $total_count );
			}
		}

		gravity_flow()->log_debug( __METHOD__ . '(): duration of get_entries: ' . timer_stop() );
		gravity_flow()->log_debug( __METHOD__ . "(): {$total_count} pending tasks." );

		return $entries;
	}

	/**
	 * Returns the total number of entries currently assigned to the logged in user or access token user.
	 *
	 * @since 2.3.2
	 *
	 * @param array $args The inbox configuration arguments.
	 *
	 * @return int
	 */
	public static function get_inbox_entries_count( $args = array() ) {
		$count           = 0;
		$search_criteria = self::get_inbox_search_criteria( $args );

		if ( ! empty( $search_criteria ) ) {
			$form_ids = self::get_inbox_form_ids( $args, $search_criteria );

			if ( ! empty( $form_ids ) ) {
				$count = GFAPI::count_entries( $form_ids, $search_criteria );
			}
		}

		return $count;
	}

	/**
	 * Returns the inbox search criteria.
	 *
	 * @since 2.3.2
	 *
	 * @param array $args The inbox configuration arguments.
	 *
	 * @return array
	 */
	public static function get_inbox_search_criteria( $args = array() ) {
		$search_criteria = array();
		$filter_key      = self::get_inbox_filter_key( $args );

		if ( empty( $filter_key ) ) {
			return $search_criteria;
		}

		$field_filters   = array();
		$field_filters[] = array(
			'key'   => $filter_key,
			'value' => 'pending',
		);

		$user_roles = gravity_flow()->get_user_roles();
		foreach ( $user_roles as $user_role ) {
			$field_filters[] = array(
				'key'   => 'workflow_role_' . $user_role,
				'value' => 'pending',
			);
		}

		$field_filters['mode'] = 'any';

		$search_criteria['field_filters'] = $field_filters;
		$search_criteria['status']        = 'active';

		/**
		 * Allows the search criteria to be modified before entries are searched for the inbox.
		 *
		 * @since 2.1
		 *
		 * @param array $sorting The search criteria.
		 */
		$search_criteria = apply_filters( 'gravityflow_inbox_search_criteria', $search_criteria );

		gravity_flow()->log_debug( __METHOD__ . '(): ' . print_r( $search_criteria, 1 ) );

		return $search_criteria;
	}

	/**
	 * Get the filter key for the current user.
	 *
	 * @param array $args The inbox configuration arguments.
	 *
	 * @return string
	 */
	public static function get_inbox_filter_key( $args = array() ) {
		$filter_key = '';

		if ( ! empty( $args['filter_key'] ) ) {
			$filter_key = $args['filter_key'];
		} elseif ( is_user_logged_in() ) {
			$filter_key = 'workflow_user_id_' . get_current_user_id();
		} elseif ( $token = gravity_flow()->decode_access_token() ) {
			$filter_key = gravity_flow()->parse_token_assignee( $token )->get_status_key();
		}

		gravity_flow()->log_debug( __METHOD__ . '(): ' . $filter_key );

		return $filter_key;
	}

	/**
	 * Returns the IDs of the forms to be included in the inbox.
	 *
	 * @since 2.3.2
	 *
	 * @param array $args            The inbox configuration arguments.
	 * @param array $search_criteria The inbox search criteria.
	 *
	 * @return array
	 */
	public static function get_inbox_form_ids( $args, $search_criteria ) {
		$form_ids = ! empty( $args['form_id'] ) ? $args['form_id'] : gravity_flow()->get_workflow_form_ids();

		/**
		 * Allows form id(s) to be adjusted to define which forms' entries are displayed in inbox table.
		 *
		 * Return an array of form ids for use with GFAPI.
		 *
		 * @since 2.2.2-dev
		 *
		 * @param array $form_ids        The form ids
		 * @param array $search_criteria The search criteria
		 */
		$form_ids = apply_filters( 'gravityflow_form_ids_inbox', $form_ids, $search_criteria );

		gravity_flow()->log_debug( __METHOD__ . '(): ' . print_r( $form_ids, 1 ) );

		return $form_ids;
	}

	/**
	 * Returns the inbox paging criteria.
	 *
	 * @since 2.3.2
	 *
	 * @param array $args The inbox configuration arguments.
	 *
	 * @return array
	 */
	public static function get_inbox_paging( $args = array() ) {
		$paging = array(
			'page_size' => 150,
		);

		if ( ! empty( $args['paging']['page_size'] ) ) {
			$paging['page_size'] = (int) $args['paging']['page_size'];
		}

		if ( ! empty( $args['paging']['offset'] ) ) {
			$paging['offset'] = (int) $args['paging']['offset'];
		}

		/**
		 * Allows the paging criteria to be modified before entries are searched for the inbox.
		 *
		 * @since 2.0.2
		 *
		 * @param array $paging The paging criteria.
		 */
		return apply_filters( 'gravityflow_inbox_paging', $paging );
	}

	/**
	 * Returns the inbox sorting criteria.
	 *
	 * @since 2.3.2
	 *
	 * @param array $args The inbox configuration arguments.
	 *
	 * @return array
	 */
	public static function get_inbox_sorting( $args = array() ) {
		$sorting = array();

		if ( ! empty( $args['sorting']['key'] ) ) {
			$sorting['key'] = $args['sorting']['key'];
		}

		if ( ! empty( $args['sorting']['direction'] ) ) {
			$sorting['direction'] = $args['sorting']['direction'];
		}

		if ( isset( $args['sorting']['is_numeric'] ) ) {
			$sorting['is_numeric'] = (bool) $args['sorting']['is_numeric'];
		}

		/**
		 * Allows the sorting criteria to be modified before entries are searched for the inbox.
		 *
		 * @param array $sorting The sorting criteria.
		 */
		return apply_filters( 'gravityflow_inbox_sorting', $sorting );
	}

}
