<?php
/**
 * Gravity Flow Reports
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Reports
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Reports
 */
class Gravity_Flow_Reports {

	/**
	 * Display of the reports page
	 *
	 * @param array $args The reports page arguments.
	 */
	public static function display( $args ) {

		$assignee_key = sanitize_text_field( rgget( 'assignee' ) );
		list( $assignee_type, $assignee_id ) = rgexplode( '|', $assignee_key, 2 );

		$range = sanitize_key( rgget( 'range' ) );
		switch ( $range ) {
			case 'last-6-months' :
				$start_date = date( 'Y-m-d', strtotime( '-6 months' ) );
				break;
			case 'last-3-months' :
				$start_date = date( 'Y-m-d', strtotime( '-3 months' ) );
				break;
			default :
				$start_date = date( 'Y-m-d', strtotime( '-1 year' ) );
		}

		$defaults = array(
			'view'              => rgget( 'view' ),
			'form_id'           => absint( rgget( 'form-id' ) ),
			'step_id'           => absint( rgget( 'step-id' ) ),
			'category'          => sanitize_key( rgget( 'category' ) ),
			'range'             => $range,
			'start_date'        => $start_date,
			'assignee'          => $assignee_key,
			'assignee_type'     => $assignee_type,
			'assignee_id'       => $assignee_id,
			'check_permissions' => true,
			'base_url'          => admin_url( 'admin.php?page=gravityflow-reports' ),
		);

		$args = array_merge( $defaults, $args );

		if ( $args['check_permissions'] && ! GFAPI::current_user_can_any( 'gravityflow_reports' ) ) {
			esc_html_e( "You don't have permission to view this page", 'gravityflow' );
			return;
		}

		$filter_vars['config']   = self::get_filter_config_vars();
		$filter_vars['selected'] = array(
			'formId'   => $args['form_id'],
			'category' => $args['category'],
			'stepId'   => empty( $args['step_id'] ) ? '' : $args['step_id'],
			'assignee' => $args['assignee'],
		);

		?>
		<script>var gravityflowFilterVars = <?php echo json_encode( $filter_vars ); ?>;</script>

		<div id="gravityflow-reports-filter" style="margin:10px 0;">
		<form method="GET" action="<?php echo esc_url( $args['base_url'] );?>">
			<input type="hidden" value="gravityflow-reports" name="page" />
			<?php self::range_drop_down( $args['range'] ); ?>
			<?php self::form_drop_down( $args['form_id'] ); ?>
			<?php self::category_drop_down( $args['category'] ); ?>
			<select id="gravityflow-reports-steps" style="display:none;" name="step-id"></select>
			<select id="gravityflow-reports-assignees" style="display:none;" name="assignee"></select>
			<input type="submit" value="<?php esc_html_e( 'Filter', 'gravityflow' )?>" class="button-secondary" />
		</form>
		</div>
		<?php


		if ( empty( $args['form_id'] ) ) {

			self::report_all_forms( $args );

			return;
		}

		$form_id = $args['form_id'];

		if ( $args['category'] == 'assignee' ) {
			if ( empty( $args['assignee_key'] ) ) {
				self::report_form_by_assignee( $form_id, $args );
			}
		} elseif ( $args['category'] == 'step' ) {
			if ( empty( $args['step_id'] ) ) {
				self::report_form_by_step( $form_id, $args );
			} else {
				$step_id = $args['step_id'];
				if ( empty( $args['assignee_id'] ) ) {
					self::report_step_by_assignee( $step_id, $args );
				} else {
					$assignee_type = $args['assignee_type'];
					$assignee_id   = $args['assignee_id'];
					self::report_assignee_by_month( $assignee_type, $assignee_id, $args );
				}
			}
		} else {
			self::report_form_by_month( $form_id, $args );
		}

	}

