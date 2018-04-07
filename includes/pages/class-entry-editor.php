<?php
/**
 * Gravity Flow Entry Editor
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Entry_Editor
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.0.30
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Entry_Editor
 */
class Gravity_Flow_Entry_Editor {

	/**
	 * The Gravity Forms form array.
	 *
	 * @var array
	 */
	public $form;

	/**
	 * The Gravity Forms Entry array.
	 *
	 * @var array
	 */
	public $entry;

	/**
	 * The current step.
	 *
	 * @var Gravity_Flow_Step $step
	 */
	public $step;

	/**
	 * Used to help determine whether to display the order summary.
	 *
	 * @var bool
	 */
	public $has_product_fields = false;

	/**
	 * Flag set in the constructor to control the visibility of empty fields.
	 *
	 * @var bool
	 */
	public $display_empty_fields;

	/**
	 * Indicates if dynamic conditional logic is enabled.
	 *
	 * @var bool
	 */
	private $_is_dynamic_conditional_logic_enabled;

	/**
	 * An array of field IDs which the user can edit.
	 *
	 * @var array
	 */
	private $_editable_fields;

	/**
	 * An array of field IDs of display fields.
	 *
	 * @var array
	 */
	private $_display_fields = array();

	/**
	 * An array of field IDs required for use with calculations.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var array
	 */
	private $_calculation_dependencies = array();

	/**
	 * The content to be displayed for the display fields.
	 *
	 * @var array
	 */
	private $_non_editable_field_content = array();

	/**
	 * The init scripts to be deregistered.
	 *
	 * @var array
	 */
	private $_non_editable_field_script_names = array();

	/**
	 * The Form Object after the non-editable and non-display fields have been removed.
	 *
	 * @var array
	 */
	private $_modified_form;

	/**
	 * Used to help determine whether the gform_product_total script should be output for the coupon field.
	 *
	 * @var bool
	 */
	private $_has_editable_product_field = false;

	/**
	 * Gravity_Flow_Entry_Editor constructor.
	 *
	 * @param array             $form                 The current form.
	 * @param array             $entry                The current entry.
	 * @param Gravity_Flow_Step $step                 The current step.
	 * @param bool              $display_empty_fields Indicates if empty fields should be displayed.
	 */
	public function __construct( $form, $entry, $step, $display_empty_fields ) {
		$this->form                                  = $form;
		$this->entry                                 = $entry;
		$this->step                                  = $step;
		$this->display_empty_fields                  = $display_empty_fields;
		$this->_is_dynamic_conditional_logic_enabled = $this->is_dynamic_conditional_logic_enabled();
		$this->_editable_fields                      = $step->get_editable_fields();
	}


	/**
	 * Renders the form. Uses GFFormDisplay::get_form() to display the fields.
	 */
	public function render_edit_form() {
		$this->add_hooks();

		// Impersonate front-end form.
		unset( $_GET['page'] );

		require_once( GFCommon::get_base_path() . '/form_display.php' );

		$html = GFFormDisplay::get_form( $this->form['id'], false, false, true, $this->entry );

		$this->remove_hooks();

		echo $html;
	}

	/**
	 * Add the filters and actions required to modify the form markup for this step.
	 */
	public function add_hooks() {
		add_filter( 'gform_pre_render', array( $this, 'filter_gform_pre_render' ), 999 );
		add_filter( 'gform_submit_button', '__return_empty_string' );
		add_filter( 'gform_disable_view_counter', '__return_true' );
		add_filter( 'gform_field_input', array( $this, 'filter_gform_field_input' ), 10, 2 );
		add_filter( 'gform_form_tag', '__return_empty_string' );
		add_filter( 'gform_get_form_filter', array( $this, 'filter_gform_get_form_filter' ) );
		add_filter( 'gform_field_container', array( $this, 'filter_gform_field_container' ), 10, 2 );
		add_filter( 'gform_has_conditional_logic', array( $this, 'filter_gform_has_conditional_logic' ), 10, 2 );
		add_filter( 'gform_field_css_class', array( $this, 'filter_gform_field_css_class' ), 10, 2 );

		add_action( 'gform_register_init_scripts', array( $this, 'deregsiter_init_scripts' ), 11 );
	}

