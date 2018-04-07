<?php
/**
 * Gravity Flow Discussion Field
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Field_Discussion
 */
class Gravity_Flow_Field_Discussion extends GF_Field_Textarea {

	/**
	 * The field type.
	 *
	 * @var string
	 */
	public $type = 'workflow_discussion';

	/**
	 * Input values are repopulated following validation errors, which is the desired behaviour.
	 * It also happened when an in progress user input step was redisplayed following a successful update, which is not desired.
	 * This is set to true in get_value_save_entry() and then set back to false after the value is cleared in get_field_input().
	 * 
	 * @var bool Should the input value be cleared?
	 */
	private $_clear_input_value = false;

	/**
	 * Adds the Workflow Fields group to the form editor.
	 *
	 * @param array $field_groups The properties for the field groups.
	 *
	 * @return array
	 */
	public function add_button( $field_groups ) {
		$field_groups = Gravity_Flow_Fields::maybe_add_workflow_field_group( $field_groups );

		return parent::add_button( $field_groups );
	}

	/**
	 * Returns the field button properties for the form editor.
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'workflow_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	/**
	 * Returns the class names of the settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'maxlen_setting',
			'size_setting',
			'rules_setting',
			'visibility_setting',
			'duplicate_setting',
			'default_value_textarea_setting',
			'placeholder_textarea_setting',
			'description_setting',
			'css_class_setting',
			'gravityflow_setting_discussion_timestamp_format',
			'rich_text_editor_setting',
		);
	}

	/**
	 * Returns the field title.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return __( 'Discussion', 'gravityflow' );
	}

	/**
	 * Return the entry value for display on the entries list page.
	 *
	 * @param string|array $value    The field value.
	 * @param array        $entry    The Entry Object currently being processed.
	 * @param string       $field_id The field or input ID currently being processed.
	 * @param array        $columns  The properties for the columns being displayed on the entry list page.
	 * @param array        $form     The Form Object currently being processed.
	 *
	 * @return string
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		$return = $value;
		if ( $return ) {
			$discussion = json_decode( $value, ARRAY_A );
			if ( is_array( $discussion ) ) {
				$item   = array_pop( $discussion );
				$return = $item['value'];
			}
		}

		return esc_html( $return );
	}

	/**
	 * Return the entry value which will replace the field merge tag.
	 *
	 * @param string       $value      The field value. Depending on the location the merge tag is being used the following functions may have already been applied to the value: esc_html, nl2br, and urlencode.
	 * @param string       $input_id   The field or input ID from the merge tag currently being processed.
	 * @param array        $entry      The Entry Object currently being processed.
	 * @param array        $form       The Form Object currently being processed.
	 * @param string       $modifier   The merge tag modifier. e.g. value.
	 * @param string|array $raw_value  The raw field value from before any formatting was applied to $value.
	 * @param bool         $url_encode Indicates if the urlencode function may have been applied to the $value.
	 * @param bool         $esc_html   Indicates if the esc_html function may have been applied to the $value.
	 * @param string       $format     The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param bool         $nl2br      Indicates if the nl2br function may have been applied to the $value.
	 *
	 * @return string
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		$value = $this->format_discussion_value( $raw_value, $format );

		return $value;
	}

	/**
	 * Return the entry value for display on the entry detail page and for the {all_fields} merge tag.
	 *
	 * @param string     $value    The field value.
	 * @param string     $currency The entry currency code.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param string     $format   The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param string     $media    The location where the value will be displayed. Possible values: screen or email.
	 *
	 * @return string
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		$value = $this->format_discussion_value( $value, $format );

		return $value;
	}

	/**
	 * Returns the field inner markup.
	 *
	 * @param array        $form  The Form Object currently being processed.
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param null|array   $entry Null or the Entry Object currently being edited.
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {
		$input          = '';
		$is_form_editor = $this->is_form_editor();

		if ( is_array( $entry ) || $is_form_editor ) {
			if ( $is_form_editor ) {
				$entry_value = json_encode( array(
					array(
						'id'           => 'example',
						'assignee_key' => 'example|John Doe',
						'timestamp'    => time(),
						'value'        => esc_attr__( 'Example comment.', 'gravityflow' ),
					),
				) );
			} else {
				$entry_value = rgar( $entry, $this->id );
			}

			$input = $this->format_discussion_value( $entry_value, 'html', rgar( $entry, 'id' ) );

			if ( $value == $entry_value || $this->_clear_input_value ) {
				$value                    = '';
				$this->_clear_input_value = false;
			}
		}

		$input .= parent::get_field_input( $form, $value, $entry );

		return $input;
	}

	/**
	 * Prepares the field entry value for output.
	 *
	 * @param string   $value    The entry value for the current field.
	 * @param string   $format   The requested format for the value; html or text.
	 * @param int|null $entry_id The ID of the entry currently being edited or null in other locations.
	 *
	 * @since 1.4.2-dev Added the $entry_id param.
	 * @since 1.3.2
	 *
	 * @return string
	 */
	public function format_discussion_value( $value, $format = 'html', $entry_id = null ) {
		$return     = '';
		$discussion = json_decode( $value, ARRAY_A );
		if ( is_array( $discussion ) ) {

			if ( $modifiers = $this->get_modifiers() ) {
				if ( in_array( 'first', $modifiers ) ) {
					$item = rgar( $discussion, 0 );

					return $this->format_discussion_item( $item, $format, $entry_id );
				} elseif ( in_array( 'latest', $modifiers ) ) {
					$item = end( $discussion );

					return $this->format_discussion_item( $item, $format, $entry_id );
				} else {
					$limit     = $this->get_limit_modifier();
					$has_limit = $limit > 0;
				}
			} else {
				$limit     = 0;
				$has_limit = false;
			}

			$reverse_comment_order = false;

			/**
			 * Allow the order of the discussion field comments to be reversed.
			 *
			 * @param bool                          $reverse_comment_order Should the comment order be reversed? Default is false.
			 * @param Gravity_Flow_Field_Discussion $this                  The field currently being processed.
			 * @param string                        $format                The requested format for the value; html or text.
			 *
			 * @since 1.4.2-dev
			 */
			$reverse_comment_order = apply_filters( 'gravityflow_reverse_comment_order_discussion_field', $reverse_comment_order, $this, $format );
			if ( $reverse_comment_order ) {
				$discussion = array_reverse( $discussion );
			}

			$count                 = 0;
			$recent_display_limit  = 0;
			$display_items         = '';
			$hidden_items          = '';

			/**
			 * Whether to show / hide the toggle to display more discussion items.
			 *
			 * @param boolean                       $hide_toggle Whether to prevent the display more toggle from displaying.
			 * @param Gravity_Flow_Field_Discussion $this        The field currently being processed.
			 *
			 * @since 2.0.2
			 */
			$display_toggle = apply_filters( 'gravityflow_discussion_items_display_toggle', true, $this );

			if ( ( ! $this->is_form_editor() && $display_toggle ) ) {
				/**
				 * Set the amount of discussion items to be shown in non-print inbox / status view when toggle is active.
				 *
				 * @param int                           $max_display_limit Amount of comments to be shown. Default is 10.
				 * @param Gravity_Flow_Field_Discussion $this              The field currently being processed.
				 *
				 * @since 1.9.2-dev
				 */
				$max_display_limit = apply_filters( 'gravityflow_discussion_items_display_limit', 10, $this );

				if ( count( $discussion ) > $max_display_limit ) {

					$recent_display_limit = count( $discussion ) - $max_display_limit;

					$view_more_label = esc_attr__( 'View More', 'gravityflow' );
					$view_less_label = esc_attr__( 'View Less', 'gravityflow' );

					if ( $format === 'html' ) {
						$return .= sprintf( "<a href='javascript:void(0);' title='%s' data-title='%s' onclick='GravityFlowEntryDetail.displayDiscussionItemToggle(%d, %d, %d);'  class='gravityflow-dicussion-item-toggle-display'>%s</a>", $view_more_label, $view_less_label, $this['formId'], $this['id'], $recent_display_limit, __( 'View More', 'gravityflow' ) );
					}
				}
			}

			foreach ( $discussion as $item ) {

				if ( $has_limit && $count === $limit ) {
					break;
				}

				if ( false === $this->is_form_editor() || $recent_display_limit > 0 ) {
					if ( $format === 'html' && $count >= $recent_display_limit ) {
						$display_items .= $this->format_discussion_item( $item, $format, $entry_id );
					} else {
						$hidden_items .= $this->format_discussion_item( $item, $format, $entry_id );
					}
				} else {
					$display_items .= $this->format_discussion_item( $item, $format, $entry_id );
				}

				$count ++;
			}

			if ( $format === 'html' ) {
				if ( ! empty( $hidden_items ) ) {
					$return .= '<div class="gravityflow-dicussion-item-hidden" style="display: none;">' . $hidden_items . '</div>' . $display_items;
				} else {
					$return .= $display_items;
				}
			} else {
				$return .= $hidden_items . $display_items;
			}
		}

		return $return;
	}

