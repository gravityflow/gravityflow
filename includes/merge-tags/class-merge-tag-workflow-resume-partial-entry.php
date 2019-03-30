<?php
/**
 * Gravity Flow Workflow Resume Partial Entry Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2019, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Workflow_Resume_Partial_Entry
 *
 * @since 2.5
 */
class Gravity_Flow_Merge_Tag_Workflow_Resume_Partial_Entry extends Gravity_Flow_Merge_Tag_Assignee_Base {

	/**
	 * The name of the merge tag.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $name = 'workflow_resume_partial_entry';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_resume_partial_entry_(url|link)(:(.*?))?}/';

	/**
	 * Replace the {workflow_resume_partial_entry_url} and {workflow_resume_partial_entry_link} merge tags.
	 *
	 * @since 2.5
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
				$type           = $match[1];
				$options_string = isset( $match[3] ) ? $match[3] : '';

				$a = $this->get_attributes( $options_string, array(
					'page_id'  => '',
					'text'     => '',
					'token'    => false,
					'assignee' => '',
					'step'     => '',
				) );

				$original_step = $this->step;

				if ( ! empty( $a['step'] ) ) {
					$this->step = gravity_flow()->get_step( $a['step'], $this->entry );
				}

				if ( ! ( $this->step && $this->step instanceof Gravity_Flow_Step_Partial_Entry_Submission ) ) {
					$text       = str_replace( $full_tag, '', $text );
					$this->step = $original_step;
					continue;
				}

				$original_entry = $this->entry;
				$this->entry    = $this->step->get_entry();

				if ( empty( $this->entry['partial_entry_id'] ) ) {
					$this->entry = $original_entry;
					$text        = str_replace( $full_tag, '', $text );
					continue;
				}

				if ( empty( $a['page_id'] ) ) {
					$a['page_id'] = $this->step->submit_page;
				}

				if ( $type == 'link' && empty( $a['text'] ) ) {
					$form      = $this->step->get_form();
					$a['text'] = esc_html( $form['title'] );
				}

				$original_assignee = $this->assignee;

				if ( ! empty( $a['assignee'] ) ) {
					$this->assignee = $this->step->get_assignee( $a['assignee'] );
				}

				if ( empty( $this->assignee ) ) {
					$text           = str_replace( $full_tag, '', $text );
					$this->assignee = $original_assignee;
					continue;
				}

				$token = $this->get_workflow_url_access_token( $a );

				$submission_url = $this->get_form_url( $a['page_id'], $token );

				$url = $this->format_value( $submission_url );

				if ( $type == 'link' ) {
					$url = sprintf( '<a href="%s">%s</a>', $url, $a['text'] );
				}

				$text = str_replace( $full_tag, $url, $text );

				$this->assignee = $original_assignee;
				$this->entry    = $original_entry;
				$this->step     = $original_step;
			}
		}

		return $text;
	}

	/**
	 * Get the access token if the token is required by the attributes.
	 *
	 * @since 2.5
	 *
	 * @param array $a The merge tag attributes.
	 *
	 * @return string
	 */
	private function get_workflow_url_access_token( $a ) {
		$force_token = $a['token'];
		$token       = '';

		if ( $this->assignee && $force_token ) {
			$token = $this->get_token();
		}

		return $token;
	}

	/**
	 * Returns the inbox URL.
	 *
	 * @since 2.5
	 *
	 * @param int|null $page_id      The ID of the WordPress Page where the shortcode is located.
	 * @param string   $access_token The access token for the current assignee.
	 *
	 * @return string
	 */
	public function get_form_url( $page_id = null, $access_token = '' ) {

		$query_args = array(
			'peid' => $this->entry['partial_entry_id'],
		);

		if ( $page_id == 'admin' ) {
			$query_args['page'] = 'gravityflow-submit';
			$query_args['id']   = $this->step->get_form_id();
		}

		return Gravity_Flow_Common::get_workflow_url( $query_args, $page_id, $this->assignee, $access_token );
	}

}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Resume_Partial_Entry );
