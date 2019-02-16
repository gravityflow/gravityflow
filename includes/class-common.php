<?php
/**
 * Gravity Flow Common Functions
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.3
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Common
 *
 * @since 1.3.3
 */
class Gravity_Flow_Common {

	/**
	 * Returns a URl to a workflow page.
	 *
	 * @param array                      $query_args   An associative array of query variables.
	 * @param int|null                   $page_id      The ID of the WordPress Page where the shortcode is located.
	 * @param Gravity_Flow_Assignee|null $assignee     The Assignee.
	 * @param string                     $access_token The access token for the current assignee.
	 *
	 * @return string
	 */
	public static function get_workflow_url( $query_args, $page_id = null, $assignee = null, $access_token = '' ) {
		if ( empty( $access_token ) && $assignee && $assignee->get_type() == 'email' ) {
			$token_lifetime_days        = apply_filters( 'gravityflow_entry_token_expiration_days', 30, $assignee );
			$token_expiration_timestamp = strtotime( '+' . (int) $token_lifetime_days . ' days' );
			$access_token               = gravity_flow()->generate_access_token( $assignee, null, $token_expiration_timestamp );
		}

		$base_url = '';
		if ( ! empty( $page_id ) && $page_id != 'admin' ) {
			$base_url = get_permalink( $page_id );
		}

		if ( empty( $base_url ) ) {
			$base_url = admin_url( 'admin.php' );
		}

		if ( ! empty( $access_token ) ) {
			$query_args['gflow_access_token'] = $access_token;
		}

		$url = add_query_arg( $query_args, $base_url );

		/**
		 * Allows the workflow URL (e.g. inbox or status page) to be modified.
		 *
		 * @since 1.9.2
		 *
		 * @param string $url The URL.
		 * @param int|null $page_id The ID of the WordPress Page where the shortcode is located.
		 * @param Gravity_Flow_Assignee $assignee The Assignee.
		 */
		$url = apply_filters( 'gravityflow_workflow_url', $url, $page_id, $assignee );

		return $url;
	}

	/**
	 * If form and field ids have bee specified for display on the inbox/status page add the columns.
	 *
	 * @param array $columns   The inbox/status page columns.
	 * @param int   $form_id   The form ID of the entries to be displayed or 0 to display entries from all forms.
	 * @param array $field_ids The field IDs or entry properties/meta to be displayed.
	 *
	 * @return array
	 */
	public static function get_field_columns( $columns, $form_id, $field_ids ) {
		if ( empty( $form_id ) || ! is_array( $field_ids ) || empty( $field_ids ) ) {
			return $columns;
		}

		$form       = GFAPI::get_form( $form_id );
		$entry_meta = GFFormsModel::get_entry_meta( $form_id );

		foreach ( $field_ids as $id ) {
			switch ( strtolower( $id ) ) {
				case 'ip' :
					$columns[ $id ] = __( 'User IP', 'gravityflow' );
					break;
				case 'source_url' :
					$columns[ $id ] = __( 'Source Url', 'gravityflow' );
					break;
				case 'payment_status' :
					$columns[ $id ] = __( 'Payment Status', 'gravityflow' );
					break;
				case 'transaction_id' :
					$columns[ $id ] = __( 'Transaction ID', 'gravityflow' );
					break;
				case 'payment_date' :
					$columns[ $id ] = __( 'Payment Date', 'gravityflow' );
					break;
				case 'payment_amount' :
					$columns[ $id ] = __( 'Payment Amount', 'gravityflow' );
					break;
				case ( ( is_string( $id ) || is_int( $id ) ) && array_key_exists( $id, $entry_meta ) ) :
					$columns[ $id ] = $entry_meta[ $id ]['label'];
					break;

				default:
					$field = GFFormsModel::get_field( $form, $id );

					if ( is_object( $field ) ) {
						$input_label_only = apply_filters( 'gform_entry_list_column_input_label_only', true, $form, $field );
						$columns[ $id ]   = GFFormsModel::get_label( $field, $id, $input_label_only );
					}
			}
		}

		return $columns;
	}

	/**
	 * Get an array of choices containing the user roles.
	 *
	 * @param bool $prefix_values Indicates if the choice value should be prefixed 'role|'. Default is true.
	 * @param bool $reverse       Indicates if the choices should be reversed. Default is false.
	 * @param bool $frontend      Indicates if the choices are being used by a front-end field. Default is false.
	 *
	 * @since 1.4.2-dev
	 *
	 * @return array
	 */
	public static function get_roles_as_choices( $prefix_values = true, $reverse = false, $frontend = false ) {
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/user.php' );
		}

		$roles = get_editable_roles();

