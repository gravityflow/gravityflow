<?php

/**
 * Gravity Flow Assignee
 *
 * @package     GravityFlow
 * @subpackage  Classes/Assignee
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Assignee
 */
class Gravity_Flow_Assignee {

	/**
	 * The unique name of this assignee.
	 *
	 * @since 2.1
	 *
	 * @var string
	 */
	public $name = 'generic';

	/**
	 * The ID of this assignee.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $id;

	/* @var string The Type of this assignee */

	/**
	 * The Type of this assignee.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The Assignee key.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * The editable fields for this assignee.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	protected $editable_fields = array();

	/**
	 * The WordPress user account for this assignee
	 *
	 * @since 1.7.1
	 *
	 * @var WP_User
	 */
	protected $user = null;

	/**
	 * The step.
	 *
	 * @since 1.0
	 *
	 * @var Gravity_Flow_Step|bool
	 */
	protected $step;

	/**
	 * Gravity_Flow_Assignee constructor.
	 *
	 * @since 1.0
	 *
	 * @param string|array           $args An assignee key or array.
	 * @param bool|Gravity_Flow_Step $step The current step or false.
	 */
	public function __construct( $args = array(), $step = false ) {
		if ( empty( $args ) ) {
			return;
		}
		$this->step = $step;
		if ( is_string( $args ) ) {
			$parts = explode( '|', $args );
			$type  = $parts[0];
			$id    = $parts[1];
		} elseif ( is_array( $args ) ) {

			$id   = $args['id'];
			$type = $args['type'];
			if ( isset( $args['editable_fields'] ) ) {
				$this->editable_fields = $args['editable_fields'];
			}
			if ( isset( $args['user'] ) && $args['user'] instanceof WP_User ) {
				$this->user = $args['user'];
			}
		} else {
			return;
		}


		switch ( $type ) {
			case 'assignee_field':
				$entry        = $this->step->get_entry();
				$assignee_key = rgar( $entry, $id );
				list( $this->type, $this->id ) = rgexplode( '|', $assignee_key, 2 );
				break;
			case 'assignee_user_field':
				$entry      = $this->step->get_entry();
				$this->id   = absint( rgar( $entry, $id ) );
				$this->type = 'user_id';
				break;
			case 'assignee_role_field':
				$entry      = $this->step->get_entry();
				$this->id   = sanitize_text_field( rgar( $entry, $id ) );
				$this->type = 'role';
				break;
			case 'email_field':
				$entry      = $this->step->get_entry();
				$this->id   = sanitize_email( rgar( $entry, $id ) );
				$this->type = 'email';
				break;
			case 'entry':
				$entry      = $this->step->get_entry();
				$this->id   = rgar( $entry, $id );
				$this->type = 'user_id';
				break;
			default:
				$this->type = $type;
				$this->id   = $id;
		}

		$this->maybe_set_user();
		$this->key = $this->type . '|' . $this->id;
	}

	/**
	 * If applicable, set the user property for the assignee.
	 *
	 * @since 1.7.1
	 */
	protected function maybe_set_user() {
		if ( ! $this->get_user() ) {
			if ( $this->get_type() === 'user_id' ) {
				$user = get_user_by( 'ID', $this->get_id() );
			} elseif ( $this->get_type() === 'email' ) {
				$user = get_user_by( 'email', $this->get_id() );
			} else {
				$user = false;
			}

			if ( $user ) {
				$this->user = $user;
			}
		}
	}

	/**
	 * Return the assignee ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Return the assignee key.
	 *
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * Return the assignee type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Return the step for this assignee.
	 *
	 * @since 2.3.2
	 *
	 * @return Gravity_Flow_Step|bool
	 */
	public function get_step() {
		return $this->step;
	}

	/**
	 * Return the editable field IDs for this assignee.
	 *
	 * @return array
	 */
	public function get_editable_fields() {
		return $this->editable_fields;
	}

	/**
	 * Returns the user account for this assignee.
	 *
	 * @since 1.7.1
	 *
	 * @return WP_User
	 */
	public function get_user() {
		return $this->user;
	}

	/**
	 * Returns the status.
	 *
	 * @return bool|mixed
	 */
	public function get_status() {

		$entry_id = $this->step->get_entry_id();
		$key      = $this->get_status_key();

		$cache_key = gravity_flow()->is_gravityforms_supported( '2.3-beta-3' ) ? get_current_blog_id() . '_' : '';
		$cache_key .= $entry_id . '_' . $key;

		global $_gform_lead_meta;
		unset( $_gform_lead_meta[ $cache_key ] );

		return gform_get_meta( $entry_id, $key );
	}

