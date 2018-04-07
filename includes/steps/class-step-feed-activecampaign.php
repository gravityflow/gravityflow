<?php
/**
 * Gravity Flow Step Feed ActiveCampaign
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_ActiveCampaign
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.1.2
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_ActiveCampaign
 */
class Gravity_Flow_Step_Feed_ActiveCampaign extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'activecampaign';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFActiveCampaign';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'ActiveCampaign';
	}

	/**
	 * Returns the feed name.
	 *
	 * @param array $feed The ActiveCampaign feed properties.
	 *
	 * @return string
	 */
	public function get_feed_label( $feed ) {
		$label = $feed['meta']['feed_name'];

		return $label;
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/activecampaign-icon.svg';
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_ActiveCampaign() );
