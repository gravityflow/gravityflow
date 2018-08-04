<?php
/**
 * Gravity Flow Support
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Support
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Support
 *
 * @since 1.0
 */
class Gravity_Flow_Support {

	/**
	 * Displays the support page content.
	 */
	public static function display() {

		$license_message = '';

		$license_key = gravity_flow()->get_app_setting( 'license_key' );

		if ( empty( $license_key ) ) {
			$activate_url = admin_url( 'admin.php?page=gravityflow_settings' );
			/* Translators: the placeholders are link tags pointing to the Gravity Flow settings page */
			$license_message = sprintf( esc_html__( 'Please %1$sactivate%2$s your license to access this page.', 'gravityflow' ), "<a href=\"{$activate_url}\">", '</a>' );
		} else {
			$response = gravity_flow()->perform_edd_license_request( 'check_license', $license_key );
			if ( is_wp_error( $response ) ) {
				$license_message = esc_html__( 'A valid license key is required to access support but there was a problem validating your license key. Please log in to GravityFlow.io and open a support ticket.', 'gravityflow' );
			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				$valid = null;
				if ( empty( $license_data ) || $license_data->license == 'invalid' ) {
					$license_message = esc_html__( 'Invalid license key. A valid license key is required to access support. Please check the status of your license key in your account area on GravityFlow.io.', 'gravityflow' );
				}
			}
		}

		if ( ! empty( $license_message ) ) {
			GFCommon::add_message( $license_message, true );
			?>
			<div class="wrap gf_entry_wrap">
				<h2 class="gf_admin_page_title">
					<span><?php esc_html_e( 'Gravity Flow Support', 'gravityflow' ); ?></span>
				</h2>
			<?php
			GFCommon::display_admin_message();
			?>
			</div>
			<?php
			return;
		}

		$message = '';

		if ( isset( $_POST['gravityflow_send_feedback'] ) ) {
			check_admin_referer( 'gravityflow_feedback' );

			$system_info = isset( $_POST['gravityflow_debug_info'] ) ? self::get_site_info() : '';

			$body = array(
				'input_values' => array(
					'input_9_3' => rgpost( 'gravityflow_first_name' ),
					'input_9_6' => rgpost( 'gravityflow_last_name' ),
					'input_2' => rgpost( 'gravityflow_email' ),
					'input_4' => rgpost( 'gravityflow_subject' ),
					'input_7' => rgpost( 'gravityflow_brief_description' ),
					'input_3' => rgpost( 'gravityflow_description' ),
					'input_5' => $system_info,
				),
			);
			$body_json = json_encode( $body );

			$options = array(
				'method'      => 'POST',
				'timeout'     => 30,
				'redirection' => 5,
				'blocking'    => true,
				'sslverify'   => false,
				'headers'     => array(),
				'body'        => $body_json,
				'cookies'     => array(),
			);

			$raw_response = wp_remote_post( 'https://gravityflow.io/gravityformsapi/forms/3/submissions/', $options );

			if ( is_wp_error( $raw_response ) ) {
				$message = '<div class="error notice notice-error is-dismissible below-h2"><p>' . esc_html__( 'There was a problem submitting your request. Please open a support ticket on GravityFlow.io', 'gravityflow' ) . '</p></div>';
			}
			$response_json = wp_remote_retrieve_body( $raw_response );

			$response = json_decode( $response_json, true );

			if ( rgar( $response, 'status' ) == '200' ) {
				$message = '<div class="updated notice notice-success is-dismissible below-h2"><p>' . esc_html__( 'Thank you! We\'ll be in touch soon.', 'gravityflow' ) . '</p></div>';
			}
		}

		$user = wp_get_current_user();

		?>
		<style>
			.gravityflow_feedback_form label {
				padding: 20px 0 10px;
				display: block;
				font-weight: bold;
			}
		</style>
		<div class="wrap gf_entry_wrap">
			<h2 class="gf_admin_page_title">
				<span><?php esc_html_e( 'Gravity Flow Support', 'gravityflow' ); ?></span>

			</h2>
			<p>
				<?php esc_html_e( 'Please check the documentation before submitting a support request', 'gravityflow' ); ?>
			</p>
			<p>
				<a href="http://docs.gravityflow.io">http://docs.gravityflow.io</a>
			</p>
			<hr />

			<?php echo $message; ?>
			<form action="" method="POST">
				<?php
				wp_nonce_field( 'gravityflow_feedback' );
				?>
				<div class="gravityflow_feedback_form">

					<label for="gravityflow_first_name">
						<?php esc_html_e( 'Name', 'gravityflow' ); ?>
					</label>

					<input id="gravityflow_first_name" type="text" class="regular-text" name="gravityflow_first_name" value="<?php echo $user->user_firstname; ?>" placeholder="<?php esc_attr_e( 'First Name', 'gravityflow' ); ?>"/>
					<input id="gravityflow_last_name" type="text" class="regular-text" name="gravityflow_last_name" value="<?php echo $user->user_lastname; ?>" placeholder="<?php esc_attr_e( 'Last Name', 'gravityflow' ); ?>"/>

					<label for="gravityflow_email">
						<?php esc_html_e( 'Purchase Email', 'gravityflow' ); ?>
					</label>

					<input id="gravityflow_email" type="email" class="regular-text" name="gravityflow_email" value="<?php echo self::get_email(); ?>"/>

					<label for="gravityflow_subject_suggestion">
						<input id="gravityflow_subject_suggestion" type="radio" name="gravityflow_subject" value="suggestion" checked="checked"/>
						<?php esc_html_e( 'General comment or suggestion', 'gravityflow' ); ?>
					</label>

					<label for="gravityflow_subject_feature_request">
						<input id="gravityflow_subject_feature_request" type="radio" name="gravityflow_subject" value="feature request"/>
						<?php esc_html_e( 'Feature request', 'gravityflow' ); ?>
					</label>

					<label for="gravityflow_subject_bug_report">
						<input id="gravityflow_subject_bug_report" type="radio" name="gravityflow_subject" value="bug report"/>
						<?php esc_html_e( 'Bug report', 'gravityflow' ); ?>
					</label>

					<label for="gravityflow_brief_description">
						<?php esc_html_e( 'Subject', 'gravityflow' ); ?>
					</label>

					<input id="gravityflow_brief_description" type="text" class="regular-text" name="gravityflow_brief_description" />


					<label for="gravityflow_description">
						<?php esc_html_e( 'Suggestion or steps to reproduce the issue.', 'gravityflow' ); ?>
					</label>

					<textarea id="gravityflow_description" name="gravityflow_description" class="widefat" cols="50" rows="10"></textarea>
					<label>
						<input type="checkbox" name="gravityflow_debug_info" value="1" checked="checked"/>
						<?php esc_html_e( 'Send debugging information. (This includes some system information and a list of active plugins. No forms or entry data will be sent.)', 'gravityflow' ); ?>
					</label>
					<br /><br />
					<input id="gravityflow_send" type="submit" class="button button-primary button-large" name="gravityflow_send_feedback" value="<?php esc_html_e( 'Send', 'gravityflow' ); ?>" />

				</div>
			</form>
		</div>
	<?php

	}

