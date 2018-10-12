<?php
/**
 * Gravity Flow Installation Wizard: Workflow Pages Step
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Installation_Wizard
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Class Gravity_Flow_Installation_Wizard_Step_Pages
 */
class Gravity_Flow_Installation_Wizard_Step_Pages extends Gravity_Flow_Installation_Wizard_Step {

	/**
	 * The step name.
	 *
	 * @var string
	 */
	protected $_name = 'pages';

	public $defaults = array(
		'workflow_pages' => 'custom',
		'inbox_page'     => true,
		'status_page'    => false,
		'submit_page'    => false,
	);

	/**
	 * Displays the content for this step.
	 */
	public function display() {
		if ( $this->workflow_pages == '' ) {
			// First run.
			$this->workflow_pages = 'admin';
		};
		echo '<p>' . esc_html__( "Users can access workflowws via the front-end of your site and/or from the built-in WordPress admin pages (Workflow menu). If you would like your users to access via the front-end of your site then you'll need to add some pages.", 'gravityflow' ) . '</p>';

		/* translators: 1. The opening link tag 2. the closing link tag */
		echo '<p>' . sprintf( esc_html__( 'Would you like to create custom inbox, status, and submit pages now? The pages will contain the %s[gravityflow]%s shortcode.', 'gravityflow' ), '<a href="http://docs.gravityflow.io/article/36-the-shortcode" target="_blank">', '</a>' ) . '</p>';

		$pages_options_style = $this->workflow_pages == 'admin' ? 'style="display:hidden;"' : '';
		?>
		<div>
			<label>
				<input id="gravityflow-custom-pages" type="radio"
				       value="custom" <?php checked( 'custom', $this->workflow_pages ); ?> name="workflow_pages"/>
				<?php esc_html_e( 'Yes, create front-end pages now. (Recommended)', 'gravityflow' ); ?>
			</label>

		</div>
		<div id="gravityflow-pages" <?php echo $pages_options_style; ?>>
			<div style="margin-left:25px;">
				<div>
					<label>
						<input id="gravityflow-inbox-page" type="hidden"
						       value="<?php echo $this->inbox_page ? '1' : '0'; ?>" name="inbox_page"/>
						<input type="checkbox" <?php checked( true, $this->inbox_page ); ?>
						       onclick="jQuery('#gravityflow-inbox-page').val(jQuery(this).prop('checked') ? 1 : 0);"/>
						<?php esc_html_e( 'Inbox', 'gravityflow' ); ?>
					</label>
					<br/>
					<div class="checkbox-description">
						<?php
						$inbox_link     = '<a href="https://docs.gravityflow.io/article/36-the-shortcode" target="_blank">';
						$approval_links = '<a href="https://docs.gravityflow.io/article/26-one-click-approval-links" target="_blank">';
						$cancel_links   = '<a href="https://docs.gravityflow.io/article/71-one-click-cancel-links" target="_blank">';
						$close_a        = '</a>';
						/* translators: 1. The opening link tag 2. the closing link tag */
						printf( esc_html__( 'The %1$sinbox%2$s is the place where workflow assignees access their pending tasks. Required for %3$sone-click approval%4$s and %5$scancel%6$s links.', 'gravityflow' ), $inbox_link, $close_a, $approval_links, $close_a, $cancel_links, $close_a );
						?>
					</div>
				</div>
				<div>
					<label>
						<input id="gravityflow-status-page" type="hidden"
						       value="<?php echo $this->status_page ? '1' : '0'; ?>" name="status_page"/>
						<input type="checkbox" <?php checked( true, $this->status_page ); ?>
						       onclick="jQuery('#gravityflow-status-page').val(jQuery(this).prop('checked') ? 1 : 0);"/>
						<?php esc_html_e( 'Status', 'gravityflow' ); ?>

					</label>
					<div class="checkbox-description">
						<?php
						/* translators: 1. The opening link tag 2. the closing link tag */
						printf( esc_html__( 'The %1$sstatus page%2$s displays all the entries for the current user. The administrator sees all the entries.', 'gravityflow' ), '<a href="https://docs.gravityflow.io/article/37-the-status-page" target="_blank">', '</a>' );
						?>
					</div>
				</div>
				<div>
					<label>
						<input id="gravityflow-submit-page" type="hidden"
						       value="<?php echo $this->submit_page ? '1' : '0'; ?>" name="submit_page"/>
						<input type="checkbox" <?php checked( true, $this->submit_page ); ?>
						       onclick="jQuery('#gravityflow-submit-page').val(jQuery(this).prop('checked') ? 1 : 0);"/>
						<?php esc_html_e( 'Submit', 'gravityflow' ); ?>

					</label>
					<br/>
					<div class="checkbox-description">
						<?php
						/* translators: 1. The opening link tag 2. the closing link tag */
						printf( esc_html__( 'The %1$ssubmit page%2$s displays the forms that have been published for workflows.', 'gravityflow' ), '<a href="https://docs.gravityflow.io/article/146-the-submit-page" target="_blank">', '</a>' );
						?>
					</div>
				</div>
			</div>
		</div>
		<div>
			<label>
				<input id="gravityflow-admin-pages" type="radio"
				       value="admin" <?php checked( 'admin', $this->workflow_pages ); ?> name="workflow_pages"/>
				<?php esc_html_e( 'No, all users will access via the WordPress Dashboard (Workflow menu).', 'gravityflow' ); ?>
			</label>
		</div>
		<script>
			(function ($) {
				$(document).ready(function () {
					var $radios = $('input[name="workflow_pages"]');

					togglePageOptions();

					$radios.change(function () {
						togglePageOptions();
					});

					function togglePageOptions() {
						var $checked = $radios.filter(function () {
							return $(this).prop('checked');
						});
						if ($checked.val() === 'custom') {
							$('#gravityflow-pages').slideDown();
						} else {
							$('#gravityflow-pages').slideUp();
						}
					}
				})
			})(jQuery);
		</script>

		<?php

	}

