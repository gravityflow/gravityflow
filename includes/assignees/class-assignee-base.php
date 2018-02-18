<?php
/**
 * Gravity Flow Assignee Base
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
abstract class Gravity_Flow_Assignee_Base {

	/**
	 * The unique name of this assignee.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $name;

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
	 * @throws Exception If the assignee can not be instantiated with the passed arguments.
	 *
	 * @param string|array           $args An assignee key or array.
	 * @param bool|Gravity_Flow_Step $step The current step or false.
	 */
	public function __construct( $args = array(), $step = false ) {
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
			throw new Exception( 'Assignees must be instantiated with either a string or an array' );
		}

		$this->type = $type;
		$this->id   = $id;

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

	public function send_notification( $message ) {

	}

	public function is_current_user() {

	}

	public function user_is_assignee( $user ) {

		// Check roles
		$current_role_status = false;
		$role                = false;

		foreach ( gravity_flow()->get_user_roles() as $role ) {
			$current_role_status = $this->step->get_role_status( $role );
			if ( $current_role_status == 'pending' ) {
				break;
			}
		}

		return array( $role, $current_role_status );
	}
}
