<?php

/**
 * Class Gravity_Flow_JS_Config
 *
 * Contains methods to add configuration objects to JS files.
 *
 * @since 2.7.1-dev
 */
class Gravity_Flow_JS_Config {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_admin_config' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_theme_config' ), 999 );
	}

	/**
	 * Localize admin config object to the scripts-admin.js file.
	 */
	public function localize_admin_config() {
		$admin  = $this->admin_config();
		$shared = $this->shared_config();

		$config = array_merge( $admin, $shared );

		wp_localize_script( Gravity_Flow::ADMIN_JS, 'gflow_config', $config );
	}

	/**
	 * Localize theme config object to the theme-scripts.js file.
	 */
	public function localize_theme_config() {
		$theme  = $this->theme_config();
		$shared = $this->shared_config();

		$config = array_merge( $theme, $shared );

		wp_localize_script( Gravity_Flow::THEME_JS, 'gflow_config', $config );
	}

	/**
	 * Configuration values to be shared between both the theme and admin JS files. Useful for things like
	 * file paths, ajax endpoints, etc.
	 *
	 * @return array
	 */
	protected function shared_config() {
		$config = array(
			'script_debug' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? 1 : 0,
			'hmr_dev'      => defined( 'HMR_DEV' ) && HMR_DEV === true ? 1 : 0,
		);

		/**
		 * Allows third-party code to modify the config array sent to both admin and theme JS.
		 *
		 * @param array $config
		 */
		return apply_filters( 'gravityflow_js_config_shared', $config );
	}

	/**
	 * Configuration values to be used within the admin JS file.
	 *
	 * @return array
	 */
	protected function admin_config() {
		$config = array();

		/**
		 * Allows third-party code to modify the config array sent to admin JS.
		 *
		 * @param array $config
		 */
		return apply_filters( 'gravityflow_js_config_admin', $config );
	}

	/**
	 * Configuration values to be used within the theme JS file.
	 *
	 * @return array
	 */
	protected function theme_config() {
		$config = array();

		/**
		 * Allows third-party code to modify the config array sent to theme JS.
		 *
		 * @param array $config
		 */
		return apply_filters( 'gravityflow_js_config_theme', $config );
	}

}