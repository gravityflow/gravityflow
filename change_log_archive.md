= 2.4.4 =
- Added security enhancements.
- Added support for license key constants in extensions.
- Fixed an issue where workflows don't start when the entry is created before the init action.
- Fixed an issue with the signature generation for GET requests for OAuth1.0a webhook requests for WordPress and Gravity Forms connected apps.
- Fixed an issue where importing a form without workflow steps would incorrectly display Gravity Flow notice. Credit: Travis Lopes, ForGravity.

= 2.4.3 =
- Fixed the styles in the installation wizard.

= 2.4.2 =
- Updated the WP E-Signature step to allow the feed to redirect to the document if processed during submission.
- Updated the license checks to reduce load on the licensing server.
- Fixed an issue with the admin notice license checks for third-party extensions.

= 2.4.1 =
- Fixed the Gravity Perks Nested Forms Add-On integration not delaying the workflow for child entries created before the parent form is submitted.
- Fixed an issue where Conditional Routing based on what values a Multiuser field contains could lead to extra users being set as step assignees.
- Fixed conditional assignee routing using the post category field.
- Fixed the inbox page excess entries indicator displaying a limit of 150 when the page size has been changed using the gravityflow_inbox_paging filter.
- Fixed an issue where the API function get_timeline would not return all expected timeline values
- Updated the licensing checks to expire at a random time to reduce spikes in traffic on the licensing server.

= 2.4 =
- Added approval step setting for notification on revert (only displays when revert setting for user input step is selected).
- Added support for multiple Gravity PDFs to be sent with each email. Credit: The Team at Gravity PDF.
- Added settings for the Email Service, default From Name, and default From Email.
- Added support for Gravity Forms OAuth1 connected apps for use in the Outgoing Webhook step.
- Added support for the Gravity Forms Advanced Post Creation Add-On.
- Added support for Gravity Forms Repeater Fields.
- Added the gform_order_summary filter enabling the GP eCommerce Fields Add-On to modify the location of discount and tax fields in the order summary table.
- Fixed status page step column from display expires date/time when step also has schedule queued.
- Fixed an issue where the file upload field appears to lose its value following progress save on the user input step.
- Fixed gravityflow_is_fulfilled entry meta being set when there are no steps to process.
- API: Added the entry filter step setting.
- API: Added the Gravity_Flow_API::get_inbox_entries() and Gravity_Flow_API::get_inbox_entries_count() methods.

= 2.3.3 =
- Fixed an issue where delayed feeds are not delayed on Gravity Forms >2.3.4.2

= 2.3.2 =
- Added the gravityflow_step_expiration_timestamp filter.
- Added the step merge tag attribute to allow merge tags to specify the step for which tokens must be generated. This allows feed add-ons to specify the step. For example, a Twilio or Slack message can contain a one-click approval link for the next step.
- Updated the processing of the workflow to trigger before the confirmation is processed.
- Updated the processing of user input step to skip required field validation when saving progress.
- Updated the timeline date/time format to reflect format selected in WordPress general settings.
- Updated Outgoing Webhook response mapping to parse merge tags in field keys.
- Updated the Outgoing Webhook step settings to display merge tag dropdown with the URL field.
- Updated the installation wizard to install Gravity Forms if it's not installed or update it if there's a newer version available.
- Fixed an edge case where the assignee attribute will remove the assignee added in the constructor and override the assignee in subsequent instances of the merge tag.
- Fixed an issue with the integrations with the Twilio Add-On where URLs get encoded breaking workflow links.
- Fixed an issue with the evaluation of date field based step expiration when weeks is the selected offset unit.
- Fixed a fatal error which can occur if Gravity PDF is activated on PHP < 5.6.
- Fixed an issue where the "Workflow: user input" notification event is triggered before the entry has been updated which results in stale data being used when notification routing/conditional logic is evaluated and when merge tags in the notification settings are processed.

= 2.3.1 =
- Added support for deep parsing of the webhook response values. e.g. "translations\0\text"
- Added the gravityflow_webhook_url_in_note filter to prevent the webhook URL from containing the URL. e.g. add_filter( 'gravityflow_webhook_url_in_note', '__return_false' );

= 2.3 =
- Added "Authorized" as an available choice for the entry Payment Status property in the step condition setting.
- Added response field mapping for JSON values to the Outgoing Webhook step.
- Added gravityflow_entry_webhook_response_mapping to allow the entry/response values to be adjusted during webhook response mapping.
- Added gravityflow_columns_inbox_table to allow columns in inbox to be adjusted in similar fashion to status (gravityflow_columns_status_table)
- Added gravityflow_date_format_current_step_merge_tag filter to allow the date/time-based modifiers for {current_step} adjust format: expiration, schedule, and start.
- Added gravityflow_feed_condition_entry_properties filter to allow modifying entry properties in the feed condition.
- Added admin notices when license keys are not valid.
- Added support for tokens in Gravity Forms confirmations. Requires Gravity Forms 2.3.3.10.
- Updated the Status Table Step Column to display the queued start time for a scheduled step and the expiration time for a step when set.
- Updated the Entry Detail workflow info box display of step expiration date to use existing gravityflow_date_format_entry_detail filter.
- Updated the step condition setting, inbox, and status pages to support custom payment statuses added by the gform_payment_statuses filter with Gravity Forms 2.4 and greater.
- Fixed an issue with the Multi-User field on the entry detail pages where the ID is displayed instead of the user's name.
- Fixed an issue with the File Upload field display the inbox shortcode page via fields attribute. The file icon/link was not displaying and generating PHP error.
- Fixed RTL CSS in the inbox and entry detail page for Divi and other themes that don't fully support RTL.

