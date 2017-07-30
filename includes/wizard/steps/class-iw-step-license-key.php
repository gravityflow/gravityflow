<?php

class Gravity_Flow_Installation_Wizard_Step_License_Key extends Gravity_Flow_Installation_Wizard_Step {
	public $required = true;

	protected $_name = 'license_key';

	public function display() {

		if ( ! $this->license_key && defined( 'GRAVITY_FLOW_LICENSE_KEY' ) ) {
			$this->license_key = GRAVITY_FLOW_LICENSE_KEY;
		}

		?>
		<p>
			<?php echo sprintf( esc_html__( 'Enter your Gravity Flow License Key below.  Your key unlocks access to automatic updates and support.  You can find your key in your purchase receipt or by logging into the %sGravity Flow%s site.', 'gravityflow' ), '<a href="http://www.gravityflow.io">', '</a>' ); ?>

		</p>
		<div>
			<input type="text" class="regular-text" id="license_key" value="<?php echo esc_attr( $this->license_key ); ?>" name="license_key" placeholder="<?php esc_attr_e( 'Enter Your License Key', 'gravityflow' ); ?>" />
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
					<input type="checkbox" id="accept_terms" value="1" <?php checked( 1, $this->accept_terms ); ?> name="accept_terms" />
					<?php esc_html_e( 'I understand the risks', 'gravityflow' ); ?> <span class="gfield_required">*</span>
				</label>
				<?php echo $message ?>
			</div>
		<?php
		}
	}

	public function get_title() {
		return esc_html__( 'License Key', 'gravityflow' );
	}

	public function validate( $posted_values ) {

		$valid_key = true;
		$terms_accepted = true;
		$license_key = rgar( $posted_values, 'license_key' );

		if ( empty( $license_key ) ) {
			$message = esc_html__( 'Please enter a valid license key.', 'gravityflow' ) . '</span>';
			$this->set_field_validation_result( 'license_key', $message );
			$valid_key = false;
		} else {
			$license_info = gravity_flow()->activate_license( $license_key );
			if ( empty( $license_info ) ||  $license_info->license !== 'valid' ) {
				$message = "&nbsp;<i class='fa fa-times gf_keystatus_invalid'></i> <span class='gf_keystatus_invalid_text'>" . __( 'Invalid or Expired Key : Please make sure you have entered the correct value and that your key is not expired.', 'gravityflow' ) . '</span>';
				$this->set_field_validation_result( 'license_key', $message );
				$valid_key = false;
			}
		}

		$accept_terms = rgar( $posted_values,  'accept_terms' );
		if ( ! $valid_key && ! $accept_terms ) {
			$this->set_field_validation_result( 'accept_terms', __( 'Please accept the terms.', 'gravityflow' ) );
			$terms_accepted = false;
		}

		$valid = $valid_key || ( ! $valid_key && $terms_accepted );
		return $valid;
	}

	public function install() {
		if ( $this->license_key ) {
			$gravityflow = gravity_flow();

			$settings = $gravityflow->get_app_settings();
			$settings['license_key'] = $this->license_key;
			gravity_flow()->update_app_settings( $settings );
		}
	}

	public function get_previous_button_text() {
		return '';
	}
}
