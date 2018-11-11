<?php
/**
 * Gravity Flow Installation Wizard
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Installation_Wizard
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * An abstract class used as the base for all installation wizard steps.
 *
 * Class Gravity_Flow_Installation_Wizard_Step
 */
abstract class Gravity_Flow_Installation_Wizard_Step extends stdClass {

	/**
	 * The step name.
	 *
	 * @var string
	 */
	protected $_name = '';

	/**
	 * The field validation results.
	 *
	 * @var array
	 */
	protected $_field_validation_results = array();

	/**
	 * The validation summary.
	 *
	 * @var string
	 */
	protected $_validation_summary = '';

	/**
	 * The step values.
	 *
	 * @var array
	 */
	private $_step_values;

	public $defaults = array();

	/**
	 * Gravity_Flow_Installation_Wizard_Step constructor.
	 *
	 * @param array $values The step values.
	 *
	 * @throws Exception When the step name has not been set.
	 */
	public function __construct( $values = array() ) {
		if ( empty( $this->_name ) ) {
			throw new Exception( 'Name not set' );
		}
		$this->_step_values = empty( $values ) ? $this->defaults : $values;
	}

	/**
	 * Returns the step name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->_name;
	}

	/**
	 * Compares the supplied key against the current step name.
	 *
	 * @param string $key The step name.
	 *
	 * @return bool
	 */
	public function is( $key ) {
		return $key == $this->get_name();
	}

	/**
	 * Returns the step title.
	 *
	 * @return string
	 */
	public function get_title() {
		return '';
	}

	/**
	 * Sets the value for the specified property.
	 *
	 * @param string $key   The property key.
	 * @param mixed  $value The property value.
	 */
	public function __set( $key, $value ) {
		$this->_step_values[ $key ] = $value;
	}

	/**
	 * Determines if the specified property has been defined.
	 *
	 * @param string $key The property key.
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->_step_values[ $key ] );
	}

	/**
	 * Deletes the specified property.
	 *
	 * @param string $key The property key.
	 */
	public function __unset( $key ) {
		unset( $this->_step_values[ $key ] );
	}

	/**
	 * Returns the specified property or an empty string for an undefined property.
	 *
	 * @param string $key The property key.
	 *
	 * @return mixed
	 */
	public function &__get( $key ) {
		if ( ! isset( $this->_step_values[ $key ] ) ) {
			$this->_step_values[ $key ] = '';
		}
		return $this->_step_values[ $key ];
	}

	/**
	 * Returns the values for the current step.
	 *
	 * @return array
	 */
	function get_values(){
		$set_values = $this->_step_values ? $this->_step_values : array();
		$values = array_merge( $this->defaults, $set_values);
		return $values;
	}

	/**
	 * Override to display content for this step.
	 */
	public function display() {
	}

	/**
	 * Override to validate the posted values for this step.
	 *
	 * @return bool
	 */
	public function validate() {
		return true;
	}

	/**
	 * Returns the validation result for the specified property or an empty string for an undefined property.
	 *
	 * @param string $key The property key.
	 *
	 * @return mixed
	 */
	public function get_field_validation_result( $key ) {
		if ( ! isset( $this->_field_validation_results[ $key ] ) ) {
			$this->_field_validation_results[ $key ] = '';
		}
		return $this->_field_validation_results[ $key ];
	}

	/**
	 * Set the field validation result for the specified property.
	 *
	 * @param string $key  The property key.
	 * @param string $text The validation result.
	 */
	public function set_field_validation_result( $key, $text ) {
		$this->_field_validation_results[ $key ] = $text;
	}

	/**
	 * Set the validation summary property.
	 *
	 * @param string $text The validation summary.
	 */
	public function set_validation_summary( $text ) {
		$this->_validation_summary = $text;
	}

	/**
	 * Returns the validation summary property.
	 *
	 * @return string
	 */
	public function get_validation_summary() {
		return $this->_validation_summary;
	}

	/**
	 * Return the markup for the validation message.
	 *
	 * @param string $key  The property key.
	 * @param bool   $echo Indicates if the message should be echoed.
	 *
	 * @return string
	 */
	public function validation_message( $key, $echo = true ) {
		$message = '';
		$validation_result = $this->get_field_validation_result( $key );
		if ( ! empty( $validation_result ) ) {
			$message = sprintf( '<div class="validation_message">%s</div>', $validation_result );
		}

		if ( $echo ) {
			echo $message;
		}
		return $message;
	}

	/**
	 * Override to determine if the current step has been completed.
	 */
	public function is_complete() {
	}

	/**
	 * Returns the next button label.
	 *
	 * @return string
	 */
	public function get_next_button_text() {
		return __( 'Next', 'gravityflow' );
	}

	/**
	 * Returns the previous button label.
	 *
	 * @return string
	 */
	public function get_previous_button_text() {
		return __( 'Back', 'gravityflow' );
	}

	/**
	 * Update the step options in the database and class property.
	 *
	 * @param array $posted_values The step values.
	 */
	public function update( $posted_values = array() ) {

		$step_values = $this->get_values();
		if ( empty( $step_values ) ) {
			$step_values = array();
		}
		$new_values = array_merge( $step_values, $posted_values );
		update_option( 'gravityflow_installation_wizard_' . $this->get_name(), $new_values );
		$this->_step_values = $new_values;
	}

	/**
	 * Override to return summary content.
	 *
	 * @param bool $echo Indicates if the summary should be echoed.
	 *
	 * @return string
	 */
	public function summary( $echo = true ) {
		return '';
	}

	/**
	 * Override to perform actions when the installation wizard is completing.
	 */
	public function install() {
		// Do something.
	}

	/**
	 * Deletes this steps values from the database.
	 */
	public function flush_values() {
		delete_option( 'gravityflow_installation_wizard_' . $this->get_name() );
	}

	function get_posted_values() {

		$posted_values = stripslashes_deep( $_POST );
		$values        = array();
		foreach ( $posted_values as $key => $value ) {
			if ( strpos( $key, '_', 0 ) !== 0 ) {
				$values[ $key ] = $value;
			}
		}
		$values = array_merge( $this->defaults, $values);
		return $values;
	}

	function get_step_settings( $step_name ) {
		$settings = get_option( 'gravityflow_installation_wizard_' . $step_name );
		return $settings;
	}
}
