<?php
/**
Plugin Name: Email Logger
Plugin URI: https://gravityflow.io
Description: Creates a post for each notification sent.
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

add_action( 'gform_after_email', 'gravityflow_filter_gform_after_email', 10, 13 );
function gravityflow_filter_gform_after_email( $is_success, $to, $subject, $message, $headers, $attachments, $message_format, $from, $from_name, $bcc, $reply_to, $entry, $cc ) {
	$page = array(
		'post_type'    => 'post',
		'post_content' => $message,
		'post_name'    => sanitize_title_with_dashes( $subject ),
		'post_parent'  => 0,
		'post_author'  => 1,
		'post_status'  => 'publish',
		'post_title'   => $subject,
	);
	wp_insert_post( $page );
}
