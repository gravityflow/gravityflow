<?php
/**
 * Gravity Flow Step Update User
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Update_User
 * @copyright   Copyright (c) 2015-2019, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Update_User
 *
 * @since 2.5
 */
class Gravity_Flow_Step_Update_User extends Gravity_Flow_Step {

	/**
	 * The step type.
	 * 
	 * @since 2.5
	 *
	 * @var string
	 */
	public $_step_type = 'update-user';

	/**
	 * Returns the step label.
	 * 
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Update User', 'gravityflow' );
	}

	/**
	 * Returns the HTML for the step icon.
	 * 
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return '<i class="fa fa-envelope-o"></i>';
	}

	/**
	 * Returns an array of settings for this step type.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = array(
			'title'       => esc_html__( 'Update User', 'gravityflow' ),
			'description' => '',
			'fields'      => array(
				array(
					'name'     => 'first_name',
					'label'    => esc_html__( 'First Name', 'gravityflow' ),
					'type'     => 'field_select',
					'args'     => array(
						'disable_first_choice' => true,
						'append_choices' => array(
							array(
								'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current first name', 'gravityflow' ) ),
								'value' => '',
							),
						),
						'callback'       => array( $this, 'is_applicable_field_for_field_select' ),
					),
					'class'    => 'medium',
					'tooltip'  => sprintf( '<h6>%s</h6> %s', esc_html__( 'First Name', 'gravityflow' ), esc_html__( 'Select the form field that should be used for the user\'s first name.', 'gravityflow' ) )
				),
				array(
					'name'     => 'last_name',
					'label'    => esc_html__( 'Last Name', 'gravityflow' ),
					'type'     => 'field_select',
					'args'     => array(
						'disable_first_choice' => true,
						'append_choices' => array(
							array(
								'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current last name', 'gravityflow' ) ),
								'value' => '',
							),
						),
						'callback' => array( $this, 'is_applicable_field_for_field_select' )
					),
					'class'    => 'medium',
					'tooltip'  => sprintf( '<h6>%s</h6> %s', esc_html__( 'Last Name', 'gravityflow' ), esc_html__( 'Select the form field that should be used for the user\'s last name.', 'gravityflow' ) )
				),
				array(
					'name'     => 'nickname',
					'label'    => esc_html__( 'Nickname', 'gravityflow' ),
					'type'     => 'field_select',
					'args'     => array(
						'disable_first_choice' => true,
						'append_choices' => array(
							array(
								'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current nickname name', 'gravityflow' ) ),
								'value' => '',
							),
						),
						'callback' => array( $this, 'is_applicable_field_for_field_select' )
					),
					'class'    => 'medium',
					'tooltip'  => sprintf( '<h6>%s</h6> %s', esc_html__( 'Nickname', 'gravityflow' ), esc_html__( 'Select the form field that should be used for the user\'s nickname.', 'gravityflow' ) )
				),
				array(
					'name'     => 'displayname',
					'label'    => esc_html__( 'Display Name', 'gravityflow' ),
					'type'     => 'select',
					'class'    => 'medium',
					'choices'  => $this->get_display_name_choices(),
					'tooltip'  => sprintf( '<h6>%s</h6> %s', esc_html__( 'Display Name', 'gravityflow' ), esc_html__( 'Select how the user\'s name should be displayed publicly.', 'gravityflow' ) )
				),
				array(
					'name'        => 'email',
					'label'       => esc_html__( 'Email Address', 'gravityflow' ),
					'type'        => 'field_select',
					'args'        => array(
						'disable_first_choice' => true,
						'input_types'          => array( 'email' ),
						'append_choices'       => $this->get_extra_email_choice()
					),
					'class'       => 'medium',
					'tooltip'     => sprintf( '<h6>%s</h6> %s', esc_html__( 'Email Address', 'gravityflow' ), esc_html__( 'Select the form field that should be used for the user\'s email address.', 'gravityflow' ) )
				),
				array(
					'name'     => 'password',
					'label'    => esc_html__( 'Password', 'gravityflow' ),
					'type'     => 'field_select',
					'class'    => 'medium',
					'args'     => array(
						'disable_first_choice' => true,
						'input_types'          => array( 'password' ),
						'append_choices'       => $this->get_extra_password_choices()
					),
					'tooltip'  => sprintf( '<h6>%s</h6> %s', esc_html__( 'Password', 'gravityflow' ), esc_html__( 'Select the form field that should be used for the user\'s password.', 'gravityflow' ) )
				),
				array(
					'name'     => 'roles_action',
					'label'    => esc_html__( 'Roles', 'gravityflow' ),
					'type'     => 'select',
					'choices'  => array(
						array(
							'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current roles', 'gravityflow' ) ),
							'value' => 'preserve',
						),
						array(
							'label' => esc_html__( 'Replace current roles', 'gravityflow' ),
							'value' => 'replace',
						),
						array(
							'label' => esc_html__( 'Add to existing roles', 'gravityflow' ),
							'value' => 'add',
						),
					),
					'onchange'      => 'jQuery(this).closest("form").submit();',
					'class'    => 'medium',
					'tooltip'  => sprintf( '<h6>%s</h6> %s', esc_html__( 'Nickname', 'gravityflow' ), esc_html__( 'Select the form field that should be used for the user\'s nickname.', 'gravityflow' ) )
				),
				array(
					'name'     => 'roles',
					'label'    => esc_html__( 'Select Roles', 'gravityflow' ),
					'type'     => 'select',
					'multiple' => 'multiple',
					'class'    => 'medium',
					'choices'  => $this->get_roles_as_choices(),
					'tooltip'  => sprintf( '<h6>%s</h6> %s', esc_html__( 'Select Roles', 'gravityflow' ), esc_html__( 'Select the role the user should be assigned.', 'gravityflow' ) ),
					'dependency' => array(
						'field'  => 'roles_action',
						'values' => array( 'replace', 'add' ),
					)
				),
				array(
					'name'      => 'userMeta',
					'label'     => esc_html__( 'User Meta', 'gravityflow' ),
					'type'      => 'generic_map',
					'enable_custom_key' => false,
					'enable_custom_value' => true,
					'key_field_title' => esc_html__( 'Field', 'gravityflow' ),
					'value_field_title' => esc_html__( 'Value', 'gravityflow' ),
					'key_choices' => $this->get_user_meta_choices(),
					'value_choices' => $this->value_mappings(),
					'class'     => 'medium'
				),
			)
		);


		return $settings;

	}

	/**
	 * Triggers sending of the selected notifications.
	 *
	 * @return bool
	 */
	function process() {
		$this->log_debug( __METHOD__ . '(): starting' );

		

		return true;
	}

