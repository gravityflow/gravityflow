<?php

namespace Gravity_Flow\Gravity_Flow\Inbox\Models;

use Gravity_Flow\Gravity_Flow\Models\Model;
use \Gravity_Flow_API;
use \GFAPI;

class Task implements Model {

	const WIDTH_SMALL = 50;
	const WIDTH_MED   = 100;
	const WIDTH_WIDE  = 200;

	private static $forms = array();

	/**
	 * @var Gravity_Flow_API $api
	 */
	private $api;

	/**
	 * @var GFAPI
	 */
	private $gf_api;

	public function __construct( Gravity_Flow_API $api, GFAPI $gf_api ) {
		$this->api    = $api;
		$this->gf_api = $gf_api;
	}

	private function parse_args( $args ) {
		$args = array_merge( $this->get_defaults(), $args );

		/**
		 * Allow the inbox page arguments to be overridden.
		 *
		 * @param array $args The inbox page arguments.
		 */
		return apply_filters( 'gravityflow_inbox_args', $args );
	}

	public function get_table_columns( $args = array() ) {
		$args = $this->parse_args( $args );

		$columns = array();
//		if ( $args['step_highlight'] ) {
//			$columns['step_highlight'] = 'step_highlight';
//		}

		if ( $args['id_column'] ) {
			$columns['id'] = __( 'ID', 'gravityflow' );
		}

		if ( $args['actions_column'] ) {
			$columns['actions'] = '';
		}

		if ( empty( $args['form_id'] ) || is_array( $args['form_id'] ) ) {
			$columns['form_title'] = __( 'Form', 'gravityflow' );
		}

		if ( $args['submitter_column'] ) {
			$columns['created_by'] = __( 'Submitter', 'gravityflow' );
		}

		if ( $args['step_column'] ) {
			$columns['workflow_step'] = __( 'Step', 'gravityflow' );
		}

		$columns['date_created'] = __( 'Submitted', 'gravityflow' );
		$columns                 = \Gravity_Flow_Common::get_field_columns( $columns, $args['form_id'], $args['field_ids'] );

		if ( $args['last_updated'] ) {
			$columns['last_updated'] = __( 'Last Updated', 'gravityflow' );
		}

		if ( $args['due_date'] ) {
			$columns['due_date'] = __( 'Due Date', 'gravityflow' );
		}

		/**
		 * Allows the columns to be filtered for the inbox table.
		 *
		 * @since 2.2.4-dev
		 *
		 * @param array $columns The columns to be filtered
		 * @param array $args The array of args for this inbox table.
		 */
		return apply_filters( 'gravityflow_columns_inbox_table', $columns, $args );
	}

	private function column_config_map() {
		$map = array(
			'id'            => array(
				'compareType' => 'int',
				'minWidth'    => self::WIDTH_SMALL,
			),
			'actions'       => array(
				'minWidth' => self::WIDTH_SMALL,
			),
			'form_title'    => array(
				'minWidth' => self::WIDTH_WIDE,
			),
			'created_by'    => array(
				'minWidth' => self::WIDTH_MED,
			),
			'workflow_step' => array(
				'minWidth' => self::WIDTH_SMALL,
			),
			'date_created'  => array(
				'sortKey'     => 'date_created_timestamp',
				'compareType' => 'date',
			),
			'last_updated'  => array(
				'sortKey'     => 'last_updated_timestamp',
				'compareType' => 'date',
			),
			'due_date'      => array(
				'sortKey'     => 'due_date_timestamp',
				'compareType' => 'date',
			),
		);

		return apply_filters( 'gflow_column_config_map', $map );
	}

	private function apply_config_to_columns( $columns ) {
		$map = $this->column_config_map();

		array_walk( $columns, function ( &$data, $name ) use ( $map ) {
			$defaults = array(
				'headerName'  => $data,
				'field'       => $name,
				'filter'      => 'agTextColumnFilter',
				'minWidth'    => self::WIDTH_MED,
				'sortable'    => true,
				'compareType' => 'string',
				'sortKey'     => $name,
			);

			$values = isset( $map[ $name ] ) ? $map[ $name ] : array();

			$data = wp_parse_args( $values, $defaults );
		} );

		return $columns;
	}

	public function get_table_header_defs( $args = array() ) {
		$headers = array();
		$columns = $this->get_table_columns( $args );
		$columns = $this->apply_config_to_columns( $columns );

		foreach ( $columns as $name => $data ) {
			$headers[] = $data;
		}

		return $headers;
	}

