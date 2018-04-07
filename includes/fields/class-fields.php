<?php
/**
 * Gravity Flow Fields Functions
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.2-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Fields
 */
class Gravity_Flow_Fields {

	/**
	 * Class constructor; if the installed Gravity Forms version is supported initialize the hooks.
	 */
	function __construct() {
		if ( ! gravity_flow()->is_gravityforms_supported() ) {
			return;
		}

		add_action( 'init', array( $this, 'init_hooks' ) );
	}

	/**
	 * Add the hooks via the WordPress init action.
	 */
	public function init_hooks() {
		add_filter( 'gform_tooltips', array( $this, 'add_tooltips' ) );

		add_action( 'gform_field_standard_settings', array( $this, 'field_settings' ) );
		add_action( 'gform_field_appearance_settings', array( $this, 'field_appearance_settings' ) );
		add_action( 'gform_entry_detail', array( 'Gravity_Flow_Field_Discussion', 'delete_discussion_item_script' ) );

		add_action( 'wp_ajax_rg_delete_file', array( 'RGForms', 'delete_file' ) );
		add_action( 'wp_ajax_nopriv_rg_delete_file', array( 'RGForms', 'delete_file' ) );
		add_action( 'wp_ajax_gravityflow_delete_discussion_item', array( 'Gravity_Flow_Field_Discussion', 'ajax_delete_discussion_item' ) );
	}

	/**
	 * Adds the Workflow Fields group to the form editor.
	 *
	 * @param array $field_groups The properties for the field groups.
	 *
	 * @return array
	 */
	public static function maybe_add_workflow_field_group( $field_groups ) {
		foreach ( $field_groups as $field_group ) {
			if ( $field_group['name'] == 'workflow_fields' ) {
				return $field_groups;
			}
		}

		$field_groups[] = array(
			'name'   => 'workflow_fields',
			'label'  => __( 'Workflow Fields', 'gravityflow' ),
			'fields' => array()
		);

		return $field_groups;
	}

	/**
	 * Add the tooltips for the workflow fields group and any custom field settings.
	 *
	 * @param array $tooltips An associative array where the key is the tooltip name and the value is the tooltip.
	 *
	 * @return array
	 */
	public function add_tooltips( $tooltips ) {
		$tooltips['form_workflow_fields']                    = '<h6>' . __( 'Workflow Fields', 'gravityflow' ) . '</h6>' . __( 'Workflow Fields add advanced workflow functionality to your forms.', 'gravityflow' );
		$tooltips['gravityflow_discussion_timestamp_format'] = '<h6>' . __( 'Custom Timestamp Format', 'gravityflow' ) . '</h6>' . sprintf( __( 'If you would like to override the default format used when displaying the comment timestamps, enter your %scustom format%s here.', 'gravityflow' ), '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">', '</a>' );

		return $tooltips;
	}

	/**
	 * Add the assignees and role settings to the general tab.
	 *
	 * @param int $position The setting position.
	 */
	public function field_settings( $position ) {
		if ( $position == 20 ) {
			// After Description setting.
			$this->setting_assignees();
			$this->setting_role();
		}
	}

	/**
	 * Output the markup for the gravityflow_setting_assignees setting to the field general tab in the form editor.
	 */
	public function setting_assignees() {
		?>

		<li class="gravityflow_setting_assignees field_setting">
			<span class="section_label"><?php esc_html_e( 'Assignees', 'gravityflow' ); ?></span>
			<div>
				<input type="checkbox" id="gravityflow-assignee-field-show-users" onclick="SetAssigneeFieldShowUsers();"/>
				<label for="gravityflow-assignee-field-show-users" class="inline">
					<?php esc_html_e( 'Show Users', 'gravityflow' ); ?>
					<?php gform_tooltip( 'gravityflow_assignee_field_show_users' ) ?>
				</label>
			</div>
			<div>
				<input type="checkbox" id="gravityflow-assignee-field-show-roles"
				       onclick="var value = jQuery(this).is(':checked'); SetFieldProperty('gravityflowAssigneeFieldShowRoles', value);"/>
				<label for="gravityflow-assignee-field-show-roles" class="inline">
					<?php esc_html_e( 'Show Roles', 'gravityflow' ); ?>
					<?php gform_tooltip( 'gravityflow_assignee_field_show_roles' ) ?>
				</label>
			</div>
			<div>
				<input type="checkbox" id="gravityflow-assignee-field-show-fields"
				       onclick="var value = jQuery(this).is(':checked'); SetFieldProperty('gravityflowAssigneeFieldShowFields', value);"/>
				<label for="gravityflow-assignee-field-show-fields" class="inline">
					<?php esc_html_e( 'Show Fields', 'gravityflow' ); ?>
					<?php gform_tooltip( 'gravityflow_assignee_field_show_fields' ) ?>
				</label>
			</div>
		</li>

		<?php
	}

