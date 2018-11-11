<?php
/**
 * Gravity Flow Workflow Note Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Workflow_Note
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Workflow_Note extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
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
	 * @param string $text The text to be processed.
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
					'history'      => false,
				) );

				$replacement = '';
				$notes       = $this->get_step_notes( $entry['id'], $a['step_id'], $a['history'] );

				if ( ! empty( $notes ) ) {
					$replacement_array = array();

					foreach ( $notes as $note ) {
						$name = $a['display_name'] ? self::get_assignee_display_name( $note['assignee_key'] ) : '';
						$date = $a['display_date'] ? Gravity_Flow_Common::format_date( $note['timestamp'], '', false, true ) : '';

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
	 * @param int             $entry_id The current entry ID.
	 * @param null|string|int $step_id  The step ID or name. Null will return the most recent note.
	 * @param bool            $history  Include notes from previous occurrences of the specified step.
	 *
	 * @return array
	 */
	protected function get_step_notes( $entry_id, $step_id, $history ) {
		$notes      = Gravity_Flow_Common::get_workflow_notes( $entry_id, true );
		$step_notes = array();

		if ( ! is_numeric( $step_id ) && is_string( $step_id ) ) {
			// Try to look up the step ID by step name.
			$step_id = $this->get_step_id_by_name( $step_id );
		}

		$step_found            = false;
		$step_timestamp        = $step_id && ! $history ? gform_get_meta( $entry_id, 'workflow_step_' . $step_id . '_timestamp' ) : 0;
		$step_status_timestamp = $step_id && ! $history ? gform_get_meta( $entry_id, 'workflow_step_status_' . $step_id . '_timestamp' ) : 0;

		foreach ( $notes as $note ) {
			if ( $step_found && ! $history &&
			     ( $step_id != $note['step_id'] || $note['timestamp'] < $step_timestamp || $note['timestamp'] > $step_status_timestamp )
			) {
				break;
			}

			if ( $step_id && $step_id != $note['step_id'] ) {
				continue;
			}

			$step_notes[] = $note;
			$step_found   = true;

			if ( is_null( $step_id ) ) {
				break;
			}
		}

		return $step_notes;
	}

	/**
	 * Retrieve the step id for the specified step name.
	 *
	 * @since 1.8.1
	 *
	 * @param string $step_name The step name.
	 *
	 * @return int|false The step ID or false if not found.
	 */
	protected function get_step_id_by_name( $step_name ) {
		$step_id = false;
		if ( is_string( $step_name ) && ! is_numeric( $step_name ) ) {
			$step_name = strtolower( $step_name );
			$steps     = gravity_flow()->get_steps( $this->form['id'] );

			foreach ( $steps as $step ) {
				if ( strtolower( $step->get_name() ) === $step_name ) {
					$step_id = $step->get_id();
					break;
				}
			}
		}

		return $step_id;
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
			$assignee = Gravity_Flow_Assignees::create( $assignee_or_key );
		} else {
			$assignee = $assignee_or_key;
		}

		return $assignee->get_display_name();
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Note );