= 2.2.3 =
- Added the "Workflow: cancelled" notification event for sending form notifications when the workflow is cancelled.
- Added the merge_tag {current_step} with [modifiers] for duration (time), expiration (date/time), ID, name (default), schedule (date/time), start (date/time) and type.
- Fixed an issue which prevents tokens from working correctly with role assignees.
- Fixed an issue which causes discussion field show / hide toggle to display on non-Gravity Flow pages.
- Fixed an issue which prevents form connector mapping assignee field and export display of user-based field types (assignee, user, multiuser)
- Fixed the step ID in the notification ID.
- Fixed an issue where reminder emails may be sent out under certain circumstances when forms or steps are inactive.
- Fixed some issues with RTL styles
- Fixed some issues with styles on the Twenty Seventeen theme.
- Fixed an issue with the status shortcode where the workflow_info and step_status attributes are ignored.

= 2.2.2 =
- Added the filter gravityflow_form_ids_inbox which allows adjustment of form id(s) when searching for entries for the inbox table.
- Added the filter gravityflow_form_ids_status which allows adjustment of form id(s) when searching for entries for the status table.
- Fixed a notice which can get displayed if GravityView is installed.
- Fixed a compatibility issue with the Nested Forms Perk.
- Fixed an issue with the assignee policy for multiple role assignees.
- Fixed an issue where date field expiration may use the wrong field to calculate the expiration date.


= 2.2.1 =
- Fixed an issue preventing the one-click approve and reject links from working.
- Fixed a fatal error which can occur if the entry is deleted before workflow processing starts.
- Fixed a fatal error which can occur when entry detail permissions are evaluated with the status page shortcode set to allow anonymous access.
- Fixed an issue with the Multi-User field on the User Input step where no fields are editable for the assignee.
- Fixed a performance issue in the WP admin which can affect sites with a lot of users.

= 2.2 =
- Added the Multi-User field.
- Added the filter gravityflow_step_status_evaluation_approval to allow the status evaluation of approval step to check custom logic (X of Y approvals).
- Added the "Display all fields except selected" choice to the Display Fields setting.
- Added searching to the lists of assignees.
- Updated the Sprout Invoices step to support the new Sprout Invoices + Gravity Forms add-on.
- Updated the existing choice labels in the Display Fields setting to "Display all fields" and "Hide all fields except selected".
- Fixed editable Post Image field not being populated with the current entry value on the User Input step.
- Fixed editable Post Image field value retaining the previous image URL on entry update if the step failed validation.
- Fixed status export .csv to include duration as a separate column so it is consistent with status export table info.
- Fixed a PHP warning for the Discussion field.
- Fixed an issue with the approval step for assignees added via custom code.
- Fixed some merge tags such as {created_by} being replaced in assignee notifications before the entry was available.

= 2.1.1 =
- Fixed editable Post Category field not being populated with the current entry value on the User Input step.
- Fixed an issue which prevents the rendering of certain merge tags e.g. {workflow_form_submission_link} in extensions and custom code.

= 2.1 =
- Added support for the Gravity Perks Nested Forms add-on.
- Added the current step as parameters to the gravityflow_feedback_approval_token and gravityflow_feedback_approval filters.
- Added the filter gravityflow_feedback_cancel_workflow to allow the user feedback to be modified when the workflow is cancelled using the cancel workflow link.
- Added the filter gravityflow_inbox_search_criteria to allow the search criteria to be modify.
- Added the filter gravityflow_assignee_choices to allow the list of assignee choices to be modified in the step settings.
- Added the filter gravityflow_step_schedule_timestamp to allow the scheduled start of steps to apply custom logic (business hours, delay specific entries, etc)
- Added the filter gravityflow_event_limit_activity_page to allow the activity page to display custom amount of events
- Added the filter gravityflow_discussion_items_display_toggle to allow view more / less effect to be modified per discussion field
- Added the filter gravityflow_inbox_paging to inbox page to enable control of pagination options
- Added the filter gravityflow_step_status_webhook to enable custom logic at conclusion of outbound webhook step processing
- Added the filter gravityflow_response_message_webhook to enable customization of timeline note for an outbound webhook step
- Added the Step ID # to workflow settings list. Accessible by mouseover in the row actions
- Added the Step ID # to sub-title of edit step screen for existing steps
- Updated the Status Page export to match table display and run gravityflow_field_value_status_table filter
- Updated the Sliced Invoices integration.
- Updated discussion field to display the view more / less effect on all step types for status and inbox pages. Previously limited to user input edit field.
- Updated the Outgoing Webhook Step to handle multiple response codes appropriate statuses: 2xx (complete), 4xx (error_client), 5xx (error_server), other (error)
- Fixed an issue where, in some situations, getting the next step was not using the latest version of the entry.
- Fixed an issue where calculated product fields hidden by conditional logic could appear in order summary when the entry is updated on the User Input step.
- Fixed an issue where feeds with conditional logic can block the progress of the workflow if the conditional is not met.
- API: Updated the way assignees are handled to allow the base assignee class to be extended so new types of assignees can be created.


= 2.0.1 =
- Added the {workflow_fields} merge tag, similar to the {all_fields} merge tag with the following true/false attributes: empty, value, admin, editable and display.
- Added 'Send to step' to admin action when workflow is complete.
- Added the gravityflow_display_field_choices filter.
- Added the gravityflow_date_format_entry_detail filter.
- Added CSS RTL improvements.
- Fixed an issue with the cron task to prevent running during the upgrade of Gravity Forms.
- Fixed an issue where Section Breaks show during the User Input step regardless of being selected in the UI.


