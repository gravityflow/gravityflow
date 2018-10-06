<?php
/**
 * Gravity Flow Merge Tag Assignee Base
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Assignee_Base
 *
 * An abstract class used as the base for all assignee specific merge tags.
 *
 * @since 2.1.2-dev
 */
abstract class Gravity_Flow_Merge_Tag_Assignee_Base extends Gravity_Flow_Merge_Tag {

	/**
	 * Returns the inbox URL.
	 *
	 * @since 2.3.2 Added $assignee arg.
	 * @since 2.1.2
	 *
	 * @param int|null              $page_id      The ID of the WordPress Page where the shortcode is located.
	 * @param string                $access_token The access token for the current assignee.
	 * @param Gravity_Flow_Assignee $assignee     The assignee for the URL.
	 *
	 * @return string
	 */
	public function get_inbox_url( $page_id = null, $access_token = '', $assignee = null ) {

		if ( empty( $assignee ) ) {
			$assignee = $this->assignee;
		}

		$query_args = array(
			'page' => 'gravityflow-inbox',
		);

		return Gravity_Flow_Common::get_workflow_url( $query_args, $page_id, $assignee, $access_token );
	}

	/**
	 * Returns the entry URL.
	 *
	 * @since 2.3.2 Added $assignee arg.
	 * @since 2.1.2
	 *
	 * @param int|null              $page_id      The ID of the WordPress Page where the shortcode is located.
	 * @param string                $access_token The access token for the current assignee.
	 * @param Gravity_Flow_Assignee $assignee
	 *
	 * @return string
	 */
	public function get_entry_url( $page_id = null, $access_token = '', $assignee = null ) {

		if ( empty( $assignee ) ) {
			$assignee = $this->assignee;
		}

		$step = $assignee ? $assignee->get_step() : $this->step;

		$form_id = $step ? $step->get_form_id() : false;
		if ( empty( $form_id ) && ! empty( $this->form ) ) {
			$form_id = $this->form['id'];
		}

		if ( empty( $form_id ) ) {
			return false;
		}

		$entry_id = $step ? $this->step->get_entry_id() : false;
		if ( empty( $entry_id ) && ! empty( $this->entry ) ) {
			$entry_id = $this->entry['id'];
		}

		if ( empty( $entry_id ) ) {
			return false;
		}

		$query_args = array(
			'page' => 'gravityflow-inbox',
			'view' => 'entry',
			'id'   => $form_id,
			'lid'  => $entry_id,
		);

		return Gravity_Flow_Common::get_workflow_url( $query_args, $page_id, $assignee, $access_token );
	}

	/**
	 * Get the number of days the token will remain valid for.
	 *
	 * @since 2.3.2 Added $assignee arg.
	 * @since 2.1.2
	 *
	 * @param Gravity_Flow_Assignee $assignee
	 *
	 * @return int
	 */
	protected function get_token_expiration_days( $assignee = null ) {
		if ( empty( $assignee ) ) {
			$assignee = $this->assignee;
		}
		return apply_filters( 'gravityflow_entry_token_expiration_days', 30, $assignee );
	}

	/**
	 * Get the scopes to be used when generating the access token.
	 *
	 * @since 2.3.2 Added $assignee arg.
	 * @since 2.1.2
	 *
	 * @param string                $action The access token action.
	 * @param Gravity_Flow_Assignee $assignee
	 *
	 * @return array
	 */
	protected function get_token_scopes( $action = '', $assignee = null ) {
		if ( empty( $action ) ) {
			return array();
		}

		if ( empty( $assignee ) ) {
			$assignee = $this->assignee;
		}

		return array(
			'pages'           => array( 'inbox' ),
			'step_id'         => $assignee->get_step()->get_id(),
			'entry_timestamp' => $assignee->get_step()->get_entry_timestamp(),
			'entry_id'        => $assignee->get_step()->get_entry_id(),
			'action'          => $action,
		);
	}

	/**
	 * Get the token for the current assignee and step.
	 *
	 * @since 2.3.2 Added $assignee arg.
	 * @since 2.1.2
	 *
	 * @param string                $action The access token action.
	 * @param Gravity_Flow_Assignee $assignee
	 *
	 * @return string
	 */
	protected function get_token( $action = '', $assignee = null ) {
		if ( empty( $assignee ) ) {
			$assignee = $this->assignee;
		}
		$scopes               = $this->get_token_scopes( $action, $assignee );
		$expiration_timestamp = strtotime( '+' . (int) $this->get_token_expiration_days( $assignee ) . ' days' );

		return gravity_flow()->generate_access_token( $assignee, $scopes, $expiration_timestamp );
	}
}
