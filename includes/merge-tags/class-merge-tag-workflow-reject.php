<?php
/**
 * Gravity Flow Workflow Reject Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Workflow_Reject
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Workflow_Reject extends Gravity_Flow_Merge_Tag_Assignee_Base {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	public $name = 'workflow_reject';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_reject_(url|link)(:(.*?))?}/';

	/**
	 * Replace the {workflow_reject_link} and {workflow_reject_url} merge tags.
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
				$type           = $match[1];
				$options_string = isset( $match[3] ) ? $match[3] : '';

				$a = $this->get_attributes( $options_string, array(
					'page_id'  => gravity_flow()->get_app_setting( 'inbox_page' ),
					'text'     => esc_html__( 'Reject', 'gravityflow' ),
					'token'    => false,
					'assignee' => '',
					'step'     => '',
				) );

				$original_step = $this->step;

				if ( ! empty( $a['step'] ) ) {
					$this->step = gravity_flow()->get_step( $a['step'], $this->entry );
				}

				if ( empty( $this->step ) ) {
					$text       = str_replace( $full_tag, '', $text );
					$this->step = $original_step;
					continue;
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

				$reject_token = $this->get_token( 'reject' );

				$url = $this->get_entry_url( $a['page_id'], $reject_token );
				$url = esc_url_raw( $url );

				$url = $this->format_value( $url );

				if ( $type == 'link' ) {
					$url = sprintf( '<a href="%s">%s</a>', $url, $a['text'] );
				}

				$text = str_replace( $full_tag, $url, $text );

				$this->step = $original_step;

				$this->assignee = $original_assignee;
			}
		}

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Reject );
