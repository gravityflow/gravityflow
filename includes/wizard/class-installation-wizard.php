<?php
/**
 * Gravity Flow Installation Wizard
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Installation_Wizard
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Controls the steps for the wizard. Gravity Forms may not be available until after the Gravity Forms step.
 *
 * Class Gravity_Flow_Installation_Wizard
 */
class Gravity_Flow_Installation_Wizard {

	/**
	 * The installation wizard step class names.
	 *
	 * @var array
	 */
	private $_step_class_names = array();

	/**
	 * Gravity_Flow_Installation_Wizard constructor.
	 */
	function __construct() {
		$path = dirname(__FILE__) . '/steps/';
		require_once( $path . 'class-iw-step.php' );
		$classes = array();
		foreach ( glob( $path . 'class-iw-step-*.php' ) as $filename ) {
			require_once( $filename );
			$regex = '/class-iw-step-(.*?).php/';
			preg_match( $regex, $filename, $matches );
			$class_name            = 'Gravity_Flow_Installation_Wizard_Step_' . str_replace( '-', '_', $matches[1] );
			$step                  = new $class_name;
			$step_name             = $step->get_name();
			$classes[ $step_name ] = $class_name;
		}
		$sorted = array();
		foreach ( $this->get_sorted_step_names() as $sorted_step_name ) {
			$sorted[ $sorted_step_name ] = $classes[ $sorted_step_name ];
		}
		$this->_step_class_names = $sorted;
	}

	/**
	 * Returns the step names in the order the steps will appear.
	 *
	 * @return array
	 */
	public function get_sorted_step_names() {
		return array(
			'license_key',
			'gravity_forms',
			'updates',
			'updates',
			'pages',
			'complete',
		);
	}

	/**
	 * Displays the HTML for the current step.
	 *
	 * @return bool
	 */
	public function display() {

		/**
		 * @var Gravity_Flow_Installation_Wizard_Step $current_step The step being displayed.
		 * @var string                                $nonce_key    The nonce key for the current step.
		 */
		list( $current_step, $nonce_key ) = $this->get_current_step();
		$this->include_styles();

		?>

		<div class="wrap about-wrap gform_installation_progress_step_wrap">

			<img style="border:0;width:350px;" src="<?php echo plugins_url( '', __FILE__ ) ?>/../../images/gravity-flow-logo.svg"/>

			<div id="gform_installation_progress">
				<?php $this->progress( $current_step ); ?>
			</div>

			<hr/>

			<br/>
			<h2>
				<?php echo $current_step->get_title(); ?>
			</h2>

			<form action="" method="POST">
				<input type="hidden" name="_step_name" value="<?php echo esc_attr( $current_step->get_name() ); ?>"/>
				<?php
				wp_nonce_field( $nonce_key, $nonce_key );

				$validation_summary = $current_step->get_validation_summary();
				if ( $validation_summary ) {
					printf( '<div class="delete-alert alert_red">%s</div>', $validation_summary );
				}

				?>
				<div class="about-text">
					<?php $current_step->display(); ?>
				</div>
				<?php
				$next_button = '';
				if ( ! $current_step->disable_next_button ) {
					if ( $current_step->is( 'pages' ) ) {
						$next_button = sprintf( '<input class="button button-primary" type="submit" value="%s" name="_install"/>', esc_attr( $current_step->get_next_button_text() ) );
					} elseif ( ! $current_step->is( 'complete' ) ) {
						$next_button = sprintf( '<input class="button button-primary" type="submit" value="%s" name="_next"/>', esc_attr( $current_step->get_next_button_text() ) );
					}
				}
				?>
				<div>
					<?php
					$previous_button_text = $current_step->get_previous_button_text();
					if ( $previous_button_text ) {
						$previous_button = $this->get_step_index( $current_step ) > 0 ? '<input name="_previous" class="button button-primary" type="submit" value="' . esc_attr( $previous_button_text ) . '" style="margin-right:30px;" />' : '';
						echo $previous_button;
					}
					echo $next_button;
					?>
				</div>
			</form>
		</div>

		<?php

		return true;
	}

