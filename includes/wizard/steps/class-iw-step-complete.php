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
			<h3><?php esc_html_e( 'Quick Start?', 'gravityflow' ) ?></h3>
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
			<h3><?php esc_html_e( 'Quick Start?', 'gravityflow' ) ?></h3>
			<p>
				<?php
				$url        = admin_url( 'admin.php?page=gf_new_form' );
				$open_a_tag = sprintf( '<a href="%s">', $url );
				printf( esc_html__( '%sCreate a Form%s and then add your Workflow steps in the Form Settings.', 'gravityflow' ), $open_a_tag, '</a>' );
				?>
			</p>
		<?php
		endif;
			?>
		<h3><?php esc_html_e( 'New to Gravity Flow?', 'gravityflow' ) ?></h3>
		<p>
			<?php
			$introduction_link = '<a href="https://docs.gravityflow.io/article/50-an-introduction-to-the-features" target="_blank">';
			$walkthrough_link = '<a href="https://docs.gravityflow.io/article/168-how-to-setup-your-first-workflow-with-gravity-flow-step-by-step" target="_blank">';
			$close_a = '</a>';
			/* translators: 1. The opening link tag 2. the closing link tag 3. The opening link tag 4. the closing link tag */
			printf( esc_html__( 'Please take a few minutes to go through the %1$sintroduction to the features%2$s and the tutorial videos below. The documentation also contains a detailed guide showing %3$show to create your first workflow%4$s.', 'gravityflow' ), $introduction_link, $close_a, $walkthrough_link, $close_a );
			?>
		</p>

		<h4>Video 1</h4>
		<section class="video widescreen">
			<iframe src="https://player.vimeo.com/video/168633964?title=0&amp;byline=0&amp;portrait=0" width="853" height="480" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="">
			</iframe>
		</section>
		<h4>Video 2</h4>
		<section class="video widescreen">
			<iframe src="https://player.vimeo.com/video/168634788?title=0&amp;byline=0&amp;portrait=0" width="853" height="480" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="">
			</iframe>
		</section>
		<h4>Video 3</h4>
		<section class="video widescreen">
			<iframe src="https://player.vimeo.com/video/200592782?title=0&amp;byline=0&amp;portrait=0" width="853" height="480" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="">
			</iframe>
		</section>
		<h4>Video 4</h4>
		<section class="video widescreen">
			<iframe src="https://player.vimeo.com/video/203577826?title=0&amp;byline=0&amp;portrait=0" width="853" height="480" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="">
			</iframe>
		</section>
		<h4>Video 5</h4>
		<section class="video widescreen">
			<iframe src="https://player.vimeo.com/video/202666475?title=0&amp;byline=0&amp;portrait=0" width="853" height="480" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="">
			</iframe>
		</section>
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
