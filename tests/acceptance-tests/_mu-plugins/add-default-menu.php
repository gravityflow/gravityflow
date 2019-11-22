<?php
// Adds default menus so Twenty Twenty theme can work as expected.
add_action( 'init', function () {
	$menu_name   = 'GF Test Menu';
	$menu_exists = wp_get_nav_menu_object( $menu_name );
	if ( ! $menu_exists ) {
		$menu_id = wp_create_nav_menu( $menu_name );
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'   => 'Home',
			'menu-item-classes' => 'home',
			'menu-item-url'     => home_url( '/' ),
			'menu-item-status'  => 'publish',
		) );
	} else {
		$menu_id = $menu_exists->term_id;
	}
	if ( ! has_nav_menu( 'primary' ) ) {
		$locations            = get_theme_mod( 'nav_menu_locations' );
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
} );
