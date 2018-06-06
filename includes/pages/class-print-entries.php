<?php
/**
 * Gravity Flow Print Entries
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Print_Entries
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Print_Entries
 */
class Gravity_Flow_Print_Entries {

	/**
	 * Output the entries to be printed.
	 */
	public static function render() {

		$entry_ids = self::get_entry_ids();

		if ( empty( $entry_ids ) ) {
			die( esc_html__( 'Form ID and Lead ID are required parameters.', 'gravityflow' ) );
		}

		self::get_header( $entry_ids );

		?>
		<body<?php echo ( is_rtl() ) ? ' class="rtl"' : ''; ?>>

		<div id="view-container">
			<?php

			$page_break = rgget( 'page_break' ) ? 'print-page-break' : false;

			require_once( GFCommon::get_base_path() . '/entry_detail.php' );

			foreach ( $entry_ids as $entry_id ) {

				$entry = RGFormsModel::get_lead( $entry_id );
				$form  = GFAPI::get_form( $entry['form_id'] );

				do_action( 'gravityflow_print_entry_header', $form, $entry );

				// Separate each entry inside a form element so radio buttons don't get treated as a single group across multiple entries.
				echo '<form>';
				$gravity_flow = gravity_flow();
				$current_step = $gravity_flow->get_current_step( $form, $entry );

				// Check view permissions.
				$entry = GFAPI::get_entry( $entry_id );

				require_once( $gravity_flow->get_base_path() . '/includes/pages/class-entry-detail.php' );

				$permission_granted = Gravity_Flow_Entry_Detail::is_permission_granted( $entry, $form, $current_step );

				/**
				 * Allows the the permission check to be overridden for the workflow entry detail page.
				 *
				 * @param bool              $permission_granted Whether permission is granted to open the entry.
				 * @param array             $entry              The current entry.
				 * @param array             $form               The form for the current entry.
				 * @param Gravity_Flow_Step $current_step       The current step.
				 */
				$permission_granted = apply_filters( 'gravityflow_permission_granted_entry_detail', $permission_granted, $entry, $form, $current_step );

				if ( ! $permission_granted ) {
					esc_attr_e( "You don't have permission to view this entry.", 'gravityflow' );
					continue;
				}

				Gravity_Flow_Entry_Detail::entry_detail_grid( $form, $entry, false, array(), $current_step );

				echo '</form>';

				if ( rgget( 'timelines' ) ) {
					Gravity_Flow_Entry_Detail::timeline( $entry, $form );
				}

				// Output entry divider/page break.
				if ( array_search( $entry_id, $entry_ids ) < count( $entry_ids ) - 1 ) {
					echo '<div class="print-hr ' . $page_break . '"></div>';
				}

				do_action( 'gravityflow_print_entry_footer', $form, $entry );
			}

			?>
		</div>
		</body>
		</html>
		<?php
	}

	/**
	 * Get an array of entry IDs to be printed.
	 *
	 * @return array
	 */
	public static function get_entry_ids() {
		$entries = rgget( 'lid' );
		if ( 0 == $entries ) {
			// Get all the entry ids for the current filter/search.
			$form_id         = 0;
			$search_criteria = self::get_search_criteria();
			$entry_ids       = GFFormsModel::search_lead_ids( $form_id, $search_criteria );
		} else {
			$entry_ids = explode( ',', $entries );
		}

		// Sort lead IDs numerically.
		sort( $entry_ids );

		return $entry_ids;
	}

	/**
	 * Retrieve the search criteria to use when getting the entry ids.
	 *
	 * @return array
	 */
	public static function get_search_criteria() {
		$search_criteria           = array();
		$filter                    = rgget( 'filter' );
		$star                      = $filter == 'star' ? 1 : null;
		$read                      = $filter == 'unread' ? 0 : null;
		$status                    = in_array( $filter, array( 'trash', 'spam' ) ) ? $filter : 'active';
		$search_criteria['status'] = $status;

		if ( $star !== null ) {
			$search_criteria['field_filters'][] = array( 'key' => 'is_starred', 'value' => (bool) $star );
		}
		if ( ! is_null( $read ) ) {
			$search_criteria['field_filters'][] = array( 'key' => 'is_read', 'value' => (bool) $read );
		}

		$search_field_id = rgget( 'field_id' );
		if ( isset( $_GET['field_id'] ) && $_GET['field_id'] !== '' ) {
			$key            = $search_field_id;
			$val            = rgget( 's' );
			$strpos_row_key = strpos( $search_field_id, '|' );
			if ( $strpos_row_key !== false ) { // Multi-row.
				$key_array = explode( '|', $search_field_id );
				$key       = $key_array[0];
				$val       = $key_array[1] . ':' . $val;
			}
			$search_criteria['field_filters'][] = array(
				'key'      => $key,
				'operator' => rgempty( 'operator', $_GET ) ? 'is' : rgget( 'operator' ),
				'value'    => $val,
			);
		}

		return $search_criteria;
	}

	/**
	 * Output the print header.
	 *
	 * @param array $entry_ids The IDs of the entries to be included in this printout.
	 */
	public static function get_header( $entry_ids ) {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		?>

		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"<?php echo ( is_rtl() ) ? ' dir="rtl"' : ''; ?>>
		<head>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
			<meta name="keywords" content=""/>
			<meta name="description" content=""/>
			<meta name="MSSmartTagsPreventParsing" content="true"/>
			<meta name="Robots" content="noindex, nofollow"/>
			<meta http-equiv="Imagetoolbar" content="No"/>
			<title>
				<?php
				$entry_count = count( $entry_ids );
				$title       = $entry_count > 1 ? esc_html__( 'Bulk Print', 'gravityflow' ) : esc_html__( 'Entry # ', 'gravityflow' ) . absint( $entry_ids[0] );
				$title       = apply_filters( 'gravityflow_page_title_print_entry', $title, $entry_count );
				echo esc_html( $title );
				?>
			</title>
			<link rel='stylesheet' href='<?php echo GFCommon::get_base_url() ?>/css/print<?php echo $min; ?>.css' type='text/css'/>
			<link rel='stylesheet' href='<?php echo gravity_flow()->get_base_url() ?>/css/entry-detail<?php echo $min; ?>.css' type='text/css'/>
			<link rel='stylesheet' href='<?php echo gravity_flow()->get_base_url() ?>/css/discussion-field<?php echo $min; ?>.css' type='text/css'/>
			<?php
			$styles = apply_filters( 'gravityflow_print_styles', false );
			if ( ! empty( $styles ) ) {
				wp_print_styles( $styles );
			}

			?>
		</head>
		<?php
	}

	/**
	 * Returns the status for the current user.
	 *
	 * @param Gravity_Flow_Step $current_step The current step for the entry being processed.
	 *
	 * @return bool|mixed
	 */
	public static function get_user_status( $current_step ) {
		$user_status = false;
		if ( $current_step ) {
			$user_status = $current_step->get_user_status();
			gravity_flow()->log_debug( __METHOD__ . '() - user status = ' . $user_status );

			if ( ! $user_status ) {
				$user_roles = gravity_flow()->get_user_roles();
				foreach ( $user_roles as $user_role ) {
					$user_status = $current_step->get_role_status( $user_role );
					if ( $user_status ) {
						break;
					}
				}
			}
		}

		return $user_status;
	}
}