	/**
	 * Get the debug info which will appear as a note on the Help Scout ticket.
	 *
	 * @since 1.6.1-dev-2 Use the system report available with Gravity Forms 2.2+.
	 *
	 * @return string
	 */
	public static function get_site_info() {
		if ( gravity_flow()->is_gravityforms_supported( '2.2' ) ) {
			require_once( GFCommon::get_base_path() . '/includes/system-status/class-gf-system-report.php' );
			$sections           = GF_System_Report::get_system_report();
			$system_report_text = GF_System_Report::get_system_report_text( $sections );

			return $system_report_text;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_list = get_plugins();
		$site_url    = get_bloginfo( 'url' );
		$plugins     = array();

		$active_plugins = get_option( 'active_plugins' );

		foreach ( $plugin_list as $key => $plugin ) {
			$is_active = in_array( $key, $active_plugins );
			if ( $is_active ) {
				$name      = substr( $key, 0, strpos( $key, '/' ) );
				$plugins[] = $name . 'v' . $plugin['Version'];
			}
		}
		$plugins = join( ', ', $plugins );

		// Get theme info.
		$theme            = wp_get_theme();
		$theme_name       = $theme->get( 'Name' );
		$theme_uri        = $theme->get( 'ThemeURI' );
		$theme_version    = $theme->get( 'Version' );
		$theme_author     = $theme->get( 'Author' );
		$theme_author_uri = $theme->get( 'AuthorURI' );

		$form_counts    = GFFormsModel::get_form_count();
		$active_count   = $form_counts['active'];
		$inactive_count = $form_counts['inactive'];
		$fc             = abs( $active_count ) + abs( $inactive_count );
		$entry_count    = GFFormsModel::get_lead_count_all_forms( 'active' );
		$im             = is_multisite()  ? 'yes' : 'no';

		global $wpdb;

		$info = array(
			'site: ' . $site_url,
			'GF version' . GFCommon::$version,
			'Gravity Flow version' . gravity_flow()->_version,
			'WordPress version: ' . get_bloginfo( 'version' ),
			'php version' . phpversion(),
			'mysql version: ' . $wpdb->db_version(),
			'theme name:' . $theme_name,
			'theme url' . $theme_uri,
			'theme version:' . $theme_version,
			'theme author: ' . $theme_author,
			'theme author URL:' . $theme_author_uri,
			'is multisite' . $im,
			'form count: ' . $fc,
			'entry count: ' . $entry_count,
			'plugins: ' . $plugins,
		);

		return join( PHP_EOL, $info );
	}

	/**
	 * Get the default value for the email field.
	 *
	 * @return string
	 */
	public static function get_email() {
		$license_data = gravity_flow()->check_license();
		$email        = rgobj( $license_data, 'customer_email' );

		if ( empty( $email ) ) {
			$email = get_option( 'admin_email' );
		}

		return $email;
	}
}
