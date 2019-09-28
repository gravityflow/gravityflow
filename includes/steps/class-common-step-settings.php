<?php
/**
 * Gravity Flow Common Step Settings Functions
 *
 * @package     GravityFlow
 * @copyright   Copyright (c) 2015-2018, Steven Henty S.L.
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5.1-dev
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Gravity_Flow_Common_Step_Settings
 */
class Gravity_Flow_Common_Step_Settings {

	/**
	 * Choices array of assignees.
	 *
	 * @var array
	 */
	private $_account_choices = array();

	/**
	 * Choices array of active Gravity PDF feeds.
	 *
	 * @var array
	 */
	private $_gpdf_choices = array();

	/**
	 * Gravity_Flow_Common_Step_Settings constructor.
	 */
	public function __construct() {
		$this->_account_choices = gravity_flow()->get_users_as_choices();
		$this->set_gpdf_choices();
	}

	/**
	 * If Gravity PDF 4 is active prepare a choices array of active PDF feeds for the current form.
	 *
	 * @since 1.5.1-dev
	 */
	public function set_gpdf_choices() {
		if ( method_exists( 'GPDFAPI', 'get_form_pdfs' ) ) {
			$form_id    = rgget( 'id' );
			$gpdf_feeds = GPDFAPI::get_form_pdfs( $form_id );

			if ( ! is_wp_error( $gpdf_feeds ) ) {

				/* Format the PDFs in the appropriate format for use in a select field */
				foreach ( $gpdf_feeds as $gpdf_feed ) {
					if ( true === $gpdf_feed['active'] ) {
						$this->_gpdf_choices[] = array(
							'label' => $gpdf_feed['name'],
							'name'  => 'notification_gravitypdf_' . $gpdf_feed['id'],
						);
					}
				}

			}
		}
	}

	/**
	 * Get the choices array for the type setting.
	 *
	 * @since 1.5.1-dev
	 *
	 * @return array
	 */
	public function get_type_choices() {
		return array(
			array( 'label' => __( 'Select', 'gravityflow' ), 'value' => 'select' ),
			array( 'label' => __( 'Conditional Routing', 'gravityflow' ), 'value' => 'routing' ),
		);
	}

	/**
	 * Get the enable notification field.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $config The notification settings properties.
	 *
	 * @return array
	 */
	public function get_notification_enabled_field( $config ) {
		return array(
			array(
				'name'    => $config['name_prefix'] . '_notification_enabled',
				'label'   => $config['label'],
				'tooltip' => $config['tooltip'],
				'type'    => 'checkbox',
				'choices' => array(
					array(
						'label'         => $config['checkbox_label'],
						'tooltip'       => $config['checkbox_tooltip'],
						'name'          => $config['name_prefix'] . '_notification_enabled',
						'default_value' => $config['checkbox_default_value'],
					),
				),
			),
		);
	}

	/**
	 * Get the notification "Send To" settings.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $config The notification settings properties.
	 *
	 * @return array
	 */
	public function get_notification_send_to_fields( $config ) {
		if ( ! rgar( $config, 'send_to_fields' ) ) {
			return array();
		}

		$prefix = rgar( $config, 'name_prefix' );

		$total_accounts = Gravity_Flow_Common::get_total_accounts();
		$args           = Gravity_Flow_Common::get_users_args();
		$number         = ( isset( $args['number'] ) && $args['number'] > 0 ) ? $args['number'] : 2000;
		/* translators: 1: Warning icon 2: Number of users displayed 3: Open link tag 4: Close link tag */
		$description = '<span class="gf_settings_description">' . sprintf( esc_html__( '%1$s The list of users contains only the first %2$s users in your site. %3$sLearn how to remove this limit%4$s. ', 'gravityflow' ), '<i class="dashicons dashicons-warning" style="color:red;"></i> ', $number, '<a href="https://docs.gravityflow.io/article/54-gravityflowgetusersargs" target="_blank">', '</a>' ) . '</span>';

		return array(
			array(
				'name'          => $prefix . '_notification_type',
				'label'         => __( 'Send To', 'gravityflow' ),
				'type'          => 'radio',
				'default_value' => 'select',
				'horizontal'    => true,
				'choices'       => $this->get_type_choices(),
			),
			array(
				'id'           => $prefix . '_notification_users',
				'name'         => $prefix . '_notification_users[]',
				'label'        => __( 'Select', 'gravityflow' ),
				'size'         => '8',
				'multiple'     => 'multiple',
				'type'         => 'select',
				'choices'      => $this->_account_choices,
				'after_select' => ( $total_accounts > $number ) ? $description : '',
			),
			array(
				'name'        => $prefix . '_notification_routing',
				'label'       => __( 'Routing', 'gravityflow' ),
				'class'       => 'large',
				'type'        => 'user_routing',
				'description' => ( $total_accounts > $number ) ? $description : '',
			),
		);
	}

