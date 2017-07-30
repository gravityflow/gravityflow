<?php
/*
Plugin Name: Gravity Flow
Plugin URI: https://gravityflow.io
Description: Build Workflow Applications with Gravity Forms.
Version: 1.8.1-dev
Author: Gravity Flow
Author URI: http://www.gravityflow.io
License: GPL-3.0+
Text Domain: gravityflow
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2015-2017 Steven Henty S.L.

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

define( 'GRAVITY_FLOW_VERSION', '1.8.1-dev' );

define( 'GRAVITY_FLOW_EDD_STORE_URL', 'https://gravityflow.io' );

define( 'GRAVITY_FLOW_EDD_ITEM_NAME', 'Gravity Flow' );

add_action( 'gform_loaded', array( 'Gravity_Flow_Bootstrap', 'load' ), 1 );

class Gravity_Flow_Bootstrap {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		if ( ! class_exists( 'Gravity_Flow_EDD_SL_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/includes/EDD_SL_Plugin_Updater.php' );
		}

		if ( ! class_exists( 'Gravity_Flow_API' ) ) {
			include( dirname( __FILE__ ) . '/includes/class-api.php' );
		}

		if ( ! class_exists( 'Gravity_Flow_Web_API' ) ) {
			include( dirname( __FILE__ ) . '/includes/class-web-api.php' );
		}

		if ( ! class_exists( 'Gravity_Flow_REST_API' ) ) {
			include( dirname( __FILE__ ) . '/includes/class-rest-api.php' );
		}

		if ( ! class_exists( 'Gravity_Flow_Extension' ) ) {
			include( dirname( __FILE__ ) . '/includes/class-extension.php' );
		}

		if ( ! class_exists( 'Gravity_Flow_Feed_Extension' ) ) {
			include( dirname( __FILE__ ) . '/includes/class-feed-extension.php' );
		}

		if ( ! class_exists( 'Gravity_Flow_Assignee' ) ) {
			include( dirname( __FILE__ ) . '/includes/class-assignee.php' );
		}

		if ( class_exists( 'GravityView_Field' ) ) {
			include( dirname( __FILE__ ) . '/includes/class-gravityview-detail-link.php' );
		}

		require_once( dirname( __FILE__ ) . '/includes/class-common.php' );

		require_once( 'class-gravity-flow.php' );
		require_once( 'includes/models/class-activity.php' );

		self::include_steps();
		self::include_fields();
		self::include_merge_tags();

		GFAddOn::register( 'Gravity_Flow' );
		do_action( 'gravityflow_loaded' );
	}

	public static function include_steps() {
		require_once( dirname( __FILE__ ) . '/includes/steps/class-step.php' );
		require_once( dirname( __FILE__ ) . '/includes/steps/class-steps.php' );
		require_once( dirname( __FILE__ ) . '/includes/steps/class-step-feed-add-on.php' );

		foreach ( glob( dirname( __FILE__ ) . '/includes/steps/class-step-*.php' ) as $gravity_flow_filename ) {
			require_once( $gravity_flow_filename );
		}
	}

	public static function include_fields() {
		require_once( dirname( __FILE__ ) . '/includes/fields/class-fields.php' );

		foreach ( glob( dirname( __FILE__ ) . '/includes/fields/class-field-*.php' ) as $gravity_flow_filename ) {
			require_once( $gravity_flow_filename );
		}
	}

	public static function include_merge_tags() {
		require_once( dirname( __FILE__ ) . '/includes/merge-tags/class-merge-tag.php' );
		require_once( dirname( __FILE__ ) . '/includes/merge-tags/class-merge-tags.php' );

		foreach ( glob( dirname( __FILE__ ) . '/includes/merge-tags/class-merge-tag-*.php' ) as $gravity_flow_filename ) {
			require_once( $gravity_flow_filename );
		}
	}

}

function gravity_flow() {
	if ( class_exists( 'Gravity_Flow' ) ) {
		return Gravity_Flow::get_instance();
	}
}

add_action( 'init', 'gravityflow_edd_plugin_updater', 0 );

function gravityflow_edd_plugin_updater() {

	$gravity_flow = gravity_flow();
	if ( $gravity_flow ) {

		if ( defined( 'GRAVITY_FLOW_LICENSE_KEY' ) ) {
			$license_key = GRAVITY_FLOW_LICENSE_KEY;
		} else {
			$settings = gravity_flow()->get_app_settings();

			$license_key = trim( rgar( $settings, 'license_key' ) );
		}

		new Gravity_Flow_EDD_SL_Plugin_Updater( GRAVITY_FLOW_EDD_STORE_URL, __FILE__, array(
			'version'   => GRAVITY_FLOW_VERSION,
			'license'   => $license_key,
			'item_name' => GRAVITY_FLOW_EDD_ITEM_NAME,
			'author'    => 'Steven Henty',
		) );
	}

}

