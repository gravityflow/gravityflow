<?php
/**
 * Gravity Flow Step Name Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Created_By
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tag_Step_Name extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 2.2.3-dev
	 *
	 * @var string
	 */
	public $name = 'step_name';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 2.2.3-dev
	 *
	 * @var string
	 */
	protected $regex = '/{step_name(:(.*?))?}/';

	/**
	 * Replace the {step_name} merge tags.
	 *
	 * @since 2.2.3-dev
	 *
	 * @param string $text The text to be processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {

		if ( empty( $this->step ) ) {
			return $text;
		}

		$current_step = $this->step;
		$step_name = $current_step->get_name();

		$text = str_replace( '{step_name}', $this->format_value( $step_name ), $text );

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Step_Name );
