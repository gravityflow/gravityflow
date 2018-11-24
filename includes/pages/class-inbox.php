<?php
/**
 * Gravity Flow Inbox
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Inbox
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Inbox
 */
class Gravity_Flow_Inbox {

	/**
	 * Displays the inbox page.
	 *
	 * @param array $args The inbox page arguments.
	 */
	public static function display( $args ) {

		$args = array_merge( self::get_defaults(), $args );

		/**
		 * Allow the inbox page arguments to be overridden.
		 *
		 * @param array $args The inbox page arguments.
		 */
		$args = apply_filters( 'gravityflow_inbox_args', $args );

		if ( has_filter( 'gravityflow_inbox_args' ) ) {
			gravity_flow()->log_debug( __METHOD__ . '(): Executing functions hooked to gravityflow_inbox_args.' );
		}

		$total_count = 0;
		$entries     = Gravity_Flow_API::get_inbox_entries( $args, $total_count );

		if ( sizeof( $entries ) > 0 ) {
			$columns = self::get_columns( $args );
			?>

			<table id="gravityflow-inbox" class="widefat gravityflow-inbox" cellspacing="0" style="border:0px;">

				<?php
				self::display_table_head( $columns );
				?>

				<tbody class="list:user user-list">
				<?php
				foreach ( $entries as $entry ) {
					self::display_entry_row( $args, $entry, $columns );
				}
				?>
				</tbody>
			</table>

			<?php
			if ( $total_count > 150 ) {
				echo '<br />';
				echo '<div class="excess_entries_indicator">';
				printf( '(Showing 150 of %d)', absint( $total_count ) );
				echo '</div>';
			}
		} else {
			?>
			<div id="gravityflow-no-pending-tasks-container">
				<div id="gravityflow-no-pending-tasks-content">
					<i class="fa fa-check-circle-o gravityflow-inbox-check"></i>
					<br/><br/>
					<?php esc_html_e( 'No pending tasks', 'gravityflow' ); ?>
				</div>

			</div>
		<?php
		}
	}

	/**
	 * Get the default arguments used when rendering the inbox page.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		$field_ids = apply_filters( 'gravityflow_inbox_fields', array() );
		$filter    = apply_filters( 'gravityflow_inbox_filter', array(
			'form_id'    => 0,
			'start_date' => '',
			'end_date'   => '',
		) );

		return array(
			'display_empty_fields' => true,
			'id_column'            => true,
			'submitter_column'     => true,
			'actions_column'       => false,
			'step_column'          => true,
			'check_permissions'    => true,
			'form_id'              => absint( rgar( $filter, 'form_id' ) ),
			'field_ids'            => $field_ids,
			'detail_base_url'      => admin_url( 'admin.php?page=gravityflow-inbox&view=entry' ),
			'last_updated'         => false,
			'step_highlight'       => true,
		);

	}

	/**
	 * Get the entries to be displayed.
	 *
	 * @deprecated 2.3.2
	 *
	 * @param array $args        The inbox page arguments.
	 * @param int   $total_count The total number of entries.
	 *
	 * @return array
	 */
	public static function get_entries( $args, &$total_count ) {
		_deprecated_function( __METHOD__, '2.3.2', 'Gravity_Flow_API::get_inbox_entries' );

		return Gravity_Flow_API::get_inbox_entries( $args, $total_count );
	}

	/**
	 * Get the columns to be displayed.
	 *
	 * @param array $args The inbox page arguments.
	 *
	 * @return array
	 */
	public static function get_columns( $args ) {
		$columns = array();
		if ( $args['step_highlight'] ) {
			$columns['step_highlight'] = 'step_highlight';
		}

		if ( $args['id_column'] ) {
			$columns['id'] = __( 'ID', 'gravityflow' );
		}

		if ( $args['actions_column'] ) {
			$columns['actions'] = '';
		}

		if ( empty( $args['form_id'] ) || is_array( $args['form_id']) ) {
			$columns['form_title'] = __( 'Form', 'gravityflow' );
		}

		if ( $args['submitter_column'] ) {
			$columns['created_by'] = __( 'Submitter', 'gravityflow' );
		}

		if ( $args['step_column'] ) {
			$columns['workflow_step'] = __( 'Step', 'gravityflow' );
		}

		$columns['date_created'] = __( 'Submitted', 'gravityflow' );
		$columns                 = Gravity_Flow_Common::get_field_columns( $columns, $args['form_id'], $args['field_ids'] );

		if ( $args['last_updated'] ) {
			$columns['last_updated'] = __( 'Last Updated', 'gravityflow' );
		}

		/**
		 * Allows the columns to be filtered for the inbox table.
		 *
		 * @since 2.2.4-dev
		 *
		 * @param array         $columns The columns to be filtered
		 * @param array         $args    The array of args for this inbox table.
		 */
		$columns = apply_filters( 'gravityflow_columns_inbox_table', $columns, $args );

		return $columns;
	}