	/**
	 * Remove the filters and actions.
	 */
	public function remove_hooks() {
		remove_filter( 'gform_pre_render', array( $this, 'filter_gform_pre_render' ), 999 );
		remove_filter( 'gform_submit_button', '__return_empty_string' );
		remove_filter( 'gform_disable_view_counter', '__return_true' );
		remove_filter( 'gform_field_input', array( $this, 'filter_gform_field_input' ), 10 );
		remove_filter( 'gform_form_tag', '__return_empty_string' );
		remove_filter( 'gform_get_form_filter', array( $this, 'filter_gform_get_form_filter' ) );
		remove_filter( 'gform_field_container', array( $this, 'filter_gform_field_container' ), 10 );
		remove_filter( 'gform_has_conditional_logic', array( $this, 'filter_gform_has_conditional_logic' ), 10 );
		remove_filter( 'gform_field_css_class', array( $this, 'filter_gform_field_css_class' ), 10 );

		remove_action( 'gform_register_init_scripts', array( $this, 'deregsiter_init_scripts' ), 11 );
	}

	/**
	 * Target of the gform_pre_render filter.
	 * Removes the page fields from the form.
	 *
	 * @param array $form The current form.
	 *
	 * @return array The filtered form.
	 */
	public function filter_gform_pre_render( $form ) {
		$form                              = $this->remove_page_fields( $form );
		$fields                            = array();
		$dynamic_conditional_logic_enabled = $this->_is_dynamic_conditional_logic_enabled;

		/**
		 * Process all other field types.
		 *
		 * @var GF_Field $field
		 */
		foreach ( $form['fields'] as $field ) {
			if ( $field->type == 'section' ) {
				// Unneeded section fields will be removed via filter_gform_field_container().
				$field->adminOnly = false;
				$fields[]         = $field;
				continue;
			}

			if ( $dynamic_conditional_logic_enabled ) {
				$conditional_logic_fields      = GFFormDisplay::get_conditional_logic_fields( $form, $field->id );
				$field->conditionalLogicFields = $conditional_logic_fields;
			}

			$field->gravityflow_is_display_field = $this->is_display_field( $field );

			// Remove unneeded fields from the form to prevent JS errors resulting from scripts expecting fields to be present and visible.
			if ( $this->can_remove_field( $field ) ) {
				continue;
			}

			$is_product_field = GFCommon::is_product_field( $field->type );

			if ( ! $this->has_product_fields && $is_product_field ) {
				$this->has_product_fields = true;
			}

			if ( ! $this->is_editable_field( $field ) ) {
				$content = $this->get_non_editable_field( $field );

				if ( empty( $content ) ) {
					continue;
				}

				$this->_non_editable_field_content[ $field->id ] = $content;
				$this->_non_editable_field_script_names[]        = $field->type . '_' . $field->id;

				if ( $field->type == 'tos' ) {
					$field->gwtermsofservice_require_scroll = false;
				}

				$field->description = null;
				$field->maxLength   = null;
			} else {
				$field->gravityflow_is_editable = true;
				if ( ! $this->_has_editable_product_field && $is_product_field && $field->type != 'total' ) {
					$this->_has_editable_product_field = true;
				}
			}

			if ( empty( $field->label ) ) {
				$field->label = $field->adminLabel;
			}

			$field->adminOnly  = false;
			$field->adminLabel = '';

			if ( $field->type === 'hidden' ) {
				// Render hidden fields as text fields.
				$field       = new GF_Field_Text( $field );
				$field->type = 'text';
			}

			$fields[] = $field;
		}

		$form['fields']       = $fields;
		$this->_modified_form = $form;

		return $form;
	}