	/**
	 * Output the markup for the gravityflow_setting_users_role_filter setting to the field general tab in the form editor.
	 */
	public function setting_role() {
		?>

		<li class="gravityflow_setting_users_role_filter field_setting">
			<label for="gravityflow_users_role_filter" class="section_label">
				<?php esc_html_e( 'Users Role Filter', 'gravityflow' ); ?>
			</label>
			<?php $this->setting_role_select(); ?>
		</li>

		<?php
	}

	/**
	 * Output the markup for the role select element.
	 */
	public function setting_role_select() {
		$choices = array(
			array(
				'value' => '',
				'label' => esc_html__( 'Include users from all roles', 'gravityflow' )
			)
		);

		$role_field = array(
			'name'     => 'gravityflow_users_role_filter',
			'choices'  => array_merge( $choices, Gravity_Flow_Common::get_roles_as_choices( false ) ),
			'onchange' => "SetFieldProperty('gravityflowUsersRoleFilter',this.value);",
		);

		$html = gravity_flow()->settings_select( $role_field, false );

		echo str_replace( sprintf( 'name="_gaddon_setting_%s"', esc_attr( $role_field['name'] ) ), '', $html );
	}

	/**
	 * Add the discussion fields custom timestamp format to the appearance tab.
	 *
	 * @param int $position The setting position.
	 */
	public function field_appearance_settings( $position ) {
		if ( $position == 0 ) {
			?>
			<li class="gravityflow_setting_discussion_timestamp_format field_setting">
				<label for="gravityflow_discussion_timestamp_format" class="section_label">
					<?php esc_html_e( 'Custom Timestamp Format', 'gravityflow' ); ?>
					<?php gform_tooltip( 'gravityflow_discussion_timestamp_format' ) ?>
				</label>
				<input id="gravityflow_discussion_timestamp_format" type="text" class="fieldwidth-4"
				       placeholder="d M Y g:i a" onkeyup="SetDiscussionTimestampFormat(jQuery(this).val());"
				       onchange="SetDiscussionTimestampFormat(jQuery(this).val());"/>
			</li>
			<?php
		}
	}

	/**
	 * Retrieves the value of the specified user property/meta key for the specified user ID.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param string|int $user_id    The user ID.
	 * @param string     $property   The user property to return.
	 * @param bool       $url_encode Indicates if the urlencode function should be applied.
	 * @param bool       $esc_html   Indicates if the esc_html function should be applied.
	 *
	 * @return string
	 */
	public static function get_user_variable( $user_id, $property, $url_encode = false, $esc_html = true ) {
		$value = $user_id;

		if ( $property != 'id' ) {
			$user = get_user_by( 'id', $user_id );

			if ( is_object( $user ) ) {
				switch ( $property ) {
					case 'email' :
						$property = 'user_email';
						break;
					case '' :
						$property = 'display_name';
				}

				if ( $property == 'roles' ) {
					$value = implode( ', ', $user->roles );
				} else {
					$value = $user->get( $property );
				}
			}
		}

		return self::maybe_format_user_variable( $value, $url_encode, $esc_html );
	}

	/**
	 * Filters the value of invalid or special characters before output.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param string|int $value      The user ID or property to be filtered.
	 * @param bool       $url_encode Indicates if the urlencode function should be applied.
	 * @param bool       $esc_html   Indicates if the esc_html function should be applied.
	 *
	 * @return string
	 */
	public static function maybe_format_user_variable( $value, $url_encode, $esc_html ) {
		if ( $url_encode ) {
			$value = urlencode( $value );
		}

		if ( $esc_html ) {
			$value = esc_html( $value );
		}

		return $value;
	}
}

new Gravity_Flow_Fields();