	/**
	 * Output the report for all forms.
	 *
	 * @param array $args The reports page arguments.
	 */
	public static function report_all_forms( $args ) {

		$defaults = array(
			'start_date'        => date( 'Y-m-d', strtotime( '-1 year' ) ),
			'check_permissions' => true,
			'base_url'          => admin_url(),
		);

		$args = array_merge( $defaults, $args );

		$rows = Gravity_Flow_Activity::get_report_data_for_all_forms( $args['start_date'] );

		if ( empty( $rows ) ) {
			esc_html_e( 'No data to display', 'gravityflow' );
			return;
		}

		$chart_data = array();

		$chart_data[] = array( esc_html__( 'Form', 'gravityflow' ), esc_html__( 'Workflows Completed', 'gravityflow' ), esc_html__( 'Average Duration (hours)', 'gravityflow' ) );

		foreach ( $rows as $row ) {
			$form         = GFAPI::get_form( $row->form_id );
			$title        = esc_html( $form['title'] );
			$chart_data[] = array( $title, absint( $row->c ), absint( $row->av ) / HOUR_IN_SECONDS );
		}

		$chart_options = array(
			'chart'  => array(
				'title'    => esc_html__( 'Forms', 'gravityflow' ),
				'subtitle' => esc_html__( 'Workflows completed and average duration', 'gravityflow' ),
			),
			'bars'   => 'horizontal',
			'height' => 200 + count( $rows ) * 100,
			'series' => array(
				array( 'axis' => 'count' ),
				array( 'axis' => 'average_duration' ),
			),
			'axes'   => array(
				'x' => array(
					'count'            => array( 'side'  => 'top',
					                             'label' => esc_html__( 'Workflows Completed', 'gravityflow' )
					),
					'average_duration' => array( 'label' => esc_html__( 'Average Duration (hours)', 'gravityflow' ) ),
				),
			),
		);

		$data_table_json = htmlentities( json_encode( $chart_data ), ENT_QUOTES, 'UTF-8', true );
		$options_json    = htmlentities( json_encode( $chart_options ), ENT_QUOTES, 'UTF-8', true );

		echo '<div id="gravityflow_chart_top_level" style="padding:20px;background-color:white;" class="gravityflow_chart" data-type="Bar" data-table="' . $data_table_json . '" data-options="' . $options_json . '""></div>';
	}

	/**
	 * Output the report for a specific form by month.
	 *
	 * @param int   $form_id The form ID.
	 * @param array $args    The reports page arguments.
	 */
	public static function report_form_by_month( $form_id, $args ) {

		$defaults = array(
			'start_date'        => date( 'Y-m-d', strtotime( '-1 year' ) ),
			'check_permissions' => true,
			'base_url'          => admin_url(),
		);

		$args = array_merge( $defaults, $args );

		$rows = Gravity_Flow_Activity::get_report_data_for_form( $form_id, $args['start_date'] );

		if ( empty( $rows ) ) {
			esc_html_e( 'No data to display', 'gravityflow' );
			return;
		}

		$chart_data = array();

		$chart_data[] = array( esc_html__( 'Month', 'gravityflow' ), esc_html__( 'Workflows Completed', 'gravityflow' ), esc_html__( 'Average Duration (hours)', 'gravityflow' ) );
		global $wp_locale;
		foreach ( $rows as $row ) {
			$chart_data[] = array( $wp_locale->get_month( $row->month ), absint( $row->c ), absint( $row->av ) / HOUR_IN_SECONDS );
		}

		$form = GFAPI::get_form( $form_id );

		$chart_options = array(
			'chart'  => array(
				'title'    => esc_html( $form['title'] ),
				'subtitle' => esc_html__( 'Workflows completed and average duration', 'gravityflow' ),
			),
			'bars'   => 'horizontal',
			'height' => 200 + count( $rows ) * 100,
			'series' => array(
				array( 'axis' => 'count' ),
				array( 'axis' => 'average_duration' ),
			),
			'axes'   => array(
				'x' => array(
					'count'            => array( 'side'  => 'top',
					                             'label' => esc_html__( 'Workflows Completed', 'gravityflow' )
					),
					'average_duration' => array( 'label' => esc_html__( 'Average Duration (hours)', 'gravityflow' ) ),
				),
			),
		);

		$data_table_json = htmlentities( json_encode( $chart_data ), ENT_QUOTES, 'UTF-8', true );
		$options_json    = htmlentities( json_encode( $chart_options ), ENT_QUOTES, 'UTF-8', true );

		echo '<div id="gravityflow_chart_top_level" style="padding:20px;background-color:white;" class="gravityflow_chart" data-type="Bar" data-table="' . $data_table_json . '" data-options="' . $options_json . '""></div>';
	}

