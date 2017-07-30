<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Field_Role
 *
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class Gravity_Flow_Field_Role extends GF_Field_Select {

	public $type = 'workflow_role';

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
		);
	}

	public function get_form_editor_field_title() {
		return __( 'Role', 'gravityflow' );
	}

	public function get_choices( $value ) {
		if ( $this->is_form_editor() ) {
			// Prevent the choices from being stored in the form meta
			$this->choices = array();
		}

		return parent::get_choices( $value );
	}

	public function get_roles_as_choices() {
		$role_choices = Gravity_Flow_Common::get_roles_as_choices( false, false, true );
		$form_id      = $this->formId;

		return apply_filters( 'gravityflow_role_field', $role_choices, $form_id, $this );
	}

	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		$assignee = parent::get_value_entry_list( $value, $entry, $field_id, $columns, $form );
		$value = $this->get_display_name( $assignee );

		return $value;
	}


	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		$value = $this->get_display_name( $value );

		return $value;
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		$assignee = parent::get_value_entry_detail( $value, $currency, $use_text, $format, $media );
		$value    = $this->get_display_name( $assignee );

		return $value;
	}

	public function get_display_name( $value ) {
		$value = translate_user_role( $value );

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
}

GF_Fields::register( new Gravity_Flow_Field_Role() );
