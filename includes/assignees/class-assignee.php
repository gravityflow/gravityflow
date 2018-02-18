<?php

require_once( 'class-assignee-base.php' );

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
class Gravity_Flow_Assignee extends Gravity_Flow_Assignee_Base {

	/**
	 * The unique name of this assignee.
	 *
	 * @since 1.0
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
	 * @throws Exception If the assignee can not be instantiated with the passed arguments.
	 *
	 * @param string|array           $args An assignee key or array.
	 * @param bool|Gravity_Flow_Step $step The current step or false.
	 */
	public function __construct( $args = array(), $step = false ) {
		parent::__construct( $args, $step );

		$type = $this->type;
		$id   = $this->id;

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

	public function send_notification( $notification ) {
		$message       = $notification['message'];
		$assignee_type = $this->get_type();
		$assignee_id   = $this->get_id();

		if ( $assignee_type == 'email' ) {
			$email                   = $assignee_id;
			$notification['id']      = 'workflow_step_' . $this->get_id() . '_user_' . $email;
			$notification['name']    = $notification['id'];
			$notification['to']      = $email;
			$notification['message'] = $this->step->replace_variables( $message, $this );
			$this->step->send_notification( $notification );

			return;
		}

		if ( $assignee_type == 'role' ) {
			$users = get_users( array( 'role' => $assignee_id ) );
		} else {
			$users = get_users( array( 'include' => array( $assignee_id ) ) );
		}

		$this->step->log_debug( __METHOD__ . sprintf( '() sending assignee notifications to %d users', count( $users ) ) );

		$user_assignee_args = array(
			'type' => $assignee_type,
			'id'   => $assignee_id,
		);
		foreach ( $users as $user ) {
			$user_assignee_args['user'] = $user;
			$user_assignee              = $this->step->get_assignee( $user_assignee_args );
			$notification['id']         = 'workflow_step_' . $this->get_id() . '_user_' . $user->ID;
			$notification['name']       = $notification['id'];
			$notification['to']         = $user->user_email;
			$notification['message']    = $this->step->replace_variables( $message, $user_assignee );
			$this->step->send_notification( $notification );
		}
	}

	public function current_user_is_assignee() {

		$assignee_key = $this->step->get_current_assignee_key();
		$assignee     = $this->step->get_assignee( $assignee_key );

		$status = $assignee->get_status();

		if ( $status == 'pending' ) {
			return true;
		}


		// Check roles
		$current_role_status = false;

		foreach ( gravity_flow()->get_user_roles() as $role ) {
			$current_role_status = $this->step->get_role_status( $role );
			if ( $current_role_status == 'pending' ) {
				break;
			}
		}

		if ( $current_role_status == 'pending' ) {
			return true;
		}
		return false;
	}
}
