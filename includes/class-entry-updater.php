<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Gravity Flow Entry Updater
 *
 * @package     GravityFlow
 * @subpackage  Classes/Gravity_Flow_Entry_Updater
 * @copyright   Copyright (c) 2015-2017, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8.1-dev
 */
class Gravity_Flow_Entry_Updater {

	/**
	 * The form associated with the current entry.
	 *
	 * @since 1.8.1-dev
	 *
	 * @var array|null
	 */
	private $_form;

	/**
	 * The current step for the entry being updated.
	 *
	 * @since 1.8.1-dev
	 *
	 * @var Gravity_Flow_Step|null
	 */
	private $_step;

	/**
	 * The entry to be updated.
	 *
	 * @since 1.8.1-dev
	 *
	 * @var array|null
	 */
	private $_entry;

	/**
	 * An array of field IDs which the user can edit.
	 *
	 * @since 1.8.1-dev
	 *
	 * @var array|null
	 */
	private $_editable_fields;

	/**
	 * An array of post field IDs to be used when updating the post created from the entry.
	 *
	 * @since 1.8.1-dev
	 *
	 * @var array
	 */
	private $_update_post_fields = array();

	/**
	 * An array of post image field IDs to be used when updating the post created from the entry.
	 *
	 * @since 1.8.1-dev
	 *
	 * @var array
	 */
	private $_update_post_images = array();

	/**
	 * Gravity_Flow_Entry_Updater constructor.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param array             $form  The form associated with the current entry.
	 * @param Gravity_Flow_Step $step  The current step for the entry being updated.
	 */
	public function __construct( $form, $step ) {
		$this->_form            = gf_apply_filters( array( 'gform_pre_validation', $form['id'] ), $form );
		$this->_step            = $step;
		$this->_entry           = $step->get_entry();
		$this->_editable_fields = $step->get_editable_fields();
	}

	/**
	 * Add a message to the Gravity Flow log file.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param string $message The message to be logged.
	 */
	public function log_debug( $message ) {
		$this->_step->log_debug( $message );
	}

	/**
	 * Flushes and reloads the cached entry for the current step.
	 *
	 * @since 1.8.1-dev
	 */
	public function refresh_entry() {
		$this->_entry = $this->_step->refresh_entry();
	}

	/**
	 * Process the entry detail page submission.
	 *
	 * Validates the note and editable fields.
	 * Updates the entry and post using the submitted values.
	 *
	 * @param string $new_status The new status for the current step.
	 *
	 * @since 1.8.1-dev
	 *
	 * @return bool|WP_Error WP_Error for an invalid note and/or editable fields; False for an invalid referer or true when the entry/post update functionality has run.
	 */
	public function process( $new_status ) {
		$this->log_debug( __METHOD__ . '(): Running.' );

		if ( ! $this->is_valid_referer() ) {
			$this->log_debug( __METHOD__ . '(): Aborting; Invalid referer.' );

			return false;
		}

		$files = $this->get_files_pre_validation();
		$form  = $this->_form;

		$is_valid          = $this->_step->validate_note( $new_status, $form );
		$is_valid          = $this->is_valid_editable_fields( $is_valid, $form );
		$validation_result = $this->_step->get_validation_result( $is_valid, $form, $new_status );

		if ( is_wp_error( $validation_result ) ) {
			// Upload valid temp single files.
			$this->maybe_upload_files( $form, $files );
			$this->log_debug( __METHOD__ . '(): Aborting; Failed validation.' );

			return $validation_result;
		}

		if ( empty( $this->_editable_fields ) ) {
			$this->log_debug( __METHOD__ . '(): Aborting; No editable fields.' );

			return true;
		}

		$original_entry     = $this->_entry;
		$previous_assignees = $this->_step->get_assignees();

		$this->process_fields();

		remove_action( 'gform_after_update_entry', array( gravity_flow(), 'filter_after_update_entry' ) );
		do_action( 'gform_after_update_entry', $form, $original_entry['id'], $original_entry );
		do_action( "gform_after_update_entry_{$form['id']}", $form, $original_entry['id'], $original_entry );

		$entry = GFFormsModel::get_lead( $original_entry['id'] );
		GFFormsModel::set_entry_meta( $entry, $form );

		$this->refresh_entry();
		$this->maybe_process_post_fields();

		GFCache::flush();

		$this->_step->maybe_adjust_assignment( $previous_assignees );

		return true;
	}