	/**
	 * Get the common notification fields.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $config The notification settings properties.
	 *
	 * @return array
	 */
	public function get_notification_common_fields( $config ) {
		$prefix = rgar( $config, 'name_prefix' );

		return array(
			array(
				'name'  => $prefix . '_notification_from_name',
				'label' => __( 'From Name', 'gravityflow' ),
				'class' => 'fieldwidth-2 merge-tag-support mt-hide_all_fields mt-position-right ui-autocomplete-input',
				'type'  => 'text',
			),
			array(
				'name'          => $prefix . '_notification_from_email',
				'label'         => __( 'From Email', 'gravityflow' ),
				'class'         => 'fieldwidth-2 merge-tag-support mt-hide_all_fields mt-position-right ui-autocomplete-input',
				'type'          => 'text',
				'default_value' => '{admin_email}',
			),
			array(
				'name'  => $prefix . '_notification_reply_to',
				'class' => 'fieldwidth-2 merge-tag-support mt-hide_all_fields mt-position-right ui-autocomplete-input',
				'label' => __( 'Reply To', 'gravityflow' ),
				'type'  => 'text',
			),
			array(
				'name'  => $prefix . '_notification_cc',
				'class' => 'fieldwidth-2 merge-tag-support mt-hide_all_fields mt-position-right ui-autocomplete-input',
				'label' => __( 'CC', 'gravityflow' ),
				'type'  => 'text',
				'tooltip'  => '<h6>' . __( 'Name', 'gravityflow' ) . '</h6>' . __( 'Be aware of any privacy policies your website is subject to that would apply to using the CC field. For example, GDPR indicates names and emails are private that should not be exposed.', 'gravityflow' ),
			),
			array(
				'name'  => $prefix . '_notification_bcc',
				'class' => 'fieldwidth-2 merge-tag-support mt-hide_all_fields mt-position-right ui-autocomplete-input',
				'label' => __( 'BCC', 'gravityflow' ),
				'type'  => 'text',
			),
			array(
				'name'  => $prefix . '_notification_subject',
				'class' => 'fieldwidth-1 merge-tag-support mt-hide_all_fields mt-position-right ui-autocomplete-input',
				'label' => __( 'Subject', 'gravityflow' ),
				'type'  => 'text',

			),
			array(
				'name'          => $prefix . '_notification_message',
				'label'         => __( 'Message', 'gravityflow' ),
				'type'          => 'visual_editor',
				'default_value' => rgar( $config, 'default_message' ),
			),
			array(
				'name'    => $prefix . '_notification_autoformat',
				'label'   => '',
				'type'    => 'checkbox',
				'choices' => array(
					array(
						'label'         => __( 'Disable auto-formatting', 'gravityflow' ),
						'name'          => $prefix . '_notification_disable_autoformat',
						'default_value' => false,
						'tooltip'       => __( 'Disable auto-formatting to prevent paragraph breaks being automatically inserted when using HTML to create the email message.', 'gravityflow' ),
					),
				),
			),
		);
	}

