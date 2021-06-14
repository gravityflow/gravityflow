<?php
/**
 * Gravity Flow integration with the Gravity Forms Partial Entries Add-On.
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2021, Steven Henty S.L.
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
	 * The form being processed.
	 *
	 * @sicne 2.7.4
	 *
	 * @var null|array
	 */
	private $_form = null;

	/**
	 * The saved partial entry from the db before the submission time update.
	 *
	 * @sicne 2.7.4
	 *
	 * @var null|array
	 */
	private $_saved_entry = null;

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

		add_filter( 'gform_pre_render', array( $this, 'filter_gform_pre_render' ), 50 );
		add_filter( 'gform_partialentries_pre_update', array( $this, 'filter_partial_entry_pre_update' ), 10, 3 );
		add_filter( 'gform_entry_id_pre_save_lead', array( $this, 'filter_entry_id_pre_save_lead' ), 8, 2 );
		add_filter( 'gform_entry_pre_update', array( $this, 'maybe_filter_entry_pre_update' ), 10, 2 );

		add_action( 'gform_partialentries_post_entry_saved', array( $this, 'maybe_trigger_workflow' ), 10, 2 );
		add_action( 'gform_partialentries_post_entry_updated', array( $this, 'maybe_trigger_workflow' ), 10, 2 );
		add_action( 'gform_entry_created', array( $this, 'remove_filter_entry_pre_update' ), 5 );
		add_action( 'gravityflow_step_complete', array( $this, 'action_step_complete' ), 10, 5 );
	}

	// # FORM SETTINGS ------------------------------------------------------------------------------------------------

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
		$field = array(
			'name'  => 'enable_workflow',
			'label' => gravity_flow()->translate_navigation_label( 'workflow' ),
		);

		$field['tooltip'] = sprintf( esc_html__( 'Start %s processing when the partial entry is saved.', 'gravityflow' ), strtolower( $field['label'] ) );

		if ( ! $add_on->is_gravityforms_supported( '2.5-rc-2' ) ) {
			$field['type']       = 'checkbox';
			$field['choices']    = array(
				array(
					'label' => esc_html__( 'Enable', 'gravityflow' ),
					'name'  => 'enable_workflow',
				),
			);
			$field['dependency'] = array(
				'field'  => 'enable',
				'values' => array( 1 ),
			);
		} else {
			$field['type']       = 'toggle';
			$field['dependency'] = array(
				'live'   => true,
				'fields' => array(
					array(
						'field' => 'enable_partial',
					),
				),
			);
		}

		return $add_on->add_field_after( 'warning_message', array( $field ), $feed_settings_fields );
	}

	// # FORM DISPLAY -------------------------------------------------------------------------------------------------

	/**
	 * Populates the form with the partial entry being resumed.
	 *
	 * @since 2.5
	 *
	 * @param array $form The form currently being displayed.
	 *
	 * @return array|false
	 */
	public function filter_gform_pre_render( $form ) {
		if ( ! $this->is_valid_form( $form ) ) {
			return $form;
		}

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
			add_filter( 'gform_form_not_found_message', array( $this, 'partial_entry_complete' ), 50 );

			return false;
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

		// The PE add-on will add the ID to the hidden input it adds via the form tag filter.
		$_POST['partial_entry_id'] = $partial_entry_id;

		$values = Gravity_Flow_Populate_Form::get_population_values_from_entry( $form, $entry );

		return Gravity_Flow_Populate_Form::do_population( $form, $values );
	}

	/**
	 * Returns a message to indicate that the Partial Entry was completed.
	 *
	 * @since 2.5.10
	 */
	public function partial_entry_complete() {
		return '<p>' . esc_html__( 'This entry has already been completed.', 'gravityflow' ) . '</p>';
	}

	// # PARTIAL ENTRY SAVE/UPDATE ------------------------------------------------------------------------------------

	/**
	 * Restores values to the entry before the database is updated.
	 *
	 * Is used during the Heartbeat or when paging.
	 *
	 * @since 2.7.4
	 *
	 * @param array $partial_entry The partial entry values to be saved to the database.
	 * @param array $saved_entry   The previous version of the entry.
	 * @param array $form          The form currently being processed.
	 *
	 * @return array
	 */
	public function filter_partial_entry_pre_update( $partial_entry, $saved_entry, $form ) {
		if ( ! $this->is_valid_form( $form ) || ! $this->is_applicable_request( $form['id'] ) ) {
			return $partial_entry;
		}

		$this->log_debug( __METHOD__ . '(): Running for partial entry #' . $partial_entry['id'] );
		$this->remove_filter_entry_pre_update();

		return $this->restore_meta( $this->restore_fields( $partial_entry, $saved_entry, $form ), $saved_entry );
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
		$this->log_debug( __METHOD__ . '(): Running for partial entry #' . $partial_entry['id'] );
		$this->remove_filter_entry_pre_update();

		if ( ! $this->is_applicable_request( $form['id'] ) ) {
			$this->log_debug( __METHOD__ . '(): Aborting; workflow processing not enabled.' );

			return;
		}

		gravity_flow()->process_workflow( $form, $partial_entry['id'] );
	}

	// # FORM SUBMISSION ----------------------------------------------------------------------------------------------

	/**
	 * Stash the form to make it available to maybe_filter_entry_pre_update().
	 *
	 * @since 2.7.4
	 *
	 * @param null|int $entry_id Null to save a new entry or the ID of the entry to be updated.
	 * @param array    $form     The form currently being processed.
	 *
	 * @return null|int
	 */
	public function filter_entry_id_pre_save_lead( $entry_id, $form ) {
		if ( $this->is_valid_form( $form ) && $this->is_applicable_request( $form['id'] ) ) {
			$this->_form = $form;
			add_filter( "gform_save_field_value_{$form['id']}", array( $this, 'filter_save_field_value' ), 10, 5 );

			if ( ! has_filter( 'gform_entry_pre_update', array( $this, 'maybe_filter_entry_pre_update' ) ) ) {
				add_filter( 'gform_entry_pre_update', array( $this, 'maybe_filter_entry_pre_update' ), 10, 2 );
			}
		}

		return $entry_id;
	}

	/**
	 * Restores field values and meta to the entry.
	 *
	 * Is used, before the entry is saved, when PE clears the entry of values using GFAPI::update_entry().
	 *
	 * Will also be used during the Heartbeat or when paging, if gform_partialentries_pre_update isn't supported.
	 *
	 * @since 2.5
	 * @since 2.7.4 Updated to use the restore_ functions.
	 *
	 * @param array $entry          The entry values to be saved to the database.
	 * @param array $original_entry The previous version of the entry.
	 *
	 * @return array
	 */
	public function maybe_filter_entry_pre_update( $entry, $original_entry ) {
		if ( ! $this->is_applicable_request( $entry['form_id'] ) ) {
			return $entry;
		}

		$this->log_debug( __METHOD__ . '(): Running for partial entry #' . $entry['id'] );
		$this->_saved_entry = $original_entry;

		if ( is_array( $this->_form ) ) {
			$entry = $this->restore_fields( $entry, $original_entry, $this->_form );
		}

		return $this->restore_meta( $entry, $original_entry );
	}

	/**
	 * Restores the field value as the entry is updated by GFFormsModel::save_entry().
	 *
	 * @since 2.7.4
	 *
	 * @param mixed         $value    The field value to be saved.
	 * @param array         $entry    The entry being populated with the values to be saved.
	 * @param null|GF_Field $field    The field currently being processed.
	 * @param array         $form     The form currently being processed.
	 * @param int|string    $input_id The field or input ID being processed.
	 *
	 * @return mixed
	 */
	public function filter_save_field_value( $value, $entry, $field, $form, $input_id ) {
		if ( ! is_array( $this->_saved_entry ) || ! $field instanceof GF_Field || ! $field->is_administrative() ) {
			return $value;
		}

		return $this->restore_value( $value, $this->_saved_entry, (string) $input_id );
	}

	// # WORKFLOW PROCESSING ------------------------------------------------------------------------------------------

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
			$this->log_debug( __METHOD__ . '(): Deleting draft submission.' );
			GFFormsModel::delete_draft_submission( $entry['resume_token'] );
		}

		$this->log_debug( __METHOD__ . '(): Deleting partial entry meta.' );
		$add_on    = GF_Partial_Entries::get_instance();
		$meta_keys = array_keys( $add_on->get_entry_meta( array(), $form_id ) );

		foreach ( $meta_keys as $meta_key ) {
			gform_delete_meta( $entry_id, $meta_key );
		}
	}

	// # HELPERS ------------------------------------------------------------------------------------------------------

	/**
	 * Writes a message to the Gravity Flow log.
	 *
	 * @since 2.7.4
	 *
	 * @param string $message The message to be logged.
	 */
	private function log_debug( $message ) {
		gravity_flow()->log_debug( $message );
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
	 * Determines if the current request is for a partial entry and that workflow processing of partial entries is enabled for the form.
	 *
	 * @since 2.7.4
	 *
	 * @param int $form_id The ID of the form currently being processed.
	 *
	 * @return bool
	 */
	private function is_applicable_request( $form_id ) {
		return ( rgpost( 'action' ) === 'heartbeat' || rgpost( 'is_submit_' . $form_id ) )
		       && rgpost( 'partial_entry_id' )
		       && $this->is_workflow_enabled( $form_id );
	}

	/**
	 * Validates the form has an id and at least one field.
	 *
	 * @since 2.7.4
	 *
	 * @param array $form The form currently being processed.
	 *
	 * @return bool
	 */
	private function is_valid_form( $form ) {
		return ! empty( $form['id'] ) && ! empty( $form['fields'] ) && is_array( $form['fields'] );
	}

	/**
	 * Restores the values of multiple fields to the partial entry.
	 *
	 * @since 2.7.4
	 *
	 * @param array $partial_entry The partial entry values to be saved to the database.
	 * @param array $saved_entry   The previous version of the entry.
	 * @param array $form          The form currently being processed.
	 *
	 * @return array
	 */
	private function restore_fields( $partial_entry, $saved_entry, $form ) {
		$this->log_debug( __METHOD__ . '(): Running for partial entry #' . $saved_entry['id'] );

		/** @var GF_Field $field */
		foreach ( $form['fields'] as $field ) {
			if ( $field->displayOnly || ! $field->is_administrative() ) {
				continue;
			}

			$inputs = $field->get_entry_inputs();

			if ( is_array( $inputs ) ) {
				foreach ( $inputs as $input ) {
					$partial_entry = $this->restore_value( $partial_entry, $saved_entry, (string) $input['id'] );
				}
			} else {
				$partial_entry = $this->restore_value( $partial_entry, $saved_entry, (string) $field->id );
			}

		}

		return $partial_entry;
	}

	/**
	 * Restores the value for a specific field or input.
	 *
	 * @since 2.7.4
	 *
	 * @param mixed|array $item        The field value or entry being processed.
	 * @param array       $saved_entry The previous version of the entry.
	 * @param string      $key         The ID of the field or input being processed.
	 *
	 * @return mixed|array
	 */
	private function restore_value( $item, $saved_entry, $key ) {
		if ( ! isset( $saved_entry[ $key ] ) || rgblank( $saved_entry[ $key ] ) ) {
			return $item;
		}

		if ( ! empty( $item['form_id'] ) ) {
			$this->log_debug( __METHOD__ . sprintf( '(): Restoring value for field #%s on partial entry update.', $key ) );

			$item[ $key ] = $saved_entry[ $key ];

			return $item;
		}

		$this->log_debug( __METHOD__ . sprintf( '(): Restoring value for field #%s on form submission.', $key ) );

		return $saved_entry[ $key ];
	}

	/**
	 * Restores the workflow meta values.
	 *
	 * @since 2.7.4
	 *
	 * @param array $partial_entry The partial entry values to be saved to the database.
	 * @param array $saved_entry   The previous version of the entry.
	 *
	 * @return array
	 */
	private function restore_meta( $partial_entry, $saved_entry ) {
		$this->log_debug( __METHOD__ . '(): Running for partial entry #' . $saved_entry['id'] );

		return array_replace( $partial_entry, $this->get_workflow_meta( $saved_entry ) );
	}

	/**
	 * Gets the workflow meta from the saved entry.
	 *
	 * @since 2.7.4
	 *
	 * @param array $saved_entry The previous version of the entry.
	 *
	 * @return array
	 */
	private function get_workflow_meta( $saved_entry ) {
		return array_filter( $saved_entry, function ( $key ) {
			return strpos( $key, 'workflow_' ) === 0;
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * Prevent other calls to GFAPI::update_entry() running our functionality.
	 *
	 * @since 2.7.4
	 */
	public function remove_filter_entry_pre_update() {
		remove_filter( 'gform_entry_pre_update', array( $this, 'maybe_filter_entry_pre_update' ) );
	}

}

Gravity_Flow_Partial_Entries::get_instance();
