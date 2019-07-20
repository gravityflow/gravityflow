<?php
/**
 * Gravity Flow Step Feed HubSpot (Gravity Forms official add-on)
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_GFHubSpot
 * @copyright   Copyright (c) 2015-2019, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.5.6
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_GFHubSpot
 */
class Gravity_Flow_Step_Feed_GFHubSpot extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @since 2.5.6
	 * @var string
	 */
	public $_step_type = 'gfhubspot';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @since 2.5.6
	 * @var string
	 */
	protected $_class_name = 'GF_HubSpot';

	/**
	 * Returns the step label.
	 *
	 * @since 2.5.6
	 *
	 * @return string
	 */
	public function get_label() {
		return 'HubSpot';
	}

	/**
	 * Returns the URL for the step icon.
	 *
	 * @since 2.5.6
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->get_base_url() . '/images/hubspot-icon.svg';
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_GFHubSpot() );