		if ( $reverse ) {
			$roles = array_reverse( $roles );
		}

		$choices = array();
		$prefix  = $prefix_values ? 'role|' : '';
		$key     = $frontend ? 'text' : 'label';

		foreach ( $roles as $role => $details ) {
			$name      = translate_user_role( $details['name'] );
			$choices[] = array( 'value' => $prefix . $role, $key => $name );
		}

		return $choices;
	}

	/**
	 * Format the date/time or timestamp for display.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param int|string $date_or_timestamp The unix timestamp or string in the Y-m-d H:i:s format to be formatted.
	 * @param string     $format            The format the date/time should be returned in. Defaults to an empty string; the date and time format from the WordPress general settings.
	 * @param bool       $is_human          Indicates if the date/time should be returned in a human readable format such as "1 hour ago". Default is false.
	 * @param bool       $include_time      Indicates if the time should be included in the returned string. Default is false.
	 *
	 * @return string
	 */
	public static function format_date( $date_or_timestamp, $format = '', $is_human = false, $include_time = false ) {
		$date_time = is_numeric( $date_or_timestamp ) ? date( 'Y-m-d H:i:s', $date_or_timestamp ) : $date_or_timestamp;

		return GFCommon::format_date( $date_time, $is_human, $format, $include_time );
	}

	/**
	 * Get the 'workflow_notes' entry meta item.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param int  $entry_id   The ID of the entry the notes are to be retrieved for.
	 * @param bool $for_output Should the notes be ordered newest to oldest? Default is false.
	 *
	 * @return array
	 */
	public static function get_workflow_notes( $entry_id, $for_output = false ) {
		$notes_json  = gform_get_meta( $entry_id, 'workflow_notes' );
		$notes_array = empty( $notes_json ) ? array() : json_decode( $notes_json, true );

		if ( $for_output && ! empty( $notes_array ) ) {
			$notes_array = array_reverse( $notes_array );
		}

		return $notes_array;
	}

	/**
	 * Add a user submitted note to the 'workflow_notes' entry meta item.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $note     The note to be added.
	 * @param int    $entry_id The ID of the entry the note is to be added to.
	 * @param int    $step_id  The ID of the current step.
	 */
	public static function add_workflow_note( $note, $entry_id, $step_id ) {
		$notes = self::get_workflow_notes( $entry_id );

		$notes[] = array(
			'id'           => uniqid( '', true ),
			'step_id'      => $step_id,
			'assignee_key' => gravity_flow()->get_current_user_assignee_key(),
			'timestamp'    => time(),
			'value'        => $note,
		);

		gform_update_meta( $entry_id, 'workflow_notes', json_encode( $notes ) );
	}

	/**
	 * Get the content which will replace the {workflow_timeline} merge tag and response for Gravity Flow API get_timeline.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param array $entry The entry to display timeline of.
	 *
	 * @return string
	 */
	public static function get_timeline( $entry = false ) {

		if ( empty( $entry ) ) {
			return '';
		}

		$notes = Gravity_Flow_Common::get_timeline_notes( $entry );

		if ( empty( $notes ) ) {
			return '';
		}

		$return = array();

		foreach ( $notes as $note ) {
			$step = Gravity_Flow_Common::get_timeline_note_step( $note );
			$name = Gravity_Flow_Common::get_timeline_note_display_name( $note, $step );
			$date = Gravity_Flow_Common::format_date( $note->date_created, '', false, true );

			$return[] = Gravity_Flow_Common::format_note( $note->value, $name, $date );
		}

		return implode( "\n\n", $return );

	}

	/**
	 * Format a note for output.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $note_value   The note value.
	 * @param string $display_name The note display name.
	 * @param string $date         The note creation date.
	 *
	 * @return string
	 */
	public static function format_note( $note_value, $display_name, $date ) {
		$separator = $display_name && $date ? ': ' : '';

		return sprintf( "%s%s%s\n%s", $display_name, $separator, $date, $note_value );
	}

	/**
	 * Get the timeline notes for the current entry.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param array $entry The current entry.
	 *
	 * @return array
	 */
	public static function get_timeline_notes( $entry ) {
		$notes = RGFormsModel::get_lead_notes( $entry['id'] );

		foreach ( $notes as $key => $note ) {
			if ( $note->note_type !== 'gravityflow' ) {
				unset( $notes[ $key ] );
			}
		}

		reset( $notes );

		array_unshift( $notes, self::get_initial_note( $entry ) );

		$notes = array_reverse( $notes );

		return $notes;
	}

	/**
	 * Get the Workflow Submitted note.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param array $entry The current entry.
	 *
	 * @return object
	 */
	public static function get_initial_note( $entry ) {
		$initial_note               = new stdClass();
		$initial_note->id           = 0;
		$initial_note->date_created = $entry['date_created'];
		$initial_note->value        = esc_html__( 'Workflow Submitted', 'gravityflow' );
		$initial_note->user_id      = $entry['created_by'];
		$user                       = get_user_by( 'id', $entry['created_by'] );
		$initial_note->user_name    = $user ? $user->display_name : $entry['ip'];

		return $initial_note;
	}

	/**
	 * Get the step for the current timeline note.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param object $note The note properties.
	 *
	 * @return bool|Gravity_Flow_Step
	 */
	public static function get_timeline_note_step( $note ) {
		$step = empty( $note->user_id ) ? Gravity_Flow_Steps::get( $note->user_name ) : false;

		return $step;
	}

	/**
	 * Get the display name for the current timeline note.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param object                 $note The note properties.
	 * @param bool|Gravity_Flow_Step $step The step or false if not available.
	 *
	 * @return string
	 */
	public static function get_timeline_note_display_name( $note, $step ) {
		if ( empty( $note->user_id ) ) {
			if ( $note->user_name !== 'gravityflow' && $step ) {
				$display_name = $step->get_label();
			} else {
				$display_name = gravity_flow()->translate_navigation_label( 'Workflow' );
			}
		} else {
			$display_name = $note->user_name;
		}

		return $display_name;
	}

	/**
	 * Get the Gravity Forms database version number.
	 *
	 * @return string
	 */
	public static function get_gravityforms_db_version() {

		if ( method_exists( 'GFFormsModel', 'get_database_version' ) ) {
			$db_version = GFFormsModel::get_database_version();
		} else {
			$db_version = GFForms::$version;
		}

		return $db_version;
	}

	/**
	 * Get the name of the Gravity Forms table containing the entry properties.
	 *
	 * @return string
	 */
	public static function get_entry_table_name() {
		return version_compare( self::get_gravityforms_db_version(), '2.3-dev-1', '<' ) ? GFFormsModel::get_lead_table_name() : GFFormsModel::get_entry_table_name();
	}

	/**
	 * Get the name of the Gravity Forms table containing the entry meta.
	 *
	 * @return string
	 */
	public static function get_entry_meta_table_name() {
		return version_compare( self::get_gravityforms_db_version(), '2.3-dev-1', '<' ) ? GFFormsModel::get_lead_meta_table_name() : GFFormsModel::get_entry_meta_table_name();
	}

	/**
	 * Get the name of the Gravity Forms column containing the entry ID.
	 *
	 * @return string
	 */
	public static function get_entry_id_column_name() {
		return version_compare( self::get_gravityforms_db_version(), '2.3-dev-1', '<' ) ? 'lead_id' : 'entry_id';
	}

	/**
	 * Determines if a field should be displayed.
	 *
	 * @since 2.0.1-dev
	 *
	 * @param GF_Field               $field            The field properties.
	 * @param Gravity_Flow_Step|null $current_step     The current step for this entry.
	 * @param array                  $form             The form for the current entry.
	 * @param array                  $entry            The entry being processed for display.
	 * @param bool                   $is_product_field Is the current field one of the product field types.
	 *
	 * @return bool
	 */
	public static function is_display_field( $field, $current_step, $form, $entry, $is_product_field = false ) {
		$display_field       = true;
		$display_fields_mode = $current_step ? $current_step->display_fields_mode : 'all_fields';

		if ( $field->type !== 'section' ) {
			if ( $display_fields_mode !== 'all_fields' ) {
				$display_fields_selected = $current_step && is_array( $current_step->display_fields_selected ) ? $current_step->display_fields_selected : array();
				$is_selected_field          = in_array( $field->id, $display_fields_selected );

				if ( ! $is_selected_field && $display_fields_mode === 'selected_fields' || $is_selected_field && $display_fields_mode === 'all_fields_except' ) {
					$display_field = false;
				}
			} elseif ( GFFormsModel::is_field_hidden( $form, $field, array(), $entry ) || $is_product_field ) {
				$display_field = false;
			}
		}

		$display_field = (bool) apply_filters( 'gravityflow_workflow_detail_display_field', $display_field, $field, $form, $entry, $current_step );

		return $display_field;
	}

	/**
	 * Checks whether a field is an editable field.
	 *
	 * @since 2.0.1-dev
	 *
	 * @param GF_Field          $field        The field to be checked.
	 * @param Gravity_Flow_Step $current_step The current step.
	 *
	 * @return bool
	 */
	public static function is_editable_field( $field, $current_step ) {
		return in_array( $field->id, $current_step->get_editable_fields() );
	}

}