	/**
	 * Returns the status key.
	 *
	 * @return string
	 */
	public function get_status_key() {
		$assignee_id = $this->get_id();

		$assignee_type = $this->get_type();

		$key = 'workflow_' . $assignee_type . '_' . $assignee_id;

		return $key;
	}

	/**
	 * Update the status entry meta items for this assignee.
	 *
	 * @param string|bool $new_assignee_status The new status for this assignee or false.
	 */
	public function update_status( $new_assignee_status = false ) {

		$key = $this->get_status_key();

		$assignee_status_timestamp = gform_get_meta( $this->step->get_entry_id(), $key . '_timestamp' );

		$duration = $assignee_status_timestamp ? time() - $assignee_status_timestamp : 0;

		gform_update_meta( $this->step->get_entry_id(), $key, $new_assignee_status );
		gform_update_meta( $this->step->get_entry_id(), $key . '_timestamp', time() );

		$this->log_event( $new_assignee_status, $duration );
	}

	/**
	 * Return the assignee display name.
	 *
	 * @return string
	 */
	public function get_display_name() {
		$user = $this->get_user();
		$name = $user ? $user->display_name : $this->get_id();

		return $name;
	}

	/**
	 * Remove the assignee from the current step by deleting the associated entry meta items.
	 */
	public function remove() {
		$key = $this->get_status_key();

		gform_delete_meta( $this->step->get_entry_id(), $key );
		gform_delete_meta( $this->step->get_entry_id(), $key . '_timestamp' );

		$reminder_timestamp_key = $key . '_reminder_timestamp';

		gform_delete_meta( $this->step->get_entry_id(), $reminder_timestamp_key );
	}

	/**
	 * Returns the status timestamp.
	 *
	 * @return bool|mixed
	 */
	public function get_status_timestamp() {

		$status_key    = $this->get_status_key();
		$timestamp_key = $status_key . '_timestamp';

		return gform_get_meta( $this->step->get_entry_id(), $timestamp_key );
	}

	/**
	 * Returns the status timestamp.
	 *
	 * @return bool|mixed
	 */
	public function get_reminder_timestamp() {

		$status_key    = $this->get_status_key();
		$timestamp_key = $status_key . '_reminder_timestamp';

		return gform_get_meta( $this->step->get_entry_id(), $timestamp_key );
	}

	/**
	 * Sets the timestamp for the reminder.
	 *
	 * @param bool|int $timestamp Unix GMT timestamp or false.
	 */
	public function set_reminder_timestamp( $timestamp = false ) {

		if ( empty( $timestamp ) ) {
			$timestamp = time();
		}

		$status_key    = $this->get_status_key();
		$timestamp_key = $status_key . '_reminder_timestamp';

		gform_update_meta( $this->step->get_entry_id(), $timestamp_key, $timestamp );
	}

	/**
	 * Log an event for the current assignee.
	 *
	 * @param string $status   The assignee status.
	 * @param int    $duration Time interval in seconds, if any.
	 */
	public function log_event( $status, $duration = 0 ) {
		gravity_flow()->log_event( 'assignee', 'status', $this->step->get_form_id(), $this->step->get_entry_id(), $status, $this->step->get_id(), $duration, $this->get_id(), $this->get_type(), $this->get_display_name() );
	}

	/**
	 * Sends a notification to the assignee.
	 *
	 * @uses Gravity_Flow_Step::send_notification() to send, log and deduplicate the notifications.
	 *
	 * @since 2.1
	 *
	 * @param array $notification The notification to be sent.
	 */
	public function send_notification( $notification ) {
		$message       = $notification['message'];
		$assignee_type = $this->get_type();
		$assignee_id   = $this->get_id();

		if ( $assignee_type == 'email' ) {
			$email                = $assignee_id;
			$notification['id']   = 'workflow_step_' . $this->step->get_id() . '_email_' . $email;
			$notification['name'] = $notification['id'];
			$notification['to']   = $email;
			$message              = $this->replace_variables( $message );
			// Call $this->step->replace_variables() for backwards compatibility
			$notification['message'] = $this->step->replace_variables( $message, $this );
			$this->step->send_notification( $notification );

			return;
		}

		if ( $assignee_type == 'role' ) {
			$users = get_users( array( 'role' => $assignee_id ) );
		} else {
			$users = get_users( array( 'include' => array( $assignee_id ) ) );
		}

		$this->step->log_debug( __METHOD__ . sprintf( '() sending notifications to %d users', count( $users ) ) );

		$user_assignee_args = array(
			'type' => $assignee_type,
			'id'   => $assignee_id,
		);
		foreach ( $users as $user ) {
			$user_assignee_args['user'] = $user;
			$user_assignee              = Gravity_Flow_Assignees::create( $user_assignee_args, $this->step );
			$notification['id']         = 'workflow_step_' . $this->step->get_id() . '_user_' . $user->ID;
			$notification['name']       = $notification['id'];
			$notification['to']         = $user->user_email;
			$message                    = $user_assignee->replace_variables( $message );
			// Call $this->step->replace_variables() for backwards compatibility
			$notification['message'] = $this->step->replace_variables( $message, $user_assignee );
			$this->step->send_notification( $notification );
		}
	}

