<?php
/**
 * Gravity Flow Activity List
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Activity_List
 *
 * @since 1.0
 */
class Gravity_Flow_Activity_List {

	/**
	 * Displays the activity list.
	 *
	 * @param array $args The page arguments.
	 */
	public static function display( $args ) {

		$defaults = array(
			'check_permissions' => true,
			'detail_base_url' => admin_url( 'admin.php?page=gravityflow-inbox&view=entry' ),
		);

		$args = array_merge( $defaults, $args );

		if ( $args['check_permissions'] && ! GFAPI::current_user_can_any( 'gravityflow_activity' ) ) {
			esc_html_e( "You don't have permission to view this page", 'gravityflow' );
			return;
		}

		/**
		*
		* @since 2.0.2
		*
		* Allows the limit for events to be modified before events are displayed on the activity page.
		*
		* @param int $limit The limit of events.
		*/
		$limit = (int) apply_filters( 'gravityflow_event_limit_activity_page', 400 );

		$events = Gravity_Flow_Activity::get_events( $limit );

		if ( sizeof( $events ) > 0 ) {
			?>

			<table id="gravityflow-activity" class="widefat" cellspacing="0" style="border:0px;">
				<thead>
				<tr>
					<th data-label="<?php esc_html_e( 'Event ID', 'gravityflow' ); ?>"><?php esc_html_e( 'Event ID', 'gravityflow' ); ?></th>
					<th><?php esc_html_e( 'Date', 'gravityflow' ); ?></th>
					<th><?php esc_html_e( 'Form', 'gravityflow' ); ?></th>
					<th><?php esc_html_e( 'Entry ID', 'gravityflow' ); ?></th>
					<th><?php esc_html_e( 'Type', 'gravityflow' ); ?></th>
					<th><?php esc_html_e( 'Event', 'gravityflow' ); ?></th>
					<th><?php esc_html_e( 'Step', 'gravityflow' ); ?></th>
					<th><?php esc_html_e( 'Duration', 'gravityflow' ); ?></th>
				</tr>
				</thead>

				<tbody class="list:user user-list">
				<?php
				foreach ( $events as $event ) {
					$form = GFAPI::get_form( $event->form_id );
					$base_url = $args['detail_base_url'];
					$url_entry = $base_url . sprintf( '&id=%d&lid=%d', $event->form_id, $event->lead_id );
					$url_entry = esc_url_raw( $url_entry );
					$link = "<a href='%s'>%s</a>";
					?>
					<tr>
						<td data-label="<?php esc_html_e( 'ID', 'gravityflow' ); ?>">
							<?php
							echo esc_html( $event->id );
							?>
						</td>
						<td data-label="<?php esc_html_e( 'Date', 'gravityflow' ); ?>">
							<?php
							echo esc_html( GFCommon::format_date( $event->date_created ) );
							?>
						</td>
						<td data-label="<?php esc_html_e( 'Form', 'gravityflow' ); ?>">
							<?php
							printf( $link, $url_entry, $form['title'] );
							?>
						</td>
						<td data-label="<?php esc_html_e( 'Entry ID', 'gravityflow' ); ?>">
							<?php
							printf( $link, $url_entry, $event->lead_id );
							?>
						</td>
						<td data-label="<?php esc_html_e( 'Type', 'gravityflow' ); ?>">
							<?php
							echo esc_html( $event->log_object );
							?>
						</td>
						<td data-label="<?php esc_html_e( 'Event', 'gravityflow' ); ?>">
							<?php
							switch ( $event->log_object ) {
								case 'workflow' :
									echo $event->log_event;
									break;
								case 'step' :
									echo esc_html( $event->log_event );
									break;
								case 'assignee' :
									echo esc_html( $event->display_name ) . ' <i class="fa fa-arrow-right"></i> ' . esc_html( $event->log_value );
									break;
								default :
									echo esc_html( $event->log_value );
							}

							?>
						</td>
						<td data-label="<?php esc_html_e( 'Step', 'gravityflow' ); ?>">
							<?php
							if ( $event->feed_id ) {
								$step = gravity_flow()->get_step( $event->feed_id );
								if ( $step ) {
									$step_name = $step->get_name();
									echo esc_html( $step_name );
								}
							}

							?>
						</td>
						<td data-label="<?php esc_html_e( 'Event', 'gravityflow' ); ?>">
							<?php
							if ( ! empty( $event->duration ) ) {

								echo self::format_duration( $event->duration );
							}
							?>
						</td>

					</tr>
				<?php
				}
				?>
				</tbody>
			</table>

		<?php
		} else {
			?>
				<div id="gravityflow-no-activity-container">

					<div id="gravityflow-no-activity-content">
						<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
						<br /><br />
						<?php esc_html_e( 'Waiting for workflow activity', 'gravityflow' ); ?>
					</div>

				</div>
			<?php
		}
	}

	/**
	 * Formats the duration for display.
	 *
	 * @param int $seconds The event duration.
	 *
	 * @return string
	 */
	public static function format_duration( $seconds ) {
		return gravity_flow()->format_duration( $seconds );
	}
}
