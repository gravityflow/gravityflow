<?php

abstract class GF_Field_UnitTestCase extends GF_UnitTestCase {

	/**
	 * @var GF_Field
	 */
	protected $field;

	public function setUp() {
		parent::setUp();

		$settings = wp_parse_args( $this->_field_properties(), array(
			'id'      => 1,
			'formId'  => 1,
			'label'   => 'Test Label',
			'type'    => 'test',
			'choices' => null,
			'inputs'  => null,
		) );

		$this->field = GF_Fields::create( $settings );
	}

	/**
	 * Returns the properties to be used when initializing the field.
	 *
	 * @return array
	 */
	protected function _field_properties() {
		return array();
	}

}
