<?php
/**
 * Gravity Flow Installation Wizard: Updates Step
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Installation_Wizard
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Class Gravity_Flow_Installation_Wizard_Step_Updates
 */
class Gravity_Flow_Installation_Wizard_Step_Gravity_Forms extends Gravity_Flow_Installation_Wizard_Step {

	/**
	 * The step name.
	 *
	 * @since 2.3.2
	 *
	 * @var string
	 */
	protected $_name = 'gravity_forms';

	/**
	 * Displays the content for this step.
	 *
	 * @since 2.3.2
	 */
	function display() {

		$license_key_step_settings = $this->get_step_settings( 'license_key' );
		$gravityforms_key          = isset( $license_key_step_settings['gravityforms_key'] ) ? $license_key_step_settings['gravityforms_key'] : '';
		$gravityforms_version      = isset( $license_key_step_settings['gravityforms_version'] ) ? $license_key_step_settings['gravityforms_version'] : '';

		$label  = '';
		$action = '';

		$action_required = false;

		$all_plugins = get_plugins();

		$gf_is_installed = isset( $all_plugins['gravityforms/gravityforms.php'] );
		if ( $gf_is_installed ) {
			// Gravity Forms is installed - try to upgrade and activate

			$gf_is_active      = class_exists( 'GFForms' );
			$installed_version = $all_plugins['gravityforms/gravityforms.php']['Version'];
			$upgrade_available = $gravityforms_key && version_compare( $installed_version, $gravityforms_version, '<' );

			if ( $gf_is_active ) {
				if ( $upgrade_available ) {
					/* translators: 1. The installed version 2. the version of the update available */
					$message = sprintf( esc_html__( 'Gravity Forms is installed and activated but there is a newer version available. You have version %1$s installed but the latest version is %2$s. If you would like us to update Gravity Forms to the latest version, check the box below.', 'gravityflow' ), $installed_version, $gravityforms_version );

					$label = esc_html__( 'Yes, update Gravity Forms', 'gravityflow' );

					$action = 'update';

					$action_required = false;
				} else {
					$message = esc_html__( "Gravity Forms is already installed and activated. Click the next button to proceed.", 'gravityflow' );

					$this->action_required = false;

					$this->action = 0;
				}
			} else {
				if ( $upgrade_available ) {
					/* translators: 1. The installed version 2. the version of the update available */
					$message = sprintf( esc_html__( 'Gravity Forms is installed and but it is not activated. There is a newer version available. You have version %1$s installed but the latest version is %2$s. Would you like us to update Gravity Forms to the latest version and activate it?', 'gravityflow' ), $installed_version, $gravityforms_version );

					$label = esc_html__( 'Yes, update and activate Gravity Forms.', 'gravityflow' );

					$action = 'update_and_activate';

					$action_required = true;
				} else {
					$message = esc_html__( 'Gravity Forms is installed and but it is not activated. Would you like us to activate it?', 'gravityflow' );

					$label = esc_html__( 'Yes, activate Gravity Forms.', 'gravityflow' );

					$action = 'activate';

					$action_required = true;
				}
			}

		} else {
			// Gravity Forms isn't installed. Try to download, install and activate it.
			if ( $gravityforms_key ) {
				$message = esc_html__( 'The Gravity Forms form builder plugin is required but it is not installed. Would you like us to download and activate it?', 'gravityflow' );

				$label = esc_html__( 'Yes, download and activate Gravity Forms.', 'gravityflow' );

				$action = 'download';

				$action_required = true;
			} else {
				/* translators: 1. The opening link tag 2. the closing link tag */
				$message = sprintf( esc_html__( 'Gravity Forms is not installed. Please %1$spurchase%2$s and install Gravity Forms before continuing.', 'gravityflow' ), '<a href="https://gravityflow.io/out/gravityforms" target="_blank">', '</a>' );
				$this->disable_next_button = true;
			}
		}
		?>
		<p>
			<?php
			echo $message;
			?>
		</p>

			<?php
			echo sprintf( '<input type="hidden" value="%s" name="action_required" />', $action_required );
			if ( $action ) {
				echo "<div>";
				echo sprintf( '<input id="gravityflow_action" type="hidden" value="%s" name="action" />', $this->action );
				$onclick = "jQuery('#gravityflow_action').val(jQuery(this).prop('checked') ? this.value : 0);";
				$required = $action_required ? '<span class="gfield_required">*</span>': '';
				echo sprintf( '<label><input type="checkbox" id="gravityflow_action" onclick="%s" onkeypress="%s" value="%s" %s />%s%s</label>', $onclick, $onclick, $action, checked( ! empty( $this->action ), true , false ), $label, $required );
				echo "</div>";
				$validation_message = $this->validation_message( 'action', false );
				if ( $validation_message ) {
					echo $validation_message;
				}
			} else {
				echo '<input id="gravityflow_action" type="hidden" value="0" name="action" />';
			}
			?>

		<?php
	}

