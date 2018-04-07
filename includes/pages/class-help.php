<?php
/**
 * Gravity Flow Help
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Help
 *
 * @since 1.0
 */
class Gravity_Flow_Help {

	/**
	 * Displays the help page content.
	 */
	public static function display() {

		?>
		<div class="wrap gf_entry_wrap">

			<h2 class="gf_admin_page_title">
				<span><?php esc_html_e( 'Help', 'gravityflow' ); ?></span>
			</h2>

			<p>
				First, draw your workflow.&nbsp;If you plan your workflow well, the rest will be straightforward. I can't stress this enough - if you don't plan, you'll very likely find the whole experience very frustrating.&nbsp;
			</p>
			<p>
				List the users or roles involved and identify what each one needs to do and at which stage. Try to separate the process into distinct steps involving approval and user input. You may find it useful to draw the process using a&nbsp;
				<a href="http://en.wikipedia.org/wiki/Swim_lane">swim lane diagram</a>.
			</p>
			<p>
				Create a form to use for your workflow. This form must contain all  the fields used at every step of the process but don't worry if you need  to add fields later.
			</p>
			<p>
				Take a look at the&nbsp;
				<a href="http://docs.gravityflow.io/category/34-walkthroughs">walkthroughs</a> and then dive deeper into the&nbsp;<a href="http://docs.gravityflow.io/category/21-user-guides">user guides</a>.
			</p>
		</div>
	<?php
	}
}
