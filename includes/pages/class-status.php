<?php
/**
 * Gravity Flow Status
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Status
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Status
 */
class Gravity_Flow_Status {

	/**
	 * Triggers display of the status page or processing of the export.
	 *
	 * @param array $args The status page arguments.
	 *
	 * @return array|WP_Error
	 */
	public static function render( $args = array() ) {
		wp_enqueue_script( 'gform_field_filter' );

		$args = array_merge( self::get_defaults(), $args );
		$args = self::maybe_add_constraint_filters( $args );

		if ( empty( $args['filter_hidden_fields'] ) ) {
			$args['filter_hidden_fields'] = array( 'page' => 'gravityflow-status' );
		}

		/**
		 * Allow the status page/export arguments to be overridden.
		 *
		 * @param array $args The status page and export arguments.
		 */
		$args = apply_filters( 'gravityflow_status_args', $args );

		if ( $args['format'] == 'table' ) {
			self::status_page( $args );
		} else {
			return self::process_export( $args );
		}
	}

	/**
	 * The default arguments to use when rendering the status page or processing the export.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'action_url'           => admin_url( 'admin.php?page=gravityflow-status' ),
			'constraint_filters'   => array(),
			'field_ids'            => apply_filters( 'gravityflow_status_fields', array() ),
			'format'               => 'table', // The output format: table or csv.
			'file_name'            => 'export.csv',
			'id_column'            => true,
			'submitter_column'     => true,
			'step_column'          => true,
			'status_column'        => true,
			'last_updated'         => false,
			'filter_hidden_fields' => array(),
		);
	}

	/**
	 * If not already configured define the default constraint filters.
	 *
	 * @param array $args The status page and export arguments.
	 *
	 * @return array
	 */
	public static function maybe_add_constraint_filters( $args ) {

		if ( empty( $args['constraint_filters'] ) ) {
			$args['constraint_filters'] = array(
				'form_id'    => 0,
				'start_date' => '',
				'end_date'   => '',
			);
		}

		$args['constraint_filters'] = apply_filters( 'gravityflow_status_filter', $args['constraint_filters'] );

		if ( ! isset( $args['constraint_filters']['form_id'] ) ) {
			$args['constraint_filters']['form_id'] = 0;
		}
		if ( ! isset( $args['constraint_filters']['start_date'] ) ) {
			$args['constraint_filters']['start_date'] = '';
		}
		if ( ! isset( $args['constraint_filters']['end_date'] ) ) {
			$args['constraint_filters']['end_date'] = '';
		}

		return $args;
	}

	/**
	 * Display the status page.
	 *
	 * @param array $args The status page arguments.
	 */
	public static function status_page( $args ) {
		$table = new Gravity_Flow_Status_Table( $args );

		?>
		<form id="gravityflow-status-filter" method="GET" action="">
			<?php
			foreach ( $args['filter_hidden_fields'] as $hidden_field => $hidden_field_value ) {
				printf( '<input type="hidden" name="%s" value="%s"/>', $hidden_field, $hidden_field_value );
			}

			$table->views();
			$table->filters();
			$table->prepare_items();
			?>
		</form>
		<form id="gravityflow-status-list" method="POST" action="">
			<?php
			$table->display();
			?>
		</form>

		<?php
		if ( is_admin() ) {
			$str = $_SERVER['QUERY_STRING'];
			parse_str( $str, $query_args );

			$remove_args = array( 'paged', '_wpnonce', '_wp_http_referer', 'action', 'action2' );
			foreach ( $remove_args as $remove_arg_key ) {
				unset( $query_args[ $remove_arg_key ] );
			}
			$query_args['gravityflow_export_nonce'] = wp_create_nonce( 'gravityflow_export_nonce' );
			$filter_args_str                        = '&' . http_build_query( $query_args );
			echo sprintf( '<br /><a class="gravityflow-export-status-button button" data-filter_args="%s">%s</a>', $filter_args_str, esc_html__( 'Export', 'gravityflow' ) );
			echo sprintf( '<img class="gravityflow-spinner" src="%s" style="display:none;margin:5px"/>', GFCommon::get_base_url() . '/images/spinner.gif' );
		}
	}

	/**
	 * Process the status export.
	 *
	 * @param array $args The status export arguments.
	 *
	 * @return array|WP_Error
	 */
	public static function process_export( $args ) {
		$upload_dir = wp_upload_dir();
		if ( ! is_writeable( $upload_dir['basedir'] ) ) {
			return new WP_Error( 'export_destination_not_writeable', esc_html__( 'The destination file is not writeable', 'gravityflow' ) );
		}

		$file_path = trailingslashit( $upload_dir['basedir'] ) . $args['file_name'] . '.csv';
		$export    = '';

		$table = new Gravity_Flow_Status_Table( $args );
		$table->prepare_items();
		$page = (int) $table->get_pagination_arg( 'page' );

		if ( $page < 2 ) {
			@unlink( $file_path );
			$export .= $table->export_column_names();
		}
		$export .= $table->export();

		@file_put_contents( $file_path, $export, FILE_APPEND );

		$per_page    = (int) $table->get_pagination_arg( 'per_page' );
		$total_items = (int) $table->get_pagination_arg( 'total_items' );
		$total_pages = (int) $table->get_pagination_arg( 'total_pages' );

		$status   = $page == $total_pages ? 'complete' : 'incomplete';
		$percent  = $page * $per_page / $total_items * 100;
		$response = array( 'status' => $status, 'percent' => (int) $percent );

		if ( $status == 'complete' ) {
			$download_args   = array(
				'nonce'     => wp_create_nonce( 'gravityflow_download_export' ),
				'action'    => 'gravityflow_download_export',
				'file_name' => $args['file_name'],
			);
			$download_url    = add_query_arg( $download_args, admin_url( 'admin-ajax.php' ) );
			$response['url'] = esc_url_raw( $download_url );
		}

		return $response;
	}
}


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class Gravity_Flow_Status_Table
 */
class Gravity_Flow_Status_Table extends WP_List_Table {

	/**
	 * The pagination arguments.
	 *
	 * @var array
	 */
	public $pagination_args;

	/**
	 * URL of this page
	 *
	 * @var string
	 */
	public $base_url;

	/**
	 * Base url of the detail page
	 *
	 * @var string
	 */
	public $detail_base_url;

	/**
	 * Total number of entries
	 *
	 * @var int
	 */
	public $total_count;
	/**
	 * Total number of pending entries
	 *
	 * @var int
	 */
	public $pending_count;
	/**
	 * Total number of complete entries
	 *
	 * @var int
	 */
	public $complete_count;
	/**
	 * Total number of cancelled entries
	 *
	 * @var int
	 */
	public $cancelled_count;

	/**
	 * The fields to be displayed.
	 *
	 * @var array
	 */
	public $field_ids = array();

	/**
	 * The filter arguments used to limit the displayed entries.
	 *
	 * @var array
	 */
	public $constraint_filters = array();

	/**
	 * Indicates if the table should include entries from all users.
	 *
	 * @var bool
	 */
	public $display_all;

	/**
	 * The bulk action properties.
	 *
	 * @var array
	 */
	public $bulk_actions;

	/**
	 * The number of entries to include on each page.
	 *
	 * @var int
	 */
	public $per_page;

	/**
	 * The steps for the specified form.
	 *
	 * @var Gravity_Flow_Step[]
	 */
	private $_steps;

	/**
	 * The filter arguments.
	 *
	 * @var array
	 */
	private $_filter_args;

	/**
	 * Should the ID column be displayed?
	 *
	 * @var bool
	 */
	public $id_column;

	/**
	 * Should the submitter column be displayed?
	 *
	 * @var bool
	 */
	public $submitter_column;

	/**
	 * Should the step column be displayed?
	 *
	 * @var bool
	 */
	public $step_column;

	/**
	 * Should the status column be displayed?
	 *
	 * @var bool
	 */
	public $status_column;

	/**
	 * Should the last updated column be displayed?
	 *
	 * @var bool
	 */
	public $last_updated;

	/**
	 * A cache of previously retrieved forms.
	 *
	 * @var array
	 */
	private $_forms = array();

