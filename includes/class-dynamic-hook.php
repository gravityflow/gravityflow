<?php
/**
 * Gravity Flow Dynamic Hook.
 *
 * Allows values to be injected into filters and actions.
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
 * Class Gravity_Flow_Dynamic_Hook
 *
 * @since 2.5
 */
class Gravity_Flow_Dynamic_Hook {

	/**
	 * @since 2.5
	 *
	 * @var mixed
	 */
	private $values;

	/**
	 * @since 2.5
	 *
	 * @var mixed
	 */
	private $class = null;

	/**
	 * Stores the values for later use.
	 *
	 * @since 2.5
	 *
	 * @param mixed $values
	 * @param null  $class
	 */
	public function __construct( $values, $class = null ) {

		$this->values = $values;

		if ( $class ) {
			$this->class = $class;
		}
	}

	/**
	 * Runs the hook callback function.
	 *
	 * @since 2.5
	 *
	 * @param string $callback    The name of the method.
	 * @param array  $filter_args The args called by the filter.
	 *
	 * @return mixed
	 */
	public function __call( $callback, $filter_args ) {

		$args = array( $filter_args, $this->values );

		if ( $this->class ) {
			if ( is_callable( array( $this->class, $callback ) ) ) {
				return call_user_func_array( array( $this->class, $callback ), $args );
			}
		}
		if ( is_callable( $callback ) ) {
			return call_user_func_array( $callback, $args );
		}
	}
}
