<?php
/**
 * Gravity Flow Merge Tags
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tags
 *
 * @since 1.7.1-dev
 */
class Gravity_Flow_Merge_Tags {

	/**
	 * The merge tag class names.
	 *
	 * @var Gravity_Flow_Merge_Tag[]
	 */
	private static $class_names = array();

	/**
	 * Get an array of registered merge tag class names.
	 *
	 * @return Gravity_Flow_Merge_Tag[]
	 */
	private static function get_class_names() {
		return self::$class_names;
	}

	/**
	 * Register the supplied merge tag.
	 *
	 * @param Gravity_Flow_Merge_Tag $merge_tag The merge tag class.
	 *
	 * @throws Exception When the merge tags name property has not been set.
	 */
	public static function register( $merge_tag ) {

		if ( ! is_subclass_of( $merge_tag, 'Gravity_Flow_Merge_Tag' ) ) {
			throw new Exception( 'Must be a subclass of Gravity_Flow_Merge_Tag' );
		}
		$name = $merge_tag->name;

		if ( empty( $name ) ) {
			throw new Exception( 'The name property must be set' );
		}

		self::$class_names[ $merge_tag->name ] = get_class( $merge_tag );
	}

	/**
	 * Get the specified merge tag class, if available.
	 *
	 * @param string     $name The merge tag class name.
	 * @param null|array $args The arguments used to initialize the class.
	 *
	 * @return Gravity_Flow_Merge_Tag|false
	 */
	public static function get( $name, $args ) {
		$classes = self::get_class_names();
		$merge_tag = false;
		if ( isset( $classes[ $name ] ) ) {
			$class_name = $classes[ $name ];
			$merge_tag = new $class_name( $args );
		}
		return $merge_tag;
	}

	/**
	 * Get an array of registered merge tag classes.
	 *
	 * @param null|array $args The arguments used to initialize the class.
	 *
	 * @return Gravity_Flow_Merge_Tag[]
	 */
	public static function get_all( $args ) {
		$merge_tags = array();
		foreach ( self::get_class_names() as $key => $class_name ) {
			$merge_tags[ $key ] = new $class_name( $args );
		}
		return $merge_tags;
	}
}
