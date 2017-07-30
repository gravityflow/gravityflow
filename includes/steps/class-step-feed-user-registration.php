<?php
/**
 * Gravity Flow Step Feed User Registration
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_User_Registration
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

add_action( 'gform_user_registered', array( 'Gravity_Flow_Step_Feed_User_Registration', 'action_gform_user_registered' ), 10, 4 );

class Gravity_Flow_Step_Feed_User_Registration extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'user_registration';

	protected $_class_name = 'GFUser';

	public function get_feed_add_on_class_name() {
		$class_name        = class_exists( 'GF_User_Registration' ) ? 'GF_User_Registration' : 'GFUser';
		$this->_class_name = $class_name;

		return $class_name;
	}

	public function get_label() {
		return esc_html__( 'User Registration', 'gravityflow' );
	}

	public function get_icon_url() {
		return '<i class="fa fa-user" style="color:darkgreen"></i>';
	}

	public function get_feeds() {
		$form_id = $this->get_form_id();

		if ( class_exists( 'GF_User_Registration' ) ) {
			$feeds = parent::get_feeds();
		} else {
			$feeds = GFUserData::get_feeds( $form_id );
		}


		return $feeds;
	}

	function process_feed( $feed ) {

		if ( class_exists( 'GF_User_Registration' ) ) {

			parent::process_feed( $feed );

			$activation_enabled = isset( $feed['meta']['userActivationEnable'] ) &&  $feed['meta']['userActivationEnable'];

			$step_complete = ! $activation_enabled;

			return $step_complete;
		}
		// User Registration < 3.0
		$form  = $this->get_form();
		$entry = $this->get_entry();
		remove_filter( 'gform_disable_registration', '__return_true' );
		GFUser::gf_create_user( $entry, $form );

		// Make sure it's not run twice
		add_filter( 'gform_disable_registration', '__return_true' );

		return true;
	}

	public function intercept_submission() {
		if ( class_exists( 'GF_User_Registration' ) ) {
			parent::intercept_submission();

			return;
		}

		add_filter( 'gform_disable_registration', '__return_true' );
	}

	public function get_feed_label( $feed ) {
		if ( class_exists( 'GF_User_Registration' ) ) {
			return parent::get_feed_label( $feed );
		}

		$label = $feed['meta']['feed_type'] == 'create' ? __( 'Create', 'gravityflow' ) : __( 'Update', 'gravityflow' );

		return $label;
	}

	public function workflow_detail_box( $form, $args ) {
		$step_status = $this->get_status();
		$status_label = $this->get_status_label( $step_status );

		$display_step_status = (bool) $args['step_status'];

		?>
		<h4 style="margin-bottom:10px;"><?php echo $this->get_name() . ' (' . $status_label . ')' ?></h4>
		<?php if ( $display_step_status ) : ?>

			<div>
				<ul>
					<li>
						<?php
						echo sprintf( '%s: %s', esc_html__( 'User Registration', 'gravityflow' ), $status_label );
						?>
					</li>
				</ul>
			</div>

			<?php
		endif;
	}

	public function entry_detail_status_box( $form ) {
		$step_status = $this->get_status();
		$status_label = $this->get_status_label( $step_status );

		?>
		<h4 style="padding:10px;"><?php echo $this->get_name() . ': ' . $status_label ?></h4>

		<div style="padding:10px;">
			<ul>
				<?php
				echo sprintf( '%s: %s', esc_html__( 'User Registration', 'gravityflow' ), $status_label );
				?>
			</ul>
		</div>
		<?php
	}

	public static function action_gform_user_registered( $user_id, $feed, $entry, $user_pass ) {

		$api = new Gravity_Flow_API( $entry['form_id'] );
		$step = $api->get_current_step( $entry );
		if ( ! $step ) {
			return;
		}
		if ( $step->get_type() == 'user_registration' ) {

			$entry_id = $entry['id'];

			/* @var Gravity_Flow_Step_Feed_Add_On $step */

			GFFormsModel::update_lead_property( $entry_id, 'created_by', $user_id, false, true );
			$activation_enabled = isset( $feed['meta']['userActivationEnable'] ) &&  $feed['meta']['userActivationEnable'];
			if ( $activation_enabled ) {
				$label = $step->get_feed_label( $feed );
				$step->add_note( sprintf( esc_html__( 'User Registration feed processed: %s', 'gravityflow' ), $label ) );
				$step->log_debug( __METHOD__ . '() - Feed processing complete: ' . $label );
				$feed_id = $feed['id'];
				$add_on_feeds = $step->get_processed_add_on_feeds( $entry_id );
				if ( ! in_array( $feed_id, $add_on_feeds ) ) {
					$add_on_feeds[] = $feed_id;
					$step->update_processed_feeds( $add_on_feeds, $entry_id );
					$api->process_workflow( $entry_id );
				}
			}
		}
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_User_Registration() );
