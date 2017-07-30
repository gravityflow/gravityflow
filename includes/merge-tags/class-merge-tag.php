<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

abstract class Gravity_Flow_Merge_Tag {

	/**
	 * The name of the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var null
	 */
	public $name = null;

	/**
	 * @var array $form The form array.
	 *
	 * @since 1.7.1-dev
	 */
	protected $form = null;

	/**
	 * @var array $form The Entry array.
	 *
	 * @since 1.7.1-dev
	 */
	protected $entry = null;

	/**
	 * @var bool $_url_encode Indicates if the replacement value should be URL encoded.
	 *
	 * @since 1.7.1-dev
	 */
	protected $url_encode = false;

	/**
	 * @var bool $_esc_html Indicates if HTML found in the replacement value should be escaped.
	 *
	 * @since 1.7.1-dev
	 */
	protected $esc_html = true;

	/**
	 * @var bool $_nl2br Indicates if newlines should be converted to html <br> tags.
	 *
	 * @since 1.7.1-dev
	 */
	protected $nl2br = true;

	/**
	 * @var string $_format Determines how the value should be formatted. HTML or text.
	 *
	 * @since 1.7.1-dev
	 */
	protected $format = 'html';

	/**
	 * @var Gravity_Flow_Step $step The current step.
	 *
	 * @since 1.7.1-dev
	 */
	protected $step = null;

	/**
	 * @var Gravity_Flow_Assignee $assignee The assignee.
	 *
	 * @since 1.7.1-dev
	 */
	protected $assignee = null;

	/**
	 * The regular expression to use for the matching.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $regex = '';

	public function __construct( $args = null ) {

		if ( isset( $args['form'] ) ) {
			$this->form = $args['form'];
		}

		if ( isset( $args['entry'] ) ) {
			$this->entry = $args['entry'];
		}

		if ( isset( $args['url_encode'] ) ) {
			$this->url_encode = (bool) $args['url_encode'];
		}

		if ( isset( $args['esc_html'] ) ) {
			$this->esc_html = (bool) $args['esc_html'];
		}

		if ( isset( $args['nl2br'] ) ) {
			$this->nl2br = (bool) $args['nl2br'];
		}

		if ( isset( $args['format'] ) ) {
			$this->format = $args['format'];
		}

		if ( isset( $args['step'] ) ) {
			$this->step = $args['step'];
		}

		if ( isset( $args['assignee'] ) ) {
			$this->assignee = $args['assignee'];
		}
	}

	protected function get_matches( $text ) {

		$matches = array();

		preg_match_all( $this->regex, $text, $matches, PREG_SET_ORDER );

		return $matches;
	}

	public function replace( $text ) {
		return new WP_Error( 'invalid-method', sprintf( __( "Method '%s' not implemented. Must be over-ridden in subclass." ), __METHOD__ ) );
	}

	/**
	 * Retrieve attributes from a string (i.e. merge tag modifiers).
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $string   The string to retrieve the attributes from.
	 * @param array  $defaults The supported attributes and their defaults.
	 *
	 * @return array
	 */
	public function get_attributes( $string, $defaults = array() ) {
		$attributes = shortcode_parse_atts( $string );

		if ( empty( $attributes ) ) {
			$attributes = array();
		}

		if ( ! empty( $defaults ) ) {
			$attributes = shortcode_atts( $defaults, $attributes );

			foreach ( $defaults as $attribute => $default ) {
				if ( $default === true ) {
					$attributes[ $attribute ] = strtolower( $attributes[ $attribute ] ) == 'false' ? false : true;
				} elseif ( $default === false ) {
					$attributes[ $attribute ] = strtolower( $attributes[ $attribute ] ) == 'true' ? true : false;
				}
			}
		}

		return $attributes;
	}

	/**
	 * Formats the value which will replace the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @param string $value The value to be formatted.
	 *
	 * @return string
	 */
	protected function format_value( $value ) {
		return GFCommon::format_variable_value( $value, $this->url_encode, $this->esc_html, $this->format, $this->nl2br );
	}
}