	/**
	 * Get the value of the limit modifier, if specified on the merge tag.
	 *
	 * @since 1.7.1-dev
	 *
	 * @return int The number of comments to return or 0 to return them all.
	 */
	public function get_limit_modifier() {
		$modifiers = shortcode_parse_atts( implode( ' ', $this->get_modifiers() ) );
		$limit     = rgar( $modifiers, 'limit', 0 );

		return absint( $limit );
	}

	/**
	 * Format a single discussion item for output.
	 *
	 * @param array    $item     The properties of the item to be processed.
	 * @param string   $format   The requested format for the value; html or text.
	 * @param int|null $entry_id The ID of the entry currently being edited or null in other locations.
	 *
	 * @since 1.7.1-dev
	 *
	 * @return string
	 */
	public function format_discussion_item( $item, $format, $entry_id ) {
		$item_datetime    = date( 'Y-m-d H:i:s', $item['timestamp'] );
		$timestamp_format = empty( $this->gravityflowDiscussionTimestampFormat ) ? 'd M Y g:i a' : $this->gravityflowDiscussionTimestampFormat;
		$date             = esc_html( GFCommon::format_date( $item_datetime, false, $timestamp_format, false ) );

		if ( $item['assignee_key'] ) {
			$assignee     = Gravity_Flow_Assignees::create( $item['assignee_key'] );
			$display_name = $assignee->get_display_name();
		} else {
			$display_name = '';
		}

		$return = '';

		$display_name = apply_filters( 'gravityflowdiscussion_display_name_discussion_field', $display_name, $item, $this );
		if ( $format === 'html' ) {
			$content = sprintf( '<div class="gravityflow-dicussion-item-header">
<span class="gravityflow-dicussion-item-name">%s</span> <span class="gravityflow-dicussion-item-date">%s</span>
%s</div>
<div class="gravityflow-dicussion-item-value">
%s
</div>', $display_name, $date, $this->get_delete_button( $item['id'], $entry_id ), $this->format_comment_value( $item['value'] ) );

			$return .= sprintf( '<div id="gravityflow-discussion-item-%s" class="gravityflow-discussion-item">%s</div>', sanitize_key( $item['id'] ), $content );

		} elseif ( $format === 'text' ) {
			$return = $date . ': ' . $display_name . "\n";
			$return .= $item['value'];
		}

		return $return;
	}

	/**
	 * Prepares the markup for the delete comment button when on the entry detail edit page.
	 *
	 * @param string $item_id  The ID of the comment currently being processed.
	 * @param int    $entry_id The ID of the entry currently being processed.
	 *
	 * @since 1.4.2-dev
	 *
	 * @return string
	 */
	public function get_delete_button( $item_id, $entry_id ) {
		if ( ! $this->is_entry_detail_edit() ) {
			return '';
		}

		$label = esc_attr__( 'Delete Comment', 'gravityflow' );
		$file  = GFCommon::get_base_url() . '/images/delete.png';

		return sprintf( "<a href='javascript:void(0);' title='%s' onclick='deleteDiscussionItem(%d, %d, %s);'><img src='%s' alt='%s' style='margin-left:8px;'/></a>", $label, $entry_id, $this->id, json_encode( $item_id ), $file, $label );
	}

	/**
	 * Formats an individual comment value for output in a location using the HTML format.
	 *
	 * @param string $value The comment value.
	 *
	 * @since 1.4.2-dev
	 *
	 * @return string
	 */
	public function format_comment_value( $value ) {
		$allowable_tags = $this->get_allowable_tags();

		if ( $allowable_tags === false ) {
			// The value is unsafe so encode the value.
			$value  = esc_html( $value );
			$return = nl2br( $value );

		} else {
			// The value contains HTML but the value was sanitized before saving.
			$return = wpautop( $value );
		}

		return $return;
	}

	/**
	 * Format the value for saving to the Entry Object.
	 *
	 * @param array|string $value      The value to be saved.
	 * @param array        $form       The Form Object currently being processed.
	 * @param string       $input_name The input name used when accessing the $_POST.
	 * @param int          $entry_id   The ID of the Entry currently being processed.
	 * @param array        $entry      The Entry Object currently being processed.
	 *
	 * @return string
	 */
	public function get_value_save_entry( $value, $form, $input_name, $entry_id, $entry ) {
		$value = $this->sanitize_entry_value( $value, $form['id'] );

		if ( $entry_id ) {
			$entry               = GFAPI::get_entry( $entry_id );
			$previous_value_json = rgar( $entry, $this->id );
			$assignee_key        = gravity_flow()->get_current_user_assignee_key();

			$new_comment = array(
				'id'             => uniqid( '', true ),
				'assignee_key' => $assignee_key,
				'timestamp'    => time(),
				'value'        => $value,
			);
			if ( empty( $previous_value_json ) ) {
				if ( ! empty( $value ) ) {
					$value = json_encode( array( $new_comment ) );
				}
			} else {
				$discussion = json_decode( $previous_value_json, ARRAY_A );
				if ( ! empty( $value ) ) {
					// Only add the comment to the discussion if a value was submitted.
					if ( is_array( $discussion ) ) {
						$discussion[] = $new_comment;
					} else {
						$discussion = array( $new_comment );
					}
				}
				$value = json_encode( $discussion );
			}

			$this->_clear_input_value = true;
		}

		return $value;
	}

	/**
	 * Format the entry value before it is used in entry exports and by framework add-ons using GFAddOn::get_field_value().
	 *
	 * @param array      $entry    The entry currently being processed.
	 * @param string     $input_id The field or input ID.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param bool|false $is_csv   Indicates if the value is going to be used in the .csv entries export.
	 *
	 * @return string
	 */
	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		return $this->format_discussion_value( rgar( $entry, $input_id ), 'text' );
	}

