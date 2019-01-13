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
	public $_step_type = 'update_user';

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
		return '<i class="fa fa-user"></i>';
	}

	/**
	 * Returns an array of settings for this step type.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = array(
			'title'       => esc_html__( 'Update User', 'gravityflow' ),
			'description' => '',
			'fields'      => array(
				array(
					'name'          => 'user_source',
					'label'         => __( 'User', 'gravityflow' ),
					'type'          => 'radio',
					'default_value' => 'created_by',
					'horizontal'    => false,
					'onchange'      => 'jQuery(this).closest("form").submit();',
					'choices'       => array(
						array( 'label' => __( 'Form Submitter ("Created By" field)', 'gravityflow' ), 'value' => 'created_by' ),
						array( 'label' => __( 'Current User', 'gravityflow' ), 'value' => 'current_user' ),
						array( 'label' => __( 'Select User Field', 'gravityflow' ), 'value' => 'select_user_field' ),
						array( 'label' => __( 'Lookup User by Email Field', 'gravityflow' ), 'value' => 'select_email_field' ),
					),
				),
				array(
					'name'          => 'user_field',
					'label'         => __( 'User Field', 'gravityflow' ),
					'type'          => 'field_select',
					'args'        => array(
						'input_types' => array( 'workflow_user' ),
					),
					'dependency' => array(
						'field'  => 'user_source',
						'values' => array( 'select_user_field' ),
					)
				),
				array(
					'name'          => 'email_field',
					'label'         => __( 'Email Field', 'gravityflow' ),
					'type'          => 'field_select',
					'args'        => array(
						'disable_first_choice' => true,
						'input_types' => array( 'email' ),
						'append_choices'       => array(
							array( 'label' => __( 'Select an Email Field', 'gravityflow' ), 'value' => '' ),
						),
					),
					'dependency' => array(
						'field'  => 'user_source',
						'values' => array( 'select_email_field' ),
					)
				),
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
					'name'          => 'display_name',
					'id'            => 'display_name_setting', // Prevents conflict with WP class
					'label'         => esc_html__( 'Display Name', 'gravityflow' ),
					'type'          => 'select',
					'class'         => 'medium',
					'default_value' => '',
					'choices'       => $this->get_display_name_choices(),
					'tooltip'       => sprintf( '<h6>%s</h6> %s', esc_html__( 'Display Name', 'gravityflow' ), esc_html__( 'Select how the user\'s name should be displayed publicly.', 'gravityflow' ) ),
				),
				array(
					'name'        => 'email',
					'label'       => esc_html__( 'Email Address', 'gravityflow' ),
					'type'        => 'field_select',
					'args'        => array(
						'disable_first_choice' => true,
						'input_types'          => array( 'email' ),
						'append_choices'       => array(
							array(
								'label' =>  sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current email', 'gravityflow'  ) ),
								'value' => ''
							)
						)
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
						'append_choices'       => array(
							array(
								'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current password', 'gravityflow'  ) ),
								'value' => ''
							)
						)
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
					'name'      => 'user_meta',
					'label'     => esc_html__( 'User Meta', 'gravityflow' ),
					'type'      => 'generic_map',
					'merge_tags'  => true,
					'key_field'   => array(
						'choices'     => $this->get_user_meta_choices(),
						'placeholder' => esc_html__( 'Custom Meta Field', 'gravityflow' ),
						'title'       => esc_html__( 'Meta Key', 'gravityflow' ),
					),
					'value_field' => array(
						'choices'      => $this->value_mappings(),
						'custom_value' => true,
						'placeholder'  => esc_html__( 'Custom Field Value', 'gravityflow' ),
					),
				),
			)
		);


		return $settings;

	}

	/**
	 * Processes the step and updates the user profile if the user can be found.
	 *
	 * @since 2.5
	 *
	 * @return bool
	 */
	function process() {
		$this->log_debug( __METHOD__ . '(): starting' );


		$user = null;

		switch( $this->user_source ) {
			case 'created_by':
				$entry = $this->get_entry();
				if (  $entry['created_by'] ) {
					$user = get_user_by( 'id', $entry['created_by'] );
				}
				break;
			case 'current_user':
				$user = wp_get_current_user();
				break;
			case 'select_user_field':
				$entry = $this->get_entry();
				if ( $entry[ $this->user_field ] ) {
					$user = get_user_by( 'ID', $entry[ $this->user_field ] );
				}
				break;
			case 'select_email_field':
				$entry = $this->get_entry();
				if ( $entry[ $this->email_field ] ) {
					$user = get_user_by( 'email', $entry[ $this->user_field ] );
				}
		}

		if ( ! $user ) {
			$this->log_debug( __METHOD__ . '(): user not found. Bailing.' );
		}

		$this->set_user_properties( $user );

		return true;
	}

	/**
	 * Returns the list of display name choices.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_display_name_choices() {
		$display_name_choices = array();
		$choices = array();

		$choices['nickname']  = '{nickname}';
		$choices['username']  = '{username}';
		$choices['firstname'] = '{first name}';
		$choices['lastname']  = '{last name}';
		$choices['firstlast'] = '{first name} {last name}';
		$choices['lastfirst'] = '{last name} {first name}';



		foreach ( $choices as $value => $label ) {
			$display_name_choices[] = array(
				'label' => $label,
				'value' => $value
			);
		}

		$display_name_choices[] =  array(
			'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Preserve current display name', 'gravityflow'  ) ),
			'value' => ''
		);

		return $display_name_choices;
	}

	/**
	 * Returns the roles as choices.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public function get_roles_as_choices() {

		$choices = array();

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
	 * Returns the user meta as choices.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
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

	/**
	 * Callback for the settings to check whether a field can be mapped.
	 *
	 * @since 2.5
	 *
	 * @param $is_applicable_field
	 * @param $field
	 *
	 * @return bool
	 */
	function is_applicable_field_for_field_select( $is_applicable_field, $field ) {

		if ( rgobj( $field, 'multipleFiles' ) ) {
			$is_applicable_field = false;
		}

		return $is_applicable_field;
	}

	/**
	 * Prepare value map.
	 *
	 * @since 2.5
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
	 * @since 2.5
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
					// If this is an address field, add full name to the list
					if ( $input_type == 'address' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
						);
					}
					// If this is a name field, add full name to the list
					if ( $input_type == 'name' ) {
						$fields[] = array(
							'value' => $field->id,
							'label' => GFCommon::get_label( $field ) . ' (' . esc_html__( 'Full', 'gravityflow' ) . ')',
						);
					}
					// If this is a checkbox field, add to the list
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

	/**
	 * Sets the user properties.
	 *
	 * @since 2.5
	 *
	 * @param WP_User $user
	 */
	public function set_user_properties( $user ) {

		$entry = $this->get_entry();

		$standard_meta = array(
			'first_name',
			'last_name',
			'nickname',
		);

		$form = $this->get_form();

		$dirty = false;

		foreach ( $standard_meta as $meta_key ) {
			$this->log_debug( sprintf( 'Processing meta item: %s', $meta_key ) );

			if ( ! $this->{$meta_key} ) {
				$this->log_debug( 'Meta item is empty. Skipping it.' );
				continue;
			}

			$this->{$meta_key};
			$value = gravity_flow()->get_field_value( $form, $entry, $this->{$meta_key} );
			$this->log_debug( sprintf( 'Meta item mapped to field: %s; Value: %s', $meta_key, $value ) );

			$result = update_user_meta( $user->ID, $meta_key, $value );
			$this->log_debug( sprintf( 'Result: %s', var_export( (bool) $result, 1 ) ) );

			$dirty = true;
		}

		if ( $this->display_name ) {
			$display_name = $this->get_display_name( $user );
			$this->update_user_property( $user->ID, 'display_name', $display_name );
			$dirty = true;
		}

		if ( $this->email ) {
			$value = gravity_flow()->get_field_value( $form, $entry, $this->email );
			$this->update_user_property( $user->ID, 'user_email', $value );
			$dirty = true;
		}

		if ( $this->password ) {
			$value = gravity_flow()->get_field_value( $form, $entry, $this->password );
			$this->update_user_property( $user->ID, 'user_pass', $value );
			$dirty = true;
		}

		if ( is_array( $this->user_meta ) || ! empty( $this->user_meta ) ) {
			foreach ( $this->user_meta as $meta_key => $meta_item ) {
				$is_custom_value = $meta_item['value'] == 'gf_custom';

				$meta_key = $meta_item['key'] == 'gf_custom' ? $meta_item['custom_key'] : $meta_item['key'];
				$meta_value = $is_custom_value ? $meta_item['custom_value'] : $meta_item['value'];

				$this->log_debug( sprintf( 'Adding meta item: %s', $meta_key ) );

				// skip empty meta items
				if ( ! $meta_key || ! $meta_value ) {
					$this->log_debug( 'Meta item is empty. Skipping it.' );
					continue;
				}

				if ( $is_custom_value ) {
					$value = GFCommon::replace_variables( $meta_value, $form, $entry, false, false, false, 'text' );
					$value = do_shortcode( $value );
				} else {
					$value = gravity_flow()->get_field_value( $form, $entry, $meta_value );
					$this->log_debug( sprintf( 'Meta item mapped to field: %s; value: %s', $meta_value, $value ) );
				}

				if ( $value && in_array( $meta_key, array( 'user_url', 'user_nicename' ) ) ) {
					$result = $this->update_user_property( $user->ID, $meta_key, $value );
				} elseif ( rgblank( $value ) ) {
					$result = delete_user_meta( $user->ID, $meta_key );
				} else {
					$result = update_user_meta( $user->ID, $meta_key, $value );
				}

				$this->log_debug( sprintf( 'Result: %s', var_export( (bool) $result, 1 ) ) );

				$dirty = true;
			}
		}


		if ( in_array( $this->roles_action, array( 'replace', 'add' ) ) ) {

			// Update roles

			if ( $this->roles_action == 'replace' ) {
				foreach ( $user->roles as $role ) {
					$user->remove_role( $role );
				};
			}

			foreach ( $this->roles as $new_role ) {
				$user->add_role( $new_role );
			}

			$dirty = true;
		}

		if ( $dirty ) {
			update_user_meta( $user->ID, '_gravityflow-update-entry-id', $entry['id'] );
		}
	}

	/**
	 * Returns the display name for the given user according to the format defined in the settings.
	 *
	 * @since 2.5
	 *
	 * @param $user
	 *
	 * @return string
	 */
	public function get_display_name( $user ) {

		$display_format = $this->display_name;

		switch( $display_format ) {
			case 'firstname':
				$display_name = $user->first_name;
				break;
			case 'lastname':
				$display_name = $user->last_name;
				break;
			case 'firstlast':
				$display_name = $user->first_name . ' ' . $user->last_name;
				break;
			case 'lastfirst':
				$display_name = $user->last_name . ' ' . $user->first_name;
				break;
			case 'nickname':
				$display_name = $user->nickname;
				break;
			default:
				$display_name = $user->user_login;
				break;
		}

		return $display_name;
	}

	/**
	 * Updates a user property
	 *
	 * @since 2.5
	 *
	 * @param $user_id
	 * @param $prop_key
	 * @param $prop_value
	 *
	 * @return bool|int|WP_Error
	 */
	public function update_user_property( $user_id, $prop_key, $prop_value ) {

		if ( ! $user_id ) {
			return false;
		}

		$user = new WP_User( $user_id );

		$new_data            = new stdClass();
		$new_data->ID        = $user->ID;
		$new_data->$prop_key = $prop_value;

		$result = wp_update_user( get_object_vars( $new_data ) );

		return $result;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Update_User() );
