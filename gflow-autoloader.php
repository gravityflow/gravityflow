<?php

/**
 * Config Loader
 */
spl_autoload_register( function ( $class ) {

	// project-specific namespace prefix
	$prefix = 'Gravity_Flow\\Gravity_Flow\\Config';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/config';

	// does the class use the namespace prefix?
	$len = strlen( $prefix );

	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// no, move to the next registered autoloader
		return;
	}

	$file = gflow_autoload_get_file_path( $class, $len, $base_dir );

	// if the file exists, require it
	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/**
 * Includes Loader
 */
spl_autoload_register( function ( $class ) {

	// project-specific namespace prefix
	$prefix = 'Gravity_Flow\\Gravity_Flow\\';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/includes';

	// does the class use the namespace prefix?
	$len = strlen( $prefix );

	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// no, move to the next registered autoloader
		return;
	}

	$file = gflow_autoload_get_file_path( $class, $len, $base_dir );

	// if the file exists, require it
	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/**
 * Get parsed file path from the provided classname and details.
 *
 * @param $class
 * @param $len
 * @param $base_dir
 *
 * @return string
 */
function gflow_autoload_get_file_path( $class, $len, $base_dir ) {
	// get the relative class name
	$relative_class = substr( $class, $len );

	$parts = explode( '\\', $relative_class );

	$parts[ count( $parts ) - 1 ] = 'class-' . $parts[ count( $parts ) - 1 ];

	$relative_class = implode( '\\', $parts );

	$file = strtolower(
		sprintf(
			'%s/%s.php',
			$base_dir,
			str_replace( '\\', '/', str_replace( '_', '-', $relative_class ) )
		)
	);

	return $file;
}