	/**
	 * Get the resend notification field.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $config The notification settings properties.
	 *
	 * @return array
	 */
	public function get_notification_resend_field( $config ) {
		if ( ! rgar( $config, 'resend_field' ) ) {
			return array();
		}

		$prefix = rgar( $config, 'name_prefix' );

		return array(
			array(
				'name'     => $prefix . '_notification_resend',
				'label'    => '',
				'type'     => 'checkbox_and_container',
				'checkbox' => array(
					'label' => __( 'Send reminder', 'gravityflow' ),
					'name'  => 'resend_assignee_emailEnable'
				),
				'settings' => array(
					array(
						'name'          => 'resend_assignee_emailValue',
						'label'         => esc_html__( 'Reminder', 'gravityflow' ),
						'type'          => 'text',
						'before'        => esc_html__( 'Resend the assignee email after', 'gravityflow' ) . ' ',
						'after'         => ' ' . esc_html__( 'day(s)', 'gravityflow' ),
						'default_value' => 7,
						'class'         => 'small-text',
					),
					array(
						'name'     => 'resend_assignee_email_repeat',
						'label'    => '',
						'type'     => 'checkbox_and_text',
						'before'   => '<br />',
						'checkbox' => array(
							'label' => esc_html__( 'Repeat reminder', 'gravityflow' ),
						),
						'text'     => array(
							'default_value' => 3,
							'class'         => 'small-text',
							'before'        => esc_html__( 'Repeat every', 'gravityflow' ) . ' ',
							'after'         => ' ' . esc_html__( 'day(s)', 'gravityflow' ),
						),
					),
				),

			),
		);
	}

	/**
	 * Get the "Attach PDF" notification field.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $config The notification settings properties.
	 *
	 * @return array
	 */
	public function get_notification_gpdf_field( $config ) {
		if ( empty( $this->_gpdf_choices ) ) {
			return array();
		}

		$choices = $this->_gpdf_choices;
		foreach( $choices as &$pdf ) {
			$pdf['name'] = rgar( $config, 'name_prefix' ) . '_' . $pdf['name'];
		}

		return array(
			array(
				'name'    => rgar( $config, 'name_prefix' ) . '_notification_gpdf',
				'label'   => esc_html__( 'Attach PDF(s)', 'gravityflow' ),
				'type'    => 'checkbox',
				'choices' => $choices,
			)
		);
	}

	/**
	 * Get the properties for the fields which appear on one notification tab.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $config The notification settings properties.
	 *
	 * @return array
	 */
	public function get_setting_notification( $config = array() ) {
		$config = array_merge( array(
			'name_prefix'            => 'assignee',
			'label'                  => '',
			'tooltip'                => '',
			'checkbox_label'         => __( 'Send an email to the assignee', 'gravityflow' ),
			'checkbox_tooltip'       => __( 'Enable this setting to send email to each of the assignees as soon as the entry has been assigned. If a role is configured to receive emails then all the users with that role will receive the email.', 'gravityflow' ),
			'checkbox_default_value' => false,
			'default_message'        => '',
			'send_to_fields'         => false,
			'resend_field'           => true,
		), $config );

		$fields = array_merge(
			$this->get_notification_enabled_field( $config ),
			$this->get_notification_send_to_fields( $config ),
			$this->get_notification_common_fields( $config ),
			$this->get_notification_resend_field( $config ),
			$this->get_notification_gpdf_field( $config )
		);

		return $fields;
	}

	/**
	 * Get the properties for the notification tabs setting.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param array $tabs The properties for each tab.
	 *
	 * @return array
	 */
	public function get_setting_notification_tabs( $tabs ) {
		return array(
			'name'    => 'notification_tabs',
			'label'   => __( 'Emails', 'gravityflow' ),
			'tooltip' => __( 'Configure the emails that should be sent for this step.', 'gravityflow' ),
			'type'    => 'tabs',
			'tabs'    => $tabs,
		);
	}

	/**
	 * Get the properties for the assignee type field.
	 *
	 * @since 1.5.1-dev
	 *
	 * @return array
	 */
	public function get_setting_assignee_type() {
		return array(
			'name'          => 'type',
			'label'         => __( 'Assign To:', 'gravityflow' ),
			'type'          => 'radio',
			'default_value' => 'select',
			'horizontal'    => true,
			'choices'       => $this->get_type_choices(),
		);
	}

