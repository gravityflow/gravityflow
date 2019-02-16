<?php
/**
Plugin Name: Due Date Column
Plugin URI: https://gravityflow.io
Description: Ensures due date info is displayed for inbox and status
Version: 1.0
Author: Gravity Flow
Author URI: https://gravityflow.io
License: GPL-2.0+
------------------------------------------------------------------------
Copyright 2015-2019 Steven Henty S.L.

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

add_filter( 'gravityflow_columns_status_table', 'status_column_due_date', 10, 3 );
function status_column_due_date( $columns, $args, $table ) {
	$columns['due_date'] = 'Due Date';
	return $columns;
}

add_filter( 'gravityflow_columns_inbox_table', 'inbox_column_due_date', 10, 2 );
function inbox_column_due_date( $columns, $args ) {
	$columns['due_date'] = 'Due Date';
	return $columns;
}