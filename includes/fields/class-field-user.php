<?php
/**
 * Gravity Flow User Field
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Field_User
 */
class Gravity_Flow_Field_User extends GF_Field_Select {

	/**
	 * The field type.
	 *
	 * @var string
	 */
	public $type = 'workflow_user';

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
			'gravityflow_setting_users_role_filter',
		);
	}

	/**
	 * Returns the field title.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return __( 'User', 'gravityflow' );
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
	 * Get an array of choices containing the users.
	 *
	 * @return array
	 */
	public function get_users_as_choices() {
		$form_id = $this->formId;

		$args = array(
			'orderby' => 'display_name',
			'role'    => $this->gravityflowUsersRoleFilter,
		);

		$args            = apply_filters( 'gravityflow_get_users_args_user_field', $args, $form_id, $this );
		$accounts        = get_users( $args );
		$account_choices = array();
		foreach ( $accounts as $account ) {
			$account_choices[] = array( 'value' => $account->ID, 'text' => $account->display_name );
		}

		return apply_filters( 'gravityflow_user_field', $account_choices, $form_id, $this );
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

		return Gravity_Flow_Fields::get_user_variable( $value, $modifier, $url_encode, $esc_html );
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
	 * Gets the display name for the selected user.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return string
	 */
	public function get_display_name( $user_id ) {
		if ( empty( $user_id ) ) {
			return '';
		}
		$user  = get_user_by( 'id', $user_id );
		$value = is_object( $user ) ? $user->display_name : $user_id;

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

		$user_id = rgar( $entry, $input_id );

		if ( $use_text == true || $is_csv == true ) {
			return $this->get_display_name( $user_id );
		}

		return $user_id;
	}

	/**
	 * Sanitize the field settings when the form is saved.
	 */
	public function sanitize_settings() {
		parent::sanitize_settings();
		if ( ! empty( $this->gravityflowUsersRoleFilter ) ) {
			$this->gravityflowUsersRoleFilter = wp_strip_all_tags( $this->gravityflowUsersRoleFilter );
		}
	}

	/**
	 * Add the users as choices.
	 *
	 * @since 1.7.1-dev
	 */
	public function post_convert_field() {
		if ( ! $this->is_form_editor() ) {
			$this->choices = $this->get_users_as_choices();
		}
	}
}

GF_Fields::register( new Gravity_Flow_Field_User() );
