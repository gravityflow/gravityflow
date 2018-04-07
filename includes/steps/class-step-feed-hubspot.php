<?php
/**
 * Gravity Flow Step Feed HubSpot
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_HubSpot
 * @copyright   Copyright (c) 2016-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.3-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_HubSpot
 */
class Gravity_Flow_Step_Feed_HubSpot extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'hubspot';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = '\BigSea\GFHubSpot\GF_HubSpot';

	/**
	 * The slug used by the add-on.
	 *
	 * @var string
	 */
	protected $_slug = 'gravityforms-hubspot';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'HubSpot';
	}

	/**
	 * Returns the class name for the add-on.
	 *
	 * @return string
	 */
	public function get_feed_add_on_class_name() {
		if ( ! class_exists( '\BigSea\GFHubSpot\GF_HubSpot' ) ) {
			$this->_class_name = 'GF_HubSpot';
		}

		return $this->_class_name;
	}

}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_HubSpot() );