= 2.0.0 =
- Added the highlight setting to all the step settings.
- Added the gravityflow_pre_cancel_workflow action.
- Added the gravityflow_timeline_step_icon filter.
- Added the sent status of a sliced invoice to the workflow detail page.
- Added the gravityflow_entry_link_inbox_table filter.
- Added the gravityflow_site_cookie_path filter.
- Added the gravityflow_step_highlight_color_inbox filter.
- Added support for save and submit buttons on the User Input step.
- Added the entry_id attribute to the shortcode for direct access to the workflow entry detail page. e.g. [gravityflow entry_id=123]
- Added the "View more" link to the discussion field for discussions with more than 10 messages.
- Updated to support Gravity Forms version 2.3.
- Fixed the HubSpot step for the "HubSpot for Gravity Forms" plugin versions 3.0+.
- Fixed a fatal error which could occur when processing the shortcode with the form attribute and the specified entry can't be retrieved.
- Fixed an issue with the timeline notes for Approval and User Input steps where the step icon is used instead of the user avatar.
- Fixed an issue with the conditional logic for Section fields on the User Input step.
- Fixed an issue that step IDs in feed conditions weren't updated after forms duplicated or imported.


= 1.9.1 =
- Fixed an issue which can cause the settings tabs not to appear on some installations.

= 1.9.0 =
- Added support for Members version 2.0+; moved the capabilities to a new Gravity Flow group with human readable labels.
- Added the gravityflow_step_assignees filter.
- Added PATCH to the list of request methods in the request method setting of the Outgoing Webhook step settings.
- Added Headers to the Outgoing Webhook step settings.
- Added the Raw body setting to the Outgoing webhook step settings.
- Added the Connected Apps Settings page to allow the Outgoing Webhook step to connect to WordPress sites running the official OAuth1 Server plugin.
- Updated the {workflow_note} merge tag to support the history modifier to control if notes from previous occurrences of the step are output e.g. {workflow_note:step_id=5 history=true}.
- Updated the {workflow_note} merge tag step_id attribute to also support step names e.g. {workflow_note:step_id='manager approval'}.
- Updated the field conditional logic setting to be enabled by default on the User Input Step.
- Fixed an issue populating multi-select fields with the existing entry value on the user input step when the field uses the new json storageType.
- Fixed the $original_entry parameter not being passed when the gform_after_update_entry hook is triggered on entry update by the user input step.
- Fixed empty fields being displayed on the user input step when 'show empty fields' was not enabled.
- Fixed an issue with the shortcode where the gravityflow_status_filter and gravityflow_permission_granted_entry_detail filters don't fire.
- Fixed the Dropbox step for Gravity Forms Dropbox Add-On versions 2.0+.
- Fixed and issue which can cause an error when the Update Entry step is triggered by the schedule setting or by an anonymous user.


= 1.8.0 =
- Added the workflow note field to the quick actions in the inbox. The note field is only displayed when required by the step settings.
- Added support for editable fields on the User Input step with calculations based on non-editable and/or non-display fields.
- Added the gravityflow_columns_status_table filter to allow the columns to be filtered on the status table.
- Added the gravityflow_field_value_status_table filter to allow the field values to be filtered on the status table.
- Added support for first, latest, and limit modifiers on the Discussion field merge tag e.g. {label:2:first}, {label:2:latest}, and {label:2:limit=3}.
- Added a class name to the entry detail table for the current step.
- Added support for using the {workflow_entry_link}, {workflow_entry_url}, {workflow_inbox_link}, {workflow_inbox_url}, {workflow_note} and {assignees} merge tags in Gravity Forms notifications.
- Added "Cancelled" to the final status entry meta options available in entry filters.
- Updated the note for expired steps to include the step name.
- Updated step conditional logic to support rules based on entry meta and properties.
- Updated the User and Role type fields so the step conditional logic rule value will use the choice drop down instead of text input.
- Updated the {workflow_note} merge tag to support the optional step_id, display_name, and display_date modifiers e.g. {workflow_note: step_id=5 display_name=true display_date=true}.
- Fixed an issue where the {workflow_note} merge tag would not return the note when the merge tag was not processed immediately after the note was added.
- Fixed some strings for translation.
- Fixed a php notice when printing entries.
- Fixed Discussion field merge tag not returning content when used in the step instructions.
- API: Fixed an issue with the add-on slug which prevents feed interception for third party add-ons.
- Fixed a JS error on the entry detail page when a User Input step has an editable Chained Select field and conditional logic is not enabled.


= 1.7 =
- Added support for unfiltered HTML in the instructions setting for users with the unfiltered_html capability.
- Added the ability to configure emails to be sent when a User Input step updates the entry and remains in progress and when the step is completed.
- Added the date field option to the step expiration setting.
- Added the $field->gravityflow_is_editable and $field->gravityflow_is_display_field properties to provide context to Gravity Forms add-ons that don't extend GF_Field for custom fields.
- Added new functionality to the Sliced Invoices step:
	- The Assign To, Assignee Email, Instructions, Display Fields, and Expiration settings.
	- The Step Completion setting allowing completion of the step to be delayed until the invoice is paid.
	- When the step is pending invoice payment the invoice details will be displayed in the workflow detail box on the inbox detail page.
- Fixed an issue with the notification message when the Discussion field merge tag content exceeded the max line length.
- Fixed an issue with the GravityView integration where the single view doesn't display if an assignee is defined the Advanced Filter criteria.
- Updated the "no assignees" note from "not required" which could have been confusing in some situations.
- Updated the feed configuration page for the Sliced Invoices & Gravity Forms Add-On:
	- Added settings for the default status of the invoice/quote.
	- Updated the choices for the Line Items map field to only include List type fields and the option to use the entry order summary.
- Renamed the User Input step 'Default Status Option' setting to 'Save Progress Option' and changed the setting type from radio buttons to drop down.


= 1.6.1 =

- Fixed an issue which can cause an error in GravityView views.

= 1.6 =

- Added support for GravityView to be used to create inbox views. Requires the GravityView Advanced Filter Extension.
- Added updater support for beta extensions.
- Added support for configuring a step to process feeds for the following third-party add-ons:
    Gravity Forms Constant Contact Add-On, Gravity Forms to Pipe Drive CRM Add-On, and Gravity Forms SendinBlue Add-On.