	/**
	 * Get the properties for the assignees field.
	 *
	 * @since 1.5.1-dev
	 *
	 * @return array
	 */
	public function get_setting_assignees() {
		$setting = array(
			'id'       => 'assignees',
			'name'     => 'assignees[]',
			'tooltip'  => __( 'Users and roles fields will appear in this list. If the form contains any assignee fields they will also appear here. Click on an item to select it. The selected items will appear on the right. If you select a role then anybody from that role can approve.', 'gravityflow' ),
			'size'     => '8',
			'multiple' => 'multiple',
			'label'    => esc_html__( 'Select Assignees', 'gravityflow' ),
			'type'     => 'select',
			'choices'  => $this->_account_choices,
		);

		$total_accounts = Gravity_Flow_Common::get_total_accounts();
		$args           = Gravity_Flow_Common::get_users_args();
		$number         = ( isset( $args['number'] ) && $args['number'] > 0 ) ? $args['number'] : 2000;
		if ( $total_accounts > $number ) {
			/* translators: 1: Warning icon 2: Number of users displayed 3: Open link tag 4: Close link tag */
			$setting['description'] = sprintf( esc_html__( '%1$s The list of users contains only the first %2$s users in your site. %3$sLearn how to remove this limit%4$s. ', 'gravityflow' ), '<i class="dashicons dashicons-warning" style="color:red;"></i> ', $number, '<a href="https://docs.gravityflow.io/article/54-gravityflowgetusersargs" target="_blank">', '</a>' );
		}

		return $setting;
	}

	/**
	 * Get the properties for the assignee routing field.
	 *
	 * @since 1.5.1-dev
	 *
	 * @return array
	 */
	public function get_setting_assignee_routing() {
		$setting = array(
			'name'    => 'routing',
			'tooltip' => __( 'Build assignee routing rules by adding conditions. Users and roles fields will appear in the first drop-down field. If the form contains any assignee fields they will also appear here. Select the assignee and define the condition for that assignee. Add as many routing rules as you need.', 'gravityflow' ),
			'label'   => __( 'Routing', 'gravityflow' ),
			'type'    => 'routing',
		);

		$total_accounts = Gravity_Flow_Common::get_total_accounts();
		$args           = Gravity_Flow_Common::get_users_args();
		$number         = ( isset( $args['number'] ) && $args['number'] > 0 ) ? $args['number'] : 2000;
		if ( $total_accounts > $number ) {
			/* translators: 1: Warning icon 2: Number of users displayed 3: Open link tag 4: Close link tag */
			$setting['description'] = sprintf( esc_html__( '%1$s The list of users contains only the first %2$s users in your site. %3$sLearn how to remove this limit%4$s. ', 'gravityflow' ), '<i class="dashicons dashicons-warning" style="color:red;"></i> ', $number, '<a href="https://docs.gravityflow.io/article/54-gravityflowgetusersargs" target="_blank">', '</a>' );
		}

		return $setting;
	}

	/**
	 * Get the properties for the instructions field.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param string $default_value The default value to appear in the editor.
	 *
	 * @return array
	 */
	public function get_setting_instructions( $default_value = '' ) {
		return array(
			'name'     => 'instructions',
			'label'    => __( 'Instructions', 'gravityflow' ),
			'type'     => 'checkbox_and_textarea',
			'tooltip'  => esc_html__( 'Activate this setting to display instructions to the user for the current step.', 'gravityflow' ),
			'checkbox' => array(
				'label' => esc_html__( 'Display instructions', 'gravityflow' ),
			),
			'textarea' => array(
				'use_editor'    => true,
				'default_value' => $default_value,
			),
		);
	}

	/**
	 * Get the properties for the display fields type field.
	 *
	 * @since 1.5.1-dev
	 *
	 * @return array
	 */
	public function get_setting_display_fields() {
		return array(
			'name'    => 'display_fields',
			'label'   => __( 'Display Fields', 'gravityflow' ),
			'tooltip' => __( 'Select the fields to hide or display.', 'gravityflow' ),
			'type'    => 'display_fields',
		);
	}

	/**
	 * Get the properties for the confirmation message setting.
	 *
	 * @since 1.5.1-dev
	 *
	 * @param string $default_value The default value to appear in the editor.
	 *
	 * @return array
	 */
	public function get_setting_confirmation_messasge( $default_value = '' ) {
		return array(
			'name'     => 'confirmation_message',
			'label'    => __( 'Confirmation Message', 'gravityflow' ),
			'type'     => 'checkbox_and_textarea',
			'tooltip'  => esc_html__( 'Activate this setting to display a custom confirmation message to the assignee for the current step.', 'gravityflow' ),
			'checkbox' => array(
				'label' => esc_html__( 'Display a custom confirmation message', 'gravityflow' ),
			),
			'textarea' => array(
				'use_editor'    => true,
				'default_value' => $default_value,
			),
		);
	}
}
