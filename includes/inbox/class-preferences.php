<?php

namespace Gravity_Flow\Gravity_Flow\Inbox;

class Preferences {

	public function get_setting( $key, $id, $view = 'inbox', $default ) {
		$value = $this->get_setting_from_user( $key, $id, $view );

		if ( $value !== false && $value !== 0 && empty( $value ) ) {
			$value = $this->get_setting_from_view( $key, $view );
		}

		if ( $value === false ) {
			return $default;
		}

		return $value;
	}

	public function save_setting( $key, $id, $value, $view = 'inbox', $type = 'view' ) {
		switch ( $type ) {
			case 'user':
				return $this->save_setting_for_user( $key, $id, $view, $value );
			case 'view':
			default:
				return $this->save_setting_for_view( $key, $id, $value );
		}
	}

	public function get_setting_from( $type, $key, $id_or_view, $default ) {
		switch ( $type ) {
			case 'user':
				return $this->get_setting_from_user( $key, $id_or_view );
			case 'view':
				return $this->get_setting_from_view( $key, $id_or_view );
			default:
				return $default;
		}
	}

	private function get_setting_from_user( $key, $id, $view ) {
		$setting_key = $this->get_setting_key( $key, $view );

		return get_user_meta( $id, $setting_key, true );
	}

	private function get_setting_from_view( $key, $view ) {
		$setting_key = $this->get_setting_key( $key, $view );

		return get_option( $setting_key );
	}

	private function save_setting_for_user( $key, $id, $view, $value ) {
		$setting_key = $this->get_setting_key( $key, $view );

		return update_user_meta( $id, $setting_key, $value );
	}

	private function save_setting_for_view( $key, $view, $value ) {
		$setting_key = $this->get_setting_key( $key, $view );

		return update_option( $setting_key, $value );
	}

	private function get_setting_key( $key, $view = false ) {
		if ( $view === false ) {
			return sprintf( 'gflow_setting_%s', $key );
		}

		return sprintf( 'gflow_setting_%s_%s', $key, $view );
	}

}