	/**
	 * Removes the form button logic and page fields so they are not taken into account when processing conditional logic for other fields.
	 * Also disables save and continue.
	 *
	 * @param array $form The form currently being processed.
	 *
	 * @return array
	 */
	public function remove_page_fields( $form ) {
		unset( $form['save'] );
		unset( $form['button']['conditionalLogic'] );

		$dynamic_conditional_logic_enabled = $this->_is_dynamic_conditional_logic_enabled;

		/* @var GF_Field $field */
		foreach ( $form['fields'] as $key => $field ) {
			if ( $field->type == 'page' ) {
				unset( $form['fields'][ $key ] );
				continue;
			}

			$is_applicable_field = $this->is_editable_field( $field );

			if ( $is_applicable_field && $field->has_calculation() ) {
				$this->set_calculation_dependencies( $field->calculationFormula );
			}

			if ( ! $is_applicable_field ) {
				// Populate the $_display_fields array.
				$is_applicable_field = $this->is_display_field( $field, true );
			}

			if ( ! $dynamic_conditional_logic_enabled || ! $is_applicable_field ) {
				// Clear the field conditional logic properties as conditional logic is not enabled for the step or the field is not for display or editable.
				$field->conditionalLogicFields = null;
				$field->conditionalLogic       = null;
			}
		}

		return $form;
	}

	/**
	 * Add the IDs of any fields in the formula to the $_calculation_dependencies array.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $formula The calculation formula to be evaluated.
	 */
	public function set_calculation_dependencies( $formula ) {
		if ( empty( $formula ) ) {
			return;
		}

		preg_match_all( '/{[^{]*?:(\d+).*?}/mi', $formula, $matches, PREG_SET_ORDER );
		if ( ! empty( $matches ) ) {
			foreach ( $matches as $match ) {
				$field_id = rgar( $match, 1 );
				if ( $field_id && ! $this->is_calculation_dependency( $field_id ) ) {
					$this->_calculation_dependencies[] = $field_id;
				}
			}
		}
	}

	/**
	 * Checks whether a field is required for calculations.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param GF_Field|string $field The field object or field ID to be checked.
	 *
	 * @return bool
	 */
	public function is_calculation_dependency( $field ) {
		$field_id = is_object( $field ) ? $field->id : $field;

		return in_array( $field_id, $this->_calculation_dependencies );
	}

	/**
	 * Determines if the field can be removed from the form object.
	 *
	 * Fields involved in conditional logic must always be added to the form.
	 *
	 * @param GF_Field $field The current field.
	 *
	 * @return bool
	 */
	public function can_remove_field( $field ) {
		$can_remove_field = ! ( $this->is_editable_field( $field ) || $this->is_display_field( $field ) || $this->is_calculation_dependency( $field ) ) && empty( $field->conditionalLogicFields );

		return $can_remove_field;
	}

	/**
	 * Target for the gform_field_input filter.
	 *
	 * Handles the construction of the field input. Returns markup for the editable field or the display value.
	 *
	 * @param string   $html  The field input markup.
	 * @param GF_Field $field The current field.
	 *
	 * @return string
	 */
	public function filter_gform_field_input( $html, $field ) {

		if ( ! $this->is_editable_field( $field ) ) {
			return rgar( $this->_non_editable_field_content, $field->id );
		}

		if ( ! empty( $html ) ) {
			// the field input has already been set via the gform_field_input filter. e.g. the Signature Add-On < v3.
			return $html;
		}

		$posted_form_id = rgpost( 'gravityflow_submit' );
		if ( $posted_form_id == $this->form['id'] && rgpost( 'step_id' ) == $this->step->get_id() ) {
			// Updated or failed validation.
			$value = GFFormsModel::get_field_value( $field );
		} else {
			$value = GFFormsModel::get_lead_field_value( $this->entry, $field );
			if ( $field->get_input_type() == 'email' && $field->emailConfirmEnabled ) {
				$_POST[ 'input_' . $field->id . '_2' ] = $value;
			}

			if ( $field->get_input_type() == 'multiselect' && $field->storageType === 'json' ) {
				$value = json_decode( $value, true );
			}
		}

		if ( $field->get_input_type() == 'fileupload' ) {
			$field->_is_entry_detail = true;
		}

		$value = apply_filters( 'gravityflow_field_value_entry_editor', $value, $field, $this->form, $this->entry, $this->step );

		$value = $this->get_post_image_value( $value, $field );
		$value = $this->get_post_category_value( $value, $field );

		$html = $field->get_field_input( $this->form, $value, $this->entry );
		$html .= $this->maybe_get_coupon_script( $field );

		if ( $field->type === 'chainedselect' && function_exists( 'gf_chained_selects' ) ) {
			if ( ! wp_script_is( 'gform_chained_selects' ) ) {
				wp_enqueue_script( 'gform_chained_selects' );
				gf_chained_selects()->localize_scripts();
			}

			if ( ! $this->_is_dynamic_conditional_logic_enabled && wp_script_is( 'gform_conditional_logic' ) ) {
				$script = "if ( typeof window.gf_form_conditional_logic === 'undefined' ) { window.gf_form_conditional_logic = []; }";
				GFFormDisplay::add_init_script( $field->formId, 'conditional_logic', GFFormDisplay::ON_PAGE_RENDER, $script );
			}
		}

		return $html;
	}

