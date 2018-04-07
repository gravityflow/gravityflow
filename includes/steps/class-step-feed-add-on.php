<?php
/**
 * Gravity_Flow_Step_Feed_Add_On
 *
 * @package     GravityFlow
 * @subpackage  Classes/Step_Feed_Add_On
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Abstract class to be used for integration with Gravity Forms Feed Add-Ons.
 * Extend this class to integrate any Gravity Forms Add-On that is built using the Feed-Add-On Framework.
 *
 * Register your extending class using Gravity_Flow_Steps::register().
 * example:
 * Gravity_Flow_Steps::register( new Gravity_Flow_Step_My_Feed_Add_On_Step() )
 *
 * Class Gravity_Flow_Step_Feed_Add_On
 *
 * @since 1.0
 */
abstract class Gravity_Flow_Step_Feed_Add_On extends Gravity_Flow_Step {

	/**
	 * The name of the class used by the add-on. Example: GFMailChimp.
	 *
	 * @var string
	 */
	protected $_class_name = '';

	/**
	 * The add-on slug. Example: gravityformsmailchimp.
	 *
	 * @var string
	 */
	protected $_slug = '';

	/**
	 * The feeds processed for the current entry.
	 *
	 * @var array
	 */
	private $_processed_feeds = array();

	/**
	 * Returns the class name for the add-on.
	 *
	 * @return string
	 */
	public function get_feed_add_on_class_name() {
		return $this->_class_name;
	}

	/**
	 * Is this feed step supported on this server? Override to hide this step in the list of step types if the requirements are not met.
	 *
	 * @return bool
	 */
	public function is_supported() {
		$is_supported      = true;
		$feed_add_on_class = $this->get_feed_add_on_class_name();
		if ( ! class_exists( $feed_add_on_class ) ) {
			$is_supported = false;
		}

		return $is_supported;
	}

	/**
	 * Returns the settings for this step.
	 *
	 * @return array
	 */
	public function get_settings() {
		$fields = array();

		if ( ! $this->is_supported() ) {
			return $fields;
		}

		$feeds = $this->get_feeds();

		$feed_choices = array();
		foreach ( $feeds as $feed ) {
			if ( $feed['is_active'] ) {
				$label = $this->get_feed_label( $feed );

				$feed_choices[] = array(
					'label' => $label,
					'name'  => 'feed_' . $feed['id'],
				);
			}
		}

		if ( ! empty( $feed_choices ) ) {
			$fields[] = array(
				'name'     => 'feeds',
				'required' => true,
				'label'    => esc_html__( 'Feeds', 'gravityflow' ),
				'type'     => 'checkbox',
				'choices'  => $feed_choices,
			);
		}

		if ( empty( $fields ) ) {
			$html     = esc_html__( "You don't have any feeds set up.", 'gravityflow' );
			$fields[] = array(
				'name'  => 'no_feeds',
				'label' => esc_html__( 'Feeds', 'gravityflow' ),
				'type'  => 'html',
				'html'  => $html,
			);
		}

		return array(
			'title'  => $this->get_label(),
			'fields' => $fields,
		);
	}


	/**
	 * Processes this step.
	 *
	 * @return bool Is the step complete?
	 */
	public function process() {
		$form     = $this->get_form();
		$entry    = $this->get_entry();
		$complete = true;

		$add_on_feeds = $this->get_processed_add_on_feeds();
		$feeds        = $this->get_feeds();

		foreach ( $feeds as $feed ) {
			$setting_key = 'feed_' . $feed['id'];
			if ( $this->{$setting_key} ) {
				if ( $this->is_feed_condition_met( $feed, $form, $entry ) ) {

					$complete = $this->process_feed( $feed );
					$label    = $this->get_feed_label( $feed );

					if ( $complete ) {
						$note = sprintf( esc_html__( 'Processed: %s', 'gravityflow' ), $label );
						$this->log_debug( __METHOD__ . '() - Feed processed: ' . $label );
						$add_on_feeds = $this->maybe_set_processed_feed( $add_on_feeds, $feed['id'] );
					} else {
						$note = sprintf( esc_html__( 'Initiated: %s', 'gravityflow' ), $label );
						$this->log_debug( __METHOD__ . '() - Feed processing initiated: ' . $label );
						$add_on_feeds = $this->maybe_unset_processed_feed( $add_on_feeds, $feed['id'] );
					}

					$this->add_note( $note );
				} else {
					$this->log_debug( __METHOD__ . '() - Feed condition not met' );
				}
			}
		}

		$this->update_processed_feeds( $add_on_feeds );

		return $complete;
	}

