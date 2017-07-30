<?php
/**
 * Gravity Flow Step Webhook
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Webhook
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Gravity Flow Web Hook
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Webhook
 * @copyright   Copyright (c) 2015-2016, Steven Henty
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
class Gravity_Flow_Step_Webhook extends Gravity_Flow_Step {
	public $_step_type = 'webhook';

	public function get_label() {
		return esc_html__( 'Outgoing Webhook', 'gravityflow' );
	}

	public function get_icon_url() {
		return '<i class="fa fa-external-link"></i>';
	}

	public function get_settings() {

		return array(
			'title'  => esc_html__( 'Outgoing Webhook', 'gravityflow' ),
			'fields' => array(
				array(
					'name'  => 'url',
					'class' => 'large',
					'label' => esc_html__( 'Outgoing Webhook URL', 'gravityflow' ),
					'type'  => 'text',
				),
				array(
					'name'          => 'method',
					'label'         => esc_html__( 'Request Method', 'gravityflow' ),
					'type'          => 'select',
					'default_value' => 'post',
					'onchange'    => "jQuery(this).closest('form').submit();",
					'choices'       => array(
						array(
							'label' => 'POST',
							'value' => 'post',
						),
						array(
							'label' => 'GET',
							'value' => 'get',
						),
						array(
							'label' => 'PUT',
							'value' => 'put',
						),
						array(
							'label' => 'DELETE',
							'value' => 'delete',
						),
					),
				),
				array(
					'name'          => 'format',
					'label'         => esc_html__( 'Format', 'gravityflow' ),
					'type'          => 'select',
					'default_value' => 'json',
					'choices'       => array(
						array(
							'label' => 'JSON',
							'value' => 'json',
						),
						array(
							'label' => 'FORM',
							'value' => 'form',
						),
					),
				),
				array(
					'name' => 'body_type',
					'label' => esc_html__( 'Request Body', 'gravityflow' ),
					'type' => 'radio',
					'default_value' => 'all_fields',
					'horizontal' => true,
					'onchange'    => "jQuery(this).closest('form').submit();",
					'choices' => array(
						array(
							'label' => __( 'All Fields', 'gravityflow' ),
							'value' => 'all_fields',
						),
						array(
							'label' => __( 'Select Fields', 'gravityflow' ),
							'value' => 'select_fields',
						),
					),
					'dependency' => array(
						'field'  => 'method',
						'values' => array( 'post', 'put' ),
					),
				),
				array(
					'name' => 'mappings',
					'label' => esc_html__( 'Field Values', 'gravityflow' ),
					'type' => 'generic_map',
					'enable_custom_key' => false,
					'enable_custom_value' => true,
					'key_field_title' => esc_html__( 'Key', 'gravityflow' ),
					'value_field_title' => esc_html__( 'Value', 'gravityflow' ),
					'value_choices' => $this->value_mappings(),
					'tooltip'   => '<h6>' . esc_html__( 'Mapping', 'gravityflow' ) . '</h6>' . esc_html__( 'Map the fields of this form to the selected form. Values from this form will be saved in the entry in the selected form' , 'gravityflow' ),
					'dependency' => array(
						'field'  => 'body_type',
						'values' => array( 'select_fields' ),
					),
				),
			),
		);
	}

	/**
	 * Process the step. For example, assign to a user, send to a service, send a notification or do nothing. Return (bool) $complete.
	 *
	 * @return bool Is the step complete?
	 */
	function process() {

		$this->send_webhook();

		return true;
	}

	/**
	 * Processes the webhook request.
	 *
	 * @return string The step status.
	 */
	function send_webhook() {

		$entry = $this->get_entry();

		$url = $this->url;

		$this->log_debug( __METHOD__ . '() - url before replacing variables: ' . $url );

		$url = GFCommon::replace_variables( $url, $this->get_form(), $entry, true, false, false, 'text' );

		$this->log_debug( __METHOD__ . '() - url after replacing variables: ' . $url );

		$method = strtoupper( $this->method );

		$body = null;

		$headers = array();

		if ( in_array( $method, array( 'POST', 'PUT' ) ) ) {
			$body = $this->get_request_body();
			if ( $this->format == 'json' ) {
				$headers = array( 'Content-type' => 'application/json' );
				$body    = json_encode( $body );
			} else {
				$headers = array();
			}
		}

		$args = array(
			'method'      => $method,
			'timeout'     => 45,
			'redirection' => 3,
			'blocking'    => true,
			'headers'     => $headers,
			'body'        => $body,
			'cookies'     => array(),
		);

		$args = apply_filters( 'gravityflow_webhook_args', $args, $entry, $this );
		$args = apply_filters( 'gravityflow_webhook_args_' . $this->get_form_id(), $args, $entry, $this );

		$response = wp_remote_request( $url, $args );

		$this->log_debug( __METHOD__ . '() - response: ' . print_r( $response, true ) );

		if ( is_wp_error( $response ) ) {
			$step_status = 'error';
		} else {
			$step_status = 'success';
		}

		$this->add_note( sprintf( esc_html__( 'Webhook sent. URL: %s', 'gravityflow' ), $url ) );

		do_action( 'gravityflow_post_webhook', $response, $args, $entry, $this );

		return $step_status;
	}

	/**
	 * Prepare value map.
	 *
	 * @return array
	 */
	public function value_mappings() {

		$form = $this->get_form();

		$fields = $this->get_field_map_choices( $form );
		return $fields;
	}

	/**
	 * Returns the choices for the source field mappings.
	 *
	 * @param $form
	 * @param null|string $field_type
	 * @param null|array $exclude_field_types
	 *
	 * @return array
	 */
	public function get_field_map_choices( $form, $field_type = null, $exclude_field_types = null ) {

		$fields = array();

		// Setup first choice
		if ( rgblank( $field_type ) || ( is_array( $field_type ) && count( $field_type ) > 1 ) ) {

			$first_choice_label = __( 'Select a Field', 'gravityflow' );

		} else {

			$type = is_array( $field_type ) ? $field_type[0] : $field_type;
			$type = ucfirst( GF_Fields::get( $type )->get_form_editor_field_title() );

			$first_choice_label = sprintf( __( 'Select a %s Field', 'gravityflow' ), $type );

		}

		$fields[] = array( 'value' => '', 'label' => $first_choice_label );

		// if field types not restricted add the default fields and entry meta
		if ( is_null( $field_type ) ) {
			$fields[] = array( 'value' => 'id', 'label' => esc_html__( 'Entry ID', 'gravityflow' ) );
			$fields[] = array( 'value' => 'date_created', 'label' => esc_html__( 'Entry Date', 'gravityflow' ) );
			$fields[] = array( 'value' => 'ip', 'label' => esc_html__( 'User IP', 'gravityflow' ) );
			$fields[] = array( 'value' => 'source_url', 'label' => esc_html__( 'Source Url', 'gravityflow' ) );
			$fields[] = array( 'value' => 'created_by', 'label' => esc_html__( 'Created By', 'gravityflow' ) );

			$entry_meta = GFFormsModel::get_entry_meta( $form['id'] );
			foreach ( $entry_meta as $meta_key => $meta ) {
				$fields[] = array( 'value' => $meta_key, 'label' => rgars( $entry_meta, "{$meta_key}/label" ) );
			}
		}

		// Populate form fields
		if ( is_array( $form['fields'] ) ) {
			foreach ( $form['fields'] as $field ) {
				$input_type = $field->get_input_type();
				$inputs     = $field->get_entry_inputs();
				$field_is_valid_type = ( empty( $field_type ) || ( is_array( $field_type ) && in_array( $input_type, $field_type ) ) || ( ! empty( $field_type ) && $input_type == $field_type ) );

				if ( is_null( $exclude_field_types ) ) {
					$exclude_field = false;
				} elseif ( is_array( $exclude_field_types ) ) {
					if ( in_array( $input_type, $exclude_field_types ) ) {
						$exclude_field = true;
					} else {
						$exclude_field = false;
					}
				} else {
					//not array, so should be single string
					if ( $input_type == $exclude_field_types ) {
						$exclude_field = true;
					} else {
						$exclude_field = false;
					}
				}

				if ( is_array( $inputs ) && $field_is_valid_type && ! $exclude_field ) {
					//If this is an address field, add full name to the list
					if ( $input_type == 'address' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
						);
					}
					//If this is a name field, add full name to the list
					if ( $input_type == 'name' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
						);
					}
					//If this is a checkbox field, add to the list
					if ( $input_type == 'checkbox' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Selected', 'gravityflow' ) . ')',
						);
					}

					foreach ( $inputs as $input ) {
						$fields[] = array(
							'value' => $input['id'],
							'label' => GFCommon::get_label( $field, $input['id'] ),
						);
					}
				} elseif ( $input_type == 'list' && $field->enableColumns && $field_is_valid_type && ! $exclude_field ) {
					$fields[] = array(
						'value' => $field->id,
						'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
					);
					$col_index = 0;
					foreach ( $field->choices as $column ) {
						$fields[] = array(
							'value' => $field->id . '.' . $col_index,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html( rgar( $column, 'text' ) ) . ')',
						);
						$col_index ++;
					}
				} elseif ( ! rgar( $field, 'displayOnly' ) && $field_is_valid_type && ! $exclude_field ) {
					$fields[] = array( 'value' => $field->id, 'label' => GFCommon::get_label( $field ) );
				}
			}
		}

		return $fields;
	}

	/**
	 * Returns the request body.
	 *
	 * @return array|null The request body.
	 */
	public function get_request_body() {
		$entry = $this->get_entry();
		if ( empty( $this->body_type ) || $this->body_type == 'all_fields' ) {
			return $entry;
		}

		return $this->do_request_body_mapping();
	}

	/**
	 * Performs the body's response mappings.
	 */
	public function do_request_body_mapping() {
		$body = array();

		if ( ! is_array( $this->mappings ) ) {

			return $body;
		}

		foreach ( $this->mappings as $mapping ) {
			if ( rgblank( $mapping['key'] ) ) {
				continue;
			}

			$body = $this->add_mapping_to_body( $mapping, $body );
		}

		return $body;
	}

	/**
	 * Add the mapped value to the body.
	 *
	 * @param array $mapping The properties for the mapping being processed.
	 * @param array $body The body to sent.
	 *
	 * @return array
	 */
	public function add_mapping_to_body( $mapping, $body ) {
		$target_field_id = trim( $mapping['custom_key'] );

		$source_field_id = (string) $mapping['value'];

		$entry = $this->get_entry();

		$form = $this->get_form();

		$source_field = GFFormsModel::get_field( $form, $source_field_id );

		if ( is_object( $source_field ) ) {
			$is_full_source      = $source_field_id === (string) intval( $source_field_id );
			$source_field_inputs = $source_field->get_entry_inputs();

			if ( $is_full_source && is_array( $source_field_inputs ) ) {
				$body[ $target_field_id ] = $source_field->get_value_export( $entry, $source_field_id, true );
			} else {
				$body[ $target_field_id ] = $this->get_source_field_value( $entry, $source_field, $source_field_id );
			}
		} elseif ( $source_field_id == 'gf_custom' ) {
			$body[ $target_field_id ] = GFCommon::replace_variables( $mapping['custom_value'], $form, $entry, false, false, false, 'text' );
		} else {
			$body[ $target_field_id ] = $entry[ $source_field_id ];
		}

		return $body;
	}

	/**
	 * Get the source field value.
	 *
	 * Returns the choice text instead of the unique value for choice based poll, quiz and survey fields.
	 *
	 * The source field choice unique value will not match the target field unique value.
	 *
	 * @param array $entry The entry being processed by this step.
	 * @param GF_Field $source_field The source field being processed.
	 * @param string $source_field_id The ID of the source field or input.
	 *
	 * @return string
	 */
	public function get_source_field_value( $entry, $source_field, $source_field_id ) {
		$field_value = $entry[ $source_field_id ];

		if ( in_array( $source_field->type, array( 'poll', 'quiz', 'survey' ) ) ) {
			if ( $source_field->inputType == 'rank' ) {
				$values = explode( ',', $field_value );
				foreach ( $values as &$value ) {
					$value = $this->get_source_choice_text( $value, $source_field );
				}

				return implode( ',', $values );
			}

			if ( $source_field->inputType == 'likert' && $source_field->gsurveyLikertEnableMultipleRows ) {
				list( $row_value, $field_value ) = rgexplode( ':', $field_value, 2 );
			}

			return $this->get_source_choice_text( $field_value, $source_field );
		}

		return $field_value;
	}

	/**
	 * Gets the choice text for the supplied choice value.
	 *
	 * @param string $selected_choice The choice value from the source field.
	 * @param GF_Field $source_field The source field being processed.
	 *
	 * @return string
	 */
	public function get_source_choice_text( $selected_choice, $source_field ) {
		return $this->get_choice_property( $selected_choice, $source_field->choices, 'value', 'text' );
	}

	/**
	 * Helper to get the specified choice property for the selected choice.
	 *
	 * @param string $selected_choice The selected choice value or text.
	 * @param array $choices The field choices.
	 * @param string $compare_property The choice property the $selected_choice is to be compared against.
	 * @param string $return_property The choice property to be returned.
	 *
	 * @return string
	 */
	public function get_choice_property( $selected_choice, $choices, $compare_property, $return_property ) {
		if ( $selected_choice && is_array( $choices ) ) {
			foreach ( $choices as $choice ) {
				if ( $choice[ $compare_property ] == $selected_choice ) {
					return $choice[ $return_property ];
				}
			}
		}

		return $selected_choice;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Webhook() );
