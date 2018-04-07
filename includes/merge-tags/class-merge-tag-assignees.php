<?php
/**
 * Gravity Flow Assignee Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Assignees
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Assignees extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	public $name = 'assignees';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{assignees(:(.*?))?}/';

	/**
	 * Replace the {assignees} merge tags.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $text The text being processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {

		if ( empty( $this->step ) ) {
			return $text;
		}

		$current_step = $this->step;

		$matches = $this->get_matches( $text );

		if ( ! empty( $matches ) ) {
			foreach ( $matches as $match ) {
				$full_tag       = $match[0];
				$options_string = isset( $match[2] ) ? $match[2] : '';

				$a = $this->get_attributes( $options_string, array(
					'status'       => true,
					'user_email'   => true,
					'display_name' => true,
				) );

				$assignees          = $current_step->get_assignees();
				$assignees_text_arr = array();

				/**
				 * The step assignees.
				 *
				 * @var Gravity_Flow_Assignee[]
				 */
				foreach ( $assignees as $step_assignee ) {
					$assignee_line = '';
					if ( $a['display_name'] ) {
						$assignee_line .= $step_assignee->get_display_name();
					}
					if ( $a['user_email'] && $step_assignee->get_type() == 'user_id' ) {
						if ( $assignee_line ) {
							$assignee_line .= ', ';
						}
						$assignee_user = new WP_User( $step_assignee->get_id() );
						$assignee_line .= $assignee_user->user_email;
					}
					if ( $a['status'] ) {
						$status = $step_assignee->get_status();
						if ( ! $status ) {
							$status = 'pending';
						}
						$assignee_line .= ' (' . gravity_flow()->translate_status_label( $status ) . ')';
					}
					$assignees_text_arr[] = $assignee_line;
				}

				$assignees_text = join( "\n", $assignees_text_arr );
				$text           = str_replace( $full_tag, $this->format_value( $assignees_text ), $text );
			}
		}

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Assignees );
