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
	 * @param int|null $page_id      The ID of the WordPress Page where the shortcode is located.
	 * @param string   $access_token The access token for the current assignee.
	 *
	 * @return string
	 */
	public function get_inbox_url( $page_id = null, $access_token = '' ) {

		$query_args = array(
			'page' => 'gravityflow-inbox',
		);

		return Gravity_Flow_Common::get_workflow_url( $query_args, $page_id, $this->assignee, $access_token );
	}

	/**
	 * Returns the entry URL.
	 *
	 * @param int|null $page_id      The ID of the WordPress Page where the shortcode is located.
	 * @param string   $access_token The access token for the current assignee.
	 *
	 * @return string
	 */
	public function get_entry_url( $page_id = null, $access_token = '' ) {

		$form_id = $this->step ? $this->step->get_form_id() : false;
		if ( empty( $form_id ) && ! empty( $this->form ) ) {
			$form_id = $this->form['id'];
		}

		if ( empty( $form_id ) ) {
			return false;
		}

		$entry_id = $this->step ? $this->step->get_entry_id() : false;
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

		return Gravity_Flow_Common::get_workflow_url( $query_args, $page_id, $this->assignee, $access_token );
	}

	/**
	 * Get the number of days the token will remain valid for.
	 *
	 * @return int
	 */
	protected function get_token_expiration_days() {
		return apply_filters( 'gravityflow_entry_token_expiration_days', 30, $this->assignee );
	}

	/**
	 * Get the scopes to be used when generating the access token.
	 *
	 * @param string $action The access token action.
	 *
	 * @return array
	 */
	protected function get_token_scopes( $action = '' ) {
		if ( empty( $action ) ) {
			return array();
		}
		return array(
			'pages'           => array( 'inbox' ),
			'step_id'         => $this->step->get_id(),
			'entry_timestamp' => $this->step->get_entry_timestamp(),
			'entry_id'        => $this->step->get_entry_id(),
			'action'          => $action,
		);
	}

	/**
	 * Get the token for the current assignee and step.
	 *
	 * @param string $action The access token action.
	 *
	 * @return string
	 */
	protected function get_token( $action = '' ) {
		$scopes               = $this->get_token_scopes( $action );
		$expiration_timestamp = strtotime( '+' . (int) $this->get_token_expiration_days() . ' days' );
		return gravity_flow()->generate_access_token( $this->assignee, $scopes, $expiration_timestamp );
	}
}
