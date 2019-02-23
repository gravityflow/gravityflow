<?php
/**
 * Gravity Flow Current Step Due Date
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Due_Date
 *
 * @since 2.5
 */
class Gravity_Flow_Merge_Tag_Due_Date extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $name = 'due_date';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	protected $regex = '/{due_date(:(.*?))?}/';

	/**
	 * Replace the {due_date} merge tags.
	 *
	 * @since 2.5
	 *
	 * @param string $text The text to be processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {

		$matches = $this->get_matches( $text );

		if ( ! empty( $matches ) ) {

			if ( empty( $this->entry ) || empty( $this->step ) ) {
				foreach ( $matches as $match ) {
					$full_tag = $match[0];
					$text = str_replace( $full_tag, '', $text );
				}
				return $text;
			}

			$current_step = $this->step;

			$due_date = $current_step->get_due_date_timestamp();

			if( $due_date ) {
				$due_date_formatted = Gravity_Flow_Common::format_date( date( 'Y-m-d H:i:s', $due_date ), '', true, true );
			} else {
				$due_date_formatted = '';
			}

			foreach ( $matches as $match ) {
				$full_tag = $match[0];
				$text     = str_replace( $full_tag, $due_date_formatted, $text );
			}
			
			return $text;

		}

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Due_Date );
