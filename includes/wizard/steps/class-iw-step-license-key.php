<?php
/**
 * Gravity Flow Installation Wizard: License Key Step
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Installation_Wizard
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Class Gravity_Flow_Installation_Wizard_Step_License_Key
 */
class Gravity_Flow_Installation_Wizard_Step_License_Key extends Gravity_Flow_Installation_Wizard_Step {

	/**
	 * Is this step required?
	 *
	 * @var bool
	 */
	public $required = true;

	/**
	 * The step name.
	 *
	 * @var string
	 */
	protected $_name = 'license_key';

	public $defaults = array(
		'license_key'  => '',
		'accept_terms' => false,
	);

	/**
	 * Displays the content for this step.
	 */
	public function display() {

		if ( ! $this->license_key && defined( 'GRAVITY_FLOW_LICENSE_KEY' ) ) {
			$this->license_key = GRAVITY_FLOW_LICENSE_KEY;
		}

		?>
		<p>
			<?php echo sprintf( esc_html__( 'Enter your Gravity Flow License Key below. Your key unlocks access to automatic updates and support. You can find your key in your purchase confirmation email or by logging into your account area on the %sGravity Flow%s site.', 'gravityflow' ), '<a href="http://www.gravityflow.io/account">', '</a>' ); ?>
		</p>
		<div>
			<input type="text" class="regular-text" id="license_key"
			       value="<?php echo esc_attr( $this->license_key ); ?>" name="license_key"
			       placeholder="<?php esc_attr_e( 'Enter Your License Key', 'gravityflow' ); ?>"/>
			<?php
			$key_error = $this->validation_message( 'license_key', false );
			if ( $key_error ) {
				echo $key_error;
			}
			?>
		</div>

		<?php
		$message = $this->validation_message( 'accept_terms', false );
		if ( $message || $key_error || $this->accept_terms ) {
			?>
			<p>
				<?php esc_html_e( "If you don't enter a valid license key, you will not be able to update Gravity Flow when important bug fixes and security enhancements are released. This can be a serious security risk for your site.", 'gravityflow' ); ?>
			</p>
			<div>
				<label>
					<input type="checkbox" id="accept_terms" value="1" <?php checked( 1, $this->accept_terms ); ?>
					       name="accept_terms"/>
					<?php esc_html_e( 'I understand the risks', 'gravityflow' ); ?> <span
							class="gfield_required">*</span>
				</label>
				<?php echo $message ?>
			</div>
			<?php
		}
	}

	/**
	 * Returns the title for this step.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'License Key', 'gravityflow' );
	}

	/**
	 * Validates the posted values for this step.
	 *
	 * @return bool
	 */
	public function validate() {

		$this->is_valid_key = true;

		$license_key = $this->license_key;

		if ( empty ( $license_key ) ) {
			$message = esc_html__( 'Please enter a valid license key.', 'gravityflow' ) . '</span>';
			$this->set_field_validation_result( 'license_key', $message );
			$this->is_valid_key = false;
		} else {
			$license_info = $this->activate_license( $license_key );
			if ( empty( $license_info ) || $license_info->license !== 'valid' ) {
				$message = "&nbsp;<i class='fa fa-times gf_keystatus_invalid'></i> <span class='gf_keystatus_invalid_text'>" . __( 'Invalid or Expired Key : Please make sure you have entered the correct value and that your key is not expired.', 'gravityflow' ) . '</span>';
				$this->set_field_validation_result( 'license_key', $message );
				$this->is_valid_key = false;
			} elseif ( $license_info->license === 'valid') {
				$this->gravityforms_key          = isset( $license_info->gravityforms_key ) && $license_info->gravityforms_key != 'not_eligible' ? $license_info->gravityforms_key : '';
				$this->gravityforms_download_url = isset( $license_info->gravityforms_download_url ) ? $license_info->gravityforms_download_url : '';
				$this->gravityforms_version      = isset( $license_info->gravityforms_version ) ? $license_info->gravityforms_version : '';
				$this->update();
			}
		}

		if ( ! $this->is_valid_key && ! $this->accept_terms ) {
			$this->set_field_validation_result( 'accept_terms', __( 'Please accept the terms', 'gravityforms' ) );
		}

		$valid = $this->is_valid_key || ( ! $this->is_valid_key && $this->accept_terms );

		return $valid;
	}

	/**
	 * Installs the license key, if supplied. Also saves the Gravity Forms Starter License key if eligible.
	 */
	public function install() {
		if ( $this->license_key ) {

			$settings                = gravity_flow()->get_app_settings();
			$settings['license_key'] = $this->license_key;
			gravity_flow()->update_app_settings( $settings );

			if ( $this->gravityforms_key && $this->gravityforms_download_url ) {
				$current_key = get_option( 'rg_gforms_key' );
				if ( empty( $current_key ) ) {
					GFFormsModel::save_key( $this->gravityforms_key );
					GFCommon::cache_remote_message();
					update_option( 'gform_pending_installation', false );
					update_option( 'rg_gforms_currency', 'USD' );
				}
			}
		}
	}

	/**
	 * Returns the previous button label.
	 *
	 * @return string
	 */
	public function get_previous_button_text() {
		return '';
	}

	/**
	 * Activates the license key for this site and clears the cached version info,
	 *
	 * @since 2.3.2
	 *
	 * @param string $license_key The license key to be activated.
	 *
	 * @return array|object
	 */
	public function activate_license( $license_key ) {
		$response = $this->perform_edd_license_request( 'activate_license', $license_key );

		set_site_transient( 'update_plugins', null );
		$path      = sanitize_key( 'gravityflow/gravityflow.php' );
		$cache_key = md5( 'edd_plugin_' . $path . '_version_info' );
		delete_transient( $cache_key );

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Send a request to the EDD store url.
	 *
	 * @since 2.3.2
	 *
	 * @param string     $edd_action      The action to perform (check_license, activate_license or
	 *                                    deactivate_license).
	 * @param string     $license         The license key.
	 * @param string|int $item_name_or_id The EDD item name. Defaults to the value of the GRAVITY_FLOW_EDD_ITEM_NAME
	 *                                    constant.
	 *
	 * @return array|WP_Error The response.
	 */
	public function perform_edd_license_request( $edd_action, $license, $item_name_or_id = GRAVITY_FLOW_EDD_ITEM_ID ) {
		$request_gravityforms_key = true;
		if ( class_exists( 'GFCommon' ) ) {
			// Only request a Gravity Forms Start license key if the current key is not valid.
			$gravityfroms_version_info = GFCommon::get_version_info();
			$valid_gravityforms_key = isset( $gravityfroms_version_info['is_valid_key'] ) ? $gravityfroms_version_info['is_valid_key'] : '';
			$request_gravityforms_key = ! $valid_gravityforms_key;
		}

		// Prepare the request arguments.
		$args = array(
			'timeout'   => 10,
			'sslverify' => true,
			'body'      => array(
				'edd_action'               => $edd_action,
				'license'                  => trim( $license ),
				'url'                      => home_url(),
				'request_gravityforms_key' => $request_gravityforms_key,
			),
		);

		if ( is_numeric( $item_name_or_id ) ) {
			$args['body']['item_id'] = $item_name_or_id;
		} else {
			$args['body']['item_name'] = urlencode( $item_name_or_id );
		}

		// Send the remote request.
		$response = wp_remote_post( GRAVITY_FLOW_EDD_STORE_URL, $args );

		return $response;
	}
}