	/**
	 * Sanitize the field settings when the form is saved.
	 */
	public function sanitize_settings() {
		parent::sanitize_settings();
		if ( ! empty( $this->gravityflowDiscussionTimestampFormat ) ) {
			$this->gravityflowDiscussionTimestampFormat = sanitize_text_field( $this->gravityflowDiscussionTimestampFormat );
		}
	}

	/**
	 * Deletes the specified comment and updates the entry in the database.
	 *
	 * @param array  $entry   The entry containing the comment to be deleted.
	 * @param string $item_id The ID of the comment to be deleted.
	 *
	 * @since 1.4.2-dev
	 *
	 * @return array|bool
	 */
	public function delete_discussion_item( $entry, $item_id ) {
		$discussion = json_decode( rgar( $entry, $this->id ), ARRAY_A );
		if ( ! is_array( $discussion ) ) {
			return false;
		}

		$item_found = false;

		foreach ( $discussion as $key => $item ) {
			if ( $item['id'] == $item_id ) {
				$item_found = true;
				unset( $discussion[ $key ] );
				break;
			}
		}

		if ( ! $item_found ) {
			return false;
		}

		$discussion = ! empty( $discussion ) ? json_encode( array_values( $discussion ) ) : '';

		return GFAPI::update_entry_field( $entry['id'], $this->id, $discussion );
	}

