<?php
/**
 * Integrates Gravity Flow with GravityView.
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.1-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Add custom options for workflow_detail_link fields
 *
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class Gravity_Flow_GravityView_Workflow_Detail_Link extends GravityView_Field {

	/**
	 * The name of the GravityView field type.
	 *
	 * @var string
	 */
	var $name = 'workflow_detail_link';

	/**
	 * The contexts in which a field is available.
	 *
	 * @var array
	 */
	var $contexts = array( 'multiple' );

	/**
	 * Can the field be sorted in search?
	 *
	 * @var bool
	 */
	var $is_sortable = false;

	/**
	 * Can the field be searched?
	 *
	 * @var bool
	 */
	var $is_searchable = false;

	/**
	 * The group this field belongs to.
	 *
	 * @var string
	 */
	var $group = 'meta';

	/**
	 * Gravity_Flow_GravityView_Workflow_Detail_Link constructor.
	 */
	public function __construct() {
		$this->label = esc_html__( 'Link to Workflow Entry Detail', 'gravityflow' );

		$this->add_hooks();

		parent::__construct();
	}

	/**
	 * Adds hooks for GravityView.
	 *
	 * @since 1.5.1-dev
	 */
	private function add_hooks() {
		add_filter( 'gravityview_entry_default_fields', array( $this, 'add_entry_default_field' ), 10, 3 );
		add_filter( 'gravityview_field_entry_value_workflow_detail_link', array( $this, 'modify_entry_value_workflow_detail_link' ), 10, 4 );
		add_filter( 'gravityflow_back_link_url_entry_detail', array( $this, 'modify_entry_detail_back_link' ), 10, 2 );
	}

	/**
	 * Add Entry Notes to the Add Field picker in Edit View
	 *
	 * @see   GravityView_Admin_Views::get_entry_default_fields()
	 *
	 * @since 1.17
	 *
	 * @param array  $entry_default_fields Fields configured to show in the picker.
	 * @param array  $form                 Gravity Forms form array.
	 * @param string $zone                 Current context: `directory`, `single`, `edit`.
	 *
	 * @return array Fields array with notes added, if in Multiple Entries or Single Entry context.
	 */
	public function add_entry_default_field( $entry_default_fields, $form, $zone ) {

		if ( in_array( $zone, array( 'directory', 'single' ) ) ) {
			$entry_default_fields['workflow_detail_link'] = array(
				'label' => __( 'Workflow Detail Link', 'gravityflow' ),
				'type'  => $this->name,
				'desc'  => __( 'Display a link to the workflow detail page.', 'gravityflow' ),
			);
		}

		return $entry_default_fields;
	}

	/**
	 * Generate the workflow detail link.
	 *
	 * @param string $output         HTML value output.
	 * @param array  $entry          The GF entry array.
	 * @param array  $field_settings Settings for the particular GV field.
	 * @param array  $field          Current field being displayed.
	 *
	 * @since 1.5.1-dev
	 *
	 * @return string
	 */
	function modify_entry_value_workflow_detail_link( $output, $entry, $field_settings, $field ) {

		$query_args = array(
			'page' => 'gravityflow-inbox',
			'view' => 'entry',
			'id'   => absint( $entry['form_id'] ),
			'lid'  => absint( $entry['id'] ),
		);

		$page_id = gravity_flow()->get_app_setting( 'inbox_page' );

		if ( empty( $page_id ) ) {
			$page_id = 'admin';
		}

		$url = Gravity_Flow_Common::get_workflow_url( $query_args, $page_id );

		if ( $page_id != 'admin' ) {
			global $post;
			$url .= '&gvp=' . $post->ID;
		}

		$text = $field_settings['workflow_detail_link_text'];

		$output = sprintf( '<a href="%s">%s</a>', $url, $text );

		return $output;
	}

	/**
	 * Adds the link text field option.
	 *
	 * @param array  $field_options The field properties.
	 * @param string $template_id   The template ID.
	 * @param string $field_id      The field ID.
	 * @param string $context       The current context.
	 * @param string $input_type    The field input type.
	 *
	 * @return array
	 */
	function field_options( $field_options, $template_id, $field_id, $context, $input_type ) {

		// Always a link!
		unset( $field_options['show_as_link'], $field_options['search_filter'] );

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$add_options = array();
		$add_options['workflow_detail_link_text'] = array(
			'type' => 'text',
			'label' => __( 'Link Text:', 'gravityflow' ),
			'desc' => null,
			'value' => __( 'View Details', 'gravityflow' ),
			'merge_tags' => true,
		);

		return $add_options + $field_options;
	}

	/**
	 * Customize the back link for pages which were accessed via Gravity View workflow detail link field.
	 *
	 * @param string $url       The potential back link URL.
	 * @param array  $args      The shortcode arguments for the page where the back link is to be displayed
	 *
	 * @since 2.5-dev
	 *
	 * @return string
	 */
	function modify_entry_detail_back_link( $url, $args ) {
		// Verify the back link is set as active on current page and the potential back link has querystring params
		if ( $args['back_link'] == true && false !== strpos( $url, '?' ) ) {

			// Parse the querystring to confirm the gvp param was added via the workflow detail link
			$querystring = explode( '?', $url, 2 );
			parse_str( end( $querystring ), $query_args );
			if ( isset( $query_args['gvp'] ) ) {

				// Confirm the gvp param is for a valid page and update the back link URL
				$view_page = get_permalink( $query_args['gvp'] );
				if ( $view_page !== false ) {
					$url = $view_page;
				}
			}
		}

		return $url;
	}

}

new Gravity_Flow_GravityView_Workflow_Detail_Link;
