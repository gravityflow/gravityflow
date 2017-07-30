<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

if ( ! class_exists( 'Gravity_Flow_Merge_Tag_Approve_Token' ) ) {
	require_once( 'class-merge-tag-workflow-approve-token.php' );
}

class Gravity_Flow_Merge_Tag_Approve extends Gravity_Flow_Merge_Tag_Approve_Token {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var null
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

			if ( empty( $this->step ) || empty( $this->assignee ) ) {
				foreach ( $matches as $match ) {
					$full_tag = $match[0];
					$text = str_replace( $full_tag, '', $text );
				}
				return $text;
			}

			$approve_token = $this->get_token();

			if ( is_array( $matches ) ) {
				foreach ( $matches as $match ) {
					$full_tag       = $match[0];
					$type           = $match[1];
					$options_string = isset( $match[3] ) ? $match[3] : '';

					$a = $this->get_attributes( $options_string, array(
						'page_id' => gravity_flow()->get_app_setting( 'inbox_page' ),
						'text'    => esc_html__( 'Approve', 'gravityflow' ),
					) );

					$approve_url = $this->get_entry_url( $a['page_id'], $approve_token );
					$approve_url = esc_url_raw( $approve_url );

					$approve_url = $this->format_value( $approve_url );

					if ( $type == 'link' ) {
						$approve_url = sprintf( '<a href="%s">%s</a>', $approve_url, $a['text'] );
					}

					$text = str_replace( $full_tag, $approve_url, $text );
				}
			}
		}

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Approve );