	/**
	 * Get the step to be displayed and it's nonce key.
	 *
	 * @return array
	 */
	public function get_current_step() {
		$name         = isset( $_POST['_step_name'] ) ? $_POST['_step_name'] : '';
		$current_step = $this->get_step( $name );
		$nonce_key    = '_gform_installation_wizard_step_' . $current_step->get_name();

		if ( isset( $_POST[ $nonce_key ] ) && check_admin_referer( $nonce_key, $nonce_key ) ) {

			if ( isset( $_POST['_previous'] ) ) {
				$posted_values = $this->get_posted_values();
				$current_step->update( $posted_values );
				$previous_step = $this->get_previous_step( $current_step );
				if ( $previous_step ) {
					$current_step = $previous_step;
				}
			} elseif ( isset( $_POST['_next'] ) ) {
				$posted_values = $this->get_posted_values();
				$current_step->update( $posted_values );
				$validation_result = $current_step->validate();
				if ( $validation_result === true ) {
					$next_step = $this->get_next_step( $current_step );
					if ( $next_step ) {
						$current_step = $next_step;
					}
				}
			} elseif ( isset( $_POST['_install'] ) ) {
				$posted_values = $this->get_posted_values();
				$current_step->update( $posted_values );
				$validation_result = $current_step->validate();
				if ( $validation_result === true ) {
					$this->complete_installation();
					$next_step = $this->get_next_step( $current_step );
					if ( $next_step ) {
						$current_step = $next_step;
					}
				}
			}

			$nonce_key = '_gform_installation_wizard_step_' . $current_step->get_name();
		}

		return array( $current_step, $nonce_key );
	}

