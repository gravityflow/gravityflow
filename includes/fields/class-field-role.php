<?php
/**
 * Gravity Flow Role Field
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Field_Role
 */
class Gravity_Flow_Field_Role extends GF_Field_Select {

	/**
	 * The field type.
	 *
	 * @var string
	 */
	public $type = 'workflow_role';

	/**
	 * Adds the Workflow Fields group to the form editor.
	 *
	 * @param array $field_groups The properties for the field groups.
	 *
	 * @return array
	 */
	public function add_button( $field_groups ) {
		$field_groups = Gravity_Flow_Fields::maybe_add_workflow_field_group( $field_groups );

		return parent::add_button( $field_groups );
	}

	/**
	 * Returns the field button properties for the form editor.
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'workflow_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	/**
	 * Returns the class names of the settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'enable_enhanced_ui_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'size_setting',
			'rules_setting',
			'placeholder_setting',
			'default_value_setting',
			'visibility_setting',
			'duplicate_setting',
			'description_setting',
			'css_class_setting',
		);
	}

	/**
	 * Returns the field title.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return __( 'Role', 'gravityflow' );
	}

	/**
	 * Return the HTML markup for the field choices.
	 *
	 * @param string $value The field value.
	 *
	 * @return string
	 */
	public function get_choices( $value ) {
		if ( $this->is_form_editor() ) {
			// Prevent the choices from being stored in the form meta.
			$this->choices = array();
		}

		return parent::get_choices( $value );
	}

	/**
	 * Get an array of choices containing the user roles.
	 *
	 * @return array
	 */
	public function get_roles_as_choices() {
		$role_choices = Gravity_Flow_Common::get_roles_as_choices( false, false, true );
		$form_id      = $this->formId;

		return apply_filters( 'gravityflow_role_field', $role_choices, $form_id, $this );
	}

	/**
	 * Return the entry value for display on the entries list page.
	 *
	 * @param string|array $value    The field value.
	 * @param array        $entry    The Entry Object currently being processed.
	 * @param string       $field_id The field or input ID currently being processed.
	 * @param array        $columns  The properties for the columns being displayed on the entry list page.
	 * @param array        $form     The Form Object currently being processed.
	 *
	 * @return string
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		$assignee = parent::get_value_entry_list( $value, $entry, $field_id, $columns, $form );
		$value    = $this->get_display_name( $assignee );

		return $value;
	}

	/**
	 * Return the entry value which will replace the field merge tag.
	 *
	 * @param string       $value      The field value. Depending on the location the merge tag is being used the following functions may have already been applied to the value: esc_html, nl2br, and urlencode.
	 * @param string       $input_id   The field or input ID from the merge tag currently being processed.
	 * @param array        $entry      The Entry Object currently being processed.
	 * @param array        $form       The Form Object currently being processed.
	 * @param string       $modifier   The merge tag modifier. e.g. value.
	 * @param string|array $raw_value  The raw field value from before any formatting was applied to $value.
	 * @param bool         $url_encode Indicates if the urlencode function may have been applied to the $value.
	 * @param bool         $esc_html   Indicates if the esc_html function may have been applied to the $value.
	 * @param string       $format     The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param bool         $nl2br      Indicates if the nl2br function may have been applied to the $value.
	 *
	 * @return string
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		$value = $this->get_display_name( $value );

		return $value;
	}

	/**
	 * Return the entry value for display on the entry detail page and for the {all_fields} merge tag.
	 *
	 * @param string     $value    The field value.
	 * @param string     $currency The entry currency code.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param string     $format   The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param string     $media    The location where the value will be displayed. Possible values: screen or email.
	 *
	 * @return string
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		$assignee = parent::get_value_entry_detail( $value, $currency, $use_text, $format, $media );
		$value    = $this->get_display_name( $assignee );

		return $value;
	}

	/**
	 * Gets the display name for the selected role.
	 *
	 * @param string $value The role name.
	 *
	 * @return string
	 */
	public function get_display_name( $value ) {
		$value = translate_user_role( $value );

		return $value;
	}

	/**
	 * Format the entry value before it is used in entry exports and by framework add-ons using GFAddOn::get_field_value().
	 *
	 * @param array      $entry    The entry currently being processed.
	 * @param string     $input_id The field or input ID.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param bool|false $is_csv   Indicates if the value is going to be used in the .csv entries export.
	 *
	 * @return string
	 */
	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		return $this->get_display_name( rgar( $entry, $input_id ) );
	}

	/**
	 * Add the roles as choices.
	 *
	 * @since 1.7.1-dev
	 */
	public function post_convert_field() {
		if ( ! $this->is_form_editor() ) {
			$this->choices = $this->get_roles_as_choices();
		}
	}

	/**
	 * Validate the field value. It must be one of the choices.
	 *
	 * Return the result (bool) by setting $this->failed_validation.
	 * Return the validation message (string) by setting $this->validation_message.
	 *
	 * @since 2.5.2
	 *
	 * @param string|array $value The field value from get_value_submission().
	 * @param array        $form  The Form Object currently being processed.
	 */
	public function validate( $value, $form ) {
		if ( ! empty( $value ) ) {
			$values = wp_list_pluck( $this->get_roles_as_choices(), 'value' );

			if ( ! in_array( $value, $values, true ) ) {
				$this->failed_validation  = true;
				$this->validation_message = esc_html__( 'Invalid selection. Please select one of the available choices.', 'gravityflow' );
			}
		}
	}
}

GF_Fields::register( new Gravity_Flow_Field_Role() );