	/**
	 * Check the submission belongs to the current step and contains a valid nonce from the entry detail page.
	 *
	 * @since 1.8.1-dev
	 *
	 * @return bool
	 */
	public function is_valid_referer() {
		return isset( $_POST['gforms_save_entry'] ) && rgpost( 'step_id' ) == $this->_step->get_id() && check_admin_referer( 'gforms_save_entry', 'gforms_save_entry' );
	}

	/**
	 * Get an array of uploaded files for the current submission.
	 *
	 * @since 1.8.1-dev
	 *
	 * @return array
	 */
	public function get_files_pre_validation() {
		if ( empty( $this->_editable_fields ) ) {
			return array();
		}

		$files = GFCommon::json_decode( rgpost( 'gform_uploaded_files' ) );
		if ( ! is_array( $files ) ) {
			$files = array();
		}

		GFFormsModel::$uploaded_files[ $this->_form['id'] ] = $files;

		return $files;
	}

	/**
	 * Determine if the editable fields for this step are valid.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param bool  $valid The steps current validation state.
	 * @param array $form  The form currently being processed.
	 *
	 * @return bool
	 */
	public function is_valid_editable_fields( $valid, &$form ) {
		if ( empty( $this->_editable_fields ) ) {
			return $valid;
		}

		$this->log_debug( __METHOD__ . '(): Running.' );

		$conditional_logic_enabled           = gravity_flow()->fields_have_conditional_logic( $form ) && $this->_step->conditional_logic_editable_fields_enabled;
		$page_load_conditional_logic_enabled = $conditional_logic_enabled && $this->_step->conditional_logic_editable_fields_mode == 'page_load';
		$dynamic_conditional_logic_enabled   = $conditional_logic_enabled && $this->_step->conditional_logic_editable_fields_mode != 'page_load';

		$editable_fields = $this->_editable_fields;
		$saved_entry     = $this->_entry;

		if ( ! $conditional_logic_enabled || $page_load_conditional_logic_enabled ) {
			$entry = $saved_entry;
		} else {
			$entry = GFFormsModel::create_lead( $form );
		}

		foreach ( $form['fields'] as $field ) {
			/* @var GF_Field $field */
			if ( in_array( $field->id, $editable_fields ) ) {
				if ( ( $dynamic_conditional_logic_enabled && GFFormsModel::is_field_hidden( $form, $field, array() ) ) ) {
					continue;
				}

				$submission_is_empty = $field->is_value_submission_empty( $form['id'] );

				if ( $field->get_input_type() == 'fileupload' ) {

					if ( $field->isRequired && $submission_is_empty && rgempty( $field->id, $saved_entry ) ) {
						$field->failed_validation  = true;
						$field->validation_message = empty( $field->errorMessage ) ? esc_html__( 'This field is required.', 'gravityflow' ) : $field->errorMessage;
						$valid                     = false;

						continue;
					}

					$field->validate( '', $form );
					if ( $field->failed_validation ) {
						$valid = false;
					}

					continue;
				}

				if ( $page_load_conditional_logic_enabled ) {
					$field_is_hidden = GFFormsModel::is_field_hidden( $form, $field, array(), $entry );
				} elseif ( $dynamic_conditional_logic_enabled ) {
					$field_is_hidden = GFFormsModel::is_field_hidden( $form, $field, array() );
				} else {
					$field_is_hidden = false;
				}

				if ( ! $field_is_hidden && $submission_is_empty && $field->isRequired ) {
					$field->failed_validation  = true;
					$field->validation_message = empty( $field->errorMessage ) ? esc_html__( 'This field is required.', 'gravityflow' ) : $field->errorMessage;
					$valid                     = false;
				} elseif ( ! $field_is_hidden && ! $submission_is_empty ) {
					$value = GFFormsModel::get_field_value( $field );

					$field->validate( $value, $form );
					$custom_validation_result = gf_apply_filters( array(
						'gform_field_validation',
						$form['id'],
						$field->id
					), array(
						'is_valid' => $field->failed_validation ? false : true,
						'message'  => $field->validation_message,
					), $value, $form, $field );

					$field->failed_validation  = rgar( $custom_validation_result, 'is_valid' ) ? false : true;
					$field->validation_message = rgar( $custom_validation_result, 'message' );

					if ( $field->failed_validation ) {
						$valid = false;
					}
				}
			}
		}

		return $valid;
	}

