<?php
/**
 * Gravity Flow Step Feed Dropbox
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Dropbox
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Dropbox
 */
class Gravity_Flow_Step_Feed_Dropbox extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'dropbox';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GF_Dropbox';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Dropbox';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/dropbox-icon.svg';
	}

	/**
	 * Returns the class name for the add-on.
	 *
	 * @return string
	 */
	public function get_feed_add_on_class_name() {
		if ( class_exists( 'GFDropbox' ) ) {
			$this->_class_name = 'GFDropbox';
		}

		return $this->_class_name;
	}

	/**
	 * Process the feed; remove the feed from the processed feeds list;
	 *
	 * @param array $feed The feed to be processed.
	 *
	 * @return bool Returning false to ensure the next step is not processed until after the files are uploaded.
	 */
	public function process_feed( $feed ) {
		$feed['meta']['workflow_step'] = $this->get_id();
		parent::process_feed( $feed );

		return false;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Dropbox() );

/**
 * If the feed for a Dropbox step was processed maybe resume the workflow.
 *
 * @param array $feed  The Dropbox feed for which uploading has just completed.
 * @param array $entry The entry which was processed.
 * @param array $form  The form object for this entry.
 */
function gravity_flow_step_dropbox_post_upload( $feed, $entry, $form ) {
	$workflow_is_pending = rgar( $entry, 'workflow_final_status' ) == 'pending';
	$feed_step_id        = rgar( $feed['meta'], 'workflow_step' );
	$entry_step_id       = rgar( $entry, 'workflow_step' );

	if ( $workflow_is_pending && ! empty( $feed_step_id ) && $feed_step_id == $entry_step_id ) {
		$step = Gravity_Flow_Steps::get( 'dropbox' );
		if ( $step ) {
			$add_on_feeds = $step->get_processed_add_on_feeds( $entry['id'] );

			if ( ! in_array( $feed['id'], $add_on_feeds ) ) {
				$add_on_feeds[] = $feed['id'];
				$step->update_processed_feeds( $add_on_feeds, $entry['id'] );
				gravity_flow()->process_workflow( $form, $entry['id'] );
			}
		}
	}
}

add_action( 'gform_dropbox_post_upload', 'gravity_flow_step_dropbox_post_upload', 10, 3 );
