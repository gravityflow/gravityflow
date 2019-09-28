<?php
/**
 * Gravity Flow Step Feed Post Creation
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Breeze
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Post_Creation
 */
class Gravity_Flow_Step_Feed_Post_Creation extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @since 1.4.3
	 *
	 * @var string
	 */
	public $_step_type = 'post_creation';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @since 2.3.4 Changed from GF_Post_Creation.
	 * @since 1.4.3
	 *
	 * @var string
	 */
	protected $_class_name = 'GF_Advanced_Post_Creation';

	/**
	 * Returns the step label.
	 *
	 * @since 1.4.3
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Post Creation';
	}

	/**
	 * Returns the class name for the add-on.
	 *
	 * @since 2.3.4
	 *
	 * @return string
	 */
	public function get_feed_add_on_class_name() {
		if ( class_exists( 'GF_Post_Creation' ) ) {
			$this->_class_name = 'GF_Post_Creation';
		}

		return $this->_class_name;
	}

	/**
	 * Processes the given feed for the add-on.
	 *
	 * @since 2.5.6
	 *
	 * @param array $feed The add-on feed properties.
	 *
	 * @return bool Is feed processing complete?
	 */
	public function process_feed( $feed ) {
		$post_id = $this->get_existing_post_id( (int) $feed['id'] );

		if ( ! empty( $post_id ) ) {
			$this->log_debug( sprintf( '%s() - post #%d already exists for this feed.', __METHOD__, $post_id ) );
		} else {
			add_filter( 'gravityflow_discussion_items_display_toggle', '__return_false', 99 );
			parent::process_feed( $feed );
			remove_filter( 'gravityflow_discussion_items_display_toggle', '__return_false', 99 );
		}

		return true;
	}

	/**
	 * Returns the ID of the post previously created by the current feed.
	 *
	 * @since 2.5.6
	 *
	 * @param int $feed_id The current feed ID.
	 *
	 * @return int|null
	 */
	public function get_existing_post_id( $feed_id ) {
		/** @var GF_Advanced_Post_Creation $add_on */
		$add_on = $this->get_add_on_instance();

		$post_ids = gform_get_meta( $this->get_entry_id(), $add_on->get_slug() . '_post_id' );

		if ( is_array( $post_ids ) ) {
			foreach ( $post_ids as $id ) {
				$post_feed_id = (int) rgar( $id, 'feed_id' );
				if ( $post_feed_id === $feed_id ) {
					return $id['post_id'];
				}
			}
		}

		return null;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Post_Creation() );