- Added the custom confirmation message to the user input step.
- Added support for the gform_field_validation filter to the user input step.
- Added the gravityflow_status_submitter_name filter.
- Added performance improvements to the status page.
- Added support for repeat reminders.
- Added the gravityflow_assignee_eamil_reminder_repeat_days filter.
    Example:
    add_filter( 'gravityflow_assignee_eamil_reminder_repeat_days', 'sh_gravityflow_assignee_eamil_reminder_repeat_days', 10, 4 );
    function sh_gravityflow_assignee_eamil_reminder_repeat_days( $form, $entry, $step, $assignee ) {
    	return 3; // Send reminders every 3 days after the initial assignee email. Return zero to disable.
    }
- Added support for manual activation in the User Registration Add-On. The User Registration step will now wait in a pending state until the account has been activated.
- Updated the User Registration step to assigned entry to the newly created user account.
- Updated merge tag processing for the Assignee and User type fields to support additional user property/meta modifiers.
- Updated the gravityflow_workflow_detail_sidebar hook to include $current_step and $args as the third and fourth parameters.
- Updated the user Input button text to "Submit" instead of "Update" when the default status is hidden.
- Updated the styles of the workflow box when the sidebar is not active to remove the padding.
- Updated the activity log and reports to exclude deleted and trashed entries.
- Fixed an issue with the inbox page which would result in the no pending tasks message when an access token is used by an assignee who is a registered user but not logged in.
- Fixed an issue which prevented the value returned by the gravityflow_feedback_approval filter from being used.
- Fixed a fatal error loading the status shortcode on some sites.
- Fixed a JavaScript error on User Input steps with field conditional logic disabled.
- Fixed the bottom bulk action drop down on the status page not displaying the print modal.
- Fixed an issue which could prevent the value drop down for the Gravity View Advanced Filter Extension being populated with the Workflow Assignee choices.
- Fixed a JavaScript error on the User Input step related to conditional logic dependent fields which are not editable or display fields.
- Fixed an issue with conditional routing where the Source URL, Starred and IP fields are ignored in rules.
- Fixed an issue which prevented the values from post fields being saved on the User Input step.
- Fixed an issue with the approval actions.


= 1.5.0 =

- Added support for configuring a step to process feeds for the upcoming Gravity Forms Post Creation Add-On.
- Added support for configuring a step for Sprout Invoices. Requires the Sprout Invoices Form Integrations add-on (creates estimates) and/or Invoice Submissions add-on (creates invoices).
- Added settings to the Workflow > Settings page for selecting which pages contain the gravityflow inbox, status and submit shortcodes. The selected inbox page will be used when preparing merge tags such as {workflow_inbox_link} when the page_id attribute is not specified.
- Added support for shortcodes in the step instructions.
- Added the gravityflow_pre_restart_workflow action.
- Added field mapping to the Outgoing Webhook step.

- Updated the step type column of the workflow steps list and the step configuration page to indicate when a plugin required by a feed based step type is missing.

- Fixed merge tag labels not being translatable.
- Fixed an issue with the inbox where field values are not displayed correctly when custom code uses the gform_entries_field_value filter.
- Fixed an issue with poor admin performance on some sites where W3TC is installed and object caching is enabled.

= 1.4.2.1 =

- Fixed an issue preventing the settings for the Workflow Role Field from opening in the Form Editor.

= 1.4.2 =

- Added the users role filter setting to the Workflow User and Assignee type fields.
- Added support for deleting Discussion field comments on the entry detail edit page.
- Added the Rich Text Editor setting support to the Discussion field. New fields only.
- Added the gravityflow_inbox_sorting filter to allow the sorting criteria to be modified before search for entries in the inbox. See http://docs.gravityflow.io/article/132-gravityflowinboxsorting
- Added the gravityflow_reverse_comment_order_discussion_field filter allowing the comments to be reversed before being displayed.
    Example: add_filter( 'gravityflow_reverse_comment_order_discussion_field', '__return_true' );
- Added Italian translation - thanks to Giacomo Papasidero.

- Updated to support the Gravity Forms 2.1 field visibility setting changes.
- Updated the inbox and status pages to remove the dependency on the entry list columns when form and field ids are specified.
- Updated the notification step to prevent the selected notifications being sent during form submission.
- Updated the last updated column on the status list to display the date created when the entry has not been updated.

- Fixed an issue where step conditional routing rules based on the entry id would not be evaluated correctly.
- Fixed an issue with duplicate Final Status choices, such as Complete, being added to the entry meta filters.
- Fixed an issue with the user input step update button which caused the signature field to fail validation.
- Fixed an issue with the workflow link/url merge tags when the specified page does not exist.
- Fixed an issue with the coupon field on the user input step when there are no editable product fields (GF Coupons v2.3.2 required).
- Fixed an issue where a feed add-ons delay_feed() method was not being called when the feed was intercepted.
- Fixed an issue with the step status showing as complete before the step processing has started.
- Fixed an issue with empty sections being displayed on the entry detail view.
- Fixed an issue with the sidebar shortcode attribute for the status page.
- Fixed an issue with dynamic assignee routing where the assignees don't update correctly after changing the value of a dependent field.
- Fixed a fatal error when the next step doesn't exist.
- Fixed an issue with the inbox and status shortcodes where the form is specified without fields.
- Fixed an issue with the conditional routing where conditions specifying the User are ignored.
- Fixed an issue with imported forms. Fixed in Gravity Forms 2.1.1.13.


= 1.4.1 =

