<?php
/**
 * Gravity Flow Step Feed Help Scout
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Step_Feed_HelpScout
 * @copyright   Copyright (c) 2016-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.2-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Step_Feed_HelpScout extends Gravity_Flow_Step_Feed_Add_On {
	public $_step_type = 'helpscout';

	protected $_class_name = 'GFHelpScout';

	public function get_label() {
		return 'Help Scout';
	}

	public function get_feed_label( $feed ) {
		$label = $feed['meta']['feed_name'];

		return $label;
	}

	public function get_icon_url() {
		return $this->get_base_url() . '/images/helpscout-icon.png';
	}
}

Gravity_Flow_Steps::register( new Gravity_Flow_Step_Feed_HelpScout() );