	/**
	 * Ensures the post image field value is in the correct format for populating the field.
	 *
	 * @since 2.1.2-dev
	 *
	 * @param string|array $value The field value.
	 * @param GF_Field     $field The current field object.
	 *
	 * @return string|array
	 */
	public function get_post_image_value( $value, $field ) {
		if ( $field->type !== 'post_image' || empty( $value ) || ! is_string( $value ) || strpos( $value, '|:|' ) === false ) {
			return $value;
		}

		$array = explode( '|:|', $value );
		$value = array(
			$field->id . '.1' => rgar( $array, 1 ), // Title.
			$field->id . '.4' => rgar( $array, 2 ), // Caption.
			$field->id . '.7' => rgar( $array, 3 ), // Description.
		);

		$path_info = pathinfo( rgar( $array, 0 ) );
		if ( ! isset( GFFormsModel::$uploaded_files[ $field->formId ]["input_{$field->id}"] ) ) {
			GFFormsModel::$uploaded_files[ $field->formId ]["input_{$field->id}"] = $path_info['basename'];
		}

		return $value;
	}

	/**
	 * Ensures the post category field value is in the correct format for populating the field.
	 *
	 * @since 2.1.1-dev
	 *
	 * @param string|array $value The field value.
	 * @param GF_Field     $field The current field object.
	 *
	 * @return string|array
	 */
	public function get_post_category_value( $value, $field ) {
		if ( $field->type !== 'post_category' || empty( $value ) ) {
			return $value;
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				if ( ! empty( $item ) ) {
					$value[ $key ] = $this->get_post_category_id( $item );
				}
			}
		} else {
			$value = $this->get_post_category_id( $value );
		}

