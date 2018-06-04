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

add_action( 'gform_after_email', 'sh_gform_after_email', 10, 13 );
function sh_gform_after_email( $is_success, $to, $subject, $message, $headers, $attachments, $message_format, $from, $from_name, $bcc, $reply_to, $entry, $cc ) {
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
