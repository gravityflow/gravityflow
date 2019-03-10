<?php
/**
 * Gravity Flow Form Population Functions
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2019, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.5
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Populate_Form
 *
 * @since 2.5
 */
class Gravity_Flow_Populate_Form {

	/**
	 * Gets an array of population compatible values for the supplied form and entry.
	 *
	 * @since 2.5
	 *
	 * @param array $form  The form to be populated.
	 * @param array $entry The entry being used as the source of the values to be populated.
	 *
	 * @return array
	 */
	public static function get_population_values_from_entry( $form, $entry ) {
		$population_values = array();

		// Entries created by other forms are out of scope.
		if ( $form['id'] != rgar( $entry, 'form_id' ) ) {
			return $population_values;
		}

		/** @var GF_Field $field */
		foreach ( $form['fields'] as $field ) {

			if ( $field->displayOnly ) {
				continue;
			}

			$field_id = strval( $field->id );
			$inputs   = $field->get_entry_inputs();
			$values   = array();

			if ( is_array( $inputs ) ) {
				foreach ( $inputs as $input ) {
					$input_id            = strval( $input['id'] );
					$values[ $input_id ] = rgar( $entry, $input_id );
				}
			} else {
				$values = rgar( $entry, $field_id );
			}

			$population_values[ $field_id ] = $values;

		}

		return $population_values;
	}

	/**
	 * Set up dynamic population of the fields using the supplied input values.
	 *
	 * @since 2.5
	 *
	 * @param array $form   The form being populated.
	 * @param array $values The values to be populated.
	 *
	 * @return array
	 */
	public static function do_population( $form, $values ) {

		/** @var GF_Field $field */
		foreach ( $form['fields'] as &$field ) {

			if ( $field->displayOnly ) {
				continue;
			}

			$field_value = rgar( $values, $field->id );

			if ( GFCommon::is_empty_array( $field_value ) ) {
				continue;
			}

			$field->allowsPrepopulate = true;

			$input_type = $field->get_input_type();

			switch ( $input_type ) {
				case 'checkbox':
					break;

				default:

					if ( ! is_array( $field_value ) ) {
						break;
					}

					$inputs = $field->get_entry_inputs();
					if ( is_array( $inputs ) ) {
						foreach ( $inputs as &$input ) {
							$input_value = rgar( $field_value, $input['id'] );
							if ( $input_value ) {
								$input['name'] = self::add_filter( $input['id'], $input_value );
							}
						}
						$field->inputs = $inputs;
						$field_value   = false;
					}
			}

			if ( $field_value ) {
				$field->inputName = self::add_filter( $field->id, $field_value );
			}

		}

		return $form;
	}

	/**
	 * Adds the filter to populate the field value.
	 *
	 * @since 2.5
	 *
	 * @param string $input_id The ID of the field or input to be populated.
	 * @param mixed  $value    The value to be populated.
	 *
	 * @return string
	 */
	public static function add_filter( $input_id, $value ) {

		$filter_name = 'gravityflow_field_' . str_replace( '.', '_', $input_id );
		add_filter( "gform_field_value_{$filter_name}", array(
			new Gravity_Flow_Dynamic_Hook( $value, __CLASS__ ),
			'filter_gform_field_value'
		), 50 );

		return $filter_name;
	}

	/**
	 * Returns the value to be populated for the field being processed.
	 *
	 * @since 2.5
	 *
	 * @param array $filter_values     The filter arguments.
	 * @param mixed $prepopulate_value The value to be populated.
	 *
	 * @return mixed
	 */
	public static function filter_gform_field_value( $filter_values, $prepopulate_value ) {
		return $prepopulate_value;
	}

}
