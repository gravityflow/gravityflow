<?php
/**
 * Gravity Flow Extension Base
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/ExtensionBase
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


if ( ! class_exists( 'GFForms' ) ) {
	die();
}
GFForms::include_addon_framework();

abstract class Gravity_Flow_Extension extends GFAddOn {

	public $edd_item_name = '';

	public function init() {
		parent::init();
		add_filter( 'gravityflow_menu_items', array( $this, 'menu_items' ) );
		add_filter( 'gravityflow_toolbar_menu_items', array( $this, 'toolbar_menu_items' ) );
	}

	public function init_admin() {
		parent::init_admin();
		add_filter( 'gravityflow_settings_menu_tabs', array( $this, 'app_settings_tabs' ) );

		// Members 2.0+ Integration.
		if ( function_exists( 'members_register_cap_group' ) ) {
			remove_filter( 'members_get_capabilities', array( $this, 'members_get_capabilities' ) );
			add_filter( 'gravityflow_members_capabilities', array( $this, 'get_members_capabilities' ) );
		}
	}

	/**
	 * Add the extension capabilities to the Gravity Flow group in Members.
	 *
	 * Override to provide human readable labels.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param array $caps The capabilities and their human readable labels.
	 *
	 * @return array
	 */
	public function get_members_capabilities( $caps ) {
		foreach ( $this->_capabilities as $capability ) {
			$caps[ $capability ] = $capability;
		}

		return $caps;
	}

	public function app_settings_tabs( $settings_tabs ) {

		$callback = 'app_settings_tab';

		if ( is_callable( array( $this, 'meets_minimum_requirements' ) ) ) {
			$meets_requirements = $this->meets_minimum_requirements();

			if ( ! $meets_requirements['meets_requirements'] ) {
				$callback = 'failed_requirements_page';
			}
		}

		$settings_tabs[] = array(
			'name'     => $this->_slug,
			'label'    => $this->get_short_title(),
			'callback' => array( $this, $callback ),
		);

		return $settings_tabs;
	}

	public function app_settings_tab() {

		require_once( GFCommon::get_base_path() . '/tooltips.php' );

		$icon = $this->app_settings_icon();
		if ( empty( $icon ) ) {
			$icon = '<i class="fa fa-cogs"></i>';
		}
		?>

		<h3><span><?php echo $icon ?><?php echo $this->app_settings_title() ?></span></h3>

		<?php

		if ( $this->maybe_uninstall() ) {
			?>
			<div class="push-alert-gold" style="border-left: 1px solid #E6DB55; border-right: 1px solid #E6DB55;">
				<?php printf( esc_html_x( '%s has been successfully uninstalled. It can be re-activated from the %splugins page%s.', 'Displayed on the settings page after uninstalling a Gravity Flow extension.', 'gravityforms' ), esc_html( $this->_title ), "<a href='plugins.php'>", '</a>' ); ?>
			</div>
			<?php
		} else {
			//saves settings page if save button was pressed
			$this->maybe_save_app_settings();

			//reads main addon settings
			$settings = $this->get_app_settings();
			$this->set_settings( $settings );

			//reading addon fields
			$sections = $this->app_settings_fields();

			GFCommon::display_admin_message();

			//rendering settings based on fields and current settings
			$this->render_settings( $sections );

			$this->render_uninstall();

		}
	}

	/**
	 * Override this function to customize the markup for the uninstall section on the plugin settings page
	 */
	public function render_uninstall() {

		?>
		<form action="" method="post">
			<?php wp_nonce_field( 'uninstall', 'gf_addon_uninstall' ) ?>
			<?php if ( $this->current_user_can_any( $this->_capabilities_uninstall ) ) { ?>

				<div class="hr-divider"></div>

				<h3><span><i
							class="fa fa-times"></i> <?php printf( esc_html_x( 'Uninstall %s Extension', 'Title for the uninstall section on the settings page for a Gravity Flow extension.', 'gravityflow' ), $this->get_short_title() ) ?></span>
				</h3>
				<div class="delete-alert alert_red">
					<h3><i class="fa fa-exclamation-triangle gf_invalid"></i> Warning</h3>

					<div class="gf_delete_notice">
						<?php echo $this->uninstall_warning_message() ?>
					</div>
					<input type="submit" name="uninstall"
					       value="<?php echo esc_attr_x( 'Uninstall Extension', 'Button text on the settings page for an extension.', 'gravityflow' ) ?>"
					       class="button"
					       onclick="return confirm('<?php echo esc_js( $this->uninstall_confirm_message() ); ?>');">
				</div>

			<?php } ?>
		</form>
		<?php
	}

	public function app_settings_fields() {
		return array(
			array(
				'title'  => $this->get_short_title(),
				'fields' => array(
					array(
						'name'                => 'license_key',
						'label'               => esc_html__( 'License Key', 'gravityflow' ),
						'type'                => 'text',
						'validation_callback' => array( $this, 'license_validation' ),
						'feedback_callback'   => array( $this, 'license_feedback' ),
						'error_message'       => __( 'Invalid license', 'gravityflow' ),
						'class'               => 'large',
						'default_value'       => '',
					),
				),
			),
		);
	}

	public function get_app_settings() {
		return parent::get_app_settings();
	}

	public function license_feedback( $value, $field ) {

		if ( empty( $value ) ) {
			return null;
		}

		$license_data = $this->check_license( $value );

		$valid = null;
		if ( empty( $license_data ) || $license_data->license == 'invalid' ) {
			$valid = false;
		} elseif ( $license_data->license == 'valid' ) {
			$valid = true;
		}

		return $valid;

	}

	public function check_license( $value ) {
		$response = gravity_flow()->perform_edd_license_request( 'check_license', $value, $this->edd_item_name );

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	public function license_validation( $field, $field_setting ) {
		$old_license = $this->get_app_setting( 'license_key' );

		if ( $old_license && $field_setting != $old_license ) {
			$response = gravity_flow()->perform_edd_license_request( 'deactivate_license', $old_license, $this->edd_item_name );
			$this->log_debug( __METHOD__ . '(): response: ' . print_r( $response, 1 ) );
		}

		if ( empty( $field_setting ) ) {
			return;
		}

		$this->activate_license( $field_setting );

	}

	public function activate_license( $license_key ) {
		$response = gravity_flow()->perform_edd_license_request( 'activate_license', $license_key, $this->edd_item_name );

		// Force plugins page to refresh the update info.
		set_site_transient( 'update_plugins', null );
		$cache_key = md5( 'edd_plugin_' . sanitize_key( $this->_path ) . '_version_info' );
		delete_transient( $cache_key );

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	public function menu_items( $menu_items ) {
		return $menu_items;
	}

	public function toolbar_menu_items( $menu_items ) {
		return $menu_items;
	}

	/**
	 * Prevent the failed requirements page being added to the Forms > Settings area.
	 * Add the settings link to the installed plugins page.
	 *
	 * @since 1.7.1-dev
	 */
	public function failed_requirements_init() {
		add_filter( 'plugin_action_links', array( $this, 'plugin_settings_link' ), 10, 2 );
	}

	/**
	 * Add the settings link for the extension to the installed plugins page.
	 *
	 * @param array  $links An array of plugin action links.
	 * @param string $file  Path to the plugin file relative to the plugins directory.
	 *
	 * @since 1.7.1-dev
	 *
	 * @return array
	 */
	public function plugin_settings_link( $links, $file ) {
		if ( $file != $this->_path ) {
			return $links;
		}

		array_unshift( $links, '<a href="' . admin_url( 'admin.php' ) . '?page=gravityflow_settings&view=' . $this->_slug . '">' . esc_html__( 'Settings', 'gravityflow' ) . '</a>' );

		return $links;
	}
}