	/**
	 * Display the table header.
	 *
	 * @param array $columns The column properties.
	 */
	public static function display_table_head( $columns ) {
		echo '<thead><tr>';

		foreach ( $columns as $label ) {

			if ( $label !== 'step_highlight' ) {
				echo sprintf( '<th data-label="%s">%s</th>', esc_attr( $label ), esc_html( $label ) );
			}
		}

		echo '</tr></thead>';
	}

	/**
	 * Display the row for the current entry.
	 *
	 * @param array $args    The inbox page arguments.
	 * @param array $entry   The entry currently being processed.
	 * @param array $columns The column properties.
	 */
	public static function display_entry_row( $args, $entry, $columns ) {
		$form      = GFAPI::get_form( $entry['form_id'] );
		$url_entry = esc_url_raw( sprintf( '%s&id=%d&lid=%d', $args['detail_base_url'], $entry['form_id'], $entry['id'] ) );
		$link      = "<a href='%s'>%s</a>";

		/**
		 * Allows the entry link to be modified for each of the entries in the inbox table.
		 *
		 * @since 1.9.2
		 *
		 * @param string $link      The entry link HTML.
		 * @param string $url_entry The entry URL.
		 * @param string $entry     The current entry.
		 * @param string $args      The inbox page arguments.
		 */
		$link = apply_filters( 'gravityflow_entry_link_inbox_table', $link, $url_entry, $entry, $args );

		$step_highlight_color = '';
		if ( array_key_exists( 'step_highlight', $columns ) && isset( $entry['workflow_step'] ) ) {
			$step = gravity_flow()->get_step( $entry['workflow_step'] );
			if ( $step ) {
				$meta = $step->get_feed_meta();

				if ( $meta && isset( $meta['step_highlight'] ) && $meta['step_highlight'] ) {
					if ( isset( $meta['step_highlight_type'] ) && $meta['step_highlight_type'] == 'color' ) {
						if ( isset( $meta['step_highlight_color'] ) && preg_match( '/^#[a-f0-9]{6}$/i', $meta['step_highlight_color'] ) ) {
							$step_highlight_color = $meta['step_highlight_color'];
						}
					}
				}
			}
		}

		/**
		 * Allow the Step Highlight colour to be overridden.
		 *
		 * @since 1.9.2
		 *
		 * @param string $highlight  The highlight color (hex value) of the row currently being processed.
		 * @param int    $form['id'] The ID of form currently being processed.
		 * @param array  $entry      The entry object for the row currently being processed.
		 *
		 * @return string
		 */
		$step_highlight_color = apply_filters( 'gravityflow_step_highlight_color_inbox', $step_highlight_color, $form['id'], $entry );

		if ( strlen( $step_highlight_color ) > 0 ) {
			echo '<tr style="border-left-color: ' . $step_highlight_color . ';">';
		} else {
			echo '<tr>';
		}

		unset( $columns['step_highlight'] );

		foreach ( $columns as $id => $label ) {
			$value = self::get_column_value( $id, $form, $entry, $columns );
			$html = $id == 'actions' ? $value : sprintf( $link, $url_entry, $value );
			echo sprintf( '<td data-label="%s">%s</td>', esc_attr( $label ), $html );
		}

		echo '</tr>';
	}