	/**
	 * Determines if there are any fields which need files uploading to the temporary folder.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param array $form  The form currently being processed.
	 * @param array $files An array of files which have already been uploaded.
	 */
	public function maybe_upload_files( $form, $files ) {
		if ( empty( $this->_editable_fields ) || empty( $_FILES ) ) {
			return;
		}

		$this->log_debug( __METHOD__ . '(): Checking for fields to process.' );

		$target_path = $this->get_temp_files_path( $form['id'] );

		foreach ( $form['fields'] as $field ) {

			/** @var GF_Field $field */
			if ( ! in_array( $field->id, $this->_editable_fields )
			     || ! in_array( $field->get_input_type(), array( 'fileupload', 'post_image' ) )
			     || $field->multipleFiles
			     || $field->failed_validation
			) {
				// Skip fields which are not editable, are the wrong type, or have failed validation.
				continue;
			}

			$files = $this->maybe_upload_temp_file( $field, $files, $target_path );
		}

		GFFormsModel::$uploaded_files[ $form['id'] ] = $files;
	}

	/**
	 * Get the temporary file path and create the folder if it does not already exist.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param int $form_id The ID of the form currently being processed.
	 *
	 * @return string
	 */
	public function get_temp_files_path( $form_id ) {
		$form_upload_path = GFFormsModel::get_upload_path( $form_id );
		$target_path      = $form_upload_path . '/tmp/';

		wp_mkdir_p( $target_path );
		GFCommon::recursive_add_index_file( $form_upload_path );

		return $target_path;
	}

	/**
	 * Upload the file to the temporary folder for the current field.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param GF_Field $field       The field properties.
	 * @param array    $files       An array of files which have already been uploaded.
	 * @param string   $target_path The path to the tmp folder the file should be moved to.
	 *
	 * @return array
	 */
	public function maybe_upload_temp_file( $field, $files, $target_path ) {
		$input_name = "input_{$field->id}";

		if ( empty( $_FILES[ $input_name ]['name'] ) ) {
			return $files;
		}

		$file_info = GFFormsModel::get_temp_filename( $field->formId, $input_name );
		$this->log_debug( __METHOD__ . "(): Uploading temporary file for field: {$field->label}({$field->id} - {$field->type}). File info => " . print_r( $file_info, true ) );

		if ( $file_info && move_uploaded_file( $_FILES[ $input_name ]['tmp_name'], $target_path . $file_info['temp_filename'] ) ) {
			GFFormsModel::set_permissions( $target_path . $file_info['temp_filename'] );
			$files[ $input_name ] = $file_info['uploaded_filename'];
			$this->log_debug( __METHOD__ . '(): File uploaded successfully.' );
		} else {
			$this->log_debug( __METHOD__ . "(): File could not be uploaded: tmp_name: {$_FILES[ $input_name ]['tmp_name']} - target location: " . $target_path . $file_info['temp_filename'] );
		}

		return $files;
	}

	/**
	 * Trigger the updating of entry values for the appropriate fields.
	 *
	 * @since 1.8.1-dev
	 */
	public function process_fields() {
		$entry = $this->_entry;

		if ( ! $entry ) {
			return;
		}

		$this->log_debug( __METHOD__ . '(): Running.' );

		$form               = $this->_form;
		$total_fields       = array();
		$calculation_fields = array();

		foreach ( $form['fields'] as $field ) {
			/** @var GF_Field $field */

			//Ignore fields that are marked as display only
			if ( $field->displayOnly && $field->type != 'password' ) {
				continue;
			}

			//process total field after all fields have been saved
			if ( $field->type == 'total' ) {
				$total_fields[] = $field;
				continue;
			}

			// process calculation fields after all fields have been saved (moved after the is hidden check)
			if ( $field->has_calculation() ) {
				$calculation_fields[] = $field;
				continue;
			}

			if ( ! in_array( $field->id, $this->_editable_fields ) ) {
				continue;
			}

			if ( ! $this->_step->conditional_logic_editable_fields_enabled ) {
				$field->conditionalLogic = null;
			}

			if ( in_array( $field->get_input_type(), array( 'fileupload', 'post_image' ) ) ) {
				$this->maybe_save_field_files( $field, $form, $entry );
				continue;
			}

			if ( $field->type == 'post_category' ) {
				$field = GFCommon::add_categories_as_choices( $field, '' );
			}

			$inputs = $field->get_entry_inputs();

			if ( is_array( $inputs ) ) {
				foreach ( $inputs as $input ) {
					$this->maybe_update_input( $form, $field, $entry, $input['id'] );
				}
			} else {
				$this->maybe_update_input( $form, $field, $entry, $field->id );
			}
		}

		$this->maybe_process_calculation_fields( $calculation_fields, $entry );
		$this->maybe_process_total_fields( $total_fields );
	}

