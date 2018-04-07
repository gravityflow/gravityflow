<?php
/**
 * Gravity Flow Created By Merge Tag
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
class Gravity_Flow_Merge_Tag_Created_By extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	public $name = 'created_by';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{created_by(:(.*?))?}/';

	/**
	 * Replace the {created_by} merge tags.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $text The text to be processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {


		$matches = $this->get_matches( $text );

		if ( ! empty( $matches ) ) {

			if ( empty( $this->entry ) || empty( $this->entry['created_by'] ) ) {
				foreach ( $matches as $match ) {
					$full_tag = $match[0];
					$text = str_replace( $full_tag, '', $text );
				}
				return $text;
			}

			$entry = $this->entry;

			$entry_creator = new WP_User( $entry['created_by'] );

			if ( ! empty( $entry['created_by'] ) ) {
				foreach ( $matches as $match ) {
					$full_tag = $match[0];
					$property = isset( $match[2] ) ? $match[2] : 'ID';

					if ( $property == 'roles' ) {
						$value = implode( ', ', $entry_creator->roles );
					} else {
						$value = $entry_creator->get( $property );
					}

					$text = str_replace( $full_tag, $this->format_value( $value ), $text );
				}
			}
		}

		return $text;
	}
}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Created_By );
