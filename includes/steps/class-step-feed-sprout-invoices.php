<?php
/**
 * Gravity Flow Step Feed Sprout Invoices
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Sprout_Invoices
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Sprout_Invoices
 */
class Gravity_Flow_Step_Feed_Sprout_Invoices extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'sprout_invoices';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @since 2.1.2-dev
	 *
	 * @var string
	 */
	protected $_class_name = 'SI_GF_Integration_Addon';

	/**
	 * The name of the slug used by the add-on.
	 *
	 * @var string
	 */
	protected $_slug = 'sprout-invoices-gravity-forms-integration';

	/**
	 * The ID of the form the Form Integrations plugin is configured to work with.
	 *
	 * @var bool|int
	 */
	protected $_estimate_form_id = false;

	/**
	 * The ID of the form the Invoice Submissions plugin is configured to work with.
	 *
	 * @var bool|int
	 */
	protected $_invoice_form_id = false;

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Sprout Invoices';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/sproutapps-icon.png';
	}

	/**
	 * Determines if this step type is supported.
	 *
	 * @return bool
	 */
	public function is_supported() {
		$form_id = $this->get_form_id();

		return $this->is_gf_add_on_supported() || $this->is_estimates_supported( $form_id ) || $this->is_invoices_supported( $form_id );
	}

	/**
	 * Check that the Form Integrations plugin is active and it is configured to work with the current form.
	 *
	 * @param int $form_id The ID of the current form.
	 *
	 * @return bool
	 */
	public function is_estimates_supported( $form_id ) {
		$is_supported = class_exists( 'SI_Form_Integrations' );

		if ( ! $is_supported ) {
			return false;
		}

		if ( ! $this->_estimate_form_id ) {
			$this->_estimate_form_id = get_option( SI_Form_Integrations::GRAVITY_FORM_ID );
		}

		return $form_id == $this->_estimate_form_id;
	}

	/**
	 * Check that the Invoice Submissions plugin is active and it is configured to work with the current form.
	 *
	 * @param int $form_id The ID of the current form.
	 *
	 * @return bool
	 */
	public function is_invoices_supported( $form_id ) {
		$is_supported = class_exists( 'SI_IS_Gravity_Forms' );

		if ( ! $is_supported ) {
			return false;
		}

		if ( ! $this->_invoice_form_id ) {
			$this->_invoice_form_id = get_option( SI_IS_Gravity_Forms::GRAVITY_FORM_ID );
		}

		return $form_id == $this->_invoice_form_id;
	}

	/**
	 * Checks if the feed based add-on is active.
	 *
	 * @since 2.1.2-dev
	 *
	 * @return bool
	 */
	public function is_gf_add_on_supported() {
		return parent::is_supported();
	}

	/**
	 * Returns the label of the given feed.
	 *
	 * @since 2.1.2-dev
	 *
	 * @param array $feed The add-on feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		$label = rgars( $feed, 'meta/feedName' );

		if ( empty( $label ) ) {
			switch ( $feed['meta']['si_generation'] ) {
				case 'estimate':
					$label = esc_html__( 'Estimate (and Client Record)', 'gravityflow' );
					break;

				case 'invoice':
					$label = esc_html__( 'Invoice (and Client Record)', 'gravityflow' );
					break;

				case 'client':
					$label = esc_html__( 'Client (only)', 'gravityflow' );
					break;
			}
		}

		return $label;
	}

	/**
	 * Returns the feeds for the add-on.
	 *
	 * The Form Integrations and Invoice Submissions add-ons do not extend the GF add-on framework so lets return dummy feeds for them.
	 *
	 * @since 2.1.2-dev Added support for the feed based add-on.
	 * @since 1.4.3-dev
	 *
	 * @return array
	 */
	public function get_feeds() {
		$form_id = $this->get_form_id();

		if ( $this->is_gf_add_on_supported() ) {
			/* @var GFFeedAddOn $add_on */
			$add_on = $this->get_add_on_instance();
			$feeds  = $add_on->get_feeds( $form_id );
		} else {
			$feeds = array();
		}

		if ( $this->is_estimates_supported( $form_id ) ) {
			$feeds[] = array(
				'id'         => 'estimate',
				'form_id'    => $form_id,
				'is_active'  => true,
				'meta'       => array(
					'feedName' => esc_html__( 'Create Estimate (Sprout Invoices Add-on - Form Integrations)', 'gravityflow' ),
				),
				'addon_slug' => $this->_step_type,
			);
		}

		if ( $this->is_invoices_supported( $form_id ) ) {
			$feeds[] = array(
				'id'         => 'invoice',
				'form_id'    => $form_id,
				'is_active'  => true,
				'meta'       => array(
					'feedName' => esc_html__( 'Create Invoice (Sprout Invoices Add-on - Invoice Submissions)', 'gravityflow' ),
				),
				'addon_slug' => $this->_step_type,
			);
		}

		return $feeds;
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @since 2.1.2-dev Added support for the feed based add-on.
	 * @since 1.4.3-dev
	 *
	 * @param array $feed The add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
	public function process_feed( $feed ) {
		$form  = $this->get_form();
		$entry = $this->get_entry();

		if ( $feed['id'] == 'estimate' && $this->is_estimates_supported( $form['id'] ) ) {
			SI_Form_Integrations::maybe_process_gravity_form( $entry, $form );
		}

		if ( $feed['id'] == 'invoice' && $this->is_invoices_supported( $form['id'] ) ) {
			SI_IS_Gravity_Forms::maybe_process_gravity_form( $entry, $form );
		}

		if ( $this->is_gf_add_on_supported() ) {
			$feed['meta']['redirect'] = false;
			parent::process_feed( $feed );
		}

		return true;
	}

	/**
	 * Prevent the feeds assigned to the current step from being processed by the add-on.
	 *
	 * If enabled prevent the Sprout Invoices/Estimates integrations from running during submission for the current form.
	 *
	 * @since 2.1.2-dev Added support for the feed based add-on.
	 * @since 1.4.3-dev
	 */
	public function intercept_submission() {
		$form_id = $this->get_form_id();

		if ( $this->feed_estimate && $this->is_estimates_supported( $form_id ) ) {
			remove_action( 'gform_after_submission', array( 'SI_Form_Integrations', 'maybe_process_gravity_form' ) );
		}

		if ( $this->feed_invoice && $this->is_invoices_supported( $form_id ) ) {
			remove_action( 'gform_entry_created', array( 'SI_IS_Gravity_Forms', 'maybe_process_gravity_form' ) );
			remove_filter( 'gform_confirmation_' . $form_id, array( 'SI_IS_Gravity_Forms', 'maybe_redirect_after_submission' ) );
		}

		if ( $this->is_gf_add_on_supported() ) {
			parent::intercept_submission();
		}
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Sprout_Invoices() );