	/**
	 * Update the entry with the calculation field values.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param GF_Field[] $fields The calculation fields to be processed.
	 * @param array      $entry  The entry being updated.
	 */
	public function maybe_process_calculation_fields( $fields, $entry ) {
		if ( empty( $fields ) ) {
			return;
		}

		$this->log_debug( __METHOD__ . '(): Saving calculation fields.' );

		foreach ( $fields as $field ) {
			// Make sure that the value gets recalculated
			$field->conditionalLogic = null;

			$inputs = $field->get_entry_inputs();

			if ( is_array( $inputs ) ) {

				if ( ! in_array( $field->id, $this->_editable_fields ) ) {
					// Make sure calculated product names and quantities are saved as if they're submitted.
					$value                                 = array( $field->id . '.1' => $entry[ $field->id . '.1' ] );
					$_POST[ 'input_' . $field->id . '_1' ] = $field->get_field_label( false, $value );
					$quantity                              = trim( $entry[ $field->id . '.3' ] );
					if ( $field->disableQuantity && empty( $quantity ) ) {
						$_POST[ 'input_' . $field->id . '_3' ] = 1;
					} else {
						$_POST[ 'input_' . $field->id . '_3' ] = $quantity;
					}
				}
				foreach ( $inputs as $input ) {
					$this->maybe_update_input( $this->_form, $field, $entry, $input['id'] );
				}
			} else {
				$this->maybe_update_input( $this->_form, $field, $entry, $field->id );
			}
		}
	}

	/**
	 * Update the entry with the total field values.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param GF_Field[] $fields The total fields to be processed.
	 */
	public function maybe_process_total_fields( $fields ) {
		$entry = GFFormsModel::get_lead( $this->_entry['id'] );
		GFFormsModel::refresh_product_cache( $this->_form, $entry );

		if ( empty( $fields ) ) {
			return;
		}

		$this->log_debug( __METHOD__ . '(): Saving total fields.' );

		foreach ( $fields as $field ) {
			$this->maybe_update_input( $this->_form, $field, $entry, $field->id );
		}
	}

	/**
	 * If any new files have been uploaded save them to the entry.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param GF_Field $field The current fields properties.
	 * @param array    $form  The form currently being processed.
	 * @param array    $entry The entry currently being processed.
	 */
	public function maybe_save_field_files( $field, $form, $entry ) {
		$input_name = 'input_' . $field->id;
		if ( $field->multipleFiles && ! isset( GFFormsModel::$uploaded_files[ $form['id'] ][ $input_name ] ) ) {
			// No new files uploaded, abort.
			return;
		}

		$existing_value = rgar( $entry, $field->id );
		$value          = $field->get_value_save_entry( $existing_value, $form, $input_name, $entry['id'], $entry );

		if ( ! empty( $value ) && $existing_value != $value ) {
			$result = GFAPI::update_entry_field( $entry['id'], $field->id, $value );
			$this->log_debug( __METHOD__ . "(): Saving: {$field->label}(#{$field->id} - {$field->type}). Result: " . var_export( $result, 1 ) );

			if ( GFCommon::is_post_field( $field ) && ! in_array( $field->id, $this->_update_post_images ) ) {
				$this->_update_post_images[] = $field->id;

				$post_images = gform_get_meta( $entry['id'], '_post_images' );
				if ( $post_images && isset( $post_images[ $field->id ] ) ) {
					wp_delete_attachment( $post_images[ $field->id ] );
					unset( $post_images[ $field->id ] );
					gform_update_meta( $entry['id'], '_post_images', $post_images, $form['id'] );
				}
			}
		}
	}