	/**
	 * Output the report for a specific form by step.
	 *
	 * @param int   $form_id The form ID.
	 * @param array $args    The reports page arguments.
	 */
	public static function report_form_by_step( $form_id, $args ) {

		$defaults = array(
			'start_date'        => date( 'Y-m-d', strtotime( '-1 year' ) ),
			'check_permissions' => true,
			'base_url'          => admin_url(),
		);

		$args = array_merge( $defaults, $args );

		$rows = Gravity_Flow_Activity::get_report_data_for_form_by_step( $form_id, $args['start_date'] );

		if ( empty( $rows ) ) {
			esc_html_e( 'No data to display', 'gravityflow' );
			return;
		}

		$chart_data = array();

		$chart_data[] = array( esc_html__( 'Step', 'gravityflow' ), esc_html__( 'Completed', 'gravityflow' ), esc_html__( 'Average Duration (hours)', 'gravityflow' ) );

		foreach ( $rows as $row ) {
			$step = gravity_flow()->get_step( $row->feed_id );
			if ( empty( $step ) ) {
				continue;
			}
			$name = esc_html( $step->get_name() );
			$chart_data[] = array( $name, absint( $row->c ), absint( $row->av ) / HOUR_IN_SECONDS );
		}

		$form = GFAPI::get_form( $form_id );

		$chart_options = array(
			'chart'  => array(
				'title'    => esc_html( $form['title'] ),
				'subtitle' => esc_html__( 'Step completed and average duration', 'gravityflow' ),
			),
			'bars'   => 'horizontal',
			'height' => 200 + count( $rows ) * 100,
			'series' => array(
				array( 'axis' => 'count' ),
				array( 'axis' => 'average_duration' ),
			),
			'axes'   => array(
				'x' => array(
					'count'            => array( 'side' => 'top', 'label' => esc_html__( 'Completed', 'gravityflow' ) ),
					'average_duration' => array( 'label' => esc_html__( 'Average Duration (hours)', 'gravityflow' ) ),
				),
			),
		);

		$data_table_json = htmlentities( json_encode( $chart_data ), ENT_QUOTES, 'UTF-8', true );
		$options_json    = htmlentities( json_encode( $chart_options ), ENT_QUOTES, 'UTF-8', true );

		echo '<div id="gravityflow_chart_top_level" style="padding:20px;background-color:white;" class="gravityflow_chart" data-type="Bar" data-table="' . $data_table_json . '" data-options="' . $options_json . '""></div>';
	}

	/**
	 * Output the report for a specific step by assignee.
	 *
	 * @param int   $step_id The step ID.
	 * @param array $args    The reports page arguments.
	 */
	public static function report_step_by_assignee( $step_id, $args ) {

		$defaults = array(
			'start_date'        => date( 'Y-m-d', strtotime( '-1 year' ) ),
			'check_permissions' => true,
			'base_url'          => admin_url( 'admin.php?page=gravityflow-reports' ),
		);

		$args = array_merge( $defaults, $args );

		$step = gravity_flow()->get_step( $step_id );
		if ( empty( $step ) ) {
			return;
		}

		$rows = Gravity_Flow_Activity::get_report_data_for_step_by_assignee( $step_id, $args['start_date'] );

		if ( empty( $rows ) ) {
			esc_html_e( 'No data to display', 'gravityflow' );
			return;
		}

		$chart_data = array();

		$chart_data[] = array( esc_html__( 'Assignee', 'gravityflow' ), esc_html__( 'Completed', 'gravityflow' ), esc_html__( 'Average Duration (hours)', 'gravityflow' ) );

		foreach ( $rows as $row ) {
			if ( $row->assignee_type == 'user_id' ) {
				$user = get_user_by( 'id', $row->assignee_id );
				$display_name = $user->display_name;
			} else {
				$display_name = $row->assignee_id;
			}

			$chart_data[] = array( $display_name, absint( $row->c ), absint( $row->av ) / HOUR_IN_SECONDS );
		}

		$chart_options = array(
			'chart'  => array(
				'title'    => esc_html( $step->get_name() ),
				'subtitle' => esc_html__( 'Step completed and average duration by assignee', 'gravityflow' ),
			),
			'bars'   => 'horizontal',
			'height' => 200 + count( $rows ) * 100,
			'series' => array(
				array( 'axis' => 'count' ),
				array( 'axis' => 'average_duration' ),
			),
			'axes'   => array(
				'x' => array(
					'count'            => array( 'side' => 'top', 'label' => esc_html__( 'Completed', 'gravityflow' ) ),
					'average_duration' => array( 'label' => esc_html__( 'Average Duration (hours)', 'gravityflow' ) ),
				),
			),
		);

		$data_table_json = htmlentities( json_encode( $chart_data ), ENT_QUOTES, 'UTF-8', true );
		$options_json    = htmlentities( json_encode( $chart_options ), ENT_QUOTES, 'UTF-8', true );

		echo '<div id="gravityflow_chart_top_level" style="padding:20px;background-color:white;" class="gravityflow_chart" data-type="Bar" data-table="' . $data_table_json . '" data-options="' . $options_json . '""></div>';
	}

