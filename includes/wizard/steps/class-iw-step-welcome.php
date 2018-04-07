<?php
/**
 * Gravity Flow Installation Wizard: Welcome Step
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Installation_Wizard
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Class Gravity_Flow_Installation_Wizard_Step_Welcome
 */
class Gravity_Flow_Installation_Wizard_Step_Welcome extends Gravity_Flow_Installation_Wizard_Step {

	/**
	 * The step name.
	 *
	 * @var string
	 */
	protected $_name = 'welcome';

	/**
	 * Displays the content for this step.
	 */
	function display() {

		esc_html_e( "Click the 'Get Started' button to complete your installation.", 'gravityflow' );
		?>

	<?php
	}

	/**
	 * Returns the next button label.
	 *
	 * @return string
	 */
	function get_next_button_text() {
		return esc_html__( 'Get Started', 'gravityflow' );
	}

	/**
	 * Returns the title for this step.
	 *
	 * @return string
	 */
	function get_title() {
		return esc_html__( 'Welcome', 'gravityflow' );
	}
}
