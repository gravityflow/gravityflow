<?php

namespace Gravity_Flow\Settings\Fields;

use Rocketgenius\Gravity_Forms\Settings\Fields;

defined( 'ABSPATH' ) || die();

// Load base classes.
require_once \GFCommon::get_base_path() . '/includes/settings/class-fields.php';


class Checkbox_And_Textarea extends \Rocketgenius\Gravity_Forms\Settings\Fields\Base {

	/**
	 * Field type.
	 *
	 * @since 2.6
	 *
	 * @var string
	 */
	public $type = 'checkbox_and_textarea';

	/**
	 * Child inputs.
	 *
	 * @since 2.6
	 *
	 * @var Base[]
	 */
	public $inputs = array();

	/**
	 * Initialize Checkbox and Textarea field.
	 *
	 * @since 2.6
	 *
	 * @param array                                $props    Field properties.
	 * @param \Rocketgenius\Gravity_Forms\Settings $settings Settings instance.
	 */
	public function __construct( $props, $settings ) {

		parent::__construct( $props, $settings );

		// Prepare Checkbox field.
		$checkbox_input           = rgars( $props, 'checkbox' );
		$checkbox_field           = array(
			'type'       => 'checkbox',
			'name'       => rgar( $props, 'name' ) . 'Enable',
			'label'      => esc_html__( 'Enable', 'gravityflow' ),
			'horizontal' => false,
			'value'      => '1',
			'choices'    => false,
			'tooltip'    => false,
		);
		$this->inputs['checkbox'] = wp_parse_args( $checkbox_input, $checkbox_field );

		// Prepare Textaea field.
		$textarea_input           = rgars( $props, 'textarea' );
		$textarea_field           = array(
			'name'    => rgar( $props, 'name' ) . 'Value',
			'type'    => 'textarea',
			'class'   => '',
			'tooltip' => false,
		);
		$textarea_field['class']  .= ' ' . $textarea_field['name'];
		$this->inputs['textarea'] = wp_parse_args( $textarea_input, $textarea_field );

		// Add on change event to Checkbox.
		if ( empty( $this->inputs['checkbox']['choices'] ) ) {
			$this->inputs['checkbox']['choices'] = array(
				array(
					'name'     => $this->inputs['checkbox']['name'],
					'label'    => $this->inputs['checkbox']['label'],
					'onchange' => sprintf(
						"( function( $, elem ) {
						$( elem ).parents( 'td' ).css( 'position', 'relative' );
						if( $( elem ).prop( 'checked' ) ) {
							$( '%1\$s' ).show();
						} else {
							$( '%1\$s' ).hide();
						}
					} )( jQuery, this );",
						"#{$this->inputs['textarea']['name']}Span" ),
				),
			);
		}

		/**
		 * Prepare input fields.
		 *
		 * @var array $input
		 */
		foreach ( $this->inputs as &$input ) {
			$input = Fields::create( $input, $this->settings );
		}

	}





	// # RENDER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Render field.
	 *
	 * @since 2.6
	 *
	 * @return string
	 */
	public function markup() {

		// Prepare markup.
		// Display description.
		$html = $this->get_description();

		$html .= sprintf(
			'<span class="%s">%s <span id="%s" style="%s">%s %s</span></span>',
			esc_attr( $this->get_container_classes() ),
			$this->inputs['checkbox']->markup(),
			$this->inputs['textarea']->name . 'Span',
			$this->inputs['checkbox']->get_value() ? '' : 'display: none;',
			$this->inputs['textarea']->markup(),
			$this->settings->maybe_get_tooltip( $this->inputs['textarea'] )
		);

		$html .= $this->get_error_icon();

		return $html;

	}





	// # VALIDATION METHODS --------------------------------------------------------------------------------------------

	/**
	 * Validate posted field value.
	 *
	 * @since 2.6
	 *
	 * @param array $values Posted field values.
	 */
	public function is_valid( $values ) {

		$this->inputs['checkbox']->is_valid( $values );
		$this->inputs['textarea']->is_valid( $values );

	}

}

Fields::register( 'checkbox_and_textarea', '\Gravity_Flow\Settings\Fields\Checkbox_and_Textarea' );
