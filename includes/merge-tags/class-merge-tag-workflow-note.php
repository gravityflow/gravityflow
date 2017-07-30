<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Merge_Tag_Workflow_Note extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var null
	 */
	public $name = 'workflow_note';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_note(:(.*?))?}/';

	/**
	 * Replace the {workflow_note} merge tags with the user submitted notes.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $text  The text to be processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {

		$entry = $this->entry;

		if ( empty( $entry ) ) {
			return $text;
		}

		$matches = $this->get_matches( $text );

		if ( ! empty( $matches ) ) {
			foreach ( $matches as $match ) {
				$full_tag  = $match[0];
				$modifiers = rgar( $match, 2 );

				$a = $this->get_attributes( $modifiers, array(
					'step_id'      => null,
					'display_name' => false,
					'display_date' => false,
				) );

				$replacement = '';
				$notes       = $this->get_step_notes( $entry['id'], $a['step_id'] );

				if ( ! empty( $notes ) ) {
					$replacement_array = array();

					foreach ( $notes as $note ) {
						$name = $a['display_name'] ? self::get_assignee_display_name( $note['assignee_key'] ) : '';
						$date = $a['display_date'] ? Gravity_Flow_Common::format_date( $note['timestamp'] ) : '';

						$replacement_array[] = self::format_note( $note['value'], $name, $date );
					}

					$replacement = $this->format_value( implode( "\n\n", $replacement_array ) );
				}

				$text = str_replace( $full_tag, $replacement, $text );
			}
		}

		return $text;
	}

	/**
	 * Get the user submitted notes for a specific step.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param int      $entry_id The current entry ID.
	 * @param int|null $step_id  The step ID or null to return the most recent note.
	 *
	 * @return array
	 */
	protected function get_step_notes( $entry_id, $step_id ) {
		$notes      = Gravity_Flow_Common::get_workflow_notes( $entry_id, true );
		$step_notes = array();

		foreach ( $notes as $note ) {
			if ( $step_id && $step_id != $note['step_id'] ) {
				continue;
			}

			$step_notes[] = $note;

			if ( is_null( $step_id ) ) {
				break;
			}
		}

		return $step_notes;
	}

	/**
	 * Format a note for output.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $note_value   The note value.
	 * @param string $display_name The note display name.
	 * @param string $date         The note creation date.
	 *
	 * @return string
	 */
	protected function format_note( $note_value, $display_name, $date ) {
		$separator = $display_name && $date ? ': ' : '';

		return sprintf( "%s%s%s\n%s", $display_name, $separator, $date, $note_value );
	}

	/**
	 * Get the assignee display name.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string|Gravity_Flow_Assignee $assignee_or_key The assignee key or object.
	 *
	 * @return string
	 */
	protected function get_assignee_display_name( $assignee_or_key ) {
		if ( ! $assignee_or_key instanceof Gravity_Flow_Assignee ) {
			$assignee = new Gravity_Flow_Assignee( $assignee_or_key );
		} else {
			$assignee = $assignee_or_key;
		}

		return $assignee->get_display_name();
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Note );
