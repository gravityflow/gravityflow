<?php
/**
 * Gravity Flow Workflow Cancel Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Workflow_Cancel
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Workflow_Cancel extends Gravity_Flow_Merge_Tag_Workflow_Url {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	public $name = 'workflow_cancel';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_cancel_(url|link)(:(.*?))?}/';

	/**
	 * Replace the {workflow_cancel_link} and {workflow_cancel_url} merge tags.
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

			$expiration_days      = apply_filters( 'gravityflow_cancel_token_expiration_days', 2, $this->assignee );
			$expiration_str       = '+' . (int) $expiration_days . ' days';
			$expiration_timestamp = strtotime( $expiration_str );

			$scopes = array(
				'pages'    => array( 'inbox' ),
				'entry_id' => $this->step->get_entry_id(),
				'action'   => 'cancel_workflow',
			);

			$cancel_token = gravity_flow()->generate_access_token( $this->assignee, $scopes, $expiration_timestamp );

			foreach ( $matches as $match ) {
				$full_tag       = $match[0];
				$type           = $match[1];
				$options_string = isset( $match[3] ) ? $match[3] : '';

				$a = $this->get_attributes( $options_string, array(
					'page_id' => gravity_flow()->get_app_setting( 'inbox_page' ),
					'text'    => esc_html__( 'Cancel Workflow', 'gravityflow' ),
				) );

				$url = $this->get_entry_url( $a['page_id'], $cancel_token );

				$url = $this->format_value( $url );

				if ( $type == 'link' ) {
					$url = sprintf( '<a href="%s">%s</a>', $url, $a['text'] );
				}

				$text = str_replace( $full_tag, $url, $text );
			}
		}

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Cancel );
