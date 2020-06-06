<?php

global $wp_rewrite, $wpdb;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
$wp_rewrite->flush_rules();


GFFormsModel::drop_tables();

// Flush out the feeds
$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}gf_addon_feed" );
GFForms::setup( true );
update_option( 'gform_pending_installation', false );
update_option( 'gravityflow_pending_installation', false );


function setup_gravity_forms_pages() {
	require_once( GFCommon::get_base_path() . '/export.php' );

	$form_filenames = glob( dirname( __FILE__ ) . '/../_data/forms/*.json' );

	foreach ( $form_filenames as $filename ) {
		GFExport::import_file( $filename );
	}

	$active_forms = GFAPI::get_forms( true );
	echo "\nActive Form count: " . count( $active_forms );
	$inactive_forms = GFAPI::get_forms( false );
	echo "\nInactive Form count: " . count( $inactive_forms );
	$forms = array_merge( $active_forms, $inactive_forms );
	echo "\nForm count: " . count( $forms );
	foreach ( $forms as $form ) {
		GFFormsModel::update_form_active( $form['id'], true );
		$page = array(
			'post_type'    => 'page',
			'post_content' => '[gravityform id=' . $form['id'] . ']',
			'post_name'    => sanitize_title_with_dashes( $form['title'] ),
			'post_parent'  => 0,
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_title'   => $form['title'],
		);
		wp_insert_post( $page );
	}
}

setup_gravity_forms_pages();

// add admins
function tests_create_testing_users() {
	$admins = array( 'admin1', 'admin2', 'admin3', 'admin4', 'admin5' );
	foreach ( $admins as $admin ) {
		$userData = array(
			'user_login' => $admin,
			'first_name' => $admin,
			'last_name'  => $admin,
			'user_pass'  => $admin,
			'user_email' => $admin . '@mail.com',
			'user_url'   => '',
			'role'       => 'administrator',
		);
		wp_insert_user( $userData );
	}
	$subscribers = array( 'subscriber', 'subscriber2' );

	foreach ( $subscribers as $subscriber ) {
		$userData = array(
			'user_login' => $subscriber,
			'first_name' => $subscriber,
			'last_name'  => $subscriber,
			'user_pass'  => $subscriber,
			'user_email' => $subscriber . '@mail.com',
			'user_url'   => '',
			'role'       => 'subscriber',
		);
		wp_insert_user( $userData );
	}

}

tests_create_testing_users();

function copy_to_dir( $pattern, $dir ) {
	foreach ( glob( $pattern ) as $file ) {
		if ( ! is_dir( $file ) && is_readable( $file ) ) {
			$dest = realpath( $dir ) . DIRECTORY_SEPARATOR . basename( $file );
			copy( $file, $dest );
		}
	}
}

if ( ! is_dir( WPMU_PLUGIN_DIR ) ) {
	mkdir( WPMU_PLUGIN_DIR );
};


echo "\nCopying mu plugins to " . WP_CONTENT_DIR . '/mu-plugins';
copy_to_dir( dirname( dirname( __FILE__ ) ) . '/_mu-plugins/*.php', WPMU_PLUGIN_DIR );


$settings                = gravity_flow()->get_app_settings();
$settings['inbox_page']  = create_workflow_page( 'inbox' );
$settings['status_page'] = create_workflow_page( 'status' );
$settings['submit_page'] = create_workflow_page( 'submit' );
gravity_flow()->update_app_settings( $settings );

create_workflow_page( 'reports' );

/**
 * Creates a new page containing the gravityflow shortcode for the specified page type.
 *
 * @param string $page The page type: inbox, status, or submit.
 *
 * @return int|string|WP_Error
 */
function create_workflow_page( $page ) {
	$post = array(
		'post_title'   => $page,
		'post_content' => sprintf( '[gravityflow page="%s"]', $page ),
		'post_excerpt' => $page,
		'post_status'  => 'publish',
		'post_type'    => 'page',
	);

	$post_id = wp_insert_post( $post );

	return $post_id ? $post_id : '';
}

