<?php
/**
 * Gravity Flow integration with the Gravity Forms Partial Entries Add-On.
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2019, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Partial_Entries
 *
 * Enables workflow processing to be triggered for partial entries.
 *
 * @since 2.5
 */
class Gravity_Flow_Partial_Entries {

	/**
	 * Indicates if workflow processing is enabled for the current form.
	 *
	 * @since 2.5
	 *
	 * @var null|bool
	 */
	private $_workflow_enabled = null;

	/**
	 * The instance of this class.
	 *
	 * @since 2.5
	 *
	 * @var null|Gravity_Flow_Partial_Entries
	 */
	private static $_instance = null;

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since 2.5
	 *
	 * @return null|Gravity_Flow_Partial_Entries
	 */
	public static function get_instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Gravity_Flow_Partial_Entries constructor.
	 *
	 * Adds the hooks on the init action, after the Gravity Forms Partial Entries Add-On has been loaded.
	 *
	 * @since 2.5
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'maybe_add_hooks' ) );
	}

	/**
	 * Determines if Partial Entries is supported.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	public function is_supported() {
		return class_exists( 'GF_Partial_Entries' ) && method_exists( 'GFFormsModel', 'add_meta_to_entry' );
	}

	/**
	 * If the Partial Entries Add-On is available add the appropriate hooks.
	 *
	 * @since 2.5
	 */
	public function maybe_add_hooks() {
		if ( ! $this->is_supported() ) {
			return;
		}

		add_filter( 'gform_gravityformspartialentries_feed_settings_fields', array(
			$this,
			'maybe_filter_feed_settings_fields'
		), 10, 2 );

		add_filter( 'gform_entry_pre_update', array( $this, 'maybe_filter_entry_pre_update' ), 10, 2 );
		add_filter( 'gform_pre_render', array( $this, 'filter_gform_pre_render' ), 50 );

		add_action( 'gform_partialentries_post_entry_saved', array( $this, 'maybe_trigger_workflow' ), 10, 2 );
		add_action( 'gform_partialentries_post_entry_updated', array( $this, 'maybe_trigger_workflow' ), 10, 2 );
		add_action( 'gravityflow_step_complete', array( $this, 'action_step_complete' ), 10, 5 );
	}

	/**
	 * Adds the enable workflow processing field to the Partial Entries Add-On feed settings page, if the form has at least one step configured.
	 *
	 * @since 2.5
	 *
	 * @param array              $feed_settings_fields The Partial Entries Add-On feed settings fields.
	 * @param GF_Partial_Entries $add_on               The current instance of the Partial Entries Add-On.
	 *
	 * @return array
	 */
	public function maybe_filter_feed_settings_fields( $feed_settings_fields, $add_on ) {

		$feed_settings_fields = $add_on->add_field_after( 'warning_message', array(
			array(
				'name'       => 'enable_workflow',
				'label'      => gravity_flow()->translate_navigation_label( 'workflow' ),
				'type'       => 'checkbox',
				'choices'    => array(
					array(
						'label' => esc_html__( 'Enable', 'gravityflow' ),
						'name'  => 'enable_workflow',
					),
				),
				'dependency' => array(
					'field'  => 'enable',
					'values' => array( 1 ),
				),
			)
		), $feed_settings_fields );


		return $feed_settings_fields;
	}

	/**
	 * Determines if workflow processing is enabled for the current forms partial entries.
	 *
	 * @since 2.5
	 *
	 * @param int $form_id The current form ID.
	 *
	 * @return bool
	 */
	public function is_workflow_enabled( $form_id ) {

		if ( ! $this->is_supported() ) {
			return false;
		}

		if ( is_null( $this->_workflow_enabled ) ) {
			$add_on        = GF_Partial_Entries::get_instance();
			$feed_settings = $add_on->get_feed_settings( $form_id );

			$this->_workflow_enabled = (bool) rgar( $feed_settings, 'enable_workflow' );
		}

		return $this->_workflow_enabled;
	}

	/**
	 * Restores the workflow meta to the entry before it is used to update the database.
	 *
	 * If the meta is not restored, the existing values in the database will be erased.
	 *
	 * @since 2.5
	 *
	 * @param array $entry          The entry values to be saved to the database.
	 * @param array $original_entry The previous version of the entry.
	 *
	 * @return array
	 */
	public function maybe_filter_entry_pre_update( $entry, $original_entry ) {
		if ( ! empty( $_POST['partial_entry_id'] ) && $this->is_workflow_enabled( $entry['form_id'] ) ) {
			gravity_flow()->log_debug( __METHOD__ . '(): Restoring workflow meta for partial entry #' . $entry['id'] );
			$meta_keys = array_keys( gravity_flow()->get_entry_meta( array(), $entry['form_id'] ) );

			foreach ( $meta_keys as $meta_key ) {
				$entry[ $meta_key ] = $original_entry[ $meta_key ];
			}
		}

		return $entry;
	}

	/**
	 * Triggers workflow processing of the partial entry, if enabled.
	 *
	 * @since 2.5
	 *
	 * @param array $partial_entry The partial entry which was saved or updated.
	 * @param array $form          The form used to create the partial entry.
	 */
	public function maybe_trigger_workflow( $partial_entry, $form ) {
		gravity_flow()->log_debug( __METHOD__ . '(): Running for partial entry #' . $partial_entry['id'] );

		if ( ! $this->is_workflow_enabled( $form['id'] ) ) {
			gravity_flow()->log_debug( __METHOD__ . '(): Aborting; workflow processing not enabled.' );

			return;
		}

		gravity_flow()->process_workflow( $form, $partial_entry['id'] );
	}

	/**
	 * Converts the partial entry to a complete entry by deleting the partial entry meta.
	 *
	 * @since 2.5
	 *
	 * @param int               $step_id  The current step ID.
	 * @param int               $entry_id The current entry ID.
	 * @param int               $form_id  The current form ID.
	 * @param string            $status   The step status.
	 * @param Gravity_Flow_Step $step     The current step.
	 */
	public function action_step_complete( $step_id, $entry_id, $form_id, $status, $step ) {
		$supported_step_types = array(
			'approval',
			'user_input',
		);

		if ( ! in_array( $step->get_type(), $supported_step_types ) ) {
			return;
		}

		$entry = $step->get_entry();
		if ( empty( $entry['partial_entry_id'] ) ) {
			return;
		}

		if ( ! empty( $entry['resume_token'] ) ) {
			gravity_flow()->log_debug( __METHOD__ . '(): Deleting draft submission.' );
			GFFormsModel::delete_draft_submission( $entry['resume_token'] );
		}

		gravity_flow()->log_debug( __METHOD__ . '(): Deleting partial entry meta.' );
		$add_on    = GF_Partial_Entries::get_instance();
		$meta_keys = array_keys( $add_on->get_entry_meta( array(), $form_id ) );

		foreach ( $meta_keys as $meta_key ) {
			gform_delete_meta( $entry_id, $meta_key );
		}
	}

	/**
	 * Populates the form with the partial entry being resumed.
	 *
	 * @since 2.5
	 *
	 * @param array $form The form currently being displayed.
	 *
	 * @return array
	 */
	public function filter_gform_pre_render( $form ) {

		$form_id = absint( $form['id'] );

		// Abort if this is a heartbeat request or if the paging or submit buttons have been used.
		if ( rgpost( 'action' ) == 'heartbeat' || rgpost( 'is_submit_' . $form_id ) ) {
			return $form;
		}

		$partial_entry_id = sanitize_key( rgget( 'peid' ) );

		// Abort if the partial entry id is not supplied or if workflow processing of partial entries is not enabled.
		if ( empty( $partial_entry_id ) || ! $this->is_workflow_enabled( $form['id'] ) ) {
			return $form;
		}

		$search_criteria = array(
			'status'        => 'active',
			'field_filters' => array(
				array( 'key' => 'partial_entry_id', 'value' => $partial_entry_id ),
			),
		);

		$entries = GFAPI::get_entries( $form['id'], $search_criteria );

		// Abort if the partial entry was not found.
		if ( empty( $entries ) ) {
			return $form;
		}

		$entry        = $entries[0];
		$current_step = gravity_flow()->get_current_step( $form, $entry );

		// Abort if the entry is not on the correct step type.
		if ( empty( $current_step ) || ! $current_step instanceof Gravity_Flow_Step_Partial_Entry_Submission ) {
			return $form;
		}

		$assignee_key = $current_step->get_current_assignee_key();
		$assignee     = $assignee_key ? $current_step->get_assignee( $assignee_key ) : false;

		// Abort if the user is not the assignee.
		if ( ! $assignee || ! $assignee->is_current_user() ) {
			return $form;
		}

		$values = Gravity_Flow_Populate_Form::get_population_values_from_entry( $form, $entry );
		$form   = Gravity_Flow_Populate_Form::do_population( $form, $values );

		// The PE add-on will add the ID to the hidden input it adds via the form tag filter.
		$_POST['partial_entry_id'] = $partial_entry_id;

		return $form;
	}

}

Gravity_Flow_Partial_Entries::get_instance();