	/**
	 * Get the value for display in the current column for the entry being processed.
	 *
	 * @param string $id      The column id, the key to the value in the entry or form.
	 * @param array  $form    The form object for the current entry.
	 * @param array  $entry   The entry currently being processed for display.
	 * @param array  $columns The columns to be displayed.
	 *
	 * @return string
	 */
	public static function get_column_value( $id, $form, $entry, $columns ) {
		$value = '';
		switch ( strtolower( $id ) ) {
			case 'form_title':
				$value = rgar( $form, 'title' );
				break;
			case 'created_by':
				$user           = get_user_by( 'id', (int) $entry['created_by'] );
				$submitter_name = $user ? $user->display_name : $entry['ip'];

				/**
				 * Allow the value displayed in the Submitter column to be overridden.
				 *
				 * @param string $submitter_name The display_name of the logged-in user who submitted the form or the guest ip address.
				 * @param array  $entry          The entry object for the row currently being processed.
				 * @param array  $form           The form object for the current entry.
				 */
				$value = apply_filters( 'gravityflow_inbox_submitter_name', $submitter_name, $entry, $form );
				break;
			case 'date_created':
				$date_created = Gravity_Flow_Common::format_date( $entry['date_created'], '', true, true );
				$value = $date_created;
				break;
			case 'last_updated':
				$last_updated = date( 'Y-m-d H:i:s', $entry['workflow_timestamp'] );
				$value = $entry['date_created'] != $last_updated ? Gravity_Flow_Common::format_date( $last_updated, '', true, true ) : '-';
				break;
			case 'workflow_step':
				if ( isset( $entry['workflow_step'] ) ) {
					$step = gravity_flow()->get_step( $entry['workflow_step'] );
					if ( $step ) {
						return $step->get_name();
					}
				}

				$value = '';
				break;
			case 'actions':
				$api = new Gravity_Flow_API( $form['id'] );
				$step = $api->get_current_step( $entry );
				if ( $step ) {
					$value = self::format_actions( $step );
				}
				break;
			case 'payment_status':
				$value = rgar( $entry, 'payment_status' );
				if ( gravity_flow()->is_gravityforms_supported( '2.4' ) ) {
					$value = GFCommon::get_entry_payment_status_text( $value );
				}
				break;
			default:
				$field = GFFormsModel::get_field( $form, $id );

				if ( is_object( $field ) ) {
					require_once( GFCommon::get_base_path() . '/entry_list.php' );
					$value = $field->get_value_entry_list( rgar( $entry, $id ), $entry, $id, $columns, $form );
				} else {
					$value = rgar( $entry, $id );
				}

				$value = apply_filters( 'gform_entries_field_value', $value, $form['id'], $id, $entry );
		}

		return $value;
	}

	/**
	 * Formats the actions for the action column.
	 *
	 * @param Gravity_Flow_Step $step The current step.
	 *
	 * @return string
	 */
	public static function format_actions( $step ) {
		$html          = '';
		$actions = $step->get_actions();
		$entry_id      = $step->get_entry_id();
		foreach ( $actions as $action ) {
			$show_workflow_note_field = (bool) $action['show_note_field'];
			$html .= sprintf( '<span id="gravityflow-action-%s-%d" data-entry_id="%d" data-action="%s" data-rest_base="%s"  data-note_field="%d" class="gravityflow-action" role="link">%s</span>', $action['key'], $entry_id, $entry_id, $action['key'], $step->get_rest_base(), $show_workflow_note_field, $action['icon'] );
		}
		if ( ! empty( $html ) ) {
			$html = sprintf( '<div id="gravityflow-actions-%d" class="gravityflow-actions gravityflow-actions-locked">
									<i class="gravityflow-actions-lock fa fa-lock" aria-hidden="true"></i>
									<i class="gravityflow-actions-unlock fa fa-unlock-alt" aria-hidden="true"></i>
									%s
									<span class="gravityflow-actions-spinner">
										<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
									</span>

									<div class="gravityflow-actions-note-field-container" style="display:none;">
										<label>%s:</label>
										<div>
											<textarea></textarea>
										</div>
									</div>
								</div>
								', $entry_id, $html, __( 'Note', 'gravityflow' ) );
		}

		return $html;
	}
}
