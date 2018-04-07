<?php
/**
 * Gravity Flow Workflow URL Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Workflow_Url
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Workflow_Url extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	public $name = 'workflow_url';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_(entry|inbox)_(url|link)(:(.*?))?}/';

	/**
	 * Replace the {workflow_entry_link}, {workflow_entry_url}, {workflow_inbox_link}, and {workflow_inbox_url} merge tags.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $text The text being processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {

		$matches = $this->get_matches( $text );

		if ( ! empty( $matches ) ) {

			foreach ( $matches as $match ) {
				$full_tag       = $match[0];
				$location       = $match[1];
				$type           = $match[2];
				$options_string = isset( $match[4] ) ? $match[4] : '';

				$a = $this->get_attributes( $options_string, array(
					'page_id' => gravity_flow()->get_app_setting( 'inbox_page' ),
					'text'    => $location == 'inbox' ? esc_html__( 'Inbox', 'gravityflow' ) : esc_html__( 'Entry', 'gravityflow' ),
					'token'   => false,
				) );

				$token = $this->get_workflow_url_access_token( $a );

				if ( $location == 'inbox' ) {
					$url = $this->get_inbox_url( $a['page_id'], $token );
				} else {
					$url = $this->get_entry_url( $a['page_id'], $token );
				}

				$url = $this->format_value( $url );

				if ( $type == 'link' ) {
					$url = sprintf( '<a href="%s">%s</a>', $url, $a['text'] );
				}

				$text = str_replace( $full_tag, $url, $text );
			}
		}

		return $text;
	}

	/**
	 * Get the access token for the workflow_entry_ and workflow_inbox_ merge tags.
	 *
	 * @param array $a The merge tag attributes.
	 *
	 * @return string
	 */
	private function get_workflow_url_access_token( $a ) {
		$force_token = $a['token'];
		$token       = '';

		if ( $this->assignee && $force_token ) {
			$token_lifetime_days        = apply_filters( 'gravityflow_entry_token_expiration_days', 30, $this->assignee );
			$token_expiration_timestamp = strtotime( '+' . (int) $token_lifetime_days . ' days' );
			$token                      = gravity_flow()->generate_access_token( $this->assignee, null, $token_expiration_timestamp );
		}

		return $token;
	}

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
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Url );
