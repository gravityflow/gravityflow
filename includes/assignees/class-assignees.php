<?php

/**
 * Gravity Flow Assignees
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Assignees
 *
 * @since 2.0.2-dev
 */
class Gravity_Flow_Assignees {
	/**
	 * The merge tag class names.
	 *
	 * @var Gravity_Flow_Assignee_Base[]
	 */
	private static $class_names = array();

	/**
	 * Get an array of registered merge tag class names.
	 *
	 * @return string[]
	 */
	private static function get_class_names() {
		return self::$class_names;
	}

	/**
	 * Register the supplied assignee.
	 *
	 * @param Gravity_Flow_Assignee_Base $assignee The assignee class.
	 *
	 * @throws Exception When the merge tags name property has not been set.
	 */
	public static function register( $assignee ) {

		if ( ! is_subclass_of( $assignee, 'Gravity_Flow_Assignee_Base' ) ) {
			throw new Exception( 'Must be a subclass of Gravity_Flow_Assignee_Base' );
		}
		$name = $assignee->name;

		if ( empty( $name ) ) {
			throw new Exception( 'The name property must be set' );
		}


		self::$class_names[ $assignee->name ] = get_class( $assignee );
	}

	/**
	 * Create the Assignee class, if available.
	 *
	 * @param null|array $args The arguments used to initialize the class.
	 * @param Gravity_Flow_Step $step The step.
	 *
	 * @return Gravity_Flow_Assignee_Base|false
	 */
	public static function create( $args, $step = null ) {

		$type = false;
		if ( is_string( $args ) ) {
			$parts = explode( '|', $args );
			$type  = $parts[0];
		} elseif ( is_array( $args ) ) {
			$type = rgar( $args, 'type' );
		}

		if ( ! $type ) {
			return false;
		}

		$classes = self::get_class_names();

		if ( isset( $classes[ $type ] ) ) {
			$class_name = $classes[ $type ];
			$assignee   = new $class_name( $args, $step );
		} else {
			$assignee = new Gravity_Flow_Assignee( $args, $step );
		}
		return $assignee;
	}

}
