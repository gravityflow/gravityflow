<?php
/**
 * Gravity Flow Workflow Approve Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Approve
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Approve extends Gravity_Flow_Merge_Tag_Assignee_Base {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	public $name = 'workflow_approve';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_approve_(url|link)(:(.*?))?}/';

	/**
	 * Replace the {workflow_approve_link} and {workflow_approve_url} merge tags.
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
					'text'     => esc_html__( 'Approve', 'gravityflow' ),
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

				$approve_token = $this->get_token( 'approve' );

				$approve_url = $this->get_entry_url( $a['page_id'], $approve_token );
				$approve_url = esc_url_raw( $approve_url );

				$approve_url = $this->format_value( $approve_url );

				if ( $type == 'link' ) {
					$approve_url = sprintf( '<a href="%s">%s</a>', $approve_url, $a['text'] );
				}

				$text = str_replace( $full_tag, $approve_url, $text );

				$this->step = $original_step;

				$this->assignee = $original_assignee;
			}
		}

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Approve );