	/**
	 * Output the report for a specific form by assignee.
	 *
	 * @param int   $form_id The form ID.
	 * @param array $args    The reports page arguments.
	 */
	public static function report_form_by_assignee( $form_id, $args ) {

		$defaults = array(
			'start_date'        => date( 'Y-m-d', strtotime( '-1 year' ) ),
			'check_permissions' => true,
			'base_url'          => admin_url( 'admin.php?page=gravityflow-reports' ),
		);

		$args = array_merge( $defaults, $args );


		$rows = Gravity_Flow_Activity::get_report_data_for_form_by_assignee( $form_id,  $args['start_date'] );

		if ( empty( $rows ) ) {
			esc_html_e( 'No data to display', 'gravityflow' );
			return;
		}

		$chart_data = array();

		$chart_data[] = array( esc_html__( 'Assignee', 'gravityflow' ), esc_html__( 'Completed', 'gravityflow' ), esc_html__( 'Average Duration (hours)', 'gravityflow' ) );

		foreach ( $rows as $row ) {
			if ( $row->assignee_type == 'user_id' ) {
				$user = get_user_by( 'id', $row->assignee_id );
				$display_name = $user->display_name;
			} else {
				$display_name = $row->assignee_id;
			}

			$chart_data[] = array( $display_name, absint( $row->c ), absint( $row->av ) / HOUR_IN_SECONDS );
		}

		$form = GFAPI::get_form( $form_id );

		$chart_options = array(
			'chart'  => array(
				'title'    => esc_html( $form['title'] ),
				'subtitle' => esc_html__( 'Step completed and average duration by assignee', 'gravityflow' ),
			),
			'bars'   => 'horizontal',
			'height' => 200 + count( $rows ) * 100,
			'series' => array(
				array( 'axis' => 'count' ),
				array( 'axis' => 'average_duration' ),
			),
			'axes'   => array(
				'x' => array(
					'count'            => array( 'side' => 'top', 'label' => esc_html__( 'Completed', 'gravityflow' ) ),
					'average_duration' => array( 'label' => esc_html__( 'Average Duration (hours)', 'gravityflow' ) ),
				),
			),
		);

		$data_table_json = htmlentities( json_encode( $chart_data ), ENT_QUOTES, 'UTF-8', true );
		$options_json    = htmlentities( json_encode( $chart_options ), ENT_QUOTES, 'UTF-8', true );

		echo '<div id="gravityflow_chart_top_level" style="padding:20px;background-color:white;" class="gravityflow_chart" data-type="Bar" data-table="' . $data_table_json . '" data-options="' . $options_json . '""></div>';
	}