- Added security enhancements.
- Added support for field filters on the status page when a form constraint is active via the shortcode.
- Added the gravityflow_shortcode_[page] filter.
    Example:
        // Adds support for [gravityflow page="custom_page"]
        add_filter('gravityflow_shortcode_custom_page', 'sh_filter_gravityflow_shortcode_custom_page', 10, 3 );
        function sh_filter_gravityflow_shortcode_custom_page( $html, $shortcode_attributes, $content ) {
            echo "My custom Gravity Flow shortcode";
        }
- Added the gravityflow_enqueue_frontend_scripts action to allow additional scripts to be enqueued when the gravityflow shortcode is present on the page.
    Example:
        add_filter('gravityflow_enqueue_frontend_scripts', 'sh_action_gravityflow_enqueue_frontend_scripts', 10);
        function sh_action_gravityflow_enqueue_frontend_scripts() {
            // enqueue custom scripts
        }
- Added the gravityflow_bulk_action_status_table filter to allow custom bulk actions to be processed on the status page.
    Example:
        add_filter( 'gravityflow_bulk_action_status_table', 'filter_gravityflow_bulk_action_status_table', 10, 4);
        function filter_gravityflow_bulk_action_status_table( $feedback, $bulk_action, $entry_ids, $args ) {
            // process entries

            return 'Done!';
        }
- Added the gravityflow_field_filters_status_table filter to allow the field filters to be modified.
    Example:
        add_filter( 'gravityflow_field_filters_status_table', 'filter_gravityflow_field_filters_status_table' );
        function filter_gravityflow_field_filters_status_table( $field_filters ) {
            // Modify the filters
            return $field_filters;
        }

- Fixed an issue with the workflow starting for spam entries.
- Fixed an issue on the user input step where the file upload field value could be lost when another field failed validation or when restarting the step and editing another field.
- Fixed an issue where the label would not be displayed on the entry detail view or user input step when the field label was empty and the admin label was configured.
- Fixed a fatal error which could occur if the gform_post_add_entry hook passes a WP_Error object for the $entry.
- Fixed a PHP warning which could occur when using the gravityflow_{type}_token_expiration_days filter.
- Fixed an issue with duplicate merge tags being added to the merge tag drop down.
- Fixed an issue with shortcodes used in the HTML field content not being processed on the entry detail view.
- Fixed an issue with the import process where the feeds remain inside the form meta.
- Fixed an issue with the import process where the revert step setting is not imported correctly.
- Fixed an issue with the permissions for printing where email assignees can't print.

= 1.4 =

- Added support for delaying the workflow until after PayPal payment.
- Added "Reminder:" to the subject line of reminder notifications.
- Added the Custom Timestamp Format setting to the Discussion field appearance tab.
- Added the {workflow_inbox_url} and {workflow_inbox_link} merge tags.
- Added the "Expired" status to the approval and user input steps.
- Added the "Next step if Expired" sub-setting to the expiration settings.
- Added support for GravityPDF v4.0 to the User Input step.
- Added support for merge tag replacement in HTML fields for the User Input and Approval Steps.
- Added support for configuring a step to process feeds for the Gravity Forms Breeze and Dropbox add-ons.
- Added support for configuring a step to process feeds for the following third-party add-ons:
    Drip Email Campaigns + Gravity Forms, Gravity Forms ConvertKit Add-On, Gravity Forms Signature Add-on by ApproveMe (WP E-Signature), HubSpot for Gravity Forms, Sliced Invoices & Gravity Forms
- Added support for admin-only fields to be used in conditional logic in Gravity Forms 2.0.
- Added the gravityflow_inbox_entry_detail_pre_process filter to allow the entry detail processing to be aborted.

- Updated minimum Gravity Forms version to 1.9.14.
- Updated feed interception to use the gform_is_delayed_pre_process_feed filter with GF1.9.14+ or gform_pre_process_feeds filter with GF2.0+.

- Fixed a fatal error in the admin actions when sending to a step which completes the workflow immediately.
- Fixed an issue with shortcodes used in the HTML field content not being processed on the user input step.
- Fixed an issue with the workflow being started when an incomplete entry is saved by the Gravity Forms Partial Entries Add-On.
- Fixed an issue when sending to another step when the current step is queued.
- Fixed an issue with assignees which don't exist being assigned to the step e.g. when an email field doesn't have a value.
- Fixed an issue with the step flow when the destination step is not active or conditions met.
- Fixed an issue with the reminders not being sent when steps are repeated.
- Fixed an issue with the status page preventing the workflow user, assignee and role fields from being displayed.
- Fixed an issue with the admin actions button on the user input step when form button conditional logic is enabled.
- Fixed a performance issue with the user input step.
- Fixed an issue with the display of Section fields on the user input step.
- Fixed an issue with the Discussion field when an in progress user input step is redisplayed following a successful update.
- Fixed an issue with the Discussion field when the form or user input step returns a validation error.
- Fixed notice caused by step processing occurring when the associated feed add-on is inactive.
- Fixed an issue with add-on feed interception running when the step is inactive.
- Fixed a fatal error which could occur if a Zapier step is configured and the add-on isn't active during step processing.

= 1.3.2 =

- Added the gravityflow_inbox_submitter_name to allow the value displayed in the Submitter column to be overridden.
    Example:
    add_filter( 'gravityflow_inbox_submitter_name', 'inbox_submitter_name', 10, 3 );
    function inbox_submitter_name( $submitter_name, $entry, $form ) {
        return 'the new submitter name';
    }
- Added support for configuring a step to process feeds for the following Gravity Forms Add-Ons:
    ActiveCampaign, Agile CRM, AWeber, Batchbook, Campfire, Capsule, CleverReach, Freshbooks, GetResponse, Help Scout, HipChat, Highrise, iContact, Mad Mimi, Slack, Trello, and Zoho CRM.