	/**
	 * Update the input value in the entry.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param array      $form     The form currently being processed.
	 * @param GF_Field   $field    The current fields properties.
	 * @param array      $entry    The entry currently being processed.
	 * @param int|string $input_id The ID of the field or input currently being processed.
	 */
	public function maybe_update_input( $form, $field, &$entry, $input_id ) {
		$input_name = 'input_' . str_replace( '.', '_', $input_id );

		if ( $field->enableCopyValuesOption && rgpost( 'input_' . $field->id . '_copy_values_activated' ) ) {
			$source_field_id   = $field->copyValuesOptionField;
			$source_input_name = str_replace( 'input_' . $field->id, 'input_' . $source_field_id, $input_name );
			$value             = rgpost( $source_input_name );
		} else {
			$value = rgpost( $input_name );
		}

		$existing_value = rgar( $entry, $input_id );
		$value          = GFFormsModel::maybe_trim_input( $value, $form['id'], $field );
		$value          = GFFormsModel::prepare_value( $form, $field, $value, $input_name, $entry['id'], $entry );

		if ( $existing_value != $value ) {
			$result = GFAPI::update_entry_field( $entry['id'], $input_id, $value );
			$this->log_debug( __METHOD__ . "(): Saving: {$field->label}(#{$input_id} - {$field->type}). Result: " . var_export( $result, 1 ) );
			if ( $result ) {
				$entry[ $input_id ] = $value;
			}

			if ( GFCommon::is_post_field( $field ) && ! in_array( $field->id, $this->_update_post_fields ) ) {
				$this->_update_post_fields[] = $field->id;
			}
		}
	}

	/**
	 * If a post exists for this entry initiate the update.
	 *
	 * @since 1.8.1-dev
	 */
	public function maybe_process_post_fields() {
		$this->log_debug( __METHOD__ . '(): Running.' );

		$post_id = $this->_entry['post_id'];

		if ( empty( $post_id ) ) {
			$this->log_debug( __METHOD__ . '(): Aborting; No post id.' );

			return;
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			$this->log_debug( __METHOD__ . '(): Aborting; Unable to get post.' );

			return;
		}

		$result = $this->process_post_fields( $post );
		$this->log_debug( __METHOD__ . '(): wp_update_post result => ' . print_r( $result, 1 ) );
	}

	/**
	 * Update the post with the field values which have changed.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param WP_Post $post The post to be updated.
	 *
	 * @return int|WP_Error
	 */
	public function process_post_fields( $post ) {
		$this->log_debug( __METHOD__ . '(): Running.' );

		$form                 = $this->_form;
		$entry                = $this->_entry;
		$post_images          = $this->process_post_images( $form, $entry );
		$has_content_template = rgar( $form, 'postContentTemplateEnabled' );

		foreach ( $this->_update_post_fields as $field_id ) {

			$field = GFFormsModel::get_field( $form, $field_id );
			$value = GFFormsModel::get_lead_field_value( $entry, $field );

			switch ( $field->type ) {
				case 'post_title' :
					$post_title       = $this->get_post_title( $value, $form, $entry, $post_images );
					$post->post_title = $post_title;
					$post->post_name  = $post_title;
					break;

				case 'post_content' :
					if ( ! $has_content_template ) {
						$post->post_content = GFCommon::encode_shortcodes( $value );
					}
					break;

				case 'post_excerpt' :
					$post->post_excerpt = GFCommon::encode_shortcodes( $value );
					break;

				case 'post_tags' :
					$this->set_post_tags( $value, $post->ID );
					break;

				case 'post_category' :
					$this->set_post_categories( $value, $post->ID );
					break;

				case 'post_custom_field' :
					$this->set_post_meta( $field, $value, $form, $entry, $post_images );
					break;
			}
		}

		if ( $has_content_template ) {
			$post->post_content = GFFormsModel::process_post_template( $form['postContentTemplate'], 'post_content', $post_images, array(), $form, $entry );
		}

		return wp_update_post( $post, true );
	}

