<?php
/**
 * Gravity Flow Step Feed Sliced Invoices
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Sliced_Invoices
 * @copyright   Copyright (c) 2016-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_Sliced_Invoices extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'slicedinvoices';

	protected $_class_name = 'Sliced_Invoices_GF';
	protected $_slug = 'slicedinvoices';

	/**
	 * Enable the expiration settings.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @return bool
	 */
	public function supports_expiration() {
		return true;
	}

	public function get_label() {
		return 'Sliced Invoices';
	}

	public function get_icon_url() {
		return $this->get_base_url() . '/images/sliced-invoices-icon.svg';
	}

	/**
	 * Returns the settings for this step.
	 *
	 * @since 1.6.1-dev-2 Added the Step Completion setting.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = parent::get_settings();

		if ( ! $this->is_supported() ) {
			return $settings;
		}

		$settings_api = $this->get_common_settings_api();

		$fields = array(
			$settings_api->get_setting_assignee_type(),
			$settings_api->get_setting_assignees(),
			$settings_api->get_setting_assignee_routing(),
			$settings_api->get_setting_notification_tabs( array(
				array(
					'label'  => __( 'Assignee Email', 'gravityflow' ),
					'id'     => 'tab_assignee_notification',
					'fields' => $settings_api->get_setting_notification( array(
						'type' => 'assignee',
					) ),
				)
			) ),
			$settings_api->get_setting_instructions(),
			$settings_api->get_setting_display_fields(),
			array(
				'name'    => 'post_feed_completion',
				'type'    => 'select',
				'label'   => __( 'Step Completion', 'gravityflow' ),
				'choices' => array(
					array( 'label' => __( 'Immediately following feed processing', 'gravityflow' ), 'value' => '' ),
					array( 'label' => __( 'Delay until invoices are paid', 'gravityflow' ), 'value' => 'delayed' ),
				),
			)
		);

		$settings['fields'] = array_merge( $settings['fields'], $fields );

		return $settings;
	}

	/**
	 * Processes this step.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @return bool Is the step complete?
	 */
	public function process() {
		$complete = parent::process();
		$this->assign();

		return $complete;
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @param array $feed The feed to be processed.
	 *
	 * @return bool Returning false if the feed created an invoice to ensure the next step is not processed until after the invoice is paid.
	 */
	public function process_feed( $feed ) {
		if ( rgars( $feed, 'meta/mappedFields_line_items' ) == 'entry_order_summary' ) {
			// The sliced add-on is expecting a list field, prevent it from attempting to process the field value and setting the line items.
			$feed['meta']['mappedFields_line_items'] = '';
			$this->product_line_items_enabled        = true;
		}

		add_action( 'sliced_gravityforms_feed_processed', array( $this, 'sliced_gravityforms_feed_processed' ), 1, 3 );
		parent::process_feed( $feed );
		remove_action( 'sliced_gravityforms_feed_processed', array( $this, 'sliced_gravityforms_feed_processed' ), 1 );

		return rgars( $feed, 'meta/post_type' ) !== 'invoice' || $this->post_feed_completion !== 'delayed' ? true : false;

	}

	/**
	 * Perform any actions once the invoice/quote has been created.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @param int   $id    The invoice (post) ID.
	 * @param array $feed  The feed which created the invoice.
	 * @param array $entry The entry which created the invoice.
	 */
	public function sliced_gravityforms_feed_processed( $id, $feed, $entry ) {

		$this->maybe_set_line_items( $id, $entry );

		if ( rgars( $feed, 'meta/post_type' ) === 'invoice' ) {
			if ( $this->post_feed_completion === 'delayed' ) {
				// Store the IDs so we can complete the step once the invoice is paid.
				update_post_meta( $id, '_gform-entry-id', rgar( $entry, 'id' ) );
				update_post_meta( $id, '_gform-feed-id', rgar( $feed, 'id' ) );
				update_post_meta( $id, '_gravityflow-step-id', $this->get_id() );
				if ( function_exists( 'sliced_get_accepted_payment_methods' ) ) {
					update_post_meta( $id, '_sliced_payment_methods', array_keys( sliced_get_accepted_payment_methods() ) );
				}
			}

			$invoice_status = rgars( $feed, 'meta/invoice_status' );
			if ( $invoice_status && class_exists( 'Sliced_Invoice' ) ) {
				Sliced_Invoice::set_status( $invoice_status, $id );
			}
		} else {
			$quote_status = rgars( $feed, 'meta/quote_status' );
			if ( $quote_status && class_exists( 'Sliced_Quote' ) ) {
				// Args are reversed, not a typo.
				Sliced_Quote::set_status( $id, $quote_status );
			}
		}
	}

	/**
	 * Set the invoice/quote line items from the entry products, if enabled.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @param int $id The invoice (post) ID.
	 * @param array $entry The entry which created the invoice.
	 */
	public function maybe_set_line_items( $id, $entry ) {
		if ( ! $this->product_line_items_enabled ) {
			return;
		}

		$products = GFCommon::get_product_fields( $this->get_form(), $entry );

		if ( empty( $products['products'] ) ) {
			return;
		}

		$line_items = array();

		foreach ( $products['products'] as $product ) {
			$options = array();
			if ( is_array( rgar( $product, 'options' ) ) ) {
				foreach ( $product['options'] as $option ) {
					$options[] = $option['option_name'];
				}
			}

			$description = '';
			if ( ! empty( $options ) ) {
				$description = esc_html__( 'options: ', 'gravityflow' ) . ' ' . implode( ', ', $options );
			}

			$line_items[] = array(
				'qty'         => esc_html( rgar( $product, 'quantity' ) ),
				'title'       => esc_html( rgar( $product, 'name' ) ),
				'description' => wp_kses_post( $description ),
				'amount'      => GFCommon::to_number( rgar( $product, 'price', 0 ), $entry['currency'] ),
			);
		}

		if ( ! empty( $products['shipping']['name'] ) ) {
			$line_items[] = array(
				'qty'         => 1,
				'title'       => esc_html( $products['shipping']['name'] ),
				'description' => '',
				'amount'      => GFCommon::to_number( rgar( $products['shipping'], 'price', 0 ), $entry['currency'] ),
			);
		}

		if ( ! empty( $line_items ) ) {
			update_post_meta( $id, '_sliced_items', $line_items );
		}
	}

	/**
	 * Display the workflow detail box for this step.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @param array $form The current form.
	 * @param array $args The page arguments.
	 */
	public function workflow_detail_box( $form, $args ) {
		$args = array(
			'post_type'  => 'sliced_invoice',
			'meta_query' => array(
				array(
					'key'   => '_gform-entry-id',
					'value' => $this->get_entry_id(),
				),
				array(
					'key'   => '_gravityflow-step-id',
					'value' => $this->get_id(),
				),
			),
		);

		$invoices = get_posts( $args );

		if ( ! empty( $invoices ) ) {
			echo sprintf( '<h4 style="margin-bottom:10px;">%s</h4>', $this->get_label() );

			$assignee_key = gravity_flow()->get_current_user_assignee_key();
			$can_edit     = $this->is_assignee( $assignee_key ) && current_user_can( 'edit_posts' );

			/* @var WP_Post $invoice */
			foreach ( $invoices as $invoice ) {
				$title = $invoice->post_title;

				if ( ! $title ) {
					$feed_id = get_post_meta( $invoice->ID, '_gform-feed-id', true );
					$feed    = gravity_flow()->get_feed( $feed_id );
					$title   = rgar( $feed['meta'], 'feedName' );
				}

				echo sprintf( '%s: %s<br><br>', esc_html__( 'Title', 'gravityflow' ), esc_html( $title ) );
				echo sprintf( '%s: %s<br><br>', esc_html__( 'Number', 'gravityflow' ), esc_html( get_post_meta( $invoice->ID, '_sliced_invoice_number', true ) ) );

				if ( function_exists( 'sliced_get_invoice_total' ) ) {
					echo sprintf( '%s: %s<br><br>', esc_html__( 'Total', 'gravityflow' ), esc_html( sliced_get_invoice_total( $invoice->ID ) ) );
				}

				if ( class_exists( 'Sliced_Shared' ) ) {
					echo sprintf( '%s: %s<br><br>', esc_html__( 'Status', 'gravityflow' ), esc_html( Sliced_Shared::get_status( $invoice->ID, 'invoice' ) ) );
				}

				echo '<div class="gravityflow-action-buttons">';
				if ( $can_edit ) {
					echo sprintf( '<a href="%s" target="_blank" class="button button-large button-primary">%s</a> ', get_edit_post_link( $invoice->ID ), esc_html__( 'Edit', 'gravityflow' ) );
				}
				echo sprintf( '<a href="%s" target="_blank" class="button button-large button-primary">%s</a><br><br>', get_permalink( $invoice ), esc_html__( 'Preview', 'gravityflow' ) );
				echo '</div>';

			}

		}
	}

	/**
	 * When restarting the workflow or step delete the step ID from the post meta of the invoices which already exist.
	 *
	 * @since 1.6.1-dev-2
	 */
	public function restart_action() {
		$args = array(
			'post_type'  => 'sliced_invoice',
			'meta_query' => array(
				array(
					'key'   => '_gform-entry-id',
					'value' => $this->get_entry_id(),
				),
				array(
					'key'   => '_gravityflow-step-id',
					'value' => $this->get_id(),
				),
			),
		);

		$invoices = get_posts( $args );

		if ( empty( $invoices ) ) {
			return;
		}

		/* @var WP_Post $invoice */
		foreach ( $invoices as $invoice ) {
			delete_post_meta( $invoice->ID, '_gravityflow-step-id' );
		}
	}

	/**
	 * Resume the workflow if the invoice is paid and originated from a feed processed by one of our steps.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @param string $id     The invoice (post) ID.
	 * @param string $status The invoice status.
	 */
	public static function invoice_status_update( $id, $status ) {
		if ( $status !== 'paid' ) {
			return;
		}

		$entry_id = get_post_meta( $id, '_gform-entry-id', true );
		$feed_id  = get_post_meta( $id, '_gform-feed-id', true );
		$step_id  = get_post_meta( $id, '_gravityflow-step-id', true );

		if ( ! $entry_id || ! $feed_id || ! $step_id ) {
			return;
		}

		$entry = GFAPI::get_entry( $entry_id );

		if ( ! is_wp_error( $entry ) && rgar( $entry, 'workflow_final_status' ) === 'pending' ) {
			$api = new Gravity_Flow_API( $entry['form_id'] );

			/* @var Gravity_Flow_Step_Feed_Sliced_Invoices $step */
			$step = $api->get_current_step( $entry );

			if ( $step && $step->get_id() == $step_id ) {
				$feed  = gravity_flow()->get_feed( $feed_id );
				$label = $step->get_feed_label( $feed );
				$step->add_note( sprintf( esc_html__( 'Invoice paid: %s', 'gravityflow' ), $label ) );
				$step->log_debug( __METHOD__ . '() - Feed processing complete: ' . $label );

				$add_on_feeds = $step->get_processed_add_on_feeds( $entry_id );
				if ( ! in_array( $feed_id, $add_on_feeds ) ) {
					$add_on_feeds[] = $feed_id;
					$step->update_processed_feeds( $add_on_feeds, $entry_id );
					$form = GFAPI::get_form( $entry['form_id'] );
					gravity_flow()->process_workflow( $form, $entry_id );
				}
			}
		}
	}

	/**
	 * Gets the choices array for the default status settings.
	 *
	 * @param string $type The object type; invoice or quote.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @return array
	 */
	public static function get_sliced_status_choices( $type ) {
		$terms   = get_terms( array( 'taxonomy' => $type . '_status', 'hide_empty' => 0 ) );
		$choices = array();

		if ( ! is_wp_error( $terms ) ) {
			/* @var WP_Term $term */
			foreach ( $terms as $term ) {
				$choices[] = array(
					'label' => $term->name,
					'value' => $term->slug !== 'draft' ? $term->slug : ''
				);
			}
		}

		return $choices;
	}

	/**
	 * Override the settings on the Sliced Invoices add-on feed configuration page.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @param array              $feed_settings_fields The array of feed settings fields which will be displayed on the add-ons feed configuration page.
	 * @param Sliced_Invoices_GF $add_on               The current instance of the add-on.
	 *
	 * @return array
	 */
	public static function feed_settings_fields( $feed_settings_fields, $add_on ) {
		$field = $add_on->get_field( 'mappedFields', $feed_settings_fields );

		if ( isset( $field['field_map'] ) && is_array( $field['field_map'] ) ) {
			foreach ( $field['field_map'] as &$child_field ) {
				if ( rgar( $child_field, 'name' ) !== 'line_items' ) {
					continue;
				}

				$child_field['field_type'] = array( 'list', 'entry_order_summary' );
			}

			$feed_settings_fields = $add_on->replace_field( 'mappedFields', $field, $feed_settings_fields );
		}

		$new_settings = array(
			array(
				'name'       => 'quote_status',
				'type'       => 'select',
				'label'      => __( 'Default Status', 'gravityflow' ),
				'choices'    => self::get_sliced_status_choices( 'quote' ),
				'dependency' => array(
					'field'  => 'post_type',
					'values' => 'quote',
				)
			),
			array(
				'name'       => 'invoice_status',
				'type'       => 'select',
				'label'      => __( 'Default Status', 'gravityflow' ),
				'choices'    => self::get_sliced_status_choices( 'invoice' ),
				'dependency' => array(
					'field'  => 'post_type',
					'values' => 'invoice',
				)
			)
		);

		$feed_settings_fields = $add_on->add_field_before( 'mappedFields', $new_settings, $feed_settings_fields );


		return $feed_settings_fields;
	}

	/**
	 * Override the map choices for the Line Item field on the Sliced Invoices add-on feed configuration page.
	 *
	 * @since 1.6.1-dev-2
	 *
	 * @param array      $fields     The value and label properties for each choice.
	 * @param int        $form_id    The ID of the form currently being configured.
	 * @param null|array $field_type Null or the field types to be included in the drop down.
	 *
	 * @return array
	 */
	public static function field_map_choices( $fields, $form_id, $field_type ) {
		if ( is_array( $field_type ) && in_array( 'entry_order_summary', $field_type ) ) {
			$fields[] = array(
				'label' => __( 'Entry Order Summary', 'gravityflow' ),
				'value' => 'entry_order_summary'
			);
		}

		return $fields;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Sliced_Invoices() );

add_action( 'sliced_invoice_status_update', array( 'Gravity_Flow_Step_Feed_Sliced_Invoices', 'invoice_status_update' ), 10, 2 );
add_filter( 'gform_slicedinvoices_feed_settings_fields', array( 'Gravity_Flow_Step_Feed_Sliced_Invoices', 'feed_settings_fields' ), 10, 2 );
add_filter( 'gform_slicedinvoices_field_map_choices', array( 'Gravity_Flow_Step_Feed_Sliced_Invoices', 'field_map_choices' ), 10, 3 );
