<?php
/**
 * Gravity Flow Installation Wizard: Completion Step
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Installation_Wizard
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Class Gravity_Flow_Installation_Wizard_Step_Complete
 */
class Gravity_Flow_Installation_Wizard_Step_Complete extends Gravity_Flow_Installation_Wizard_Step {

	/**
	 * The step name.
	 *
	 * @var string
	 */
	protected $_name = 'complete';

	/**
	 * Displays the content for this step.
	 */
	public function display() {

		$url = admin_url( 'admin.php?page=gf_edit_forms&view=settings&subview=gravityflow&id=' );

		$forms = GFFormsModel::get_forms();
		?>
		<script>
			(function($) {
				$(document).ready(function () {
					$('#add_workflow_step').click(function(){
						window.location.href = <?php echo json_encode( $url ); ?> + $('#form_id').val();
						return false;
					})
				});
			})( jQuery );
		</script>

		<style>
			.circle{
				background: #808080;
				border-radius: 50%;
				-moz-border-radius: 50%;
				-webkit-border-radius: 50%;
				color: #ffffff;
				display: inline-block;
				font-weight: bold;
				line-height: 1.6em;
				margin-right: 5px;
				text-align: center;
				width: 1.6em;
			}
		</style>

		<p>
			<?php
			esc_html_e( 'Congratulations! Now you can set up your first workflow.', 'gravityflow' );
			?>
		</p>

		<?php
		if ( ! empty( $forms ) ) : ?>
			<h4>
				<span class="circle">1</span>
				<?php
				esc_html_e( 'Select a Form to use for your Workflow', 'gravityflow' );
				?>
			</h4>
			<p>
				<select id="form_id">
				<?php

				foreach ( $forms as $form ) {
					printf( '<option value="%d">%s</option>', $form->id, $form->title );
				}
				?>
				</select>
			</p>
			<h4>
				<span class="circle">2</span>
				<?php
				esc_html_e( 'Add Workflow Steps in the Form Settings', 'gravityflow' );
				?>
			</h4>
			<p>
				<a id="add_workflow_step" class="button button-primary" href="#" ><?php esc_html_e( 'Add Workflow Steps', 'gravityflow' )?></a>
			</p>
			<br />
			<p>
				<?php
				$url = admin_url( 'admin.php?page=gf_new_form' );
				$open_a_tag = sprintf( '<a href="%s">', $url );
				printf( esc_html__( "Don't have a form you want to use for the workflow? %sCreate a Form%s and add your steps in the Form Settings later.", 'gravityflow' ), $open_a_tag,  '</a>' );
				?>
			</p>
			<?php
		else :
			?>
			<p>
				<?php
				$url = admin_url( 'admin.php?page=gf_new_form' );
				$open_a_tag = sprintf( '<a href="%s">', $url );
				printf( esc_html__( '%sCreate a Form%s and then add your Workflow steps in the Form Settings.', 'gravityflow' ), $open_a_tag,  '</a>' );
				?>
			</p>
			<?php
		endif;
			?>
	<?php
	}

	/**
	 * Returns the title for this step.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Installation Complete', 'gravityflow' );
	}

	/**
	 * Returns the next button label.
	 *
	 * @return string
	 */
	public function get_next_button_text() {
		return '';
	}

	/**
	 * Returns the previous button label.
	 *
	 * @return string
	 */
	public function get_previous_button_text() {
		return '';
	}
}