- Added post action settings to the Approval step if the form has post fields.
- Added support for a delay offset to the date field option of the schedule step setting.
- Added the following attributes to the shortcode: step_status, workflow_info and sidebar.
    Example: ​step_status="false" workflow_info="false" sidebar="false"
- Added the gravityflow_revert_label_workflow_detail filter to allow the 'Revert' label to be modified on the Approval step.
- Added the gravityflow_reject_label_workflow_detail filter to allow the 'Reject' label to be modified on the Approval step.
- Added the gravityflow_approve_label_workflow_detail filter to allow the 'Approve' label to be modified on the Approval step.
    Example:
    add_filter( 'gravityflow_approve_label_workflow_detail', 'filter_approve_label_workflow_detail', 10, 2 );
    function filter_approve_label_workflow_detail( $approve_label, $step ) {
        return 'Your new label';
    }
- Added the gravityflow_admin_actions_workflow_detail filter to allow the choices in the admin actions drop down on the entry detail page to be modified.
    Example:
    add_filter( 'gravityflow_admin_actions_workflow_detail', 'filter_admin_actions_workflow_detail', 10, 5 );
    function filter_admin_actions_workflow_detail( $admin_actions, $current_step, $steps, $form, $entry ) {
        $admin_actions[] = array( 'label' => 'your new action', 'value' => 'your_new_action' );

        return $admin_actions;
    }
- Added the Discussion Field.

- Updated to only add workflow notification events if a step has been configured for the form.
- Updated choices for the notification events setting to be translatable.
- Updated the list of steps in the 'Send to Step' section of the admin actions to display only active steps.
- Updated the styles of the front-end entry detail page when the workflow info and step status are hidden.

- Fixed an issue which caused all the Zapier feeds for a form to be processed on the Zapier step. Requires Zapier 1.8.3.
- Fixed an issue with feed conditional logic evaluation for the Zapier step.
- Fixed an issue with the license validation logging statement.
- Fixed an issue with including the timelines with the printout from the entry detail page.

= 1.3.1 =
- Added support for Signature Add-On v3.0.
- Added the gravityflow_assignee_status_list_user_input filter to allow the assignee status list to be hidden.
    Example:
    add_action( 'gravityflow_assignee_status_list_user_input', 'sh_filter_gravityflow_assignee_status_list_user_input', 10, 3 );
    function sh_filter_gravityflow_assignee_status_list_user_input( $display, $form, $step ) {
        return false;
    }
- Added the gravityflow_below_workflow_info_entry_detail filter to allow content to be added below the workflow info on the entry detail page.
    Example:
    add_action( 'gravityflow_below_workflow_info_entry_detail', 'sh_action_gravityflow_below_workflow_info_entry_detail', 10, 3 );
    function sh_action_gravityflow_below_workflow_info_entry_detail( $form, $entry, $step ) {
        echo 'My content';
    }
- Added the gravityflow_feedback_message_user_input filter to allow the feedback message to be modified on the user input step.
    Example:
    add_filter( 'gravityflow_feedback_message_user_input', 'sh_filter_gravityflow_feedback_message_user_input', 10, 5 );
    function sh_filter_gravityflow_feedback_message_user_input( $feedback, $new_status, $assignee, $form, $step ) {
        return 'Success!';
    }
- Added the gravityflow_step_column_status_page filter to allow the value of the step column to be modified on the status page.
    Example:
    add_filter( 'gravityflow_step_column_status_page', 'sh_filter_gravityflow_step_column_status_page', 10, 2 );
    function sh_filter_gravityflow_step_column_status_page( $output, $entry ) {
        if ( empty( $entry['workflow_step'] ) ) {
            $output = 'Workflow Ended';
        }
        return $output;
    }
- Added the Disable auto-formatting setting for the assignee, rejection, and approval email messages.
- Added the generic map step setting type.
- Added the workflow_current_status entry meta to track the status of steps that can end in a status other than 'complete'.
- Added the gravityflow_below_status_list_user_input action to allow content to be added in the workflow box below the status list.
- Added Gravity_Flow_API::get_timeline()
- Added the gravityflow_permission_granted_entry_detail filter to allow the permission check to be overridden for the workflow entry detail page.
    Example:
    add_filter( 'gravityflow_permission_granted_entry_detail', 'sh_filter_gravityflow_permission_granted_entry_detail', 10, 2 );
    function sh_filter_gravityflow_permission_granted_entry_detail( $permission_granted, $entry ) {
        return true;
    }
- Added the gravityflow_complete_label_user_input filter to allow the 'complete' label to be modified on the User Input step.
    Example:
    add_filter( 'gravityflow_complete_label_user_input', 'sh_filter_gravityflow_complete_label_user_input', 10, 2 );
    function sh_filter_gravityflow_complete_label_user_input( $complete_label, $step ) {
        return 'Finished editing';
    }
- Added the gravityflow_in_progress_label_user_input filter to allow the 'in progress' label to be modified on the User Input step.
    Example:
    add_filter( 'gravityflow_in_progress_label_user_input', 'sh_filter_gravityflow_in_progress_label_user_input', 10, 2 );
    function sh_filter_gravityflow_in_progress_label_user_input( $complete_label, $step ) {
        return 'Save for later';
    }
- Added timelines and page break options to bulk printing on the status page.
- Added the gravityflow_inbox_args filter so the inbox criteria can be modified.
- Added the 'Required Reverted or Rejected' to the options in the Workflow note setting.
- Added the gravityflow_status_args filter to allow the status table to be modified.
- Added the gravityflow-instructions and gravityflow-timeline CSS classes.
- Added the gravityflow_webhook_args_[Form ID] filter immediately after the gravityflow_webhook_args filter.

- Updated $field->get_value_export() for the Workflow fields to return the display name.
- Updated the entry meta so that the status columns don't appear automatically in the Gravity Forms entry list.
- Updated the styles of the workflow detail page for narrow screens to display the entry first and then the info box below.