		return $value;
	}

	/**
	 * Returns the post category id from the supplied value.
	 *
	 * The entry value will be in the format "category_name:category_id".
	 *
	 * @since 2.1.1-dev
	 *
	 * @param string $value The field value.
	 *
	 * @return string
	 */
	public function get_post_category_id( $value ) {
		$parts = explode( ':', $value );

		return isset( $parts[1] ) ? $parts[1] : $parts[0];
	}

	/**
	 * Get the gform_product_total script for the coupon field when there aren't any editable product fields.
	 *
	 * @param GF_Field $field The field currently being processed.
	 *
	 * @return string
	 */
	public function maybe_get_coupon_script( $field ) {
		if ( $field->type != 'coupon' || $this->_has_editable_product_field ) {
			return '';
		}

		$total = GFCommon::get_order_total( $this->form, $this->entry );

		return "<script type='text/javascript'>gform.addFilter('gform_product_total', function (total, formId) {return {$total};}, 49);</script>";
	}

	/**
	 * Checks whether dynamic conditional logic is enabled.
	 *
	 * @return bool
	 */
	public function is_dynamic_conditional_logic_enabled() {
		return $this->step && $this->step->conditional_logic_editable_fields_enabled && $this->step->conditional_logic_editable_fields_mode != 'page_load' && gravity_flow()->fields_have_conditional_logic( $this->form );
	}

	/**
	 * Target for the gform_get_form_filter filter.
	 * Strips the closing form tag and replaces the Gravity Forms token for Gravity Flow's token.
	 *
	 * @param string $form_string The form markup.
	 *
	 * @return string
	 */
	public function filter_gform_get_form_filter( $form_string ) {
		$form_string = str_replace( 'gform_submit', 'gravityflow_submit', $form_string );
		$form_string = str_replace( '</form>', '', $form_string );

		return $form_string;
	}

	/**
	 * Generates and returns the markup for a display field.
	 *
	 * @param GF_Field $field The current field object.
	 *
	 * @return string
	 */
	public function get_non_editable_field( $field ) {

		if ( $field->type == 'html' ) {
			$html = GFCommon::replace_variables( $field->content, $this->form, $this->entry, false, true, false, 'html' );
			$html = do_shortcode( $html );

			return $html;
		}

		$html  = '';

		$value = RGFormsModel::get_lead_field_value( $this->entry, $field );

		$conditional_logic_dependency = $this->_is_dynamic_conditional_logic_enabled && ! empty( $field->conditionalLogicFields );

		if ( $conditional_logic_dependency || $this->is_calculation_dependency( $field ) ) {
			$html = $field->get_field_input( $this->form, $value, $this->entry );
		}

		if ( ! $this->is_display_field( $field ) ) {

			return $html;
		}

		if ( $html ) {
			$html = '<div style="display:none;">' . $html . '</div>';
		}

		$value = $this->maybe_get_product_calculation_value( $value, $field );

		$input_type = $field->get_input_type();
		if ( $input_type == 'hiddenproduct' ) {
			$display_value = $value[ $field->id . '.2' ];
		} else {
			$display_value = GFCommon::get_lead_field_display( $field, $value, $this->entry['currency'] );
		}

		$display_value = apply_filters( 'gform_entry_field_value', $display_value, $field, $this->entry, $this->form );

		if ( $this->display_empty_fields ) {
			if ( empty( $display_value ) || $display_value === '0' ) {
				$display_value = '&nbsp;';
			}
			$display_value = sprintf( '<div class="gravityflow-field-value">%s<div>', $display_value );
		} else {
			if ( empty( $display_value ) || $display_value === '0' ) {
				$display_value = '';
			} else {
				$display_value = sprintf( '<div class="gravityflow-field-value">%s<div>', $display_value );
			}
		}

		$html .= $display_value;

		return $html;
	}

	/**
	 * If this is a calculated product field ensure the input values are set.
	 *
	 * @param mixed    $value The field value.
	 * @param GF_Field $field The current field object.
	 *
	 * @return mixed
	 */
	public function maybe_get_product_calculation_value( $value, $field ) {
		if ( $field->type == 'product' && $field->has_calculation() ) {
			$product_name = trim( $value[ $field->id . '.1' ] );
			$price        = trim( $value[ $field->id . '.2' ] );
			$quantity     = trim( $value[ $field->id . '.3' ] );

			if ( empty( $product_name ) ) {
				$value[ $field->id . '.1' ] = $field->get_field_label( false, $value );
			}

			if ( empty( $price ) ) {
				$value[ $field->id . '.2' ] = '0';
			}

			if ( empty( $quantity ) ) {
				$value[ $field->id . '.3' ] = '0';
			}
		}

		return $value;
	}

	/**
	 * Checks whether the given field is a display field and whether it should be displayed.
	 *
	 * @param GF_Field $field   The field to be checked.
	 * @param bool     $is_init Return after checking the $_display_fields array? Default is false.
	 *
	 * @return bool
	 */
	public function is_display_field( $field, $is_init = false ) {
		if ( in_array( $field->id, $this->_display_fields ) ) {
			return true;
		}

		if ( ! $is_init ) {
			return false;
		}

		$display_field = Gravity_Flow_Common::is_display_field( $field, $this->step, $this->form, $this->entry );

		if ( $display_field ) {
			$this->_display_fields[] = $field->id;
		}

		return $display_field;
	}

	/**
	 * Checks whether a field is an editable field.
	 *
	 * @param GF_Field $field The field to be checked.
	 *
	 * @return bool
	 */
	public function is_editable_field( $field ) {
		return Gravity_Flow_Common::is_editable_field( $field, $this->step );
	}

	/**
	 * Check if the current field is hidden.
	 *
	 * @param GF_Field $field The field to be checked.
	 *
	 * @return bool
	 */
	public function is_hidden_field( $field ) {

		return ! $this->is_editable_field( $field ) && ! $this->is_display_field( $field ) && isset( $this->_non_editable_field_content[ $field->id ] );
	}

	/**
	 * Check if the display mode is selected_fields and that all this sections fields are hidden.
	 *
	 * @param GF_Field[] $section_fields The fields located in the current section.
	 *
	 * @return bool
	 */
	public function section_fields_hidden( $section_fields ) {
		if ( $this->step->display_fields_mode == 'selected_fields' ) {
			foreach ( $section_fields as $field ) {
				if ( ! $this->is_hidden_field( $field ) ) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Checks whether the section should be hidden for the given section field.
	 *
	 * Hidden sections contain no editable fields and no non-empty display fields.
	 *
	 * @param GF_Field_Section $section_field  The current section field.
	 * @param GF_Field[]       $section_fields The fields located in the current section.
	 *
	 * @return bool
	 */
	public function is_section_hidden( $section_field, $section_fields ) {
		if ( ! empty( $section_fields ) ) {
			foreach ( $section_fields as $field ) {
				if ( $this->is_editable_field( $field ) || $this->is_display_field( $field ) ) {

					return false;
				}
			}

			if ( $this->step->display_fields_mode == 'all_fields' ) {

				return GFCommon::is_section_empty( $section_field, $this->_modified_form, $this->entry ) || ! $this->display_empty_fields;
			}
		}

		return true;
	}

	/**
	 * Retrieve an array of fields located within the specified section.
	 *
	 * @param int $section_field_id The ID of the current section field.
	 *
	 * @return array
	 */
	public function get_section_fields( $section_field_id ) {
		$section_fields = GFCommon::get_section_fields( $this->_modified_form, $section_field_id );
		if ( count( $section_fields ) >= 1 ) {
			// Remove the section field.
			unset( $section_fields[0] );
		}

		return $section_fields;
	}

	/**
	 * Target for the gform_field_container filter.
	 *
	 * Removes the markup completely for section fields that are hidden.
	 *
	 * Fields with conditional logic remain on the form to avoid JS errors.
	 *
	 * @param string   $field_container The field container HTML.
	 * @param GF_Field $field           The current field object.
	 *
	 * @return string
	 */
	public function filter_gform_field_container( $field_container, $field ) {
		if ( $field->type == 'section' ) {
			$section_fields = $this->get_section_fields( $field->id );

			if ( $this->section_fields_hidden( $section_fields )
			     || ( $this->is_section_hidden( $field, $section_fields ) && empty( $field->conditionalLogic ) ) // Section fields with conditional logic must be added to the form so fields inside the section can be hidden or displayed dynamically.
			) {
				return '';
			}
		}

		if ( $this->is_hidden_field( $field ) ) {
			$field_container = sprintf( '<li id="field_%s_%s" style="display:none;">%s</li>', $field->formId, $field->id, $this->_non_editable_field_content[ $field->id ] );
		}

		return $field_container;
	}

	/**
	 * Target for the gform_has_conditional_logic filter.
	 *
	 * Checks the conditional logic setting and configures the form accordingly.
	 *
	 * @return bool
	 */
	public function filter_gform_has_conditional_logic() {

		return $this->_is_dynamic_conditional_logic_enabled;
	}

	/**
	 * Target for the gform_field_css_class filter.
	 *
	 * Checks the step settings and adds the appropriate classes.
	 *
	 * @param string   $classes The field classes.
	 * @param GF_Field $field   The current field object.
	 *
	 * @return string
	 */
	public function filter_gform_field_css_class( $classes, $field ) {
		$is_editable = $this->is_editable_field( $field );
		$class       = $is_editable ? 'gravityflow-editable-field' : 'gravityflow-display-field';
		if ( $is_editable && $this->step->highlight_editable_fields_enabled ) {
			$class .= ' ' . $this->step->highlight_editable_fields_class;
		}

		$classes .= ' ' . $class;

		return $classes;
	}

	/**
	 * Deregister init scripts for any non-editable fields to prevent js errors.
	 *
	 * @param array $form The filtered form object.
	 */
	public function deregsiter_init_scripts( $form ) {
		$script_names = $this->_non_editable_field_script_names;
		if ( ! empty( $script_names ) ) {
			$init_scripts = GFFormDisplay::$init_scripts[ $form['id'] ];
			if ( ! empty( $init_scripts ) ) {
				$location = GFFormDisplay::ON_PAGE_RENDER;
				foreach ( $script_names as $name ) {
					unset( $init_scripts[ $name . '_' . $location ] );
				}
				GFFormDisplay::$init_scripts[ $form['id'] ] = $init_scripts;
			}

		}
	}
}
