<?php
/**
Plugin Name: Gravity Flow
Plugin URI: https://gravityflow.io
Description: Build Workflow Applications with Gravity Forms.
Version: 2.4.4-dev
Author: Gravity Flow
Author URI: https://gravityflow.io
License: GPL-2.0+
Text Domain: gravityflow
Domain Path: /languages

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

define( 'GRAVITY_FLOW_VERSION', '2.4.4-dev' );

define( 'GRAVITY_FLOW_EDD_STORE_URL', 'https://gravityflow.io' );

define( 'GRAVITY_FLOW_EDD_ITEM_ID', 1473 );

add_action( 'gform_loaded', array( 'Gravity_Flow_Bootstrap', 'load' ), 1 );

/**
 * Class Gravity_Flow_Bootstrap
 */
class Gravity_Flow_Bootstrap {

	/**
	 * Includes the required files and registers the add-on with Gravity Forms.
	 */
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

		if ( class_exists( 'GravityView_Field' ) ) {
			include( dirname( __FILE__ ) . '/includes/class-gravityview-detail-link.php' );
		}

		require_once( dirname( __FILE__ ) . '/includes/class-common.php' );

		require_once( 'includes/class-connected-apps.php' );
		require_once( 'class-gravity-flow.php' );
		require_once( 'includes/models/class-activity.php' );
		require_once( 'includes/integrations/class-gp-nested-forms.php' );

		self::include_assignees();
		self::include_steps();
		self::include_fields();
		self::include_merge_tags();

		GFAddOn::register( 'Gravity_Flow' );
		do_action( 'gravityflow_loaded' );
	}

	/**
	 * Includes the assignee classes.
	 */
	public static function include_assignees() {
		require_once( dirname( __FILE__ ) . '/includes/assignees/class-assignees.php' );
		require_once( dirname( __FILE__ ) . '/includes/assignees/class-assignee.php' );
	}

	/**
	 * Includes the step classes.
	 */
	public static function include_steps() {
		require_once( dirname( __FILE__ ) . '/includes/steps/class-step.php' );
		require_once( dirname( __FILE__ ) . '/includes/steps/class-steps.php' );
		require_once( dirname( __FILE__ ) . '/includes/steps/class-step-feed-add-on.php' );

		foreach ( glob( dirname( __FILE__ ) . '/includes/steps/class-step-*.php' ) as $gravity_flow_filename ) {
			require_once( $gravity_flow_filename );
		}
	}

	/**
	 * Includes the field classes.
	 */
	public static function include_fields() {
		require_once( dirname( __FILE__ ) . '/includes/fields/class-fields.php' );

		foreach ( glob( dirname( __FILE__ ) . '/includes/fields/class-field-*.php' ) as $gravity_flow_filename ) {
			require_once( $gravity_flow_filename );
		}
	}

	/**
	 * Includes the merge tag classes.
	 */
	public static function include_merge_tags() {
		require_once( dirname( __FILE__ ) . '/includes/merge-tags/class-merge-tag.php' );
		require_once( dirname( __FILE__ ) . '/includes/merge-tags/class-merge-tags.php' );

		foreach ( glob( dirname( __FILE__ ) . '/includes/merge-tags/class-merge-tag-*.php' ) as $gravity_flow_filename ) {
			require_once( $gravity_flow_filename );
		}
	}

}

/**
 * Returns an instance of the Gravity_Flow class.
 *
 * @return Gravity_Flow|null
 */
function gravity_flow() {
	if ( class_exists( 'Gravity_Flow' ) ) {
		return Gravity_Flow::get_instance();
	}

	return null;
}

add_action( 'init', 'gravityflow_action_init', 0 );

/**
 * Initialize the EDD plugin updater or prepare the installation wizard.
 */
