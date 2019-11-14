<?php
// Prevents the Twenty Twenty theme using it's default menu that lists all the pages.
add_filter( 'has_nav_menu', function ( $has_nav_menu, $location ) {
	if ( $location === 'primary' ) {
		return true;
	}
	return $has_nav_menu;
}, 10, 2 );
