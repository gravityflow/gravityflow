<?php

class Gravity_Flow_Installation_Wizard_Step_Updates extends Gravity_Flow_Installation_Wizard_Step {

	protected $_name = 'updates';

	function display() {
		if ( $this->background_updates == '' ) {
			// first run
			$this->background_updates = 'enabled';
		};

		?>
		<p>
			<?php
			esc_html_e( 'Gravity Flow will download important bug fixes, security enhancements and plugin updates automatically. Updates are extremely important to the security of your WordPress site.', 'gravityflow' );
			?>
		</p>
		<p>

			<?php
			esc_html_e( 'This feature is activated by default unless you opt to disable it below. We only recommend disabling background updates if you intend on managing updates manually. A valid license is required for background updates.', 'gravityflow' );
			?>

		</p>
		<div>
			<label>
				<input type="radio" id="background_updates_enabled" value="enabled" <?php checked( 'enabled', $this->background_updates ); ?> name="background_updates"/>
				<?php esc_html_e( 'Keep background updates enabled', 'gravityflow' ); ?>
			</label>
		</div>
		<div>
			<label>
				<input type="radio" id="background_updates_disabled" value="disabled" <?php checked( 'disabled', $this->background_updates ); ?> name="background_updates"/>
				<?php esc_html_e( 'Turn off background updates', 'gravityflow' ); ?>
			</label>
		</div>
		<div id="accept_terms_container" style="display:none;">
			<div style="background: #fff none repeat scroll 0 0;box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);padding: 1px 12px;border-left: 4px solid #dd3d36;margin: 5px 0 15px;display: inline-block;">

				<h3><i class="fa fa-exclamation-triangle gf_invalid"></i> <?php _e( 'Are you sure?', 'gravityflow' ); ?>
				</h3>
				<p>
					<strong><?php esc_html_e( 'By disabling background updates your site may not get critical bug fixes and security enhancements. We only recommend doing this if you are experienced at managing a WordPress site and accept the risks involved in manually keeping your WordPress site updated.', 'gravityflow' ); ?></strong>
				</p>
			</div>
			<label>
				<input type="checkbox" id="accept_terms" value="1" <?php checked( 1, $this->accept_terms ); ?> name="accept_terms"/>
				<?php esc_html_e( 'I Understand and Accept the Risk', 'gravityflow' ); ?> <span class="gfield_required">*</span>
			</label>
			<?php $this->validation_message( 'accept_terms' ); ?>
		</div>

		<script>
			(function($) {
				$(document).ready(function() {

					$('#accept_terms_container').toggle($('#background_updates_disabled').is(':checked'));

					$('#background_updates_disabled').click(function(){
						$("#accept_terms_container").slideDown();
					});
					$('#background_updates_enabled').click(function(){
						$("#accept_terms_container").slideUp();
					});
				})
			})(jQuery);
		</script>

	<?php
	}

	function get_title() {
		return esc_html__( 'Background Updates', 'gravityflow' );
	}

	function validate( $posted_values ) {
		$valid = true;
		if ( $this->background_updates == 'disabled' && empty( $this->accept_terms ) ) {
			$this->set_field_validation_result( 'accept_terms', esc_html__( 'Please accept the terms.', 'gravityflow' ) );
			$valid = false;
		}

		return $valid;
	}

	function summary( $echo = true ) {
		$html = $this->background_updates !== 'disabled' ? esc_html__( 'Enabled', 'gravityflow' ) . '&nbsp;<i class="fa fa-check gf_valid"></i>' :   esc_html__( 'Disabled', 'gravityflow' ) . '&nbsp;<i class="fa fa-times gf_invalid"></i>' ;
		if ( $echo ) {
			echo $html;
		}
		return $html;
	}

	function install() {
		$gravityflow = gravity_flow();

		$settings = $gravityflow->get_app_settings();
		$settings['background_updates'] = $this->background_updates !== 'disabled';
		gravity_flow()->update_app_settings( $settings );

	}
}