	/**
	 * Target of the wp_ajax_gravityflow_delete_discussion_item hook; handles the ajax request to delete a comment.
	 *
	 * @since 1.4.2-dev
	 */
	public static function ajax_delete_discussion_item() {
		check_ajax_referer( 'gravityflow_delete_discussion_item', 'gravityflow_delete_discussion_item' );

		$entry_id = absint( $_POST['entry_id'] );
		$entry    = GFAPI::get_entry( $entry_id );

		if ( is_wp_error( $entry ) ) {
			die();
		}

		$form     = GFAPI::get_form( $entry['form_id'] );
		$field_id = absint( $_POST['field_id'] );
		$field    = GFFormsModel::get_field( $form, $field_id );

		if ( ! $field instanceof Gravity_Flow_Field_Discussion ) {
			die();
		}

		$item_id = $_POST['item_id'];
		$result  = $field->delete_discussion_item( $entry, $item_id );

		die( $result === true ? sanitize_key( $item_id ) : false );
	}

	/**
	 * Target of the gform_entry_detail hook; includes the script for the delete comment link.
	 *
	 * @since 1.4.2-dev
	 */
	public static function delete_discussion_item_script() {
		if ( GFCommon::is_entry_detail_edit() ) {
			?>

			<script type="text/javascript">
				function deleteDiscussionItem(entryId, fieldId, itemId) {

					if (!confirm(<?php echo json_encode( __( "Would you like to delete this comment? 'Cancel' to stop. 'OK' to delete", 'gravityflow' ) ); ?>))
						return;

					jQuery.post(ajaxurl, {
						entry_id: entryId,
						field_id: fieldId,
						item_id: itemId,
						action: 'gravityflow_delete_discussion_item',
						gravityflow_delete_discussion_item: '<?php echo wp_create_nonce( 'gravityflow_delete_discussion_item' ); ?>'
					}, function (response) {
						if (response) {
							jQuery('#gravityflow-discussion-item-' + response).remove();
						} else {
							alert(<?php echo json_encode( __( 'There was an issue deleting this comment.', 'gravityflow' ) ); ?>)
						}
					});
				}
			</script>

			<?php
		}
	}
}

GF_Fields::register( new Gravity_Flow_Field_Discussion() );