	/**
	 * Returns the title for this step.
	 *
	 * @since 2.3.2
	 *
	 * @return string
	 */
	function get_title() {
		return esc_html__( 'Install Gravity Forms', 'gravityflow' );
	}

	/**
	 * Validates the posted values for this step.
	 *
	 * @since 2.3.2
	 *
	 * @return bool
	 */
	function validate() {

		if ( $this->action_required && empty( $this->action ) ) {
			$this->set_field_validation_result( 'action', esc_html__( 'This is required in order to complete the installation.', 'gravityflow' ) );

			return false;
		}

		if ( empty( $this->action ) ) {
			return true;
		}

		$valid = true;

		switch ( $this->action ) {
			case 'update':
			case 'update_and_activate':
			case 'download':
				$license_key_step_settings = $this->get_step_settings( 'license_key' );
				$gravityforms_download_url = $license_key_step_settings['gravityforms_download_url'];
				$result                    = $this->install_plugin( $gravityforms_download_url );
				if ( is_wp_error( $result ) ) {
					$this->set_field_validation_result( 'action', $result->get_error_message() );
					$valid = false;
				}
		}

		if ( $valid && $this->action != 'update' ) {
			$result = activate_plugin( 'gravityforms/gravityforms.php' );
			if ( is_wp_error( $result ) ) {
				$this->set_field_validation_result( 'action', $result->get_error_message() );
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * Returns the summary content.
	 *
	 * @since 2.3.2
	 *
	 * @param bool $echo Indicates if the summary should be echoed.
	 *
	 * @return string
	 */
	function summary( $echo = true ) {
		$html = $this->background_updates !== 'disabled' ? esc_html__( 'Enabled', 'gravityflow' ) . '&nbsp;<i class="fa fa-check gf_valid"></i>' : esc_html__( 'Disabled', 'gravityflow' ) . '&nbsp;<i class="fa fa-times gf_invalid"></i>';
		if ( $echo ) {
			echo $html;
		}

		return $html;
	}


	/**
	 * Installs/upgrades Gravity Forms silently given the zip URL.
	 *
	 * @since 2.3.2
	 *
	 * @param $plugin_zip
	 *
	 * @return bool|\WP_Error
	 */
	function install_plugin( $plugin_zip ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once 'class-iw-installation-skin.php';
		wp_cache_flush();

		add_filter( 'upgrader_package_options', array( $this, 'filter_upgrader_package_options' ) );

		$upgrader = new Plugin_Upgrader( new Gravity_Flow_Quiet_Installation_Skin() );

		$result = $upgrader->install( $plugin_zip );

		remove_filter( 'upgrader_package_options', array( $this, 'filter_upgrader_package_options' ) );

		return $result;
	}

	/**
	 * Filters the package options before running an update.
	 *
	 * @since 2.3.2
	 *
	 * @return array
	 */
	function filter_upgrader_package_options( $options ) {
		$options['abort_if_destination_exists'] = false;

		return $options;
	}
}


