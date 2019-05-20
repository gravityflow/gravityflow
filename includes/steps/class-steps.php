<?php
/**
 * Gravity Flow Steps
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Steps
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Steps {

	/* @var Gravity_Flow_Step[] */
	private static $_steps = array();

	/**
	 * @param Gravity_Flow_Step $step
	 *
	 * @throws Exception
	 */
	public static function register( $step ) {
		if ( ! is_subclass_of( $step, 'Gravity_Flow_Step' ) ) {
			throw new Exception( 'Must be a subclass of Gravity_Flow_Step' );
		}
		$step_type = $step->get_type();
		if ( empty( $step_type ) ) {
			throw new Exception( 'The step_type must be set' );
		}
		if ( isset( self::$_steps[ $step_type ] ) ) {
			throw new Exception( 'Step type already registered: ' . $step_type );
		}
		self::$_steps[ $step_type ] = $step;
	}

	public static function exists( $step_type ) {
		return isset( self::$_steps[ $step_type ] );
	}

	/**
	 * @param $step_type
	 *
	 * @return Gravity_Flow_Step
	 */
	public static function get_instance( $step_type ) {
		return isset( self::$_steps[ $step_type ] ) ? self::$_steps[ $step_type ] : false;
	}

	/**
	 * Alias for get_instance()
	 *
	 * @param $step_type
	 *
	 * @return Gravity_Flow_Step
	 */
	public static function get( $step_type ) {
		return self::get_instance( $step_type );
	}

	/**
	 * @return Gravity_Flow_Step[]
	 */
	public static function get_all() {
		return self::$_steps;
	}

	/**
	 * @param $feed
	 *
	 * @return Gravity_Flow_Step | bool
	 */
	public static function create( $feed, $entry = null ) {
		$step_type = rgar( $feed['meta'], 'step_type' );

		if ( empty( $step_type ) || ! isset( self::$_steps[ $step_type ] ) ) {
			return false;
		}
		$class      = self::$_steps[ $step_type ];
		$class_name = get_class( $class );
		$step       = new $class_name( $feed, $entry );

		return $step;

	}
}
