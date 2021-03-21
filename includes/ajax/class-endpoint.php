<?php

namespace Gravity_Flow\Gravity_Flow\Ajax;

abstract class Endpoint {

	protected $response_factory;

	protected $data = array();
	protected $required_args = array();
	protected $optional_args = array();

	public function __construct( Config $config, Response_Factory $factory ) {
		$this->response_factory = $factory;

		foreach ( $config->args() as $arg ) {
			$name = $arg['name'];

			if ( $arg['required'] ) {
				$this->required_args[ $name ] = $arg;
			} else {
				$this->optional_args[ $name ] = $arg;
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
			if ( ! isset( $posted_values[ $oname ] ) ) {
				continue;
			}

			$this->add_values_to_data( $oname, $config );
		}

		return $this->handle();
	}

	protected function add_values_to_data( $name, $config ) {
		$value = $_POST[ $rname ];

		if ( is_callable( $config['sanitization'] ) ) {
			$value = call_user_func( $config['sanitization'], $value );
		}

		$this->data[ $oname ] = $value;
	}

	abstract protected function handle();

}