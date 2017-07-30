<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Field_User
 *
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class Gravity_Flow_Field_User extends GF_Field_Select {

	public $type = 'workflow_user';

	public function add_button( $field_groups ) {
		$field_groups = Gravity_Flow_Fields::maybe_add_workflow_field_group( $field_groups );

		return parent::add_button( $field_groups );
	}

	public function get_form_editor_button() {
		return array(
			'group' => 'workflow_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

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

	public function get_form_editor_field_title() {
		return __( 'User', 'gravityflow' );
	}

	public function get_choices( $value ) {
		if ( $this->is_form_editor() ) {
			// Prevent the choices from being stored in the form meta
			$this->choices = array();
		}

		return parent::get_choices( $value );
	}

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

	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		$assignee = parent::get_value_entry_list( $value, $entry, $field_id, $columns, $form );
		$value    = $this->get_display_name( $assignee );

		return $value;
	}


	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {

		return Gravity_Flow_Fields::get_user_variable( $value, $modifier, $url_encode, $esc_html );
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		$assignee = parent::get_value_entry_detail( $value, $currency, $use_text, $format, $media );
		$value    = $this->get_display_name( $assignee );

		return $value;
	}

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
	 * @param array $entry The entry currently being processed.
	 * @param string $input_id The field or input ID.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param bool|false $is_csv Is the value going to be used in the .csv entries export?
	 *
	 * @return string
	 */
	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		return $this->get_display_name( rgar( $entry, $input_id ) );
	}

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
