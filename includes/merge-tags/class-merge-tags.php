<?php

class Gravity_Flow_Merge_Tags {

	private static $class_names = array();

	private static function get_class_names() {
		return self::$class_names;
	}

	/**
	 * @param Gravity_Flow_Merge_Tag $merge_tag
	 *
	 * @throws Exception
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
	 * @param $args
	 *
	 * @return Gravity_Flow_Merge_Tag
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
	 * @param $args
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