	/**
	 * Output the report for a specific assignee by month.
	 *
	 * @param string $assignee_type The assignee type.
	 * @param string $assignee_id   The assignee ID.
	 * @param array  $args          The reports page arguments.
	 */
	public static function report_assignee_by_month( $assignee_type, $assignee_id, $args ) {

		$defaults = array(
			'start_date'        => date( 'Y-m-d', strtotime( '-1 year' ) ),
			'check_permissions' => true,
			'base_url'          => admin_url( 'admin.php?page=gravityflow-reports' ),
		);

		$args = array_merge( $defaults, $args );

		$rows = Gravity_Flow_Activity::get_report_data_for_assignee_by_month( $assignee_type, $assignee_id,  $args['start_date'] );

		if ( empty( $rows ) ) {
			esc_html_e( 'No data to display', 'gravityflow' );
			return;
		}

		$chart_data = array();

		$chart_data[] = array( esc_html__( 'Month', 'gravityflow' ), esc_html__( 'Workflows Completed', 'gravityflow' ), esc_html__( 'Average Duration (hours)', 'gravityflow' ) );
		global $wp_locale;
		foreach ( $rows as $row ) {
			$chart_data[] = array( $wp_locale->get_month( $row->month ) . ' ' . $row->year, absint( $row->c ), absint( $row->av ) / HOUR_IN_SECONDS );
		}

		if ( $assignee_type == 'user_id' ) {
			$user = get_user_by( 'id', $assignee_id );
			$display_name = $user->display_name;
		} else {
			$display_name = $assignee_id;
		}

		$chart_options = array(
			'chart'  => array(
				'title'    => esc_html( $display_name ),
				'subtitle' => esc_html__( 'Workflows completed and average duration by month', 'gravityflow' ),
			),
			'bars'   => 'horizontal',
			'height' => 200 + count( $rows ) * 100,
			'series' => array(
				array( 'axis' => 'count' ),
				array( 'axis' => 'average_duration' ),
			),
			'axes'   => array(
				'x' => array(
					'count'            => array( 'side'  => 'top',
					                             'label' => esc_html__( 'Workflows Completed', 'gravityflow' )
					),
					'average_duration' => array( 'label' => esc_html__( 'Average Duration (hours)', 'gravityflow' ) ),
				),
			),
		);

		$data_table_json = htmlentities( json_encode( $chart_data ), ENT_QUOTES, 'UTF-8', true );
		$options_json    = htmlentities( json_encode( $chart_options ), ENT_QUOTES, 'UTF-8', true );

		echo '<div id="gravityflow_chart_top_level" style="padding:20px;background-color:white;" class="gravityflow_chart" data-type="Bar" data-table="' . $data_table_json . '" data-options="' . $options_json . '""></div>';
	}

	/**
	 * Format the duration for output.
	 *
	 * @param int $seconds The duration in seconds.
	 *
	 * @return string
	 */
	public static function format_duration( $seconds ) {
		return gravity_flow()->format_duration( $seconds );
	}

	/**
	 * Returns the HTML for the form drop down.
	 *
	 * @param string|int $selected_value The selected form.
	 * @param bool       $echo           Indicates if the content should be echoed.
	 *
	 * @return string
	 */
	public static function form_drop_down( $selected_value, $echo = true ) {
		$m = array();

		$m[] = '<select id="gravityflow-form-drop-down" name="form-id">';
		$m[] = sprintf( '<option value="" %s>%s</option>', selected( $selected_value, '', false ) , esc_html__( 'Select A Workflow Form', 'gravityflow' ) );
		$form_ids = self::get_form_ids();
		foreach ( $form_ids as $form_id ) {
			$form = GFAPI::get_form( $form_id );
			$m[] = sprintf( '<option value="%s" %s>%s</option>', $form_id,  selected( $selected_value, $form_id, false ), $form['title'] );
		}

		$m[] = '</select>';
		$html = join( '', $m );
		if ( $echo ) {
			echo $html;
		}
		return $html;
	}

