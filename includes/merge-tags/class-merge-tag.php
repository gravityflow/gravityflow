<?php
/**
 * Gravity Flow Merge Tag
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Merge_Tag
 *
 * An abstract class used as the base for all merge tags.
 *
 * @since 1.7.1-dev
 */
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
	 * The form array.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var null|array
	 */
	protected $form = null;

	/**
	 * The Entry array.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var null|array
	 */
	protected $entry = null;

	/**
	 * Indicates if the replacement value should be URL encoded.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var bool
	 */
	protected $url_encode = false;

	/**
	 * Indicates if HTML found in the replacement value should be escaped.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var bool
	 */
	protected $esc_html = true;

	/**
	 * Indicates if newlines should be converted to html <br> tags.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var bool
	 */
	protected $nl2br = true;

	/**
	 * Determines how the value should be formatted. HTML or text.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var string
	 */
	protected $format = 'html';

	/**
	 * The current step.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var null|Gravity_Flow_Step
	 */
	protected $step = null;

	/**
	 * The assignee.
	 *
	 * @since 1.7.1-dev
	 *
	 * @var null|Gravity_Flow_Assignee
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

	/**
	 * Gravity_Flow_Merge_Tag constructor.
	 *
	 * @since 1.7.1
	 *
	 * @param null|array $args The arguments used to initialize the class.
	 */
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

	/**
	 * Get an array of matches for the current merge tags pattern.
	 *
	 * @since 1.7.1
	 *
	 * @param string $text The text which may contain merge tags to be processed.
	 *
	 * @return array
	 */
	protected function get_matches( $text ) {

		$matches = array();

		preg_match_all( $this->regex, $text, $matches, PREG_SET_ORDER );

		return $matches;
	}

	/**
	 * Override this to replace the matches in the supplied text.
	 *
	 * @since 1.7.1
	 *
	 * @param string $text The text which may contain merge tags to be processed.
	 *
	 * @return WP_Error|string
	 */
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