	/**
	 * Attach any new images to the post and set the featured image.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param array $form  The form currently being processed.
	 * @param array $entry The entry currently being processed.
	 *
	 * @return array
	 */
	public function process_post_images( $form, $entry ) {
		$this->log_debug( __METHOD__ . '(): Running.' );

		$post_id     = $entry['post_id'];
		$post_images = gform_get_meta( $entry['id'], '_post_images' );
		if ( ! $post_images ) {
			$post_images = array();
		}

		foreach ( $this->_update_post_images as $field_id ) {
			$value = rgar( $entry, $field_id );
			list( $url, $title, $caption, $description ) = rgexplode( '|:|', $value, 4 );

			if ( empty( $url ) ) {
				continue;
			}

			$image_meta = array(
				'post_excerpt' => $caption,
				'post_content' => $description,
			);

			// Adding title only if it is not empty. It will default to the file name if it is not in the array.
			if ( ! empty( $title ) ) {
				$image_meta['post_title'] = $title;
			}

			$media_id = GFFormsModel::media_handle_upload( $url, $post_id, $image_meta );

			if ( $media_id ) {
				$post_images[ $field_id ] = $media_id;

				// Setting the featured image.
				$field = RGFormsModel::get_field( $form, $field_id );
				if ( $field && $field->postFeaturedImage ) {
					$result = set_post_thumbnail( $post_id, $media_id );
				}
			}

		}

		if ( ! empty( $post_images ) ) {
			gform_update_meta( $entry['id'], '_post_images', $post_images, $form['id'] );
		}

		return $post_images;
	}

	/**
	 * Get the post title.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param string $value       The entry field value.
	 * @param array  $form        The form currently being processed.
	 * @param array  $entry       The entry currently being processed.
	 * @param array  $post_images The images which have been attached to the post.
	 *
	 * @return string
	 */
	public function get_post_title( $value, $form, $entry, $post_images ) {
		if ( rgar( $form, 'postTitleTemplateEnabled' ) ) {
			return GFFormsModel::process_post_template( $form['postTitleTemplate'], 'post_title', $post_images, array(), $form, $entry );
		}

		return GFCommon::encode_shortcodes( $value );
	}

	/**
	 * Set the post tags.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param string|array $value   The entry field value.
	 * @param int          $post_id The ID of the post created from the current entry.
	 */
	public function set_post_tags( $value, $post_id ) {
		$post_tags = array( $value ) ? array_values( $value ) : explode( ',', $value );

		wp_set_post_tags( $post_id, $post_tags, false );
	}

	/**
	 * Set the post categories.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param string|array $value   The entry field value.
	 * @param int          $post_id The ID of the post created from the current entry.
	 */
	public function set_post_categories( $value, $post_id ) {
		$post_categories = array();

		foreach ( explode( ',', $value ) as $cat_string ) {
			$cat_array = explode( ':', $cat_string );
			// the category id is the last item in the array, access it using end() in case the category name includes colons.
			array_push( $post_categories, end( $cat_array ) );
		}

		wp_set_post_categories( $post_id, $post_categories, false );
	}

	/**
	 * Set the post meta.
	 *
	 * @since 1.8.1-dev
	 *
	 * @param GF_Field     $field       The Post Custom Field.
	 * @param string|array $value       The entry field value.
	 * @param array        $form        The form currently being processed.
	 * @param array        $entry       The entry currently being processed.
	 * @param array        $post_images The images which have been attached to the post.
	 */
	public function set_post_meta( $field, $value, $form, $entry, $post_images ) {
		$post_id = $entry['post_id'];

		delete_post_meta( $post_id, $field->postCustomFieldName );

		if ( ! empty( $field->customFieldTemplateEnabled ) ) {
			$value = GFFormsModel::process_post_template( $field->customFieldTemplate, 'post_custom_field', $post_images, array(), $form, $entry );
		}

		switch ( $field->inputType ) {
			case 'list' :
				$value = maybe_unserialize( $value );
				if ( is_array( $value ) ) {
					foreach ( $value as $item ) {
						if ( is_array( $item ) ) {
							$item = implode( '|', $item );
						}

						if ( ! rgblank( $item ) ) {
							add_post_meta( $post_id, $field->postCustomFieldName, $item );
						}
					}
				}
				break;

			case 'multiselect' :
			case 'checkbox' :
				$value = ! is_array( $value ) ? explode( ',', $value ) : $value;
				foreach ( $value as $item ) {
					if ( ! rgblank( $item ) ) {
						add_post_meta( $post_id, $field->postCustomFieldName, $item );
					}
				}
				break;

			case 'date' :
				$value = GFCommon::date_display( $value, $field->dateFormat );
				if ( ! rgblank( $value ) ) {
					add_post_meta( $post_id, $field->postCustomFieldName, $value );
				}
				break;

			default :
				if ( ! rgblank( $value ) ) {
					add_post_meta( $post_id, $field->postCustomFieldName, $value );
				}
				break;
		}
	}

}