	/**
	 * Checks whether the current user (WP or Token auth) is an assignee.
	 *
	 * @since 2.1
	 *
	 * @return bool
	 */
	public function is_current_user() {

		$current_user_assignee_key = $this->step->get_current_assignee_key();
		$current_user_assignee     = $this->step->get_assignee( $current_user_assignee_key );

		$type = $this->get_type();

		if ( ! $current_user_assignee ) {
			return false;
		}

		$status = $this->get_status();

		if ( $status != 'pending' ) {
			return false;
		}

		if ( in_array( $type, array( 'user_id', 'email', 'role' ) ) && $current_user_assignee->get_id() == $this->get_id() ) {
			return true;
		}

		if ( $type == 'role' ) {
			$user = wp_get_current_user();
			$role = $this->get_id();
			if ( in_array( $role, (array) $user->roles ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Processes the status update for the assignee.
	 *
	 * @since 2.1
	 *
	 * @param string $new_status The status string e.g. complete, approved, rejected.
	 *
	 * @return bool|WP_Error True on success or WP_Error
	 */
	public function process_status( $new_status ) {

		$current_user_status = $this->get_status();

		list( $role, $current_role_status ) = $this->step->get_current_role_status();

		if ( $current_user_status != 'pending' && $current_role_status != 'pending' ) {
			$error = new WP_Error( esc_html__( 'The status could not be changed because this step has already been processed.', 'gravityflow' ) );
			return $error;
		}

		if ( $current_user_status == 'pending' ) {
			$this->update_status( $new_status );
		}

		if ( $current_role_status == 'pending' ) {
			$this->step->update_role_status( $role, $new_status );
		}

		$this->step->refresh_entry();

		$success = true;

		return $success;
	}

	/**
	 * Returns the label to be displayed for the assignee on the workflow detail page.
	 *
	 * @since 2.1
	 *
	 * @return string
	 */
	public function get_status_label() {

		$assignee_status_label = '';
		$user_approval_status  = $this->get_status();

		$this->step->log_debug( __METHOD__ . '(): status for: ' . $this->get_key() );
		$this->step->log_debug( __METHOD__ . '(): assignee status: ' . $user_approval_status );

		$status_label = $this->step->get_status_label( $user_approval_status );
		if ( ! empty( $user_approval_status ) ) {
			$assignee_type = $this->get_type();

			switch ( $assignee_type ) {
				case 'email':
					$type_label   = esc_html__( 'Email', 'gravityflow' );
					$display_name = $this->get_id();
					break;
				case 'role':
					$type_label   = esc_html__( 'Role', 'gravityflow' );
					$display_name = translate_user_role( $this->get_id() );
					break;
				case 'user_id':
					$user         = get_user_by( 'id', $this->get_id() );
					$display_name = $user ? $user->display_name : $this->get_id() . ' ' . esc_html__( '(Missing)', 'gravityflow' );
					$type_label   = esc_html__( 'User', 'gravityflow' );
					break;
				default:
					$display_name = $this->get_id();
					$type_label   = $this->get_type();
			}
			$assignee_status_label = sprintf( '%s: %s (%s)', $type_label, $display_name, $status_label );

			$assignee_status_label = apply_filters( 'gravityflow_assignee_status_workflow_detail', $assignee_status_label, $this, $this );

		}
		return $assignee_status_label;
	}

	/**
	 * Override this method to replace merge tags.
	 * Important: call the parent method first.
	 * $text = parent::replace_variables( $text );
	 *
	 * @since 2.1
	 *
	 * @param string $text The text containing merge tags to be processed.
	 *
	 * @return string
	 */
	public function replace_variables( $text ) {

		$args = array(
			'assignee' => $this,
			'step'     => $this->step,
		);

		$merge_tags = Gravity_Flow_Merge_Tags::get_all( $args );

		foreach ( $merge_tags as $merge_tag ) {
			if ( $merge_tag instanceof Gravity_Flow_Merge_Tag_Assignee_Base ) {
				$text = $merge_tag->replace( $text );
			}
		}

		return $text;
	}
}
