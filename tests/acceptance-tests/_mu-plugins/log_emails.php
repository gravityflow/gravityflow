<?php
/**
Plugin Name: Email Logger
Plugin URI: https://gravityflow.io
Description: Creates a post for each notification sent.
Version: 1.0
Author: Gravity Flow
Author URI: https://gravityflow.io
License: GPL-2.0+
*/

add_filter( 'gravityflow_notification', 'sh_gravityflow_notification', 10, 4 );
function sh_gravityflow_notification ( $notification, $form, $entry, $step ) {
	$page = array(
		'post_type'    => 'post',
		'post_content' => $notification['message'],
		'post_name'    => sanitize_title_with_dashes( $notification['subject'] ),
		'post_parent'  => 0,
		'post_author'  => 1,
		'post_status'  => 'publish',
		'post_title'   => $notification['subject'],
	);
	wp_insert_post( $page );
	return $notification;
}
