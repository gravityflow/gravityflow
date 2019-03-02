<?php
/**
 * Gravity Flow Workflow Timeline Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

if ( ! class_exists( 'Gravity_Flow_Merge_Tag_Workflow_Note' ) ) {
	require_once( 'class-merge-tag-workflow-note.php' );
}

/**
 * Class Gravity_Flow_Merge_Tag_Workflow_Timeline
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Workflow_Timeline extends Gravity_Flow_Merge_Tag_Workflow_Note {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	public $name = 'workflow_timeline';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_timeline(:(.*?))?}/';

	/**
	 * Replace the {workflow_timeline} merge tags with the entire timeline for the current entry.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $text The text to be processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {

		if ( empty( $this->entry ) ) {
			return $text;
		}

		$matches = $this->get_matches( $text );

		if ( is_array( $matches ) && isset( $matches[0] ) ) {
			$full_tag = $matches[0][0];
			$timeline = Gravity_Flow_Common::get_timeline( $this->entry );
			$text     = str_replace( $full_tag, $this->format_value( $timeline ), $text );
		}

		return $text;
	}

}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Timeline );