- Fixed an issue with the final status when approval steps are not the last step.
- Fixed an issue with the user input step when the max number of characters setting is set for a field that's not editable.
- Fixed an issue with the widths of the columns on the workflow detail page on some themes.
- Fixed an issue with the workflow note retaining the value after updating the entry.
- Fixed an issue with the styles of the timeline.
- Fixed an issue with the user input step where hidden fields are not displayed.
- Fixed an issue with status list when displaying names of assignees whose accounts no longer exist.
- Fixed an issue on the entry detail page for entries on the approval step and completed entries where product fields are displayed in the list of fields. Product fields are displayed in the order summary but they can also be displayed in the list by selecting the fields in the display fields step setting.
- Fixed an issue with Gravity_Flow_API::get_current_step() for entries that have not started the workflow.
- Fixed an issue with the support form.
- Fixed an issue with the user input step where conditional logic is not disabled correctly in some cases.
- Fixed an issue with the user input step where the save and continue button might appear.
- Fixed an issue with the update button on the user input step under certain conditions.
- Fixed an issue with the field label showing the admin label on approval steps.
- Fixed the feedback after sending an entry to a different step.

= 1.3 =
- Added support for the id, user_email and display_name modifiers for the User field merge tag.
- Added the gravityflow_entry_count_step_list so the entry counts on the step list page can be turned off.
    Example: add_filter( 'gravityflow_entry_count_step_list', '__return_false' );
- Added the highlight editable fields setting to the user input step.
- Added the Order Summary step setting for user input and approval steps with pricing fields.
- Added support for dynamic conditional logic.
- Added the feed extension class.
- Added support for the created_by, and workflow_timeline merge tags within Gravity Forms notifications.
- Added the gravityflow_post_process_workflow action.
    Example: add_action( 'gravityflow_post_process_workflow'. 'sh_gravityflow_post_process_workflow', 10, 4);
    function sh_gravityflow_post_process_workflow( $form, $entry_id, $step_id, $step_id_before_processing ) {
        // Do something every time the workflow is processed.
    }
- Added the gravityflow_update_button_text_user_input filter to allow the button text to be changed on the user input step.
    Example:
        add_filter( 'gravityflow_update_button_text_user_input', 'sh_gravityflow_update_button_text_user_input', 10, 3 );
        function sh_gravityflow_update_button_text_user_input( $text, $form_id, $step ) {
            return 'Submit';
        }
- Added the form ID and field as parameters to the gravityflow_get_users_args_assignee_field and gravityflow_get_users_args_user_field filters.
- Added the step_column, submitter_column and status_column attributes to the shortcode.
- Added support for the display_name attribute in the assignees merge tag. e.g. {assignees: display_name=true}
- Added the instructions setting to the user input and approval steps.
- Added support for an area for instructions at the top of the workflow detail page.
- Added the gravityflow_editable_fields_user_input filter to allow the editable fields array to be modified for the user input step.
    Example:
        add_filter( 'gravityflow_editable_fields_user_input', 'sh_gravityflow_editable_fields_user_input', 10, 2);
        function sh_gravityflow_editable_fields_user_input( $editable_fields, $step ){
            // Use these variable to program your editable fields logic
            // $entry = $step->get_entry();
            // $entry_id = $step->get_entry_id();
            // $form = $step->get_form();
            // $form_id = $step->get_form_id();

            // Return an array of IDs
            // e.g. array( 1, 2, 3 );
            return $editable_fields;
        }
- Added a setting in the user input step to allow field conditional logic to be displayed to the editable fields.
- Added support for sorting on the field columns in the status page.
- Added the gravityflow_permission_denied_message_entry_detail filter to allow the error message to be customized.
- Added the hidden option to the default status setting of the User Input step.
- Added support for the {created_by:[property]} and {assignees} merge tags
- Added support for field validation in the User Input step.
- Added the last_updated attribute to the inbox shortcode to activate the last updated column to appear in the inbox list.
- Added total count indicator below the inbox when entry count > 150.
- Added the timeline attribute to the shortcode so the timeline can be hidden.
- Added the date field option to the schedule setting to allow steps to be scheduled for a date in the entry.
- Added the workflow note setting to the approval and user input steps so the note box can be hidden, required or required depending on the status.
- Added the gravityflow_validation_approval and gravityflow_validation_user_input filters to allow custom validation.
- Added support for required fields in the User Input step.

- Updated Gravity PDF integration so it's fully compatible with Gravity PDF 4.0 RC2.
- Updated the user input conditional logic setting to display an option to deactivate dynamic conditional logic when page conditional logic is present on the form.
- Updated the entry detail page to hide fields when the page is hidden by conditional logic.
- Updated the user input step to display the front end field labels instead of the admin labels.
- Updated styles of the front end validation message.
- Updated the field labels in the entry detail page to display the full label instead of the admin label.
- Updated the workflow detail page to respect the conditional logic rules.
- Updated the auto-update and license check component.


- Fixed an issue with the user input step where values are not displayed in editable fields after saving a previous step in which those field are not editable.
- Fixed an issue with the entry count column in the step list.
- Fixed an issue with the approval step expiration where the emails don't get send.
- Fixed an issue with the status of all the steps afte restarting the workflow.
- Fixed an issue with the order summary setting.
- Fixed an issue with the gravityflow_entry_count_step_list filter.
- Fixed an issue with the validation of the user input step where required fields that are hidden by conditional logic can fail validation.
- Fixed a PHP notice on the entry detail page when accessing the entry when not on a step.
- Fixed an issue affecting access to the entry detail page.
- Fixed an issue with the notification workflow notification where the workflow note merge tag doesn't get replaced.
- Fixed an issue where the gform_field_content was not getting triggered in the workflow detail page.
- Fixed an issue where the workflow complete notifications where the entry contains the wrong status.
- Fixed validation of the file upload field.
- Fixed an issue with the email field with confirmation enabled where the confirmation is not automatically set to the value.
- Fixed an issue with the field column values in the status list.
- Fixed an issue with the email subject not replacing merge tags.
- Fixed an issue with the multi-file upload field where files can't be deleted by email assignees or users authenticating by token.
- Fixed an issue with styles for the inbox shortcode where the field value columns don't adapt well to narrow screens.
- Fixed an issue with the URL in the entry link merge tag.
- Fixed an issue with the timeline notes for email assignees
- Fixed an issue in the inbox where the form name doesn't appear.
- Fixed an issue with the expiration delay calculation for units other than hours.
- Fixed an issue where the confirmation page is not displayed in certain conditions.


