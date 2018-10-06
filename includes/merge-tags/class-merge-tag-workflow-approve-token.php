<?php
/**
 * Gravity Flow Workflow Approve Token Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Approve_Token
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Approve_Token extends Gravity_Flow_Merge_Tag_Assignee_Base {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	public $name = 'workflow_approve_token';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_approve_token}/';

	/**
	 * Replace the {workflow_token_link} merge tags.
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
					$text     = str_replace( $full_tag, '', $text );
				}

				return $text;
			}

			$token = $this->get_token( 'approve' );

			$token = $this->format_value( $token );

			$text = str_replace( '{workflow_approve_token}', $token, $text );
		}

		return $text;
	}

	/**
	 * Get the number of days the token will remain valid for.
	 *
	 * @since 2.3.2 Added $assignee arg.
	 * @since 2.1.2-dev
	 *
	 * @param Gravity_Flow_Assignee $assignee
	 *
	 * @return int
	 */
	protected function get_token_expiration_days( $assignee = null ) {

		if ( empty( $assignee ) ) {
			$assignee = $this->assignee;
		}

		return apply_filters( 'gravityflow_approval_token_expiration_days', 2, $assignee );
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Approve_Token );