	/**
	 * Get the value for display in the current column for the entry being processed.
	 *
	 * @param string $id The column id, the key to the value in the entry or form.
	 * @param array $form The form object for the current entry.
	 * @param array $entry The entry currently being processed for display.
	 * @param array $columns The columns to be displayed.
	 *
	 * @return string
	 */
	private function get_column_value( $id, $form, $entry, $columns ) {
		$value = '';
		switch ( strtolower( $id ) ) {
			case 'step_highlight':
				$step_highlight_color = '';
				if ( array_key_exists( 'step_highlight', $columns ) && isset( $entry['workflow_step'] ) ) {
					$step = gravity_flow()->get_step( $entry['workflow_step'] );
					if ( $step ) {
						if ( $step->step_highlight && $step->step_highlight_type == 'color' && preg_match( '/^#[a-f0-9]{6}$/i', $step->step_highlight_color ) ) {
							$step_highlight_color = $step->step_highlight_color;
						}
					}
				}

				if ( isset( $entry['workflow_step'] ) ) {
					$step = gravity_flow()->get_step( $entry['workflow_step'], $entry );
					if ( $step && $step->due_date && $step->is_overdue() && $step->due_date_highlight_type == 'color' && preg_match( '/^#[a-f0-9]{6}$/i', $step->due_date_highlight_color ) ) {
						$step_highlight_color = $step->due_date_highlight_color;
					}
				}
				/**
				 * Allow the Step Highlight colour to be overridden.
				 *
				 * @since 1.9.2
				 *
				 * @param string $step_highlight_color The highlight color (hex value) of the row currently being processed.
				 * @param int $form ['id'] The ID of form currently being processed.
				 * @param array $entry The entry object for the row currently being processed.
				 *
				 * @return string
				 */
				$value = apply_filters( 'gravityflow_step_highlight_color_inbox', $step_highlight_color, $form['id'], $entry );
				break;
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
				 * @param array $entry The entry object for the row currently being processed.
				 * @param array $form The form object for the current entry.
				 */
				$value = apply_filters( 'gravityflow_inbox_submitter_name', $submitter_name, $entry, $form );
				break;
			case 'date_created':
				$date_created = \Gravity_Flow_Common::format_date( $entry['date_created'], '', true, true );
				$value        = $date_created;
				break;
			case 'last_updated':
				$last_updated = date( 'Y-m-d H:i:s', $entry['workflow_timestamp'] );
				$value        = $entry['date_created'] != $last_updated ? \Gravity_Flow_Common::format_date( $last_updated, '', true, true ) : '-';
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
				$api  = new Gravity_Flow_API( $form['id'] );
				$step = $api->get_current_step( $entry );
				if ( $step ) {
					$value = self::format_actions( $step );
				}
				break;
			case 'payment_status':
				$value = rgar( $entry, 'payment_status' );
				if ( gravity_flow()->is_gravityforms_supported( '2.4' ) ) {
					$value = \GFCommon::get_entry_payment_status_text( $value );
				}
				break;

			case 'due_date':
				$api  = new Gravity_Flow_API( $form['id'] );
				$step = $api->get_current_step( $entry );
				if ( $step && $step->due_date ) {
					$value = \Gravity_Flow_Common::format_date( date( 'Y-m-d H:i:s', $step->get_due_date_timestamp() ), '', true, false );
				} else {
					$value = '-';
				}
				break;
			default:
				$field = \GFFormsModel::get_field( $form, $id );

				if ( is_object( $field ) ) {
					require_once( \GFCommon::get_base_path() . '/entry_list.php' );
					$value = $field->get_value_entry_list( rgar( $entry, $id ), $entry, $id, $columns, $form );
				} else {
					$value = rgar( $entry, $id );
				}

				$value = apply_filters( 'gform_entries_field_value', $value, $form['id'], $id, $entry );
		}

		return $value;
	}

	/**
	 * Get the default arguments used when rendering the inbox page.
	 *
	 * @return array
	 */
	private function get_defaults() {
		$field_ids = apply_filters( 'gravityflow_inbox_fields', array() );

		$filter = apply_filters( 'gravityflow_inbox_filter', array(
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
			'due_date'             => false,
			'step_highlight'       => true,
		);

	}

	public function get_inbox_tasks( $args ) {
		$args = $this->parse_args( $args );

		$tasks = array();

		$entries = $this->api::get_inbox_entries( $args );

		if ( empty( $entries ) ) {
			return $tasks;
		}

		$columns = $this->get_table_columns( $args );

		foreach ( $entries as $entry ) {
			$tasks[] = $this->get_data_for_row( $args, $entry, $columns );
		}

		return $tasks;
	}

	private function get_form( $form_id ) {
		if ( isset( self::$forms[ $form_id ] ) ) {
			return self::$forms[ $form_id ];
		}

		self::$forms[ $form_id ] = $this->gf_api::get_form( $form_id );

		return self::$forms[ $form_id ];
	}

	private function get_data_for_row( $args, $entry, $columns ) {
		$data      = array();
		$form      = $this->get_form( $entry['form_id'] );
		$url_entry = esc_url_raw( sprintf( '%s&id=%d&lid=%d', $args['detail_base_url'], $entry['form_id'], $entry['id'] ) );
		$link      = "<a href='%s'>%s</a>";

		/**
		 * Allows the entry URL to be modified for each of the entries in the inbox table.
		 *
		 * @since 2.5.6
		 *
		 * @param string $url_entry The entry URL.
		 * @param string $entry The current entry.
		 * @param string $args The inbox page arguments.
		 * @param array $form The form object for the current entry.
		 *
		 * @return string
		 */
		$url_entry = apply_filters( 'gravityflow_entry_url_inbox_table', $url_entry, $entry, $args, $form );

		/**
		 * Allows the entry link to be modified for each of the entries in the inbox table.
		 *
		 * @since 1.9.2
		 *
		 * @param string $link The entry link HTML.
		 * @param string $url_entry The entry URL.
		 * @param string $entry The current entry.
		 * @param string $args The inbox page arguments.
		 *
		 * @return string
		 */
		$link = apply_filters( 'gravityflow_entry_link_inbox_table', $link, $url_entry, $entry, $args );

		foreach ( $columns as $name => $label ) {
			$data[ $name ] = $this->get_column_value( $name, $form, $entry, $columns );
		}

		return $data;
	}

}
