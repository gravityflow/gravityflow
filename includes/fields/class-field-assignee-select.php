<?php
/**
 * Gravity Flow Assignee Field
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Field_Assignee_Select
 */
class Gravity_Flow_Field_Assignee_Select extends GF_Field_Select {

	/**
	 * The field type.
	 *
	 * @var string
	 */
	public $type = 'workflow_assignee_select';

	/**
	 * Indicates if this field type can be used when configuring conditional logic rules.
	 *
	 * @return bool
	 */
	public function is_conditional_logic_supported() {
		return false;
	}

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
			'gravityflow_setting_assignees',
		);
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
	 * Returns the field title.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return __( 'Assignee', 'gravityflow' );
	}

	/**
	 * Return the HTML markup for the field choices.
	 *
	 * @param string $value The field value.
	 *
	 * @return string
	 */
	public function get_choices( $value ) {

		$include_users  = (bool) $this->gravityflowAssigneeFieldShowUsers;
		$include_roles  = (bool) $this->gravityflowAssigneeFieldShowRoles;
		$include_fields = (bool) $this->gravityflowAssigneeFieldShowFields;

		$choices = $this->get_assignees_as_choices( $value, $include_users, $include_roles, $include_fields );

		return $choices;
	}

	/**
	 * Get account choices.
	 *
	 * @since 2.5.2
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return array|mixed|void
	 */
	public function get_account_choices( $form_id ) {
		$account_choices = array();

		$args = array(
			'orderby' => array( 'display_name', 'user_login' ),
			'fields'  => array( 'ID', 'display_name', 'user_login' ),
			'role'    => $this->gravityflowUsersRoleFilter,
		);

		$args     = apply_filters( 'gravityflow_get_users_args_assignee_field', $args, $form_id, $this );
		$accounts = get_users( $args );
		foreach ( $accounts as $account ) {
			$account_choices[] = array( 'value' => 'user_id|' . $account->ID, 'text' => $account->display_name );
		}

		$account_choices = apply_filters( 'gravityflow_assignee_field_users', $account_choices, $form_id, $this );

		return $account_choices;
	}

	/**
	 * Get fields choices.
	 *
	 * @since 2.5.2
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return array|mixed|void
	 */
	public function get_fields_choices( $form_id ) {
		$fields_choices = array(
			array(
				'text'  => __( 'User (Created by)', 'gravityflow' ),
				'value' => 'entry|created_by',
			),
		);

		$fields_choices = apply_filters( 'gravityflow_assignee_field_fields', $fields_choices, $form_id, $this );

		return $fields_choices;
	}

	/**
	 * Return the HTML markup for the field choices.
	 *
	 * @param string $value          The field value.
	 * @param bool   $include_users  Indicates if the users should be added as choices.
	 * @param bool   $include_roles  Indicates if the roles should be added as choices.
	 * @param bool   $include_fields Indicates if the fields should be added as choices.
	 *
	 * @return string
	 */
	public function get_assignees_as_choices( $value, $include_users = true, $include_roles = true, $include_fields = true ) {
		$form_id   = $this->formId;
		$optgroups = array();

		if ( $include_users ) {
			$account_choices = $this->get_account_choices( $form_id );

			if ( ! empty( $account_choices ) ) {
				$users_opt_group = new GF_Field();
				//Placeholder set to None to prevents GFCommon::get_select_choices() adding an empty option when the view query arg is set to entry.
				$users_opt_group->placeholder = 'None';
				$users_opt_group->choices     = $account_choices;

				$optgroups[] = array(
					'label'   => __( 'Users', 'gravityflow' ),
					'choices' => GFCommon::get_select_choices( $users_opt_group, $value, false ),
				);
			}
		}


		if ( $include_roles ) {
			$role_choices = Gravity_Flow_Common::get_roles_as_choices( true, true, true );
			$role_choices = apply_filters( 'gravityflow_assignee_field_roles', $role_choices, $form_id, $this );

			if ( ! empty( $role_choices ) ) {
				$roles_opt_group = new GF_Field();
				//Placeholder set to None to prevents GFCommon::get_select_choices() adding an empty option when the view query arg is set to entry.
				$roles_opt_group->placeholder = 'None';
				$roles_opt_group->choices     = $role_choices;

				$optgroups[] = array(
					'label'   => __( 'Roles', 'gravityflow' ),
					'key'     => 'roles',
					'choices' => GFCommon::get_select_choices( $roles_opt_group, $value, false ),
				);
			}
		}

		if ( $include_fields ) {
			$form_id = $this->formId;
			$form    = GFAPI::get_form( $form_id );
			if ( rgar( $form, 'requireLogin' ) ) {
				$fields_choices = $this->get_fields_choices( $form_id );

				if ( ! empty( $fields_choices ) ) {
					$fields_opt_group = new GF_Field();
					//Placeholder set to None to prevents GFCommon::get_select_choices() adding an empty option when the view query arg is set to entry.
					$fields_opt_group->placeholder = 'None';
					$fields_opt_group->choices     = $fields_choices;

					$optgroups[] = array(
						'label'   => __( 'Fields', 'gravityflow' ),
						'choices' => GFCommon::get_select_choices( $fields_opt_group, $value, false ),
					);
				}
			}
		}

		$html = '';

		if ( ! empty( $this->placeholder ) ) {
			$selected = empty( $value ) ? "selected='selected'" : '';
			$html     = sprintf( "<option value='' %s class='gf_placeholder'>%s</option>", $selected, esc_html( $this->placeholder ) );
		}

		foreach ( $optgroups as $optgroup ) {
			$html .= sprintf( '<optgroup label="%s">%s</optgroup>', $optgroup['label'], $optgroup['choices'] );
		}

		return $html;
	}

	/**
	 * Return the values of the field choices.
	 *
	 * @since 2.5.2
	 *
	 * @param bool $include_users Indicates if the users should be added as choices.
	 * @param bool $include_roles Indicates if the roles should be added as choices.
	 * @param bool $include_fields Indicates if the fields should be added as choices.
	 *
	 * @return array
	 */
	public function get_choices_values( $include_users = true, $include_roles = true, $include_fields = true ) {
		$values  = array();
		$form_id = $this->formId;

		if ( $include_users ) {
			$account_choices = $this->get_account_choices( $form_id );

			if ( ! empty( $account_choices ) ) {
				$values = array_merge( wp_list_pluck( $account_choices, 'value' ), $values );
			}
		}

		if ( $include_roles ) {
			$role_choices = Gravity_Flow_Common::get_roles_as_choices( true, true, true );
			$role_choices = apply_filters( 'gravityflow_assignee_field_roles', $role_choices, $form_id, $this );

			if ( ! empty( $role_choices ) ) {
				$values = array_merge( wp_list_pluck( $role_choices, 'value' ), $values );
			}
		}

		if ( $include_fields ) {
			$form_id = $this->formId;
			$form    = GFAPI::get_form( $form_id );
			if ( rgar( $form, 'requireLogin' ) ) {
				$fields_choices = $this->get_fields_choices( $form_id );

				if ( ! empty( $fields_choices ) ) {
					$values = array_merge( wp_list_pluck( $fields_choices, 'value' ), $values );
				}
			}
		}

		return $values;
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
		$value = $this->get_display_name( $value, $modifier, $url_encode, $esc_html );

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
	 * Gets the display name for the selected choice (assignee).
	 *
	 * @param string $assignee   The assignee key.
	 * @param string $modifier   The merge tag modifier.
	 * @param bool   $url_encode Indicates if the urlencode function may have been applied to the $value.
	 * @param bool   $esc_html   Indicates if the esc_html function may have been applied to the $value.
	 *
	 * @return string
	 */
	public function get_display_name( $assignee, $modifier = '', $url_encode = false, $esc_html = true ) {
		if ( empty( $assignee ) ) {
			return '';
		}
		list( $type, $value ) = explode( '|', $assignee, 2 );
		switch ( $type ) {
			case 'role' :
				$value = translate_user_role( $value );
				break;
			case 'user_id' :
				$value = Gravity_Flow_Fields::get_user_variable( $value, $modifier );
		}

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

		$assignee = rgar( $entry, $input_id );

		list( $type, $value ) = explode( '|', $assignee, 2 );
		switch ( $type ) {
			case 'role':
				$value = translate_user_role( $value );
				break;
			case 'user_id':
				if ( $use_text == false && $is_csv == false ) {
					$value = $assignee;
				} else {
					$value = $this->get_display_name( $assignee );
				}
		}

		return $value;
	}

	/**
	 * Sanitize the field settings when the form is saved.
	 */
	public function sanitize_settings() {
		parent::sanitize_settings();
		if ( ! empty( $this->gravityflowUsersRoleFilter ) ) {
			$this->gravityflowUsersRoleFilter = wp_strip_all_tags( $this->gravityflowUsersRoleFilter );
		}

		$this->gravityflowAssigneeFieldShowUsers  = (bool) $this->gravityflowAssigneeFieldShowUsers;
		$this->gravityflowAssigneeFieldShowRoles  = (bool) $this->gravityflowAssigneeFieldShowRoles;
		$this->gravityflowAssigneeFieldShowFields = (bool) $this->gravityflowAssigneeFieldShowFields;
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
			$include_users  = (bool) $this->gravityflowAssigneeFieldShowUsers;
			$include_roles  = (bool) $this->gravityflowAssigneeFieldShowRoles;
			$include_fields = (bool) $this->gravityflowAssigneeFieldShowFields;

			$values = $this->get_choices_values( $include_users, $include_roles, $include_fields );

			if ( ! in_array( $value, $values, true ) ) {
				$this->failed_validation  = true;
				$this->validation_message = esc_html__( 'Invalid selection. Please select one of the available choices.', 'gravityflow' );
			}
		}
	}
}

GF_Fields::register( new Gravity_Flow_Field_Assignee_Select() );
