<?php
/**
 * Gravity Flow Workflow Fields Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag_Workflow_Fields
 *
 * @since 2.0.1-dev
 */
class Gravity_Flow_Merge_Tag_Workflow_Fields extends Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 2.0.1-dev
	 *
	 * @var null
	 */
	public $name = 'workflow_fields';

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 2.0.1-dev
	 *
	 * @var string
	 */
	protected $regex = '/{workflow_fields(:(.*?))?}/';

	/**
	 * Replace the {workflow_fields} merge tags with the field values for the current steps display/editable fields.
	 *
	 * @since 2.0.1-dev
	 *
	 * @param string $text  The text to be processed.
	 *
	 * @return string
	 */
	public function replace( $text ) {

		$entry = $this->entry;

		if ( empty( $entry ) || empty( $this->step ) ) {
			return $text;
		}

		$matches = $this->get_matches( $text );

		if ( ! empty( $matches ) ) {
			add_filter( 'gform_merge_tag_filter', array( $this, 'merge_tag_filter' ), 20, 4 );

			foreach ( $matches as $match ) {
				$full_tag  = $match[0];
				$modifiers = rgar( $match, 2 );

				$a = $this->get_attributes( $modifiers, array(
					'empty'    => false, // Output empty fields.
					'value'    => false, // Output choice values.
					'admin'    => false, // Output admin labels.
					'editable' => true, // Output the steps editable fields.
					'display'  => true, // Output the steps display fields.
				) );

				$replacement = GFCommon::get_submitted_fields( $this->form, $entry, $a['empty'], ! $a['value'], $this->format, $a['admin'], $this->name, $this->get_options_string( $a ) );
				$text        = str_replace( $full_tag, $replacement, $text );
			}

			remove_filter( 'gform_merge_tag_filter', array( $this, 'merge_tag_filter' ), 20 );
		}

		return $text;
	}

	/**
	 * Prepare a comma separated string containing only those attributes set to true.
	 *
	 * @since 2.0.1-dev
	 *
	 * @param array $attributes The merge tag attributes.
	 *
	 * @return string
	 */
	public function get_options_string( $attributes ) {
		$options = implode( ',', array_keys( array_filter( $attributes ) ) );

		return $options;
	}

	/**
	 * Prevents GFCommon::get_submitted_fields including non-editable and non-display fields in the content replacing the merge tag.
	 *
	 * @since 2.0.1-dev
	 *
	 * @param string   $value     The current merge tag value for the field.
	 * @param string   $merge_tag The current merge tag name.
	 * @param string   $modifiers The modifiers for the current merge tag.
	 * @param GF_Field $field     The field currently being processed.
	 *
	 * @return bool
	 */
	public function merge_tag_filter( $value, $merge_tag, $modifiers, $field ) {
		$modifiers_array        = $field->get_modifiers();
		$display_editable_field = in_array( 'editable', $modifiers_array ) && Gravity_Flow_Common::is_editable_field( $field, $this->step );
		$display_display_field  = in_array( 'display', $modifiers_array ) && Gravity_Flow_Common::is_display_field( $field, $this->step, $this->form, $this->entry );

		if ( ! $display_editable_field && ! $display_display_field ) {
			// Removing non-editable and non-display field from merge tag output.
			return false;
		}

		return $value;
	}

}

Gravity_Flow_Merge_Tags::register( new Gravity_Flow_Merge_Tag_Workflow_Fields );