	/**
	 * Returns the feeds for the add-on.
	 *
	 * @return array|mixed
	 */
	public function get_feeds() {
		$form_id = $this->get_form_id();

		if ( $this->is_supported() ) {
			/* @var GFFeedAddOn $add_on */
			$add_on = $this->get_add_on_instance();
			$feeds  = $add_on->get_feeds( $form_id );
		} else {
			$feeds = array();
		}

		return $feeds;
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @param array $feed The add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
	public function process_feed( $feed ) {
		$form   = $this->get_form();
		$entry  = $this->get_entry();
		$add_on = $this->get_add_on_instance();

		$add_on->process_feed( $feed, $entry, $form );

		return true;
	}

	/**
	 * Prevent the feeds assigned to the current step from being processed by the associated add-on.
	 */
	public function intercept_submission() {
		$form_id = $this->get_form_id();
		$slug    = $this->get_slug();
		add_filter( "gform_{$slug}_pre_process_feeds_{$form_id}", array( $this, 'pre_process_feeds' ), 10, 2 );
	}

	/**
	 * Returns the label of the given feed.
	 *
	 * @param array $feed The add-on feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		$label = $feed['meta']['feedName'];

		return $label;
	}

	/**
	 * Determines if the supplied feed should be processed.
	 *
	 * @param array $feed  The current feed.
	 * @param array $form  The current form.
	 * @param array $entry The current entry.
	 *
	 * @return bool
	 */
	public function is_feed_condition_met( $feed, $form, $entry ) {

		return gravity_flow()->is_feed_condition_met( $feed, $form, $entry );
	}

	/**
	 * Retrieve an instance of the add-on associated with this step.
	 *
	 * @return GFFeedAddOn
	 */
	public function get_add_on_instance() {
		$add_on = call_user_func( array( $this->get_feed_add_on_class_name(), 'get_instance' ) );

		return $add_on;
	}

	/**
	 * Remove the feeds assigned to the current step from the array to be processed by the associated add-on.
	 *
	 * @param array $feeds An array of $feed objects for the add-on currently being processed.
	 * @param array $entry The entry object currently being processed.
	 *
	 * @return array
	 */
	public function pre_process_feeds( $feeds, $entry ) {
		if ( is_array( $feeds ) ) {
			foreach ( $feeds as $key => $feed ) {
				$setting_key = 'feed_' . $feed['id'];
				if ( $this->{$setting_key} ) {
					$this->get_add_on_instance()->log_debug( __METHOD__ . "(): Delaying feed (#{$feed['id']} - {$this->get_feed_label( $feed )}) for entry #{$entry['id']}." );
					$this->get_add_on_instance()->delay_feed( $feed, $entry, $this->get_form() );
					unset( $feeds[ $key ] );
				}
			}
		}

		return $feeds;
	}

	/**
	 * Ensure active steps are not processed if the associated add-on is not available.
	 *
	 * @return bool
	 */
	public function is_active() {
		$is_active = parent::is_active();

		if ( $is_active && ! $this->is_supported() ) {
			$is_active = false;
		}

		return $is_active;
	}

	/**
	 * Get the slug for the add-on associated with this step.
	 *
	 * @return string
	 */
	public function get_slug() {
		if ( empty( $this->_slug ) ) {
			$this->_slug = $this->get_add_on_instance()->get_slug();
		}

		return $this->_slug;
	}

	/**
	 * Retrieve an array containing the IDs of all the feeds processed for the current entry.
	 *
	 * @param bool|int $entry_id False or the ID of the entry the meta should be retrieved from.
	 *
	 * @return array
	 */
	public function get_processed_feeds( $entry_id = false ) {
		if ( ! empty( $this->_processed_feeds ) ) {
			return $this->_processed_feeds;
		}

		if ( ! $entry_id ) {
			$entry_id = $this->get_entry_id();
		}

		$processed_feeds = gform_get_meta( $entry_id, 'processed_feeds' );
		if ( empty( $processed_feeds ) ) {
			$processed_feeds = array();
		}

		$this->_processed_feeds = $processed_feeds;

		return $processed_feeds;
	}


	/**
	 * Retrieve an array of this add-ons feed IDs which have been processed for the current entry.
	 *
	 * @param bool|int $entry_id False or the ID of the entry the meta should be retrieved from.
	 *
	 * @return array
	 */
	public function get_processed_add_on_feeds( $entry_id = false ) {
		$processed_feeds = $this->get_processed_feeds( $entry_id );
		$add_on_feeds    = rgar( $processed_feeds, $this->get_slug() );
		if ( empty( $add_on_feeds ) ) {
			$add_on_feeds = array();
		}

		return $add_on_feeds;
	}

	/**
	 * Add the ID of the current feed to the processed feeds array for the current add-on.
	 *
	 * @param array $add_on_feeds The IDs of the processed feeds.
	 * @param int   $feed_id      The ID of the processed feed.
	 *
	 * @return array
	 */
	public function maybe_set_processed_feed( $add_on_feeds, $feed_id ) {
		if ( ! in_array( $feed_id, $add_on_feeds ) ) {
			$add_on_feeds[] = $feed_id;
		}

		return $add_on_feeds;
	}

	/**
	 * If necessary remove the current feed from the processed feeds array for the current add-on.
	 *
	 * @param array $add_on_feeds The IDs of the processed feeds.
	 * @param int   $feed_id      The ID of the processed feed.
	 *
	 * @return array
	 */
	public function maybe_unset_processed_feed( $add_on_feeds, $feed_id ) {
		foreach ( $add_on_feeds as $key => $id ) {
			if ( $id == $feed_id ) {
				unset( $add_on_feeds[ $key ] );
				break;
			}
		}

		return $add_on_feeds;
	}

	/**
	 * Update the processed_feeds array for the current entry.
	 *
	 * @param array    $add_on_feeds The IDs of the processed feeds for the current add-on.
	 * @param bool|int $entry_id     False or the ID of the entry the meta should be saved for.
	 */
	public function update_processed_feeds( $add_on_feeds, $entry_id = false ) {
		if ( ! $entry_id ) {
			$entry_id = $this->get_entry_id();
		}

		$processed_feeds                      = $this->get_processed_feeds( $entry_id );
		$processed_feeds[ $this->get_slug() ] = $add_on_feeds;
		$this->_processed_feeds               = $processed_feeds;

		gform_update_meta( $entry_id, 'processed_feeds', $processed_feeds );
	}

	/**
	 * Evaluates the status for the step.
	 *
	 * The step is only complete when all the feeds for this step have been added to the entry meta processed_feeds array.
	 *
	 * @return string 'pending' or 'complete'
	 */
	public function status_evaluation() {
		$add_on_feeds = $this->get_processed_add_on_feeds();
		$feeds        = $this->get_feeds();

		$form  = $this->get_form();
		$entry = $this->get_entry();

		foreach ( $feeds as $feed ) {
			$setting_key = 'feed_' . $feed['id'];
			if ( $this->{$setting_key} && ! in_array( $feed['id'], $add_on_feeds ) && $this->is_feed_condition_met( $feed, $form, $entry ) ) {
				return 'pending';
			}
		}

		return 'complete';
	}

}
