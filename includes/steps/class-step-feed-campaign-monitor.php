<?php
/**
 * Gravity Flow Step Feed Campaign Monitor
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_Campaign_Monitor
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Step_Feed_Campaign_Monitor
 */
class Gravity_Flow_Step_Feed_Campaign_Monitor extends Gravity_Flow_Step_Feed_Add_On {

	/**
	 * The step type.
	 *
	 * @var string
	 */
	public $_step_type = 'campaign_monitor';

	/**
	 * The name of the class used by the add-on.
	 *
	 * @var string
	 */
	protected $_class_name = 'GFCampaignMonitor';

	/**
	 * Returns the step label.
	 *
	 * @return string
	 */
	public function get_label() {
		return 'Campaign Monitor';
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_Campaign_Monitor() );
