<?php
/**
 * Gravity Flow Step Feed Zapier
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Zapier
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Zapier
 */
class Gravity_Flow_Step_Feed_Zapier extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $_step_type = 'zapier';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @since 1.0.0
	 * @since 2.5.10 Updated to support Zapier v4.0
	 *
	 * @var string
	 */
	protected $_class_name = 'GF_Zapier';

	/**
	 * The slug used by the add-on.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	protected $_slug = 'gravityformszapier';

	/**
	 * Returns the step label.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Zapier';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/zapier-icon.svg';
	}

	/**
	 * Returns the settings for this step.
	 *
	 * @since 2.7.3
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = parent::get_settings();

		if ( empty( $settings ) ) {
			return $settings;
		}

		$form_setting = array(
			'name'          => 'zapier_form',
			'label'         => esc_html__( 'Form', 'gravityflow' ),
			'type'          => 'select',
			'tooltip'       => esc_html__( 'Select the form which has the Zapier feed(s) to be processed by this step.', 'gravityflow' ),
			'default_value' => '',
			'choices'       => array(
				array(
					'label' => esc_html__( 'This Form', 'gravityflow' ),
					'value' => '',
				),
			),
			'onchange'      => "jQuery(this).closest('form').submit();",
		);

		$forms = GFFormsModel::get_forms( true );

		foreach ( $forms as $form ) {
			if ( $this->get_form_id() == $form->id ) {
				continue;
			}

			$form_setting['choices'][] = array(
				'label' => esc_html( $form->title ),
				'value' => absint( $form->id ),
			);
		}

		array_unshift( $settings['fields'], $form_setting );

		return $settings;
	}

	/**
	 * Customizes the feeds setting.
	 *
	 * @since 2.7.3
	 *
	 * @return array
	 */
	protected function get_feeds_setting() {
		$feeds   = $this->get_feeds();
		$choices = array();

		foreach ( $feeds as $feed ) {
			if ( empty( $feed['is_active'] ) ) {
				continue;
			}

			$choice = array(
				'label' => $this->get_feed_label( $feed ),
				'name'  => 'feed_' . $feed['id'],
			);

			$zap_id = absint( rgar( $feed['meta'], 'zapID' ) );

			if ( ! empty( $zap_id ) ) {
				$choice['tooltip'] = sprintf(
					// Translators: 1. Opening <a> tag for link to zap overview, 2. Closing <a> tag, 3. Opening <a> tag for link to zap editor, 4. The zap ID.
					esc_html__( '%1$sView%2$s or %3$sedit%2$s zap (%4$s) on zapier.com', 'gravityflow' ),
					'<a href="' . esc_url( 'https://zapier.com/app/zap/' . $zap_id ) . '" target="_blank">',
					'</a>',
					'<a href="' . esc_url( 'https://zapier.com/app/editor/' . $zap_id ) . '" target="_blank">',
					$zap_id
				);
			}

			$choices[] = $choice;
		}

		if ( empty( $choices ) ) {
			return array(
				'name'  => 'no_feeds',
				'label' => esc_html__( 'Feeds', 'gravityflow' ),
				'type'  => 'html',
				'html'  => sprintf(
					// Translators: 1. Opening <a> tag for link to Zapier, 2. Closing <a> tag.
					esc_html__( 'The selected form doesn\'t have any feeds. %1$sCreate a zap%2$s on zapier.com or select a different form.', 'gravityflow' ),
					'<a href="' . esc_url( 'https://zapier.com/apps/gravity-forms/integrations' ) . '" target="_blank">',
					'</a>'
				),
			);
		}

		return array(
			'name'     => 'feeds',
			'required' => true,
			'label'    => esc_html__( 'Feeds', 'gravityflow' ),
			'type'     => 'checkbox',
			'choices'  => $choices,
		);
	}

	/**
	 * Returns the feeds for the add-on.
	 *
	 * @since 1.0.0
	 * @since 2.5.10 Updated to support Zapier v4.0
	 * @since 2.7.3  Updated to support processing feeds from other forms.
	 *
	 * @return array
	 */
	public function get_feeds() {
		$form_id = $this->get_setting( 'zapier_form' );
		if ( empty( $form_id ) ) {
			$form_id = $this->get_form_id();
		}

		if ( class_exists( 'GFZapierData' ) ) {
			return GFZapierData::get_feed_by_form( $form_id );
		} elseif ( function_exists( 'gf_zapier' ) ) {
			return gf_zapier()->get_feeds( $form_id );
		}

		return array();
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @since 1.0.0
	 * @since 2.5.10 Updated to support Zapier v4.0
	 *
	 * @param array $feed The add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
	public function process_feed( $feed ) {

		// If the step is delayed, there might be several entries to be processed at the same time,
		// don't use the stored body, or the first entry in queue will be used as the body for all entries.
		add_filter( 'gform_zapier_use_stored_body', '__return_false' );

		if ( class_exists( 'GFZapier' ) ) {
			$form  = $this->get_form();
			$entry = $this->get_entry();

			if ( method_exists( 'GFZapier', 'process_feed' ) ) {
				GFZapier::process_feed( $feed, $entry, $form );
			} else {
				GFZapier::send_form_data_to_zapier( $entry, $form );
			}
		} else {
			parent::process_feed( $feed );
		}

		return true;
	}

	/**
	 * Prevent the feeds assigned to the current step from being processed by the associated add-on.
	 *
	 * @since 1.0.0
	 * @since 2.5.10 Updated to support Zapier v4.0
	 */
	public function intercept_submission() {
		if ( class_exists( 'GFZapier' ) ) {
			remove_action( 'gform_after_submission', array( 'GFZapier', 'send_form_data_to_zapier' ) );
		} else {
			parent::intercept_submission();
		}
	}

	/**
	 * Returns the feed name.
	 *
	 * @since 1.0.0
	 * @since 2.5.10 Updated to support Zapier v4.0
	 *
	 * @param array $feed The feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		if ( isset( $feed['name'] ) ) {
			return $feed['name'];
		}

		return parent::get_feed_label( $feed );
	}

	/**
	 * Determines if the supplied feed should be processed.
	 *
	 * @since 1.3.2
	 * @since 2.5.10 Updated to support Zapier v4.0
	 *
	 * @param array $feed  The current feed.
	 * @param array $form  The current form.
	 * @param array $entry The current entry.
	 *
	 * @return bool
	 */
	public function is_feed_condition_met( $feed, $form, $entry ) {
		if ( class_exists( 'GFZapier' ) ) {
			return GFZapier::conditions_met( $form, $feed, $entry );
		}

		return parent::is_feed_condition_met( $feed, $form, $entry );
	}

	/**
	 * Returns the class name for the add-on.
	 *
	 * @since 2.5.10
	 *
	 * @return string
	 */
	public function get_feed_add_on_class_name() {
		if ( class_exists( 'GFZapier' ) ) {
			$this->_class_name = 'GFZapier';
		}

		return parent::get_feed_add_on_class_name();
	}

	/**
	 * Updates the selected Zapier feed IDs in the step meta when Zapier Add-On 4.0+ migrates the feeds to the add-on framework.
	 *
	 * @since 2.5.10
	 *
	 * @param array $migrated_feeds An array of migrated Zapier feeds.
	 */
	public static function migrate( $migrated_feeds ) {
		$steps = gravity_flow()->get_steps();

		foreach ( $steps as $step ) {
			if ( $step->get_type() !== 'zapier' ) {
				continue;
			}

			$to_migrate = array();
			$step_meta  = $step->get_feed_meta();

			foreach ( $migrated_feeds as $feed ) {
				$legacy_id = rgars( $feed, 'meta/legacy_id' );
				if ( $legacy_id && isset( $step_meta[ 'feed_' . $legacy_id ] ) ) {
					$to_migrate[ $legacy_id ] = $step_meta[ 'feed_' . $legacy_id ] == '1' ? $feed['id'] : false;
					unset( $step_meta[ 'feed_' . $legacy_id ] );
				}
			}

			if ( ! empty( $to_migrate ) ) {
				foreach ( $to_migrate as $legacy_id => $new_id ) {
					if ( $new_id !== false ) {
						$step_meta[ 'feed_' . $new_id ] = '1';
					}
				}
				gravity_flow()->update_feed_meta( $step->get_id(), $step_meta );
			}

		}
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Zapier() );

add_action( 'gform_zapier_post_migrate_feeds', array( 'Gravity_Flow_Step_Feed_Zapier', 'migrate' ) );
