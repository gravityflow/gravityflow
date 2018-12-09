<?php
/**
Plugin Name: Remove Widgets
Plugin URI: https://gravityflow.io
Description: Removes widgets to prevent polluting the tests.
Version: 1.0
Author: Gravity Flow
Author URI: https://gravityflow.io
License: GPL-2.0+
------------------------------------------------------------------------
Copyright 2015-2018 Steven Henty S.L.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses.
 */

// Make sure no widgets are added to the page.
// e.g. The Recent posts widgets can pollute the tests.
remove_action( 'init', 'wp_widgets_init', 1 );
add_action( 'init', function () {
	do_action( 'widgets_init' );
}, 1 );