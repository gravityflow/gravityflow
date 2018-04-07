<?php
/**
 * Gravity Flow GP Nested Forms
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_GP_Nested_Forms
 *
 * Enables the Nested Form field to function on the workflow detail page.
 *
 * @since 2.0.2-dev
 */
class Gravity_Flow_GP_Nested_Forms {

	/**
	 * The current form array.
	 *
	 * @since 2.0.2-dev
	 *
	 * @var array
	 */
	private $_form = array();

	/**
	 * The current entry array.
	 *
	 * @since 2.0.2-dev
	 *
	 * @var array
	 */
	private $_entry = array();

	/**
	 * The current step.
	 *
	 * @since 2.0.2-dev
	 *
	 * @var bool|Gravity_Flow_Step
	 */
	private $_current_step = false;

	/**
	 * The Nested Form IDs.
	 *
	 * @since 2.0.2-dev
	 *
	 * @var array
	 */
	private $_nested_forms = array();

	/**
	 * The instance of this class.
	 *
	 * @since 2.0.2-dev
	 *
	 * @var null|Gravity_Flow_GP_Nested_Forms
	 */
	private static $_instance = null;

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since 2.0.2-dev
	 *
	 * @return null|Gravity_Flow_GP_Nested_Forms
	 */
	public static function get_instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Gravity_Flow_GP_Nested_Forms constructor.
	 *
	 * Adds the hooks on the init action, after the GP Nested Form add-on has been loaded.
	 *
	 * @since 2.0.2-dev
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'maybe_add_hooks' ) );
	}

	/**
	 * Returns the parent entry step ID.
	 *
	 * @since 2.0.2-dev
	 *
	 * @return int
	 */
	private function get_step_id() {
		return absint( rgpost( 'step_id' ) );
	}

	/**
	 * Returns the parent entry ID.
	 *
	 * @since 2.0.2-dev
	 *
	 * @return int
	 */
	private function get_entry_id() {
		return absint( rgget( 'lid' ) );
	}

	/**
	 * Returns the parent form ID.
	 *
	 * @since 2.0.2-dev
	 *
	 * @return int
	 */
	private function get_form_id() {
		return absint( rgget( 'id' ) );
	}

	/**
	 * Returns the parent entry.
	 *
	 * @since 2.0.2-dev
	 *
	 * @return array
	 */
	private function get_entry() {
		if ( empty( $this->_entry ) ) {
			$this->_entry = GFAPI::get_entry( $this->get_entry_id() );
		}

		return $this->_entry;
	}

	/**
	 * Returns the parent form.
	 *
	 * @since 2.0.2-dev
	 *
	 * @return array
	 */
	private function get_form() {
		if ( empty( $this->_form ) ) {
			$this->_form = GFAPI::get_form( $this->get_form_id() );
		}

		return $this->_form;
	}

	/**
	 * Returns the current step or false.
	 *
	 * @since 2.0.2-dev
	 *
	 * @return bool|Gravity_Flow_Step
	 */
	private function get_current_step() {
		if ( empty( $this->_current_step ) ) {
			$this->_current_step = gravity_flow()->get_current_step( $this->get_form(), $this->get_entry() );
		}

		return $this->_current_step;
	}

	/**
	 * Returns the key to be used for the entry meta item.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param bool|int $step_id False or the parent forms current step ID.
	 *
	 * @return string
	 */
	private function get_meta_key( $step_id = false ) {
		if ( empty( $step_id ) ) {
			$step_id = $this->get_step_id();
		}

		return 'workflow_step_' . $step_id . '_process_nested_form';
	}

	/**
	 * Determines if this is a workflow detail page submission for the current step.
	 *
	 * @since 2.0.2-dev
	 *
	 * @return bool
	 */
	private function is_current_step_submission() {
		return ! empty( $_POST ) && rgpost( 'gravityflow_submit' ) == rgar( $this->get_form(), 'id' ) && $this->get_step_id() == $this->get_current_step()->get_id();
	}

	/**
	 * If this is a workflow detail page submission and the form has a Nested Form field delete the cookie set by the perk.
	 *
	 * @since 2.0.2-dev
	 */
	private function maybe_delete_cookie() {
		if ( ! $this->is_current_step_submission() ) {
			return;
		}

		$nested_fields = GFAPI::get_fields_by_type( $this->get_form(), 'form' );

		if ( ! empty( $nested_fields ) ) {
			$session = new GPNF_Session( $this->get_form_id() );
			$session->delete_cookie();
		}
	}

	/**
	 * If the GP Nested Forms add-on is available add the appropriate hooks for the current location.
	 *
	 * @since 2.0.2-dev
	 */
	public function maybe_add_hooks() {
		if ( ! function_exists( 'gp_nested_forms' ) ) {
			return;
		}

		add_filter( 'gravityflow_status_filter', array( $this, 'filter_gravityflow_status_filter' ) );

		$this->maybe_add_detail_page_hooks();
	}

	/**
	 * If the Nested Forms query string parameters are present use them to configure the constraint filters.
	 *
	 * Ensures only the child entries belonging to specified parent entry are listed.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param array $args The status page constraint filters.
	 *
	 * @return array
	 */
	public function filter_gravityflow_status_filter( $args ) {
		$parent_entry_id      = rgget( GPNF_Entry::ENTRY_PARENT_KEY );
		$nested_form_field_id = rgget( GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY );

		if ( ! $parent_entry_id || ! $nested_form_field_id ) {
			return $args;
		}

		$args['form_id']         = rgget( 'id' );
		$args['field_filters'][] = array(
			'key'   => GPNF_Entry::ENTRY_PARENT_KEY,
			'value' => $parent_entry_id
		);
		$args['field_filters'][] = array(
			'key'   => GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY,
			'value' => $nested_form_field_id
		);

		return $args;
	}

	/**
	 * If this is the workflow detail page add the hooks which need loading first.
	 *
	 * @since 2.0.2-dev
	 */
	private function maybe_add_detail_page_hooks() {
		if ( ! gravity_flow()->is_workflow_detail_page() ) {
			return;
		}

		add_action( 'gform_after_update_entry', array( $this, 'action_gform_after_update_entry' ), 10, 3 );
		add_action( 'gravityflow_step_complete', array( $this, 'action_gravityflow_step_complete' ), 10, 5 );

		add_filter( 'gravityflow_field_value_entry_editor', array(
			$this,
			'filter_gravityflow_field_value_entry_editor'
		), 10, 5 );
		add_filter( 'gravityflow_is_delayed_pre_process_workflow', array(
			$this,
			'filter_gravityflow_is_delayed_pre_process_workflow'
		) );
		add_filter( 'gpnf_entry_url', array( $this, 'filter_gpnf_entry_url' ), 10, 3 );
		add_filter( 'gpnf_template_args', array( $this, 'filter_gpnf_template_args' ), 10, 2 );

		$this->maybe_delete_cookie();
	}

	/**
	 * Delays processing of the workflow for the child form entries.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param bool $is_delayed Indicates if workflow processing is delayed.
	 *
	 * @return bool
	 */
	public function filter_gravityflow_is_delayed_pre_process_workflow( $is_delayed ) {
		if ( gp_nested_forms()->is_nested_form_submission() ) {
			$parent_form       = GFAPI::get_form( gp_nested_forms()->get_parent_form_id() );
			$nested_form_field = gp_nested_forms()->get_posted_nested_form_field( $parent_form );
			$is_delayed        = $nested_form_field->gpnfFeedProcessing !== 'child';
		}

		return $is_delayed;
	}

	/**
	 * Replaces the entry detail page URL with the workflow detail page URL for the child entry.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param string $url      The entry detail page URL.
	 * @param int    $entry_id The child entry ID.
	 * @param int    $form_id  The Nested Form form ID.
	 *
	 * @return string
	 */
	public function filter_gpnf_entry_url( $url, $entry_id, $form_id ) {
		return add_query_arg( array( 'id' => $form_id, 'lid' => $entry_id ) );
	}

	/**
	 * Replaces the entries list page URL with the status page URL.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param array    $args  The arguments that will be used to render the Nested Form field template.
	 * @param GF_Field $field The Nested Form field.
	 *
	 * @return array
	 */
	public function filter_gpnf_template_args( $args, $field ) {
		if ( isset( $args['related_entries_link'] ) ) {
			$url                          = $this->get_related_entries_url( $field );
			$args['related_entries_link'] = $url ? preg_replace( '~href=".+"~', 'href="' . $url . '"', $args['related_entries_link'] ) : '';
		}

		return $args;
	}

	/**
	 * Returns the Status page URL configured with the Nested Forms query arguments for listing the child entries.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param GF_Field $field The Nested Form field.
	 *
	 * @return bool|string
	 */
	public function get_related_entries_url( $field ) {
		if ( ! GFAPI::current_user_can_any( array( 'gravityflow_status', 'gravityflow_status_view_all' ) ) ) {
			return false;
		}

		$url        = false;
		$query_args = array(
			'page'                                  => 'gravityflow-status',
			'id'                                    => $field->gpnfForm,
			GPNF_Entry::ENTRY_PARENT_KEY            => rgget( 'lid' ),
			GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY => $field->id,
		);

		if ( ! is_admin() ) {
			$page_id = gravity_flow()->get_app_setting( 'status_page' );
			if ( $page_id !== 'admin' ) {
				$url = get_permalink( $page_id );
			}

			if ( ! $url ) {
				return false;
			}

			$query_args['page'] = false;
		}

		return add_query_arg( $query_args, $url );
	}

	/**
	 * Determines if the current field is an editable Nested Form field so the required functionality can be included.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param mixed             $value        The current field value.
	 * @param GF_Field          $field        The current field.
	 * @param array             $form         The parent form.
	 * @param array             $entry        The parent entry.
	 * @param Gravity_Flow_Step $current_step The current step for the parent entry.
	 *
	 * @return mixed
	 */
	public function filter_gravityflow_field_value_entry_editor( $value, $field, $form, $entry, $current_step ) {
		if ( $field->type === 'form' ) {
			$this->_form           = $form;
			$this->_entry          = $entry;
			$this->_current_step   = $current_step;
			$this->_nested_forms[] = $field->gpnfForm;
			$this->add_late_hooks( $form['id'], $field->id );
		}

		return $value;
	}

	/**
	 * Includes the hooks which will enable the Nested Form field to function on the entry editor.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param int $form_id  The parent form ID.
	 * @param int $field_id The current Nested Form field ID.
	 */
	private function add_late_hooks( $form_id, $field_id ) {
		// Removing to prevent the child form markup being generated before the entry editor filters are removed.
		remove_action( 'gform_get_form_filter', array( gp_nested_forms(), 'handle_nested_forms_markup' ) );

		add_filter( "gpnf_init_script_args_{$form_id}_{$field_id}", array(
			$this,
			'filter_gpnf_init_script_args'
		), 10, 2 );

		if ( ! has_action( 'admin_footer', array( $this, 'output_nested_forms_markup' ) ) ) {
			add_action( 'admin_footer', array( $this, 'output_nested_forms_markup' ) );
		}

		if ( ! has_action( 'wp_footer', array( $this, 'output_nested_forms_markup' ) ) ) {
			add_action( 'wp_footer', array( $this, 'output_nested_forms_markup' ) );
		}
	}

	/**
	 * Generate and output the child form and modal markup for the Nested Form field in the page footer.
	 *
	 * @since 2.0.2-dev
	 */
	public function output_nested_forms_markup() {
		// Prevent GFCommon::get_field_input() displaying the "Product fields are not editable" message.
		unset( $_GET['view'] );
		echo gp_nested_forms()->get_nested_forms_markup( $this->get_form() );

		if ( is_admin() ) {
			// GP Nested Forms forces the child form init scripts into the footer and uses gform_footer_init_scripts_filter to modify them.
			// GFForms::get_form() does not call GFFormDisplay::footer_init_scripts() in the admin.
			foreach ( $this->_nested_forms as $nested_form_id ) {
				GFFormDisplay::footer_init_scripts( $nested_form_id );
			}
		}
	}

	/**
	 * If the child form has entries for this parent entry add them to the fields init script arguments so they will be listed in the table on field render.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param array    $args  The arguments that will be used to initialize the nested forms frontend script.
	 * @param GF_Field $field The Nested Form field object.
	 *
	 * @return array
	 */
	public function filter_gpnf_init_script_args( $args, $field ) {
		if ( empty( $args['entries'] ) && ! $this->is_current_step_submission() ) {
			$value   = GFFormsModel::get_lead_field_value( $this->get_entry(), $field );
			$entries = gp_nested_forms()->get_entries( $value );

			if ( ! empty( $entries ) ) {
				$nested_form = GFAPI::get_form( $field->gpnfForm );
				foreach ( $entries as $entry ) {
					$args['entries'][] = gp_nested_forms()->get_entry_display_values( $entry, $nested_form, $field->gpnfFields );
				}
			}
		}

		return $args;
	}

	/**
	 * Determines if the parent entry values of any Nested Form fields have changed so an entry meta item can be set to flag them for processing on step completion.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param array $form           The parent form.
	 * @param int   $entry_id       The parent entry ID.
	 * @param array $original_entry The parent entry before it was updated.
	 */
	public function action_gform_after_update_entry( $form, $entry_id, $original_entry ) {
		if ( ! $this->is_current_step_submission() ) {
			return;
		}

		foreach ( $form['fields'] as $field ) {
			if ( $field->type !== 'form' ) {
				continue;
			}

			$entry = GFAPI::get_entry( $entry_id );

			if ( rgar( $entry, $field->id ) !== rgar( $original_entry, $field->id ) ) {
				gform_update_meta( $entry_id, $this->get_meta_key(), true, $form['id'] );
				break;
			}
		}
	}

	/**
	 * Triggers processing of the Nested Form fields, if the entry meta item indicates processing is required.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param int               $step_id      The parent step ID.
	 * @param int               $entry_id     The parent entry ID.
	 * @param int               $form_id      The parent form ID.
	 * @param string            $status       The step status.
	 * @param Gravity_Flow_Step $current_step The step being completed by the parent form.
	 */
	public function action_gravityflow_step_complete( $step_id, $entry_id, $form_id, $status, $current_step ) {
		$meta_key            = $this->get_meta_key( $step_id );
		$requires_processing = gform_get_meta( $entry_id, $meta_key );
		if ( $requires_processing ) {
			$current_step->log_debug( __METHOD__ . '(): triggering processing of delayed nested form notifications and feeds.' );
			$entry = $current_step->get_entry();
			$form  = $current_step->get_form();
			$this->maybe_process_nested_forms( $entry, $form );
			gform_delete_meta( $entry_id, $meta_key );
		}
	}

	/**
	 * Triggers processing of any Nested Form fields for the supplied parent form and entry.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param array $entry The parent entry.
	 * @param array $form  The parent form.
	 */
	private function maybe_process_nested_forms( $entry, $form ) {
		remove_filter( 'gravityflow_is_delayed_pre_process_workflow', array(
			$this,
			'filter_gravityflow_is_delayed_pre_process_workflow'
		) );

		foreach ( $form['fields'] as $field ) {
			if ( $field->type !== 'form' ) {
				continue;
			}

			$this->maybe_process_nested_form( $field, $entry );
		}

		gpnf_notification_processing()->maybe_send_child_notifications( $entry, $form );
		gpnf_feed_processing()->process_feeds( $entry, $form );
	}

	/**
	 * Triggers processing of the child entries for the supplied Nested Form field.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param GF_Field $field The Nested Form field.
	 * @param array    $entry The parent entry.
	 */
	private function maybe_process_nested_form( $field, $entry ) {
		$child_entries = gp_nested_forms()->get_entries( rgar( $entry, $field->id ) );
		if ( empty( $child_entries ) ) {
			return;
		}

		$nested_form = GFAPI::get_form( $field->gpnfForm );

		foreach ( $child_entries as $child_entry ) {
			$this->process_child_entry( $child_entry, $nested_form, $field, $entry['id'] );
		}
	}

	/**
	 * Processes the child form entry.
	 *
	 * Creates the post, links the child entry with the parent entry, and starts the workflow.
	 *
	 * @since 2.0.2-dev
	 *
	 * @param array    $entry             The child form entry.
	 * @param array    $nested_form       The Nested Form.
	 * @param GF_Field $nested_form_field The Nested Form field from the parent form.
	 * @param int      $parent_entry_id   The parent entry ID.
	 */
	private function process_child_entry( $entry, $nested_form, $nested_form_field, $parent_entry_id ) {
		GFCommon::create_post( $nested_form, $entry );
		$entry_object = new GPNF_Entry( $entry );
		$entry_object->set_parent_form( $nested_form_field->formId, $parent_entry_id );

		if ( $nested_form_field->gpnfFeedProcessing === 'child' ) {
			return;
		}

		gravity_flow()->action_entry_created( $entry, $nested_form );
		gravity_flow()->process_workflow( $nested_form, $entry['id'] );
	}

}

Gravity_Flow_GP_Nested_Forms::get_instance();