function gravityflow_action_init() {

	$gravity_flow = gravity_flow();

	if ( $gravity_flow ) {

		if ( defined( 'GRAVITY_FLOW_LICENSE_KEY' ) ) {
			$license_key = GRAVITY_FLOW_LICENSE_KEY;
		} else {
			$settings = gravity_flow()->get_app_settings();

			$license_key = trim( rgar( $settings, 'license_key' ) );
		}

		new Gravity_Flow_EDD_SL_Plugin_Updater( GRAVITY_FLOW_EDD_STORE_URL, __FILE__, array(
			'version' => GRAVITY_FLOW_VERSION,
			'license' => $license_key,
			'item_id' => GRAVITY_FLOW_EDD_ITEM_ID,
			'author'  => 'Steven Henty',
		) );

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'gravityflow-installation') {
			// The installation wizard was initiated before Gravity Forms was activated - allow it to continue on the same page.
			add_action( 'admin_menu', 'gravityflow_create_menu_item' );
		}

	} elseif ( ! is_multisite() && current_user_can( 'manage_options' ) ) {
		// Gravity Forms isn't installed and activated.

		// Add a Gravity Flow menu item
		add_action( 'admin_menu', 'gravityflow_create_menu_item' );
	}
}

/**
 * Creates the menu item for the installation wizard.
 *
 * @since 2.3.2
 */
function gravityflow_create_menu_item() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	add_menu_page( __( 'Gravity Flow', 'gravityflow' ), __( 'Gravity Flow', 'gravityforms' ), 'manage_options', 'gravityflow-installation', 'gravityflow_installation_wizard', gravityflow_icon(), '16.10' );
}

/**
 * Displays the installation wizard. Callback for the Gravity Flow admin menu which is added when Gravity Forms isn't available.
 *
 * @since 2.3.2
 */
function gravityflow_installation_wizard() {
	require_once( 'includes/wizard/class-installation-wizard.php' );
	$wizard = new Gravity_Flow_Installation_Wizard;
	$wizard->display();
}


/**
 * Returns the SVG icon for use in the admin menu.
 *
 * @since 2.3.2
 *
 * @return string
 */
function gravityflow_icon() {
	$svg_xml = '<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg width="100%" height="100%" viewBox="0 20 581 640" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;">
    <g id="Layer 1" transform="matrix(1,0,0,1,-309.5,-180)">
        <g transform="matrix(3.27114,0,0,3.27114,-738.318,-1054.55)">
            <path d="M377.433,481.219L434.396,514.29C444.433,519.437 453.741,520.595 464.421,516.392C477.161,511.373 485.868,500.993 486.898,487.138C487.756,476.115 483.38,464.791 475.273,457.241C465.622,448.191 452.797,446.132 440.272,449.392C437.999,449.864 434.096,451.494 431.179,452.566C429.935,452.995 428.905,453.381 428.262,453.467C423.286,453.982 420.584,447.333 425.045,444.716C434.61,439.097 447.607,437.339 456.272,438.197C466.738,439.355 476.603,443.901 484.152,451.322C493.117,460.201 497.75,472.126 497.407,484.736C496.935,502.623 486.855,517.936 470.469,525.228C460.432,529.646 449.108,530.461 438.685,527.33C434.953,526.214 432.723,524.885 429.334,522.997L371.9,490.784L369.026,495.717L362.163,478.645L380.393,476.158L377.433,481.219Z" style="fill:white;"/>
        </g>
        <g transform="matrix(3.27114,0,0,3.27114,-738.318,-1054.55)">
            <path d="M440.702,485.937L383.782,452.909C373.702,447.762 364.394,446.604 353.714,450.807C341.017,455.826 332.31,466.206 331.237,480.061C330.379,491.084 334.755,502.408 342.862,509.957C352.555,519.008 365.338,521.067 377.906,517.807C380.136,517.335 384.082,515.705 386.956,514.633C388.2,514.204 389.23,513.818 389.916,513.732C394.892,513.217 397.594,519.866 393.09,522.482C383.525,528.101 370.528,529.86 361.863,529.002C351.397,527.844 341.532,523.297 334.025,515.877C325.018,506.998 320.428,495.073 320.728,482.463C321.2,464.576 331.28,449.263 347.709,441.971C357.703,437.553 369.027,436.738 379.45,439.869C383.224,440.984 385.455,442.271 388.801,444.159L446.235,476.415L449.152,471.439L456.015,488.554L437.742,491.041L440.702,485.937Z" style="fill:white;"/>
        </g>
    </g>
</svg>';

	$icon = sprintf( 'data:image/svg+xml;base64,%s', base64_encode( $svg_xml ) );
	return $icon;
}
