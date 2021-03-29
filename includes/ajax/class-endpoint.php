<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

abstract class Endpoint {

	protected $response_factory;

	protected $config;

	protected $data          = array();
	protected $required_args = array();
	protected $optional_args = array();

	public function __construct( Config $config, Response_Factory $factory ) {
		$this->response_factory = $factory;
		$this->config           = $config;

		foreach ( $config->args() as $arg ) {
			if ( $arg->is_required() ) {
				$this->required_args[ $arg->name() ] = $arg;
			} else {
				$this->optional_args[ $arg->name() ] = $arg;
			}
		}
	}

	public function delegate() {
		$posted_values = $_POST;

		foreach ( $this->required_args as $rname => $config ) {
			if ( ! isset( $posted_values[ $rname ] ) ) {
				return new \WP_Error( 'Required argument ' . $rname . ' not set.' );
			}

			$this->add_values_to_data( $rname, $config );
		}

		foreach ( $this->optional_args as $oname => $config ) {
			if ( ! isset( $posted_values[ $oname ] ) && is_null( $config->default_value() ) ) {
				continue;
			}

			$this->add_values_to_data( $oname, $config );
		}

		return $this->handle();
	}

	protected function add_values_to_data( $name, Argument $config ) {
		$value = isset( $_POST[ $name ] ) ? $_POST[ $name ] : $config->default_value();
		$value = $config->sanitize( $value );

		$this->data[ $name ] = $value;
	}

	protected function data( $name ) {
		return isset( $this->data[ $name ] ) ? $this->data[ $name ] : null;
	}

	public function is_public() {
		return $this->config->is_public();
	}

	public function name() {
		return $this->config->name();
	}

	public function nonce() {
		return wp_create_nonce( $this->name );
	}

	abstract protected function handle();

}