	/**
	 * Returns the HTML for the range drop down.
	 *
	 * @param string|int $selected_value The selected range.
	 * @param bool       $echo           Indicates if the content should be echoed.
	 *
	 * @return string
	 */
	public static function range_drop_down( $selected_value, $echo = true ) {
		$m   = array();
		$m[] = '<select id="gravityflow-reports-range" name="range">';
		$m[] = sprintf( '<option value="last-12-months" %s>%s</option>', selected( $selected_value, 'last-12-months', false ), esc_html__( 'Last 12 months', 'gravityflow' ) );
		$m[] = sprintf( '<option value="last-6-months" %s>%s</option>', selected( $selected_value, 'last-6-months', false ), esc_html__( 'Last 6 months', 'gravityflow' ) );
		$m[] = sprintf( '<option value="last-3-months" %s>%s</option>', selected( $selected_value, 'last-3-months', false ), esc_html__( 'Last 3 months', 'gravityflow' ) );
		$m[] = '</select>';

		$html = join( '', $m );

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Returns the HTML for the category drop down.
	 *
	 * @param string|int $selected_value The selected category.
	 * @param bool       $echo           Indicates if the content should be echoed.
	 *
	 * @return string
	 */
	public static function category_drop_down( $selected_value, $echo = true ) {
		$m = array();
		$m[] = '<select id="gravityflow-reports-category" name="category" style="display:none;">';
		$m[] = sprintf( '<option value="month" %s>%s</option>', selected( $selected_value, 'month', false ), esc_html__( 'Month', 'gravityflow' ) );
		$m[] = sprintf( '<option value="assignee" %s >%s</option>', selected( $selected_value, 'assignee', false ), esc_html__( 'Assignee', 'gravityflow' ) );
		$m[] = sprintf( '<option value="step" %s >%s</option>', selected( $selected_value, 'step', false ), esc_html__( 'Step', 'gravityflow' ) );
		$m[] = '</select>';

		$html = join( '', $m );

		if ( $echo ) {
			echo $html;
		}
		return $html;
	}

	/**
	 * Output the step filter.
	 *
	 * @param array $args The reports page arguments.
	 */
	public static function step_filter( $args ) {
		?>
		<form method="GET" action="<?php echo esc_url( $args['base_url'] );?>">
			<input type="hidden" value="step" name="view"/>
			<input type="hidden" value="gravityflow-reports" name="page" />
			<?php self::step_drop_down( $args['form_id'] ); ?>
			<input type="submit" value="<?php esc_html_e( 'Filter', 'gravityflow' )?>" />
		</form>
	<?php
	}

	/**
	 * Returns the HTML for the step drop down.
	 *
	 * @param int  $form_id The form ID.
	 * @param bool $echo    Indicates if the content should be echoed.
	 *
	 * @return string
	 */
	public static function step_drop_down( $form_id, $echo = true ) {
		$m = array();

		$m[] = '<select id="gravityflow-step-drop-down" name="step-id">';
		$m[] = sprintf( '<option value="">%s</option>', esc_html__( 'All Steps', 'gravityflow' ) );
		$steps = gravity_flow()->get_steps( $form_id );
		foreach ( $steps as $step ) {
			$m[] = sprintf( '<option value="%s">%s</option>', $step->get_id(), $step->get_name() );
		}

		$m[] = '</select>';
		$html = join( '', $m );
		if ( $echo ) {
			echo $html;
		}
		return $html;
	}

	/**
	 * Get an array of form IDs which have workflows.
	 *
	 * @return array
	 */
	public static function get_form_ids() {
		return gravity_flow()->get_workflow_form_ids();
	}

	/**
	 * Returns step and assignee properties to be used when rendering the filters.
	 *
	 * @return array
	 */
	public static function get_filter_config_vars() {
		$form_ids   = self::get_form_ids();
		$steps_vars = array();
		foreach ( $form_ids as $form_id ) {
			$steps                  = gravity_flow()->get_steps( $form_id );
			$steps_vars[ $form_id ] = array();
			foreach ( $steps as $step ) {
				$assignees     = $step->get_assignees();
				$assignee_vars = array();
				foreach ( $assignees as $assignee ) {
					$assignee_id = $assignee->get_id();
					if ( ! empty( $assignee_id ) ) {
						$assignee_vars[] = array( 'key'  => $assignee->get_key(),
						                          'name' => $assignee->get_display_name()
						);
					}
				}
				$steps_vars[ $form_id ][ $step->get_id() ] = array( 'id'        => $step->get_id(),
				                                                    'name'      => $step->get_name(),
				                                                    'assignees' => $assignee_vars
				);
			}
		}

		return $steps_vars;
	}
}
