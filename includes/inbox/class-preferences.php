<?php

namespace Gravity_Flow\Gravity_Flow\Inbox;

class Preferences {

	public function get_setting( $key, $id, $view = 'inbox', $default ) {
		$value = $this->get_setting_from_user( $key, $id );

		if ( $value !== false && $value !== 0 && empty( $value ) ) {
			$value = $this->get_setting_from_view( $key, $view );
		}

		if ( $value === false ) {
			return $default;
		}

		return $value;
	}

	public function save_setting( $key, $id, $value, $type = 'view' ) {
		$method = sprintf( 'save_setting_for_' . $type );
		$this->$method( $key, $id, $value );
	}

	public function get_setting_from( $type, $key, $id, $default ) {
		switch ( $type ) {
			case 'user':
				return $this->get_setting_from_user( $key, $id );
			case 'view':
				return $this->get_setting_from_view( $key, $id );
			default:
				return $default;
		}
	}

	private function get_setting_from_user( $key, $id ) {
		$setting_key = $this->get_setting_key( $key );

		return get_user_meta( $id, $setting_key, true );
	}

	private function get_setting_from_view( $key, $id ) {
		$setting_key = $this->get_setting_key( $key, $id );

		return get_option( $setting_key );
	}

	private function save_setting_for_user( $key, $id, $value ) {
		$setting_key = $this->get_setting_key( $key );

		return update_user_meta( $id, $setting_key, $value );
	}

	private function save_setting_for_view( $key, $id, $value ) {
		$setting_key = $this->get_setting_key( $key, $id );

		return update_option( $setting_key, $value );
	}

	private function get_setting_key( $key, $id = false ) {
		if ( $id === false ) {
			return sprintf( 'gflow_setting_%s', $key );
		}

		return sprintf( 'gflow_setting_%s_%s', $key, $id );
	}

}