	/**
	 * All the args for the table.
	 *
	 * @var array $args
	 */
	public $args;

	/**
	 * Gravity_Flow_Status_Table constructor.
	 *
	 * @param array $args The status page arguments.
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'singular'           => 'entry',    // Not translated - only used in class names
			'plural'             => 'entries',  // Not translated - only used in class names
			'ajax'               => false,
			'base_url'           => admin_url( 'admin.php?page=gravityflow-status' ),
			'detail_base_url'    => admin_url( 'admin.php?page=gravityflow-inbox&view=entry' ),
			'constraint_filters' => array(),
			'field_ids'          => array(),
			'screen'             => 'gravityflow-status',
			'display_all'        => GFAPI::current_user_can_any( 'gravityflow_status_view_all' ),
			'per_page'           => 20,
			'id_column'          => true,
			'submitter_column'   => true,
			'step_column'        => true,
			'status_column'      => true,
			'last_updated'       => false,
		);

		$args = wp_parse_args( $args, $default_args );

		$default_bulk_actions = array( 'print' => esc_html__( 'Print', 'gravityflow' ) );

		if ( GFAPI::current_user_can_any( 'gravityflow_admin_actions' ) ) {
			$default_bulk_actions['restart_workflow'] = esc_html__( 'Restart Workflow', 'gravityflow' );
		}

		$args['bulk_actions'] = isset( $args['bulk_actions'] ) ? array_merge( $default_bulk_actions, $args['bulk_actions'] ) : $default_bulk_actions;

		require_once( ABSPATH . 'wp-admin/includes/template.php' );
		if ( ! class_exists( 'WP_Screen' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
		}

		parent::__construct( $args );

		$this->base_url           = $args['base_url'];
		$this->detail_base_url    = $args['detail_base_url'];
		$this->constraint_filters = $args['constraint_filters'];
		if ( ! is_array( $args['field_ids'] ) ) {
			$args['field_ids'] = empty( $args['field_ids'] ) ? array() : explode( ',', $args['field_ids'] );
		}
		$this->field_ids    = $args['field_ids'];
		$this->display_all  = $args['display_all'];
		$this->bulk_actions = $args['bulk_actions'];
		$this->set_counts();
		$this->per_page = $args['per_page'];
		$this->id_column = $args['id_column'];
		$this->step_column = $args['step_column'];
		$this->submitter_column = $args['submitter_column'];
		$this->status_column = $args['status_column'];
		$this->last_updated = $args['last_updated'];
	}

	/**
	 * The text to be displayed if there are no workflow entries.
	 */
	public function no_items() {
		esc_html_e( "You haven't submitted any workflow forms yet.", 'gravityflow' );
	}

