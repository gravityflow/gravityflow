<?php
// Make sure no widgets are added to the page.
// e.g. The Recent posts widgets can pollute the tests.
remove_action('init', 'wp_widgets_init', 1);
add_action('init', function () {
	do_action('widgets_init');
}, 1);

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
}, 1 );

// Disable header search form.
set_theme_mod( 'enable_header_search', false );

// Display the smooth scrolling effect because it broke our tests.
add_action( 'wp_head', function() {
	echo '<style>html {scroll-behavior: auto !important;} #site-content header{display: none;}</style>';
} );

// Remove comment form.
add_filter( 'comments_open', '__return_false' );