= 1.2 =
- Added the {workflow_timeline} merge tag to display a basic timeline with very simple formatting.
- Added the display fields setting to the Approval and User Input steps.
- Added the content of html field to the workflow detail page.
- Added the gravityflow_assignee_status_workflow_detail filter to allow the assignee status label to be modified on the workflow detail page. Currently only supported by the Approval Step.
- Added the gravityflow_webhook_args filter so the webhook request args can be modified.
- Added the gravityflow_post_webhook action which fires after the webhook request.
- Added the token attribute to the workflow entry link merge tag which forces the token to be added to the link regardless of the assignee type. Useful for sending links that don't require login to WordPress users.
- Added restart_step() restart_workflow() send_to_step() add_timeline_note() and log_activity() to Gravity_Flow_API
- Added support for starting workflows when an entry is added using the API.
- Added the GET forms/[ID]/steps Web API endpoint. Returns all the steps for a form.
- Added the GET entries/[ID]/assignees Web API endpoint. Returns all the assignees for the current step of the specified entry.
- Added the GET entries/[ID]/steps Web API endpoint. Returns all the steps for the specified entry.
- Added the POST entries/[ID]/assignees/[KEY] Web API endpoint. Processes a status update for a specified assignee of the current step of the specified entry.
- Added support for step duplication.
- Fixed an issue with the recalculation of calculated fields when not editable.
- Fixed an issue with the display of hidden product fields.
- Fixed an issue with the confirmation page for users with the gravityflow_status_view_all capability when transitioning steps.
- Fixed a deprecation warning on Gravity Forms 2.0
- Fixed an issue preventing upgrade on some Windows systems.
- Fixed an issue with the recalculation of calculated fields hidden by conditional logic.
- Fixed an issue with editable fields on user input steps hidden by conditional logic on form submission.
- Fixed an issue with the timeline note not registering a WordPress user name correctly when using the token in the workflow entry link.
- Fixed an issue after completing a step where assignees might see field values on the next step if they were hidden from the previous step.
- Fixed an issue where the Revert setting doesn't appear for new Approval Steps even though there's a User Input step in the list.
- Fixed an issue on the status page where a warning is displayed if a user account no longer exists.

= 1.1.3 =
- Added support for the revert button in the Approval Step so entries can be sent to a User Input step as a third alternative to "approve" or "reject".
- Added the expiration setting to the approval and user input steps.
- Added the username/step type to the timeline notes classes to allow certain note types to be hidden using CSS.
- Updated the timeline to display the step icon when a user avatar is not available.
- Fixed an issue with the column header texts where the inbox and status pages use different terminology.

= 1.1.2 =
- Added options to the workflow email settings: From Name, From Email, Reply To, BCC, Subject.
- Added support for the User Registration Add-On version 3
- Added support for Gravity PDF 4.
- Added the Workflow Fields section in the form editor.
- Added the User field.
- Added the Role field.
- Added schedule date to the workflow entry detail for queued entries.
- Updated the default number of users returned for settings and for the assignee field from 300 to 1000.
- Fixed an issue with the status shortcode on WordPress 4.4
- Fixed an issue with the schedule date setting for installations in timezones < UTC.
- Fixed an issue with the schedule step setting where the values are not retained after changing the step type.
- Fixed an issue with the assignee by month report where the axis labels were switched.
- Fixed an issue with the status export where the created_by column is missing for forms submitted by anonymous users.
- Fixed a compatibility issue with the Gravity Perks Limit Dates Perk.

= 1.1.1 =
- Added the id_column attribute to the shortcode so the ID column can be hidden.
- Added the Restart Workflow bulk action to the status page.
- Added entries to the status page which were created before steps were added.
- Added support for custom status labels.
- Added support for custom navigation labels.
- Added support for the Signature Add-on in the shortcode.
- Added step icons to the step list.
- Updated the submit page to display the forms in alphabetical order.
- Fixed an issue with the assignee field where the placeholder doesn't work correctly.
- Fixed an issue with Gravity PDF integration in certain situations which prevents the PDF from attaching.
- Fixed an issue with the merge tags in the assignee reminder email.
- Fixed an issue with the assignee field where the number 1 appears at the top of the lists of users and fields.
- Removed the redundant 'workflow: notification step' event in the Gravity Forms notification settings.
- API: Added the Gravity_Flow_Extension class.

= 1.1 =
- Added one-click cancel links so workflows can be cancelled by clicking on a link in an email.
- Added export to the admin UI status list.
- Added support for SMS message steps via Twilio. Requires the Gravity Forms Twilio Add-On and a Twilio account.
- Added support for form import and export. Requires Gravity Forms 1.9.13.29.
- Updated the step type radio buttons to display as buttons with icons.
- Fixed an issue when updating step settings where where entries may not get reassigned correctly to new roles.
- Fixed an issue when duplicating forms where the next step points to another step.
- Fixed the merge tag UI for the Workflow Notification setting on the Notification step.
- Fixed an issue with the status permissions.
- Fixed some untranslatable strings.

= 1.0 =
- All New!