	/**
	 * Get the views.
	 *
	 * @return array
	 */
	public function get_views() {
		$current         = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
		$total_count     = '&nbsp;<span class="count">(' . $this->total_count . ')</span>';
		$complete_count  = '&nbsp;<span class="count">(' . $this->complete_count . ')</span>';
		$pending_count   = '&nbsp;<span class="count">(' . $this->pending_count . ')</span>';
		$cancelled_count = '&nbsp;<span class="count">(' . $this->cancelled_count . ')</span>';

		$pending_label = gravity_flow()->translate_status_label( 'pending' );
		$complete_label = gravity_flow()->translate_status_label( 'complete' );
		$cancelled_label = gravity_flow()->translate_status_label( 'cancelled' );

		$views = array(
			'all'       => sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( array(
				'status',
				'paged',
			) ) ), $current === 'all' || $current == '' ? ' class="current"' : '', esc_html__( 'All', 'gravityflow' ) . $total_count ),
			'pending'   => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array(
				'status' => 'pending',
				'paged'  => false,
			) ) ), $current === 'pending' ? ' class="current"' : '', esc_html( $pending_label ) . $pending_count ),
			'complete'  => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array(
				'status' => 'complete',
				'paged'  => false,
			) ) ), $current === 'complete' ? ' class="current"' : '',esc_html( $complete_label ) . $complete_count ),
			'cancelled' => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( array(
				'status' => 'cancelled',
				'paged'  => false,
			) ) ), $current === 'cancelled' ? ' class="current"' : '', esc_html( $cancelled_label ) . $cancelled_count ),
		);

		return $views;
	}

	/**
	 * Output the status filters.
	 */
	public function filters() {
		wp_print_styles( array( 'thickbox' ) );
		add_thickbox();
		?>
		<div id="gravityflow-status-filters">

			<div id="gravityflow-status-date-filters">
				<?php
				$filter_entry_id = $this->entry_id_input();
				$start_date      = $this->date_input( esc_html__( 'Start:', 'gravityflow' ), 'start_date' );
				$end_date        = $this->date_input( esc_html__( 'End:', 'gravityflow' ), 'end_date' );
				$filter_form_id  = $this->form_select();
				$this->status_input();
				?>

				<div id="entry_filters" style="display:inline-block;"></div>
				<input type="submit" class="button-secondary" value="<?php esc_html_e( 'Apply', 'gravityflow' ); ?>"/>

				<?php if ( ! empty( $start_date ) || ! empty( $end_date ) || ! empty( $filter_form_id ) | ! empty( $filter_entry_id ) ) : ?>
					<a href="<?php echo esc_url( $this->base_url ); ?>"
					   class="button-secondary"><?php esc_html_e( 'Clear Filter', 'gravityflow' ); ?></a>
				<?php endif; ?>
			</div>
			<?php $this->search_box( esc_html__( 'Search', 'gravityflow' ), 'gravityflow-search' ); ?>
		</div>

		<?php
		$this->output_filter_scripts();
		$this->output_print_modal();
		$this->process_bulk_action();
	}

	/**
	 * Output an input for the entry id filter.
	 *
	 * @return int|string The entry ID to filter the entries by.
	 */
	public function entry_id_input() {
		$filter_entry_id = empty( $_REQUEST['entry-id'] ) ? '' : absint( $_REQUEST['entry-id'] );

		printf( '<input placeholder="ID" type="text" name="entry-id" id="entry-id" class="small-text" value="%s"/> ', $filter_entry_id );

		return $filter_entry_id;
	}

	/**
	 * Output a datepicker input for the specified filter if it is not defined in the constraint filters.
	 *
	 * @param string $label  The label to be displayed for this input.
	 * @param string $filter The filter key as used in the constraint filters (start_date or end_date).
	 *
	 * @return null|string The date to filter the entries by.
	 */
	public function date_input( $label, $filter ) {
		if ( ! empty( $this->constraint_filters[ $filter ] ) ) {
			return null;
		}

		$id   = str_replace( '_', '-', $filter );
		$date = isset( $_REQUEST[ $id ] ) ? $this->sanitize_date( $_REQUEST[ $id ] ) : null;

		printf( '<label for="%s">%s</label> <input type="text" id="%s" name="%s" class="datepicker medium-text ymd_dash" value="%s" placeholder="%s"/> ', $id, $label, $id, $id, $date, esc_attr__( 'yyyy-mm-dd', 'gravityflow' ) );

		return $date;
	}

	/**
	 * Output the forms drop down or a hidden input if a form was specified in the constraint filters.
	 *
	 * @return string|int $filter_form_id The form ID to filter the entries by.
	 */
	public function form_select() {
		if ( ! empty( $this->constraint_filters['form_id'] ) ) {

			printf( '<input type="hidden" name="form-id" id="gravityflow-form-select" value="%s">', esc_attr( $this->constraint_filters['form_id'] ) );

			return '';

		} else {

			$filter_form_id = empty( $_REQUEST['form-id'] ) ? '' : absint( $_REQUEST['form-id'] );
			$selected       = selected( '', $filter_form_id, false );
			$options        = sprintf( '<option value="" %s >%s</option>', $selected, esc_html__( 'Workflow Form', 'gravityflow' ) );
			$forms          = GFAPI::get_forms();

			foreach ( $forms as $form ) {
				$form_id = absint( $form['id'] );
				$steps   = gravity_flow()->get_steps( $form_id );
				if ( ! empty( $steps ) ) {
					$selected = selected( $filter_form_id, $form_id, false );
					$options .= sprintf( '<option value="%d" %s>%s</option>', $form_id, $selected, esc_html( $form['title'] ) );
				}
			}

			printf( '<select id="gravityflow-form-select" name="form-id">%s</select>', $options );

			return $filter_form_id;

		}
	}

	/**
	 * Output the hidden input for the status filter.
	 */
	public function status_input() {
		$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';

		if ( ! empty( $status ) ) {
			printf( '<input type="hidden" name="status" value="%s"/>', esc_attr( $status ) );
		}
	}

	/**
	 * Get the field filters to be output with the filter scripts.
	 *
	 * @return null|array
	 */
	public function get_field_filters() {
		$field_filters = null;

		$forms = GFAPI::get_forms();
		foreach ( $forms as $form ) {
			$form_filters = GFCommon::get_field_filter_settings( $form );

			$empty_filter = array(
				'key'       => '',
				'text'      => esc_html__( 'Fields', 'gravityflow' ),
				'operators' => array(),
			);
			array_unshift( $form_filters, $empty_filter );
			$field_filters[ $form['id'] ] = $form_filters;
		}

		/**
		 * Allows modification of the field filters in the status table.
		 *
		 * @param array $field_filters An associative array of filters by Form ID.
		 */
		$field_filters = apply_filters( 'gravityflow_field_filters_status_table', $field_filters );

		return $field_filters;
	}

	/**
	 * Get the field id for use with the filter scripts.
	 *
	 * @return string
	 */
	public function get_init_filter_field_id() {
		$search_field_ids = isset( $_REQUEST['f'] ) ? $_REQUEST['f'] : '';

		return ( $search_field_ids && is_array( $search_field_ids ) ) ? $search_field_ids[0] : '';
	}

	/**
	 * Get the operator for use with the filter scripts.
	 *
	 * @return bool|string
	 */
	public function get_init_filter_operator() {
		$search_operators = isset( $_REQUEST['o'] ) ? $_REQUEST['o'] : '';
		$search_operator  = ( $search_operators && is_array( $search_operators ) ) ? $search_operators[0] : false;

		return empty( $search_operator ) ? 'contains' : $search_operator;
	}

	/**
	 * Get the value for use with the filter scripts.
	 *
	 * @return int|string
	 */
	public function get_init_filter_value() {
		$values = isset( $_REQUEST['v'] ) ? $_REQUEST['v'] : '';

		return ( $values && is_array( $values ) ) ? $values[0] : 0;
	}

	/**
	 * Get the init filters to be output with the filter scripts.
	 *
	 * @return array
	 */
	public function get_init_filter_vars() {
		return array(
			'mode'    => 'off',
			'filters' => array(
				array(
					'field'    => $this->get_init_filter_field_id(),
					'operator' => $this->get_init_filter_operator(),
					'value'    => $this->get_init_filter_value(),
				),
			),
		);
	}

	/**
	 * Output the filter scripts to the page.
	 */
	public function output_filter_scripts() {
		?>
		<script>
			(function ($) {
				$(document).ready(function () {
					var gformFieldFilters = <?php echo json_encode( $this->get_field_filters() ); ?>,
						gformInitFilter = <?php echo json_encode( $this->get_init_filter_vars() ); ?>;
					var $form_select = $('#gravityflow-form-select');
					var filterFormId = $form_select.val();
					var $entry_filters = $('#entry_filters');
					if (filterFormId) {
						$entry_filters.gfFilterUI(gformFieldFilters[filterFormId], gformInitFilter, false);
						if ($('.gform-filter-field').val() === '') {
							$('.gform-filter-operator').hide();
							$('.gform-filter-value').hide();
						}
					}
					$form_select.change(function () {
						filterFormId = $form_select.val();
						if (filterFormId) {
							$entry_filters.gfFilterUI(gformFieldFilters[filterFormId], gformInitFilter, false);
							$('.gform-filter-field').val('');
							$('.gform-filter-operator').hide();
							$('.gform-filter-value').hide();
						} else {
							$entry_filters.html('');
						}

					});
					$('#entry_filters').on('change', '.gform-filter-field', function () {
						if ($('.gform-filter-field').val() === '') {
							$('.gform-filter-operator').hide();
							$('.gform-filter-value').hide();
						}
					});
				});

			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Output the markup for the print modal.
	 */
	public function output_print_modal() {
		?>
		<div id="print_modal_container" style="display:none;">
			<div id="print_container">

				<div class="tagsdiv">
					<div id="print_options">

						<p class="description"><?php esc_html_e( 'Print all of the selected entries at once.', 'gravityflow' ); ?></p>

						<input type="checkbox" name="gravityflow-print-timelines" value="print_timelines" checked="checked" id="gravityflow-print-timelines"/>
						<label for="gravityflow-print-timelines"><?php esc_html_e( 'Include timelines', 'gravityflow' ); ?></label>
						<br/><br/>

						<input type="checkbox" name="gravityflow-print-page-break" value="print_page_break" checked="checked" id="gravityflow-print-page-break"/>
						<label for="gravityflow-print-page-break"><?php esc_html_e( 'Add page break between entries', 'gravityflow' ); ?></label>
						<br/><br/>

						<input id="gravityflow-bulk-print-button" type="button" value="<?php esc_attr_e( 'Print', 'gravityflow' ); ?>" class="button"/>

					</div>
				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Output the row checkbox.
	 *
	 * @param array $item The current entry.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		$feed_id = rgar( $item, 'id' );

		return sprintf( '<input type="checkbox" class="gravityflow-cb-entry-id" name="entry_ids[]" value="%s" />', esc_attr( $feed_id ) );
	}

	/**
	 * Output the entry ID.
	 *
	 * @param array $item The current entry.
	 */
	public function column_id( $item ) {
		$url_entry = esc_url( $this->get_entry_url( $item ) );
		$label     = absint( $item['id'] );
		$label     = $this->filter_field_value( $label, $item, 'id' );

		$link = "<a href='{$url_entry}'>$label</a>";
		echo $link;
	}

	/**
	 * Output the column value.
	 *
	 * @param array  $item        The current entry.
	 * @param string $column_name The column name.
	 */
	public function column_default( $item, $column_name ) {
		$url_entry = esc_url( $this->get_entry_url( $item ) );
		$form_id   = rgar( $item, 'form_id' );
		$form      = GFAPI::get_form( $form_id );

		/* @var GF_Field $field */
		$field = GFFormsModel::get_field( $form, $column_name );
		$value = rgar( $item, $column_name );
		if ( $field ) {
			$columns = RGFormsModel::get_grid_columns( $form_id, true );
			$value   = $field->get_value_entry_list( $value, $item, $column_name, $columns, $form );
		}

		$label = esc_html( $value );
		$label = $this->filter_field_value( $label, $item, $column_name );

		$link = "<a href='{$url_entry}'>$label</a>";
		echo $link;
	}

	/**
	 * Outputs the workflow final status.
	 *
	 * @param array $item The current entry.
	 */
	public function column_workflow_final_status( $item ) {
		$url_entry    = esc_url( $this->get_entry_url( $item ) );
		$final_status = rgar( $item, 'workflow_final_status' );
		$label        = empty( $final_status ) ? '' : gravity_flow()->translate_status_label( $final_status );
		$label        = esc_html( $label );
		$label        = $this->filter_field_value( $label, $item, 'final_status' );
		$link         = "<a href='{$url_entry}'>$label</a>";

		echo $link;

		$args = $this->get_filter_args();

		if ( empty( $item['workflow_step'] ) ) {
			return;
		}

		if ( ! isset( $args['form-id'] ) ) {
			$duration     = time() - strtotime( $item['date_created'] );
			$duration_str = ' ' . $this->format_duration( $duration );
			echo $duration_str;

			return;
		}

		$step_id = $this->get_filter_step_id();

		if ( $step_id ) {
			return;
		}

		$steps = $this->get_steps( $item['form_id'] );
		if ( $steps ) {
			$pending = $rejected = $green = 0;
			$id      = 'gravityflow-status-assignees-' . absint( $item['id'] );
			$m[]     = '<ul id="' . $id . '" style="display:none;">';
			$step    = gravity_flow()->get_step( $item['workflow_step'], $item );

			$meta_key = '';
			$assignee = false;

			if ( $step ) {
				$step_type = $step->get_type();
				if ( ( $step_type == 'approval' && ! $step->unanimous_approval ) || $step->assignee_policy == 'any' ) {
					$duration     = time() - strtotime( $item['date_created'] );
					$duration_str = ' (' . $this->format_duration( $duration ) . ') ';
					echo $duration_str;

					return;
				}

				$assignees = $step->get_assignees();
				foreach ( $assignees as $assignee ) {
					$duration_str = '';
					$meta_key     = sprintf( 'workflow_%s_%s', $assignee->get_type(), $assignee->get_id() );
					if ( isset( $item[ $meta_key ] ) ) {
						if ( $item[ $meta_key ] == 'pending' ) {
							$pending ++;
							if ( $item[ $meta_key . '_timestamp' ] ) {
								$duration     = time() - $item[ $meta_key . '_timestamp' ];
								$duration_str = ' (' . $this->format_duration( $duration ) . ') ';
							} else {
								$duration_str = '';
							}
						} elseif ( $item[ $meta_key ] == 'rejected' ) {
							$rejected ++;
						} else {
							$green ++;
						}
						$m[] = '<li>' . $assignee->get_display_name() . ': ' . $item[ $meta_key ] . $duration_str . '</li>';
					}
				}
			}
			$m[] = '</ul>';

			if ( $green == 0 && $rejected == 0 && $pending == 1 && $assignee ) {
				if ( $item[ $meta_key . '_timestamp' ] ) {
					$duration     = time() - $item[ $meta_key . '_timestamp' ];
					$duration_str = ' (' . $this->format_duration( $duration ) . ') ';
					echo ': ' . $assignee->get_display_name() . $duration_str;
				}
			} else {
				$assignee_icons = array();
				for ( $i = 0; $i < $green; $i ++ ) {
					$assignee_icons[] = "<i class='fa fa-male' style='color:green;'></i>";
				}
				for ( $i = 0; $i < $rejected; $i ++ ) {
					$assignee_icons[] = "<i class='fa fa-male' style='color:red;'></i>";
				}
				for ( $i = 0; $i < $pending; $i ++ ) {
					$assignee_icons[] = "<i class='fa fa-male' style='color:silver;'></i>";
				}
				echo sprintf( ":&nbsp;&nbsp;<span style='cursor:pointer;' onclick=\"jQuery('#$id').slideToggle()\">%s</span>", join( "\n", $assignee_icons ) );
				echo join( "\n", $m );
			}
		}

	}

	/**
	 * Outputs the entry submitter details.
	 *
	 * @param array $item The current entry.
	 */
	public function column_created_by( $item ) {
		$url_entry = esc_url( $this->get_entry_url( $item ) );

		$user_id = $item['created_by'];
		if ( $user_id ) {
			$user = get_user_by( 'id', $user_id );
			if ( empty( $user ) || is_wp_error( $user ) ) {
				$display_name = $user_id . ' ' . esc_html__( '(deleted)', 'gravityflow' );
			} else {
				$display_name = $user->display_name;
			}
		} else {
			$display_name = $item['ip'];
		}
		$label = esc_html( $display_name );

		$form_id = rgar( $item, 'form_id' );
		$form = $this->get_form( $form_id );

		/**
		 * Allow the value displayed in the Submitter column to be overridden.
		 *
		 * @param string $label The display_name of the logged-in user who submitted the form or the guest ip address.
		 * @param array  $item  The entry object for the row currently being processed.
		 * @param array  $form  The form object for the current entry.
		 */
		$label = apply_filters( 'gravityflow_status_submitter_name', $label, $item, $form );
		$label = $this->filter_field_value( $label, $item, 'created_by' );

		$link  = "<a href='{$url_entry}'>$label</a>";
		echo $link;
	}

	/**
	 * Outputs the form title.
	 *
	 * @param array $item The current entry.
	 */
	public function column_form_id( $item ) {
		$url_entry = esc_url( $this->get_entry_url( $item ) );

		$form_id = $item['form_id'];
		$form    = $this->get_form( $form_id );

		$label = esc_html( $form['title'] );
		$label = $this->filter_field_value( $label, $item, 'form_id' );

		$link  = "<a href='{$url_entry}'>$label</a>";
		echo $link;
	}

	/**
	 * Outputs the current step name.
	 *
	 * @param array $item The current entry.
	 */
	public function column_workflow_step( $item ) {
		$step_id = rgar( $item, 'workflow_step' );
		if ( $step_id > 0 ) {
			$step      = gravity_flow()->get_step( $step_id );
			$url_entry = esc_url( $this->get_entry_url( $item ) );

			$label = $step ? esc_html( $step->get_name() ) : '';
			$step_status = rgar( $item, 'workflow_step_status_' . $step->get_id() );

			if ( 'queued' == $step_status ) {

				$step->_entry = $item;
				$scheduled_timestamp = $step->get_schedule_timestamp();
				switch ( $step->schedule_type ) {
					case 'date':
						$scheduled_date = $step->schedule_date;
						break;
					case 'date_field':
						$scheduled_date_str = date( 'Y-m-d H:i:s', $scheduled_timestamp );
						$scheduled_date     = get_date_from_gmt( $scheduled_date_str );
						break;
					case 'delay':
					default:
						$scheduled_date_str = date( 'Y-m-d H:i:s', $scheduled_timestamp );
						$scheduled_date     = get_date_from_gmt( $scheduled_date_str );
				}

				$label .= '' . sprintf( '<div class="step_status_queue">(%s: %s)</div>', esc_html__( 'Queued', 'gravityflow' ), $scheduled_date );

			} elseif ( $step->supports_expiration() && $step->expiration ) {
				$step->_entry = $item;
				$expiration_timestamp = $step->get_expiration_timestamp();
				$expiration_date_str  = date( 'Y-m-d H:i:s', $expiration_timestamp );
				$expiration_date      = get_date_from_gmt( $expiration_date_str );
				$label .= sprintf( '<div class="step_status_expires">(%s: %s)</div>', esc_html__( 'Expires', 'gravityflow' ), $expiration_date );
			}

			$label = $this->filter_field_value( $label, $item, 'workflow_step' );
			$link  = "<a href='{$url_entry}'>$label</a>";
			$output = $link;
		} else {
			$output = '<span class="gravityflow-empty">&dash;</span>';
		}

		/**
		 * Allow the value in the step column on the status page to be modified.
		 *
		 * @param string $output The column value to be output.
		 * @param array  $item   The Entry.
		 */
		$output = apply_filters( 'gravityflow_step_column_status_page', $output, $item );
		echo $output;
	}

	/**
	 * Outputs the entry creation date.
	 *
	 * @param array $item The current entry.
	 */
	public function column_date_created( $item ) {
		$url_entry = esc_url( $this->get_entry_url( $item ) );
		$label     = Gravity_Flow_Common::format_date( $item['date_created'], '', true, true );
		$label     = $this->filter_field_value( $label, $item, 'date_created' );
		$link      = "<a href='{$url_entry}'>$label</a>";
		echo $link;
	}

	/**
	 * Outputs the workflow timestamp.
	 *
	 * @param array $item The current entry.
	 */
	public function column_workflow_timestamp( $item ) {
		$label = '-';

		if ( ! empty( $item['workflow_timestamp'] ) ) {
			$url_entry    = esc_url( $this->get_entry_url( $item ) );
			$last_updated = esc_html( Gravity_Flow_Common::format_date( $item['workflow_timestamp'], '', true, true ) );
			$last_updated = $this->filter_field_value( $last_updated, $item, 'workflow_timestamp' );
			$label        = "<a href='{$url_entry}'>$last_updated</a>";
		}

		echo $label;
	}

	/**
	 * Outputs the entry payment status.
	 *
	 * @since 2.2.4-dev
	 *
	 * @param array $item The current entry.
	 */
	public function column_payment_status( $item ) {
		$url_entry      = esc_url( $this->get_entry_url( $item ) );
		$payment_status = rgar( $item, 'payment_status' );
		if ( gravity_flow()->is_gravityforms_supported( '2.4' ) ) {
			$payment_status = GFCommon::get_entry_payment_status_text( $payment_status );
		}

		$label = $this->filter_field_value( $payment_status, $item, 'payment_status' );
		$link  = "<a href='{$url_entry}'>$label</a>";
		echo $link;
	}

	/**
	 * Returns the entry detail URL.
	 *
	 * @since 2.2.4-dev
	 *
	 * @param array $entry The current entry.
	 *
	 * @return string
	 */
	public function get_entry_url( $entry ) {
		return sprintf( '%s&id=%d&lid=%d', $this->detail_base_url, $entry['form_id'], $entry['id'] );
	}

	/**
	 * Allows the field value to be filtered before being displayed in the status table.
	 *
	 * @since 2.2.4-dev
	 *
	 * @param string $value       The value to be displayed.
	 * @param array  $entry       The current entry.
	 * @param string $column_name The current column name.
	 *
	 * @return string
	 */
	public function filter_field_value( $value, $entry, $column_name ) {
		$form_id = $entry['form_id'];

		/**
		 * Allows the field value to be filtered in the status table.
		 *
		 * @since 1.7.1
		 *
		 * @param string $value       The value to be displayed.
		 * @param int    $form_id     The Form ID.
		 * @param string $column_name The column name.
		 * @param array  $item        The entry array.
		 */
		return apply_filters( 'gravityflow_field_value_status_table', $value, $form_id, $column_name, $entry );
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 1.0
	 * @access protected
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$bulk_actions = $this->bulk_actions;

		return $bulk_actions;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since 1.3.3
	 * @access protected
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'id'                    => array( 'id', false ),
			'created_by'            => array( 'created_by', false ),
			'workflow_final_status' => array( 'workflow_final_status', false ),
			'date_created'          => array( 'date_created', false ),
		);

		if ( $this->last_updated ) {
			$sortable_columns['workflow_timestamp'] = array( 'workflow_timestamp', false );
		}

		$args = $this->get_filter_args();

		if ( ! empty( $args['form-id'] ) && ! empty( $this->field_ids ) ) {
			$form = $this->get_form( $args['form-id'] );

			foreach ( $this->field_ids as $field_id ) {
				$field_id = trim( $field_id );
				$field    = GFFormsModel::get_field( $form, $field_id );
				if ( is_object( $field ) && in_array( $field->get_input_type(), array( 'workflow_user', 'workflow_assignee_select', 'workflow_role' ) ) ) {
					continue;
				}
				$sortable_columns[ $field_id ] = array( $field_id, false );
			}
		}

		return $sortable_columns;
	}

	/**
	 * Get the columns to be displayed in the table.
	 *
	 * @return array
	 */
	public function get_columns() {

		$args = $this->get_filter_args();

		$columns['cb'] = esc_html__( 'Checkbox', 'gravityflow' );
		if ( $this->id_column ) {
			$columns['id'] = esc_html__( 'ID', 'gravityflow' );
		}
		$columns['date_created'] = esc_html__( 'Date', 'gravityflow' );
		if ( ! isset( $args['form-id'] ) ) {
			$columns['form_id'] = esc_html__( 'Form', 'gravityflow' );
		}
		if ( $this->submitter_column ) {
			$columns['created_by'] = esc_html__( 'Submitter', 'gravityflow' );
		}

		if ( $this->step_column ) {
			$columns['workflow_step'] = esc_html__( 'Step', 'gravityflow' );
		}

		if ( $this->status_column ) {
			$columns['workflow_final_status'] = esc_html__( 'Status', 'gravityflow' );
		}

		$columns = Gravity_Flow_Common::get_field_columns( $columns, rgar( $args, 'form-id' ), $this->field_ids );

		if ( $step_id = $this->get_filter_step_id() ) {
			unset( $columns['workflow_step'] );
			$step      = gravity_flow()->get_step( $step_id );
			$assignees = $step->get_assignees();
			foreach ( $assignees as $assignee ) {
				$meta_key             = sprintf( 'workflow_%s_%s', $assignee->get_type(), $assignee->get_id() );
				$columns[ $meta_key ] = $assignee->get_display_name();
			}
		}

		if ( $this->last_updated ) {
			$columns['workflow_timestamp'] = esc_html__( 'Last Updated', 'gravityflow' );
		}

		/**
		 * Allows the columns to be filtered for the status table.
		 *
		 * @since 1.7.1
		 *
		 * @param array         $columns The columns to be filtered
		 * @param array         $args    The array of args for this status table.
		 * @param WP_List_Table $this    The current WP_List_Table object.
		 */
		$columns = apply_filters( 'gravityflow_columns_status_table', $columns, $args, $this );

		return $columns;
	}

	/**
	 * Get the step to filter by, if applicable.
	 *
	 * @return bool|int
	 */
	public function get_filter_step_id() {
		$step_id = false;
		$args    = $this->get_filter_args();
		if ( isset( $args['form-id'] ) && isset( $args['field_filters'] ) ) {
			unset( $args['field_filters']['mode'] );
			$criteria     = array( 'key' => 'workflow_step' );
			$step_filters = wp_list_filter( $args['field_filters'], $criteria );
			$step_id      = count( $step_filters ) > 0 ? $step_filters[0]['value'] : false;
		}

		return $step_id;
	}

	/**
	 * Get the filter arguments.
	 *
	 * @return array
	 */
	public function get_filter_args() {

		if ( isset( $this->_filter_args ) ) {
			return $this->_filter_args;
		}

		$args = array();

		if ( ! empty( $this->constraint_filters['form_id'] ) ) {
			$args['form-id'] = absint( $this->constraint_filters['form_id'] );
		} elseif ( ! empty( $_REQUEST['form-id'] ) ) {
			$args['form-id'] = absint( $_REQUEST['form-id'] );
		}
		$f = isset( $_REQUEST['f'] ) ? $_REQUEST['f'] : '';
		if ( ! empty( $args['form-id'] ) && $f !== '' ) {
			$form                  = $this->get_form( absint( $args['form-id'] ) );
			$field_filters         = $this->get_field_filters_from_request( $form );
			$args['field_filters'] = $field_filters;
		}

		if ( ! empty( $this->constraint_filters['start_date'] ) ) {
			$start_date         = $this->constraint_filters['start_date'];
			$start_date_gmt     = $this->prepare_start_date_gmt( $start_date );
			$args['start-date'] = $start_date_gmt;
		} elseif ( ! empty( $_REQUEST['start-date'] ) ) {
			$start_date         = urldecode( $_REQUEST['start-date'] );
			$start_date         = $this->sanitize_date( $start_date );
			$start_date_gmt     = $this->prepare_start_date_gmt( $start_date );
			$args['start-date'] = $start_date_gmt;
		}

		if ( ! empty( $this->constraint_filters['end_date'] ) ) {
			$end_date         = $this->constraint_filters['end_date'];
			$end_date_gmt     = $this->prepare_end_date_gmt( $end_date );
			$args['end-date'] = $end_date_gmt;
		} elseif ( ! empty( $_REQUEST['end-date'] ) ) {
			$end_date         = urldecode( $_REQUEST['end-date'] );
			$end_date         = $this->sanitize_date( $end_date );
			$end_date_gmt     = $this->prepare_end_date_gmt( $end_date );
			$args['end-date'] = $end_date_gmt;
		}

		if ( ! empty( $this->constraint_filters['field_filters'] ) ) {
			$constraint_field_filters = $this->constraint_filters['field_filters'];
			if ( ! empty( $constraint_field_filters ) ) {
				$filters                                  = ! empty( $args['field_filters'] ) ? $args['field_filters'] : array();
				$args['field_filters']         = array_merge( $filters, $constraint_field_filters );
			}
		}

		$this->_filter_args = $args;

		return $args;
	}

	/**
	 * Sets the filter counts.
	 */
	public function set_counts() {

		$args                  = $this->get_filter_args();
		$counts                = $this->get_counts( $args );
		$this->total_count     = $counts->total;
		$this->pending_count   = $counts->pending;
		$this->complete_count  = $counts->complete;
		$this->cancelled_count = $counts->cancelled;
	}

	/**
	 * Get the filter counts.
	 *
	 * @param array $args The filter arguments.
	 *
	 * @return stdClass|string
	 */
	public function get_counts( $args ) {

		if ( ! empty( $args['field_filters'] ) ) {
			return $this->get_field_filter_counts( $args );
		}

		$form_clause = $this->get_form_clause( $args );

		if ( is_object( $form_clause ) ) {
			return $form_clause;
		}

		global $wpdb;

		$start_clause   = $this->get_start_clause( $args );
		$end_clause     = $this->get_end_clause( $args );
		$user_id_clause = $this->get_user_id_clause();

		if ( version_compare( $this->get_gravityforms_db_version(), '2.3-dev-1', '<' ) ) {
			$lead_table = GFFormsModel::get_lead_table_name();
			$meta_table = GFFormsModel::get_lead_meta_table_name();

			$sql     = "SELECT
		(SELECT count(distinct(l.id)) FROM $lead_table l WHERE l.status='active' $form_clause $start_clause $end_clause $user_id_clause) as total,
		(SELECT count(distinct(l.id)) FROM $lead_table l INNER JOIN  $meta_table m ON l.id = m.lead_id WHERE l.status='active' AND meta_key='workflow_final_status' AND meta_value='pending' $form_clause $start_clause $end_clause $user_id_clause) as pending,
		(SELECT count(distinct(l.id)) FROM $lead_table l INNER JOIN  $meta_table m ON l.id = m.lead_id WHERE l.status='active' AND meta_key='workflow_final_status' AND meta_value NOT IN('pending', 'cancelled') $form_clause $start_clause $end_clause $user_id_clause) as complete,
		(SELECT count(distinct(l.id)) FROM $lead_table l INNER JOIN  $meta_table m ON l.id = m.lead_id WHERE l.status='active' AND meta_key='workflow_final_status' AND meta_value='cancelled' $form_clause $start_clause $end_clause $user_id_clause) as cancelled
		";
		} else {
			$entry_table = GFFormsModel::get_entry_table_name();
			$meta_table = GFFormsModel::get_entry_meta_table_name();

			$sql     = "SELECT
		(SELECT count(distinct(l.id)) FROM $entry_table l WHERE l.status='active' $form_clause $start_clause $end_clause $user_id_clause) as total,
		(SELECT count(distinct(l.id)) FROM $entry_table l INNER JOIN  $meta_table m ON l.id = m.entry_id WHERE l.status='active' AND meta_key='workflow_final_status' AND meta_value='pending' $form_clause $start_clause $end_clause $user_id_clause) as pending,
		(SELECT count(distinct(l.id)) FROM $entry_table l INNER JOIN  $meta_table m ON l.id = m.entry_id WHERE l.status='active' AND meta_key='workflow_final_status' AND meta_value NOT IN('pending', 'cancelled') $form_clause $start_clause $end_clause $user_id_clause) as complete,
		(SELECT count(distinct(l.id)) FROM $entry_table l INNER JOIN  $meta_table m ON l.id = m.entry_id WHERE l.status='active' AND meta_key='workflow_final_status' AND meta_value='cancelled' $form_clause $start_clause $end_clause $user_id_clause) as cancelled
		";
		}

		$results = $wpdb->get_results( $sql );

		return $results[0];
	}

	/**
	 * Get the status counts based on the field filters.
	 *
	 * @param array $args The status page arguments.
	 *
	 * @return stdClass
	 */
	public function get_field_filter_counts( $args ) {
		if ( isset( $args['form-id'] ) ) {
			$form_ids = absint( $args['form-id'] );
		} else {
			$form_ids = $this->get_workflow_form_ids();
		}

		$results            = new stdClass();
		$results->total     = 0;
		$results->pending   = 0;
		$results->complete  = 0;
		$results->cancelled = 0;

		if ( empty( $form_ids ) ) {
			$this->items = array();

			return $results;
		}

		$base_search_criteria = $pending_search_criteria = $complete_search_criteria = $cancelled_search_criteria = $this->get_search_criteria();

		$pending_search_criteria['field_filters'][]  = array(
			'key'   => 'workflow_final_status',
			'value' => 'pending',
		);
		$complete_search_criteria['field_filters'][] = array(
			'key'      => 'workflow_final_status',
			'operator' => 'not in',
			'value'    => array( 'pending', 'cancelled' ),
		);
		$cancelled_search_criteria['field_filters'][] = array(
			'key'   => 'workflow_final_status',
			'value' => 'cancelled',
		);

		$results->total     = GFAPI::count_entries( $form_ids, $base_search_criteria );
		$results->pending   = GFAPI::count_entries( $form_ids, $pending_search_criteria );
		$results->complete  = GFAPI::count_entries( $form_ids, $complete_search_criteria );
		$results->cancelled = GFAPI::count_entries( $form_ids, $cancelled_search_criteria );

		return $results;
	}

	/**
	 * Prepare the form part of the where clause or a basic results object if there are no forms to query.
	 *
	 * @param array $args The status page arguments.
	 *
	 * @return stdClass|string
	 */
	public function get_form_clause( $args ) {
		if ( ! empty( $args['form-id'] ) ) {
			$form_clause = ' AND l.form_id=' . absint( $args['form-id'] );
		} else {
			$form_ids = $this->get_workflow_form_ids();

			if ( empty( $form_ids ) ) {
				$results            = new stdClass();
				$results->total     = 0;
				$results->pending   = 0;
				$results->complete  = 0;
				$results->cancelled = 0;

				return $results;
			}
			$form_clause = ' AND l.form_id IN(' . join( ',', $form_ids ) . ')';
		}

		return $form_clause;
	}

	/**
	 * If a start-date was specified in the page arguments prepare that part of the where clause.
	 *
	 * @param array $args The status page arguments.
	 *
	 * @return string
	 */
	public function get_start_clause( $args ) {
		$start_clause = '';

		if ( ! empty( $args['start-date'] ) ) {
			global $wpdb;
			$start_clause = $wpdb->prepare( ' AND l.date_created >= %s', $args['start-date'] );
		}

		return $start_clause;
	}

	/**
	 * If an end-date was specified in the page arguments prepare that part of the where clause.
	 *
	 * @param array $args The status page arguments.
	 *
	 * @return string
	 */
	public function get_end_clause( $args ) {
		$end_clause = '';

		if ( ! empty( $args['end-date'] ) ) {
			global $wpdb;
			$end_clause = $wpdb->prepare( ' AND l.date_created <= %s', $args['end-date'] );
		}

		return $end_clause;
	}

	/**
	 * If the page is not configured to display entries for all users prepare the created_by part of the where clause.
	 *
	 * @return string
	 */
	public function get_user_id_clause() {
		$user_id_clause = '';

		if ( ! $this->display_all ) {
			global $wpdb;
			$user_id_clause = $wpdb->prepare( ' AND created_by=%d', get_current_user_id() );
		}

		return $user_id_clause;
	}

	/**
	 * Format the start date to be used in the entry search.
	 *
	 * @param string $start_date The submitted date.
	 *
	 * @return string
	 */
	public function prepare_start_date_gmt( $start_date ) {

		try {
			$start_date = new DateTime( $start_date );
		} catch ( Exception $e ) {
			return '';
		}

		$start_date_str = $start_date->format( 'Y-m-d H:i:s' );
		$start_date_gmt = get_gmt_from_date( $start_date_str );

		return $start_date_gmt;
	}

	/**
	 * Format the end date to be used in the entry search.
	 *
	 * @param string $end_date The submitted date.
	 *
	 * @return string
	 */
	public function prepare_end_date_gmt( $end_date ) {

		try {
			$end_date = new DateTime( $end_date );
		} catch ( Exception $e ) {
			return '';
		}

		$end_datetime_str = $end_date->format( 'Y-m-d H:i:s' );
		$end_date_str     = $end_date->format( 'Y-m-d' );

		// Extend end date till the end of the day unless a time was specified. 00:00:00 is ignored.
		if ( $end_datetime_str == $end_date_str . ' 00:00:00' ) {
			$end_date = $end_date->format( 'Y-m-d' ) . ' 23:59:59';
		} else {
			$end_date = $end_date->format( 'Y-m-d H:i:s' );
		}
		$end_date_gmt = get_gmt_from_date( $end_date );

		return $end_date_gmt;
	}

	/**
	 * Get an array of form IDs which have workflows.
	 *
	 * @return array
	 */
	public function get_workflow_form_ids() {
		return gravity_flow()->get_workflow_form_ids();
	}

	/**
	 * Output the columns for a single row.
	 *
	 * @param array $item The entry.
	 */
	protected function single_row_columns( $item ) {
		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class='$column_name column-$column_name'";

			$style = '';
			if ( in_array( $column_name, $hidden ) ) {
				$style = ' style="display:none;"';
			}

			$data_label = ( ! empty( $column_display_name ) ) ? " data-label='$column_display_name'" : '';

			$attributes = "$class$style$data_label";

			if ( 'cb' == $column_name ) {
				echo '<th data-label="' . esc_html__( 'Select', 'gravityflow' ) . '" scope="row" class="check-column">';
				echo $this->column_cb( $item );
				echo '</th>';
			} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo "<td $attributes>";
				echo call_user_func( array( $this, 'column_' . $column_name ), $item );
				echo '</td>';
			} else {
				echo "<td $attributes>";
				echo $this->column_default( $item, $column_name );
				echo '</td>';
			}
		}
	}

	/**
	 * Gets the entries to be included in the table.
	 */
	public function prepare_items() {

		$filter_args = $this->get_filter_args();

		if ( isset( $filter_args['form-id'] ) ) {
			$form_ids = absint( $filter_args['form-id'] );
			$this->apply_entry_meta( $form_ids );
		} else {
			$form_ids = $this->get_workflow_form_ids();

			if ( empty( $form_ids ) ) {
				$this->items = array();

				return;
			}
		}

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$search_criteria = $this->get_search_criteria();

		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'date_created';

		$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc';

		$user = get_current_user_id();
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen ) {
				$option = $screen->get_option( 'per_page', 'option' );
			}
		}

		$per_page_setting = ! empty( $option ) ? get_user_meta( $user, $option, true ) : false;
		$per_page         = empty( $per_page_setting ) ? $this->per_page : $per_page_setting;

		$page_size    = $per_page;
		$current_page = $this->get_pagenum();
		$offset       = $page_size * ( $current_page - 1 );

		$paging = array( 'page_size' => $page_size, 'offset' => $offset );

		$total_count = 0;

		$sorting = array( 'key' => $orderby, 'direction' => $order );

		/**
		 * Allows form id(s) to be adjusted to define which forms' entries are displayed in status table.
		 * 
		 * Return an array of form ids for use with GFAPI.
		 *
		 * @since 2.2.2-dev
		 * 
		 * @param array   $form_ids       The form ids
		 * @param array   $search_criteria The search criteria
		 */
		$form_ids = apply_filters( 'gravityflow_form_ids_status', $form_ids, $search_criteria );

		gravity_flow()->log_debug( __METHOD__ . '(): search criteria: ' . print_r( $search_criteria, true ) );
		timer_start();
		$entries = GFAPI::get_entries( $form_ids, $search_criteria, $sorting, $paging, $total_count );
		gravity_flow()->log_debug( __METHOD__ . '(): duration of get_entries: ' . timer_stop() );
		gravity_flow()->log_debug( __METHOD__ . '(): count entries: ' . count( $entries ) );
		gravity_flow()->log_debug( __METHOD__ . '(): total count: ' . $total_count );

		$this->pagination_args = array(
			'total_items' => $total_count,
			'per_page'    => $page_size,
		);

		$this->set_pagination_args( $this->pagination_args );

		$this->items = $entries;
	}

	/**
	 * Get the search criteria array for use with the GFAPI.
	 *
	 * @return array
	 */
	public function get_search_criteria() {
		$filter_args = $this->get_filter_args();

		global $current_user;
		$search_criteria['status'] = 'active';

		if ( ! empty( $filter_args['start-date'] ) ) {
			$search_criteria['start_date'] = $filter_args['start-date'];
		}
		if ( ! empty( $filter_args['end-date'] ) ) {
			$search_criteria['end_date'] = $filter_args['end-date'];
		}

		if ( ! empty( $_REQUEST['entry-id'] ) ) {
			$search_criteria['field_filters'][] = array(
				'key'   => 'id',
				'value' => absint( $_REQUEST['entry-id'] ),
			);
		}

		if ( ! empty( $_REQUEST['status'] ) ) {
			if ( $_REQUEST['status'] == 'complete' ) {
				$search_criteria['field_filters'][] = array(
					'key'      => 'workflow_final_status',
					'operator' => 'not in',
					'value'    => array( 'pending', 'cancelled' ),
				);
			} else {
				$search_criteria['field_filters'][] = array(
					'key'   => 'workflow_final_status',
					'value' => sanitize_text_field( $_REQUEST['status'] ),
				);
			}
		}

		if ( ! $this->display_all ) {
			$search_criteria['field_filters'][] = array(
				'key'   => 'created_by',
				'value' => $current_user->ID,
			);
		}

		if ( ! empty( $filter_args['field_filters'] ) ) {
			$filters                                  = ! empty( $search_criteria['field_filters'] ) ? $search_criteria['field_filters'] : array();
			$search_criteria['field_filters']         = array_merge( $filters, $filter_args['field_filters'] );
			$search_criteria['field_filters']['mode'] = 'all';
		}

		return $search_criteria;
	}

	/**
	 * Get an array of submitted field filters.
	 *
	 * @param array $form The current form.
	 *
	 * @return array
	 */
	public function get_field_filters_from_request( $form ) {
		$field_filters = array();
		$filter_fields = isset( $_REQUEST['f'] ) ? $_REQUEST['f'] : '';
		if ( is_array( $filter_fields ) && $filter_fields[0] !== '' ) {
			$filter_operators = $_REQUEST['o'];
			$filter_values    = $_REQUEST['v'];
			for ( $i = 0; $i < count( $filter_fields ); $i ++ ) {
				$field_filter = array();
				$key          = $filter_fields[ $i ];
				if ( 'entry_id' == $key ) {
					$key = 'id';
				}
				$operator       = $filter_operators[ $i ];
				$val            = $filter_values[ $i ];
				$strpos_row_key = strpos( $key, '|' );
				if ( $strpos_row_key !== false ) { // Multi-row likert.
					$key_array = explode( '|', $key );
					$key       = $key_array[0];
					$val       = $key_array[1] . ':' . $val;
				}
				$field_filter['key'] = $key;

				$field = GFFormsModel::get_field( $form, $key );
				if ( $field ) {
					$input_type = GFFormsModel::get_input_type( $field );
					if ( $field->type == 'product' && in_array( $input_type, array( 'radio', 'select' ) ) ) {
						$operator = 'contains';
					}
				}

				$field_filter['operator'] = $operator;
				$field_filter['value']    = $val;
				$field_filters[]          = $field_filter;
			}
		}
		$field_filters['mode'] = isset( $_REQUEST['mode'] ) ? $_REQUEST['mode'] : '';

		return $field_filters;
	}

	/**
	 * Get the steps for the specified form.
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return Gravity_Flow_Step[]
	 */
	public function get_steps( $form_id ) {
		if ( ! isset( $this->_steps ) ) {
			$this->_steps = gravity_flow()->get_steps( $form_id );

		}

		return $this->_steps;
	}

	/**
	 * Add the assignee status and timestamp of each step to the entry meta.
	 *
	 * @param int $form_id The form ID.
	 */
	public function apply_entry_meta( $form_id ) {
		global $_entry_meta;

		$_entry_meta[ $form_id ] = apply_filters( 'gform_entry_meta', array(), $form_id );

		$steps = $this->get_steps( $form_id );

		$entry_meta = array();

		foreach ( $steps as $step ) {
			$assignees = $step->get_assignees();
			foreach ( $assignees as $assignee ) {
				$meta_key                               = sprintf( 'workflow_%s_%s', $assignee->get_type(), $assignee->get_id() );
				$entry_meta[ $meta_key ]                = array(
					'label'             => __( 'Status:', 'gravityflow' ) . ' ' . $assignee->get_id(),
					'is_numeric'        => false,
					'is_default_column' => false,
				);
				$entry_meta[ $meta_key . '_timestamp' ] = array(
					'label'             => __( 'Status:', 'gravityflow' ) . ' ' . $assignee->get_id(),
					'is_numeric'        => false,
					'is_default_column' => false,
				);
			}
		}

		$_entry_meta[ $form_id ] = array_merge( $_entry_meta[ $form_id ], $entry_meta );
	}

	/**
	 * Format the duration for output.
	 *
	 * @param int $seconds The duration in seconds.
	 *
	 * @return string
	 */
	public function format_duration( $seconds ) {
		return gravity_flow()->format_duration( $seconds );
	}

	/**
	 * Prepare the column headers to be included in the export.
	 *
	 * @param bool $echo Indicates if the content should be echoed.
	 *
	 * @return string
	 */
	public function export_column_names( $echo = true ) {
		$columns    = $this->get_columns();

		if ( isset( $columns['workflow_final_status'] ) ) {
			$final_status_offset = array_search( 'workflow_final_status', array_keys( $columns ) ) + 1;
			$columns = array_slice( $columns, 0, $final_status_offset, true ) + array( 'duration' => esc_html__( 'Duration', 'gravityflow' ) ) + array_slice( $columns, $final_status_offset, NULL, true );
		}

		$export_arr = array();

		foreach ( $columns as $key => $column_title ) {
			if ( $key == 'cb' ) {
				continue;
			}
			$export_arr[] = '"' . $column_title . '"';
		}

		return join( ',', $export_arr ) . "\r\n";
	}

	/**
	 * Process the selected bulk action.
	 */
	public function process_bulk_action() {

		$bulk_action = $this->current_action();

		if ( empty( $bulk_action ) ) {
			return;
		}

		if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

			$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			$nonce_action = 'bulk-' . $this->_args['plural'];

			if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
				wp_die();
			}
		}

		$entry_ids = rgpost( 'entry_ids' );
		if ( empty( $entry_ids ) || ! is_array( $entry_ids ) ) {
			return;
		}

		$entry_ids = wp_parse_id_list( $entry_ids );

		$feedback = '';

		/**
		 * Allows custom bulk actions to be processed in the status table.
		 *
		 * Return a string for a standard admin message. Return an instance of WP_Error to display an error.
		 *
		 * @param string|WP_Error $feedback    The admin message.
		 * @param string          $bulk_action The action.
		 * @param array           $entry_ids   The entry IDs to be processed.
		 * @param array           $this        ->args The args for this table.
		 */
		$feedback = apply_filters( 'gravityflow_bulk_action_status_table', $feedback, $bulk_action, $entry_ids, $this->args );

		if ( ! empty( $feedback ) ) {
			if ( is_wp_error( $feedback ) ) {
				$this->display_message( $feedback->get_error_message(), true );
			} else {
				$this->display_message( $feedback );
			}
			return;
		}

		if ( $bulk_action !== 'restart_workflow' ) {
			return;
		}

		$forms = array();
		foreach ( $entry_ids  as $entry_id ) {
			$entry = GFAPI::get_entry( $entry_id );
			$form_id = absint( $entry['form_id'] );
			if ( ! isset( $forms[ $form_id ] ) ) {
				$forms[ $form_id ] = $this->get_form( $form_id );
			}
			$form = $forms[ $form_id ];
			$current_step = gravity_flow()->get_current_step( $form, $entry );
			if ( $current_step ) {
				$assignees = $current_step->get_assignees();
				foreach ( $assignees as $assignee ) {
					$assignee->remove();
				}
			}
			$feedback = esc_html__( 'Workflow restarted.',  'gravityflow' );
			gravity_flow()->add_timeline_note( $entry_id, $feedback );
			gform_update_meta( $entry_id, 'workflow_final_status', 'pending' );
			gform_update_meta( $entry_id, 'workflow_step', false );
			gravity_flow()->log_event( 'workflow', 'restarted', $form_id, $entry_id );
			gravity_flow()->process_workflow( $form, $entry_id );

		}

		$message = esc_html__( 'Workflows restarted.',  'gravityflow' );
		$this->display_message( $message );

		return;
	}

	/**
	 * Displays an error or updated type message.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param string $message  The message to be displayed.
	 * @param bool   $is_error Is this an error message? Default false.
	 */
	public function display_message( $message, $is_error = false ) {
		$class = $is_error ? 'error' : 'updated';

		echo '<div class="' . $class . ' below-h2"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
	}

	/**
	 * Prepare the data to be included in the export.
	 *
	 * @return string
	 */
	public function export() {

		$export      = '';
		$rows        = array();
		$columns     = $this->get_columns();
		if ( isset( $columns['workflow_final_status'] ) ) {
			$final_status_offset = array_search( 'workflow_final_status', array_keys( $columns ) ) + 1;
			$columns = array_slice( $columns, 0, $final_status_offset, true) + array( 'duration' => esc_html__( 'Duration', 'gravityflow' ) ) + array_slice( $columns, $final_status_offset, NULL, true );
		}
		$column_keys = array_keys( $columns );

		if ( ( $cb = array_search( 'cb', $column_keys ) ) !== false ) {
			unset( $column_keys[ $cb ] );
		}

		foreach ( $this->items as $item ) {
			$row_values = array();
			foreach ( $column_keys as $column_key ) {
				$col_val = null;
				if ( array_key_exists( $column_key, $item ) ) {
					switch ( $column_key ) {
						case 'form_id':
							$form_id = rgar( $item, 'form_id' );
							$form = $this->get_form( $form_id );
							$col_val = $form['title'];
							break;
						case 'created_by':
							$user_id = $item['created_by'];
							if ( $user_id ) {
								$user         = get_user_by( 'id', $user_id );
								if ( empty( $user ) || is_wp_error( $user ) ) {
									$col_val = $user_id . ' ' . esc_html__( '(deleted)', 'gravityflow' );
								} else {
									$col_val = $user->display_name;
								}
							} else {
								$col_val = $item['ip'];
							}
							break;
						case 'workflow_step':
							$step_id = rgar( $item, 'workflow_step' );
							if ( $step_id > 0 ) {
								$step      = gravity_flow()->get_step( $step_id );
								$col_val = $step ? $step->get_name() : $step_id;
							} else {
								$col_val = $step_id;
							}
							break;
						default:
							$col_val = $item[ $column_key ];
					}
				} else {
					switch ( $column_key ) {
						case 'duration':
							if ( $item['workflow_final_status'] == 'pending' ) {
								$duration     = time() - strtotime( $item['date_created'] );
								$duration_str = $this->format_duration( $duration );
								$col_val = $duration_str;
							} else {
								$col_val = '';
							}
							break;
					}
				}

				/**
				 * Allows the field value to be filtered in the status table.
				 *
				 * @since 1.7.1
				 *
				 * @param string $label The value to be displayed
				 * @param int $item['form_id'] The Form ID
				 * @param string 'id'
				 * @param array $item The entry array.
				 */
				$col_val = apply_filters( 'gravityflow_field_value_status_table', $col_val, $item['form_id'], $column_key, $item );

				if ( null !== $col_val ) {
					$row_values[] = '"' . addslashes( $col_val ) . '"';
				}
			}
			$rows[] = join( ',', $row_values );
		}

		$export .= join( "\r\n", $rows );

		return $export . "\r\n";
	}

	/**
	 * Removes all characters except numbers and hyphens.
	 *
	 * @param string $unsafe_date The date to be sanitized.
	 *
	 * @return string
	 */
	public function sanitize_date( $unsafe_date ) {
		$safe_date = preg_replace( '([^0-9-])', '', $unsafe_date );

		return (string) $safe_date;
	}

	/**
	 * Get the specified form.
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return array
	 */
	private function get_form( $form_id ) {
		if ( isset( $this->_forms[ $form_id ] ) ) {
			return $this->_forms[ $form_id ];
		}

		$this->_forms[ $form_id ] = GFAPI::get_form( $form_id );

		return $this->_forms[ $form_id ];
	}

	/**
	 * Get the Gravity Forms database version number.
	 *
	 * @return string
	 */
	private function get_gravityforms_db_version() {
		return Gravity_Flow_Common::get_gravityforms_db_version();
	}
}
