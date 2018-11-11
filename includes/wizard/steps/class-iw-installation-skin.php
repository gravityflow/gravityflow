<?php

/**
 * Allows the plugin installer to run without outputting anything.
 *
 * Class Gravity_Flow_Quiet_Installation_Skin
 *
 * @since 2.3.2
 */
class Gravity_Flow_Quiet_Installation_Skin extends WP_Upgrader_Skin {
	public function feedback( $string ) {
		// no output
	}
}