	public function get_display_name_choices() {

		$display_names = $this->get_display_names();
		$choices       = array();

		$choices[] =  array(
			'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current display name', 'gravityflow'  ) ),
			'value' => ''
		);

		foreach ( $display_names as $value => $label ) {
			$choices[] = array(
				'label' => $label,
				'value' => $value
			);
		}

		return $choices;
	}

	public function get_display_names() {

		$choices   = array();

		$choices['_preserve_display_name'] = sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current display name', 'gravityflow'  ) );

		$choices['nickname']  = '{nickname}';
		$choices['username']  = '{username}';
		$choices['firstname'] = '{first name}';
		$choices['lastname']  = '{last name}';
		$choices['firstlast'] = '{first name} {last name}';
		$choices['lastfirst'] = '{last name} {first name}';

		return $choices;

	}

	public function get_roles_as_choices() {

		$roles   = array_reverse( get_editable_roles() );

		foreach ( $roles as $role => $details ) {
			$name      = translate_user_role( $details['name'] );
			$choices[] = array(
				'label' => $name,
				'value' => $role
			);
		}

		return $choices;
	}

	/**
	 * Returns the "preserve current first name" choice.
	 *
	 * @since 3.8.2
	 *
	 * @return array
	 */
	public function get_extra_name_choice() {

		$choices   = array();
		$choices[] = array(
			'label' =>  sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current first name', 'gravityflow'  ) ),
			'value' => ''
		);


		return $choices;
	}

	/**
	 * Returns the "preserve current email" choice if this is an update type feed.
	 *
	 * @since 3.8.2
	 *
	 * @return array
	 */
	public function get_extra_email_choice() {

		$choices   = array();
		$choices[] = array(
			'label' =>  sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current email', 'gravityflow'  ) ),
			'value' => ''
		);


		return $choices;
	}

	public function get_extra_password_choices() {

		$choices   = array();

		$choices[] = array(
			'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current password', 'gravityflow'  ) ),
			'value' => ''
		);


		return $choices;
	}

	public function get_user_meta_choices() {
		global $wpdb;

		// standard meta
		$standard_meta = array(
			'label'   => esc_html__( 'Standard User Meta', 'gravityflow' ),
			'choices' => array(
				array(
					'label' => esc_html__( 'Website', 'gravityflow' ),
					'value' => 'user_url'
				),
				array(
					'label' => esc_html__( 'AIM', 'gravityflow' ),
					'value' => 'aim'
				),
				array(
					'label' => esc_html__( 'Yahoo', 'gravityflow' ),
					'value' => 'yim'
				),
				array(
					'label' => esc_html__( 'Jabber / Google Talk', 'gravityflow' ),
					'value' => 'jabber'
				),
				array(
					'label' => esc_html__( 'Biographical Information', 'gravityflow' ),
					'value' => 'description'
				)
			)
		);

		// other meta

		$keys = array();

		if ( is_array( $keys ) && empty( $keys ) ) {
			$raw_keys = $wpdb->get_results( sprintf( 'select distinct meta_key from %s where meta_key not like "%s" order by meta_key asc', $wpdb->usermeta, '\_%' ) );
			$exclude  = array( 'user_url', 'aim', 'yim', 'jabber', 'description' );

			foreach ( $raw_keys as $key ) {
				if ( ! in_array( $key->meta_key, $exclude ) ) {
					$keys[] = array(
						'name'     => $key->meta_key,
						'label'    => $key->meta_key,
						'required' => false
					);
				}
			}
		}

		$other_meta = null;

		if ( ! empty( $keys ) ) {
			$other_meta = array(
				'label'   => esc_html__( 'Other User Meta', 'gravityflow' ),
				'choices' => $keys
			);
		}

		// custom option to add custom meta key
		$add_custom_meta = array(
			'label' => esc_html__( 'Add Custom Meta', 'gravityflow' ),
			'value' => 'gf_custom'
		);

		$empty_choice = array(
			'label' => esc_html__( 'Select Meta Key', 'gravityflow' ),
			'value' => ''
		);

		$choices   = array();
		$choices[] = $empty_choice;
		$choices[] = $standard_meta;
		if ( ! empty( $other_meta ) ) {
			$choices[] = $other_meta;
		}
		$choices[] = $add_custom_meta;

		return $choices;
	}

	function is_applicable_field_for_field_select( $is_applicable_field, $field ) {

		if ( rgobj( $field, 'multipleFiles' ) ) {
			$is_applicable_field = false;
		}

		return $is_applicable_field;
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
	 * Returns the field map choices.
	 *
	 * @param array             $form
	 * @param null|array|string $field_type
	 * @param null|array        $exclude_field_types
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
				} elseif ( ! rgar( $field, 'displayOnly' ) && $field_is_valid_type && ! $exclude_field ) {
					$fields[] = array( 'value' => $field->id, 'label' => GFCommon::get_label( $field ) );
				}
			}
		}

		return $fields;
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Update_User() );
