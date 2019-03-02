<?php
/**
 * Gravity Flow Step Feed WP E-Signature
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Esign
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Esign
 */
class Gravity_Flow_Step_Feed_Esign extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'wp-e-signature';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFEsignAddOn';

	/**
	 * The slug used by the add-on.
	 *
	 * @var string
	 */
	protected $_slug = 'esig-gf';

	/**
	 * The E-Signature feed ID.
	 *
	 * @var int
	 */
	protected $_feed_id;

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'WP E-Signature';
	}

	/**
	 * Returns the feed name.
	 *
	 * @param array $feed The E-Signature feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		$sad_page_id    = rgars( $feed, 'meta/esig_gf_sad' );
		$sad            = new esig_sad_document();
		$document_id    = $sad->get_sad_id( $sad_page_id );
		$document_model = new WP_E_Document();
		$document       = $document_model->getDocument( $document_id );

		return $document->document_title;
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/esig-icon.png';
	}

	/**
	 * Returns an array of settings for this step type.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = parent::get_settings();

		if ( ! $this->is_supported() ) {
			return $settings;
		}

		$settings_api = $this->get_common_settings_api();

		$fields = array(
			$settings_api->get_setting_assignee_type(),
			$settings_api->get_setting_assignees(),
			$settings_api->get_setting_assignee_routing(),
			$settings_api->get_setting_notification_tabs( array(
				array(
					'label'  => __( 'Assignee Email', 'gravityflow' ),
					'id'     => 'tab_assignee_notification',
					'fields' => $settings_api->get_setting_notification( array(
						'type'            => 'assignee',
						'label'           => __( 'Send Email to the assignee(s).', 'gravityflow' ),
						'tooltip'         => __( 'Enable this setting to send email to each of the assignees as soon as the entry has been assigned. If a role is configured to receive emails then all the users with that role will receive the email.', 'gravityflow' ),
						'default_message' => __( 'A new document has been generated and requires a signature. Please check your Workflow Inbox.', 'gravityflow' ),
						'resend_enabled'  => true,
					) ),
				)
			) ),
			$settings_api->get_setting_instructions( esc_html__( 'Instructions: check the signature invite status in the WP E-Signature section of the Workflow sidebar and resend if necessary.', 'gravityflow' ) ),
			$settings_api->get_setting_display_fields(),
		);

		$settings['fields'] = array_merge( $settings['fields'], $fields );

		return $settings;
	}

	/**
	 * Prevent the feeds assigned to the current step from being processed by the associated add-on.
	 */
	public function intercept_submission() {
		parent::intercept_submission();

		remove_filter( 'gform_confirmation', array( ESIG_GRAVITY_Admin::get_instance(), 'reroute_confirmation' ) );
	}

	/**
	 * Processes this step.
	 *
	 * @return bool Is the step complete?
	 */
	public function process() {
		$complete = parent::process();
		$this->assign();

		return $complete;
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @param array $feed The add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
	public function process_feed( $feed ) {
		$this->_feed_id = $feed['id'];
		add_action( 'esig_sad_document_invite_send', array( $this, 'sad_document_invite_send' ) );

		if ( ! $this->is_redirect_supported( rgar( $feed['meta'], 'esign_gf_logic' ) ) ) {
			$feed['meta']['esign_gf_logic'] = 'email';
		}

		parent::process_feed( $feed );

		return false;
	}

	/**
	 * Determines if the redirect action can be used for this step.
	 *
	 * @since 2.4.1
	 *
	 * @param string $action The feed action (Signing Logic); redirect or email.
	 *
	 * @return bool
	 */
	public function is_redirect_supported( $action ) {
		if ( $action !== 'redirect'
		     || ! class_exists( 'GFFormDisplay' )
		     || empty( $_POST['gform_submit'] )
		     || $_POST['gform_submit'] != $this->get_form_id()
		     || ! did_action( 'gform_entry_created' )
		     || did_action( 'gform_confirmation' )
		     || ( function_exists( 'gp_nested_forms' ) && gp_nested_forms()->is_nested_form_submission() )
		) {
			return false;
		}

		$submission_entry = GFFormsModel::get_current_lead();

		return rgar( $submission_entry, 'id' ) == $this->get_entry_id();
	}

	/**
	 * Determines if this step type is supported.
	 *
	 * @return bool
	 */
	public function is_supported() {
		return parent::is_supported() && class_exists( 'esig_sad_document' ) && class_exists( 'WP_E_Document' );
	}

	/**
	 * Target of esig_sad_document_invite_send hook. Store the feed id which created this document in the WP E-Signature meta.
	 *
	 * @param array $args The properties related to the document which was saved.
	 */
	public function sad_document_invite_send( $args ) {
		if ( ! empty( $this->_feed_id ) && class_exists( 'WP_E_Meta' ) ) {
			$sig_meta_api = new WP_E_Meta();
			$sig_meta_api->add( $args['document']->document_id, 'esig_gravity_feed_id', $this->_feed_id );
			$this->save_document_id( $args['document']->document_id );
		}
	}

	/**
	 * Store the current document ID in the entry meta for this step.
	 *
	 * @param int $document_id The documents unique ID assigned by WP E-Signature.
	 */
	public function save_document_id( $document_id ) {
		$document_ids = $this->get_document_ids();
		if ( ! in_array( $document_id, $document_ids ) ) {
			$document_ids[] = $document_id;
		}

		gform_update_meta( $this->get_entry_id(), 'workflow_step_' . $this->get_id() . '_document_ids', $document_ids );
	}

	/**
	 * Retrieve this entries document IDs for the current step.
	 *
	 * @return array
	 */
	public function get_document_ids() {
		$document_ids = gform_get_meta( $this->get_entry_id(), 'workflow_step_' . $this->get_id() . '_document_ids' );
		if ( empty( $document_ids ) ) {
			$document_ids = array();
		}

		return $document_ids;
	}

	/**
	 * Displays content inside the Workflow metabox on the workflow detail page.
	 *
	 * @param array $form The Form array which may contain validation details.
	 * @param array $args Additional args which may affect the display.
	 */
	public function workflow_detail_box( $form, $args ) {
		$document_ids = $this->get_document_ids();

		if ( ! empty( $document_ids ) && class_exists( 'WP_E_Document' ) ) {
			echo sprintf( '<h4 style="margin-bottom:10px;">%s</h4>', $this->get_label() );

			$doc_api    = new WP_E_Document();
			$invite_api = new WP_E_Invite();

			global $current_user;
			$user_email = $current_user->user_email;
			if ( empty( $user_email ) ) {
				$assignee_key = gravity_flow()->get_current_user_assignee_key();
				list( $type, $user_id ) = rgexplode( '|', $assignee_key, 2 );
				$user_email = $type == 'email' ? $user_id : '';
			}

			foreach ( $document_ids as $document_id ) {
				$document = $doc_api->getDocument( $document_id );

				echo sprintf( '%s: %s<br><br>', esc_html__( 'Title', 'gravityflow' ), esc_html( $document->document_title ) );


				echo sprintf( '%s: %s', esc_html__( 'Invite Status', 'gravityflow' ), $invite_api->is_invite_sent( $document_id ) ? esc_html__( 'Sent', 'gravityflow' ) : esc_html__( 'Error: Not Sent', 'gravityflow' ) );
				echo '&nbsp;';
				$params     = array(
					'page'        => 'esign-resend_invite-document',
					'document_id' => $document_id,
				);
				$resend_url = add_query_arg( $params, admin_url() );
				echo sprintf( '&nbsp;-&nbsp;<a href="%s">%s</a><br><br>', esc_url( $resend_url ), esc_html__( 'Resend', 'gravityflow' ) );

				$text = '';

				if ( ! empty( $user_email ) ) {
					$invitations = $invite_api->getInvitations( $document_id );

					if ( $user_email == $invitations[0]->user_email ) {
						$url  = $invite_api->get_invite_url( $invitations[0]->invite_hash, $document->document_checksum );
						$text = esc_html__( 'Review &amp; Sign', 'gravityflow' );
					}
				}

				if ( empty( $url ) || empty( $text ) ) {
					$url  = $invite_api->get_preview_url( $document_id );
					$text = esc_html__( 'Preview', 'gravityflow' );
				}
				echo '<br /><div class="gravityflow-action-buttons">';
				echo sprintf( '<a href="%s" target="_blank" class="button button-large button-primary">%s</a><br><br>', $url, $text );
				echo '</div>';

			}
		}
	}

	/**
	 * Deletes custom entry meta when the step or workflow is restarted.
	 */
	public function restart_action() {
		gform_delete_meta( $this->get_entry_id(), 'workflow_step_' . $this->get_id() . '_document_ids' );
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Esign() );

/**
 * Resume the workflow if the completed document originated from a feed processed by one of our steps.
 *
 * @param array $args The properties related to the completed document.
 */
function gravity_flow_step_esign_signature_saved( $args ) {
	if ( class_exists( 'WP_E_Meta' ) ) {
		$sig_meta_api = new WP_E_Meta();
		$entry_id     = $sig_meta_api->get( $args['invitation']->document_id, 'esig_gravity_entry_id' );
		$feed_id      = $sig_meta_api->get( $args['invitation']->document_id, 'esig_gravity_feed_id' );

		if ( $entry_id && $feed_id ) {
			$entry = GFAPI::get_entry( $entry_id );

			if ( ! is_wp_error( $entry ) && is_array( $entry ) && rgar( $entry, 'workflow_final_status' ) == 'pending' ) {
				$api = new Gravity_Flow_API( $entry['form_id'] );

				/* @var Gravity_Flow_Step_Feed_Esign $step */
				$step = $api->get_current_step( $entry );

				if ( $step ) {
					$feed  = gravity_flow()->get_feed( $feed_id );
					$label = $step->get_feed_label( $feed );
					$step->add_note( sprintf( esc_html__( 'Document signed: %s', 'gravityflow' ), $label ) );
					$step->log_debug( __METHOD__ . '() - Feed processing complete: ' . $label );

					$add_on_feeds = $step->get_processed_add_on_feeds( $entry_id );
					if ( ! in_array( $feed_id, $add_on_feeds ) ) {
						$add_on_feeds[] = $feed_id;
						$step->update_processed_feeds( $add_on_feeds, $entry_id );
						$form = GFAPI::get_form( $entry['form_id'] );
						gravity_flow()->process_workflow( $form, $entry_id );
					}
				}
			}
		}
	}
}

add_action( 'esig_signature_saved', 'gravity_flow_step_esign_signature_saved' );