	/**
	 * Returns the title for this step.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Workflow Pages', 'gravityflow' );
	}

	/**
	 * Configures the plugin settings with the custom page IDs.
	 */
	public function install() {
		if ( $this->workflow_pages == 'custom' ) {

			$settings = gravity_flow()->get_app_settings();

			if ( $this->inbox_page ) {
				$settings['inbox_page'] = $this->create_page( 'inbox' );
			}

			if ( $this->status_page ) {
				$settings['status_page'] = $this->create_page( 'status' );
			}

			if ( $this->submit_page ) {
				$settings['submit_page'] = $this->create_page( 'submit' );
			}

			gravity_flow()->update_app_settings( $settings );
		}
	}

	/**
	 * Creates a new page containing the gravityflow shortcode for the specified page type.
	 *
	 * @param string $page The page type: inbox, status, or submit.
	 *
	 * @return int|string|WP_Error
	 */
	public function create_page( $page ) {
		$post = array(
			'post_title'   => $this->get_page_title( $page ),
			'post_content' => sprintf( '[gravityflow page="%s"]', $page ),
			'post_excerpt' => $this->get_page_title( $page ),
			'post_status'  => 'publish',
			'post_type'    => 'page',
		);

		$post_id = wp_insert_post( $post );

		return $post_id ? $post_id : '';
	}

	/**
	 * Return page title for the specified page type.
	 *
	 * @param string $page The page type: inbox, status, or submit.
	 *
	 * @return string
	 */
	public function get_page_title( $page ) {
		$titles = array(
			'inbox'  => esc_html__( 'Workflow Inbox', 'gravityflow' ),
			'status' => esc_html__( 'Workflow Status', 'gravityflow' ),
			'submit' => esc_html__( 'Submit a Workflow Form', 'gravityflow' ),
		);

		return $titles[ $page ];
	}
}
