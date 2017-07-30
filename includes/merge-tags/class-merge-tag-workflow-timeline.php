<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

if ( ! class_exists( 'Gravity_Flow_Merge_Tag_Workflow_Note' ) ) {
	require_once( 'class-merge-tag-workflow-note.php' );
}

class Gravity_Flow_Merge_Tag_Workflow_Timeline extends Gravity_Flow_Merge_Tag_Workflow_Note {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var null
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
	 * @param string $text  The text to be processed.
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
			$timeline = $this->get_timeline();
			$text     = str_replace( $full_tag, $this->format_value( $timeline ), $text );
		}

		return $text;
	}

	/**
	 * Get the content which will replace the {workflow_timeline} merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @return string
	 */
	protected function get_timeline() {

		if ( empty( $this->entry ) ) {
			return '';
		}

		$entry = $this->entry;

		$notes = Gravity_Flow_Common::get_timeline_notes( $entry );

		if ( empty( $notes ) ) {
			return '';
		}

		$return = array();

		foreach ( $notes as $note ) {
			$step = Gravity_Flow_Common::get_timeline_note_step( $note );
			$name = Gravity_Flow_Common::get_timeline_note_display_name( $note, $step );
			$date = Gravity_Flow_Common::format_date( $note->date_created );

			$return[] = $this->format_note( $note->value, $name, $date );
		}

		return implode( "\n\n", $return );
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Timeline );