	/**
	 * Registers the admin styles and includes the inline style block.
	 */
	public function include_styles() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		// Register admin styles.
		//wp_register_style( 'gform_admin', GFCommon::get_base_url() . "/css/admin{$min}.css" );
		//wp_print_styles( array( 'jquery-ui-styles', 'gform_admin' ) );
		?>
		<style>
			#gform_installation_progress li {
				display: inline-block;
				padding: 10px 20px 10px 0;
			}

			.gform_installation_progress_current_step {
				color: black;
			}

			.gform_installation_progress_step_pending {
				color: silver;
			}

			.gform_installation_progress_step_complete {
				color: black;
			}

			.gform_installation_progress_step_wrap p {
				color: black;
			}

			.about-text input.regular-text {
				font-size: 19px;
				padding: 8px;
			}

			/* Copied from Gravity Forms Admin Styles in case Gravity Forms is not installed */
			.validation_message {
				color: #9E0B0F !important;
				font-size: 14px;
				font-family: sans-serif;
				letter-spacing: normal;
			}

			.gfield_required {
				color: #9E0B0F;
				margin-left: 4px;
			}

			.checkbox-description{
				font-size:14px;
				margin-bottom: 10px;
			}
		</style>
		<?php
	}

	/**
	 * Returns the specified step.
	 *
	 * @param bool|string $name The step name.
	 *
	 * @return Gravity_Flow_Installation_Wizard_Step
	 */
	public function get_step( $name = false ) {

		if ( empty( $name ) ) {
			$class_names = array_keys( $this->_step_class_names );
			$name        = $class_names[0];
		}

		$current_step_values = get_option( 'gravityflow_installation_wizard_' . $name );

		$step = new $this->_step_class_names[$name]( $current_step_values );

		return $step;
	}

	/**
	 * Returns the previous step.
	 *
	 * @param Gravity_Flow_Installation_Wizard_Step $current_step The current step.
	 *
	 * @return bool|Gravity_Flow_Installation_Wizard_Step
	 */
	public function get_previous_step( $current_step ) {
		$current_step_name = $current_step->get_name();

		$step_names = array_keys( $this->_step_class_names );
		$i          = array_search( $current_step_name, $step_names );

		if ( $i == 0 ) {
			return false;
		}

		$previous_step_name = $step_names[ $i - 1 ];

		return $this->get_step( $previous_step_name );
	}

	/**
	 * Returns the next step.
	 *
	 * @param Gravity_Flow_Installation_Wizard_Step $current_step The current step.
	 *
	 * @return bool|Gravity_Flow_Installation_Wizard_Step
	 */
	public function get_next_step( $current_step ) {
		$current_step_name = $current_step->get_name();

		$step_names = array_keys( $this->_step_class_names );
		$i          = array_search( $current_step_name, $step_names );

		if ( $i == count( $step_names ) - 1 ) {
			return false;
		}

		$next_step_name = $step_names[ $i + 1 ];

		return $this->get_step( $next_step_name );
	}

	/**
	 * Performs the actions to complete the installation such as saving options to the database.
	 */
	public function complete_installation() {
		foreach ( array_keys( $this->_step_class_names ) as $step_name ) {
			$step = $this->get_step( $step_name );
			$step->install();
			$step->flush_values();
		}
		update_option( 'gravityflow_pending_installation', false );
	}

	/**
	 * Returns the posted options.
	 *
	 * @return array
	 */
	public function get_posted_values() {
		$posted_values = stripslashes_deep( $_POST );
		$values        = array();
		foreach ( $posted_values as $key => $value ) {
			if ( strpos( $key, '_', 0 ) !== 0 ) {
				$values[ $key ] = $value;
			}
		}

		return $values;
	}

	/**
	 * Returns the HTML markup for the installation progress.
	 *
	 * @param Gravity_Flow_Installation_Wizard_Step $current_step The current step.
	 * @param bool                                  $echo         Indicates if the HTML should be echoed.
	 *
	 * @return string
	 */
	public function progress( $current_step, $echo = true ) {
		$html              = '<ul id="gform_installation_progress">';
		$done              = true;
		$current_step_name = $current_step->get_name();
		$yes_img_url = plugins_url( '', __FILE__ ) . '/../../images/yes.png';
		foreach ( array_keys( $this->_step_class_names ) as $step_name ) {
			$class = '';
			$step  = $this->get_step( $step_name );
			if ( $current_step_name == $step_name ) {
				$class .= 'gform_installation_progress_current_step ';
				$done = $step->is( 'complete' ) ? true : false;
			} else {
				$class .= $done ? 'gform_installation_progress_step_complete' : 'gform_installation_progress_step_pending';
			}
			$yes_visibility = $done ? '' : 'style="visibility:hidden"';
			$check = sprintf( '<img src="%s" %s />', $yes_img_url, $yes_visibility );

			$html .= sprintf( '<li id="gform_installation_progress_%s" class="%s">%s&nbsp;%s</li>', esc_attr( $step->get_name() ), esc_attr( $class ), esc_html( $step->get_title() ), $check );
		}
		$html .= '</ul>';

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Get the index for the current step in the _step_class_names array.
	 *
	 * @param Gravity_Flow_Installation_Wizard_Step $step The current step.
	 *
	 * @return mixed
	 */
	public function get_step_index( $step ) {
		$i = array_search( $step->get_name(), array_keys( $this->_step_class_names ) );

		return $i;
	}

	/**
	 * Display the summary.
	 */
	public function summary() {
		?>

		<h3>Summary</h3>
		<?php
		echo '<table class="form-table"><tbody>';
		$steps = $this->get_steps();
		foreach ( $steps as $step ) {
			$step_summary = $step->summary( false );
			if ( $step_summary ) {
				printf( '<tr valign="top"><th scope="row"><label>%s</label></th><td>%s</td></tr>', esc_html( $step->get_title() ), $step_summary );
			}
		}
		echo '</tbody></table>';

	}

	/**
	 * Get an array containing all the steps.
	 *
	 * @return Gravity_Flow_Installation_Wizard_Step[]
	 */
	public function get_steps() {
		$steps = array();
		foreach ( array_keys( $this->_step_class_names ) as $step_name ) {
			$steps[] = $this->get_step( $step_name );
		}

		return $steps;
	}

	/**
	 * Flush the values for all steps.
	 */
	public function flush_values() {
		$steps = $this->get_steps();
		foreach ( $steps as $step ) {
			$step->flush_values();
		}
	}

}
