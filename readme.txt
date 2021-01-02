=== Gravity Flow ===
Contributors: stevehenty
Tags: workflow, approvals, gravity forms
Requires at least: 5.2
Tested up to: 5.5.1
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add workflow processes to your Gravity Forms.

== Description ==

Gravity Flow is a business process workflow platform for WordPress.


= Who is it for? =

Gravity Flow is for organisations and departments of any size that need to get a form-based workflow process up and running online quickly with no programming. These processes usually already exist either offline or online but are often inefficiently implemented.

= How does it work? =

An end-user submits a web form which generates an entry. The entry is then passed around between users and systems on an established path until the process is complete. Each user or system in the workflow will add something to the process before allowing the entry to proceed to the next step.

For example, an employee may add additional information and a manager another might add their approval. Connected systems might send an email, add the user to a mailing list, create a user account or send data to an ERP system.

Gravity Flow requires [Gravity Forms](https://gravityflow.io/out/gravityforms)

Facebook: [Gravity Flow](https://facebook.com/gravityflow.io)

Twitter: [Gravity Flow](https://twitter.com/GravityFlow_io)

= Requirements =

1. [Purchase and install Gravity Forms](https://gravityflow.io/out/gravityforms)
2. Wordpress 4.2+
3. Gravity Forms 2.3+


= Support =

If you find any that needs fixing, or if you have any ideas for improvements, please get in touch:
https://gravityflow.io/contact/


== Installation ==

1.  Download the zipped file.
1.  Extract and upload the contents of the folder to /wp-contents/plugins/ folder
1.  Go to the Plugin management page of WordPress admin section and enable the 'Gravity Flow' plugin

== Frequently Asked Questions ==

= Which license of Gravity Forms do I need? =
Gravity Flow will work with any license of [Gravity Forms](https://gravityflow.io/out/gravityforms).

== ChangeLog ==

= 2.6 =
- Added support for Gravity Forms 2.5.
- Added the 'gravityflow_inbox_count_display' filter to allow the inbox count to be hidden in the WordPress admin menu.
- Added the 'gravityflow_major_version_auto_updates_allowed' filter to allow only minor versions to be auto-updated.
- Fixed an issue with Step Assignees on User Input Step causing a Fatal Error.


= 2.5.12 =
- Added the red bubble inbox count display on the WP Dashboard Workflow menu item.
- Added support for the WordPress 5.5 enable/disable auto-updates feature on the installed plugins page.
- Updated the CSS classname 'wrap' by making it specific as 'gravityflow_wrap'. This avoids conflicts with the CSS rules of some themes.
- Fixed an issue where GP Limit Dates does not function on the User Input step when the min and max range is based on non-editable Date fields.
- Fixed an issue where merge tags for the Confirmation Message in the User Input Step are evaluated before the workflow step completes.
- Fixed an issue on the User Registration Step, where manual User Activation feed setting was sending email with activation link to User.
- Fixed an issue on the User and Multi-User field 'Users Role Filter' setting display (but not filter save).
- Fixed an issue on front-end pages with WordPress 5.5 removing the page parameter.
- Fixed an issue where combination of inbox shortcode arguments would prevent role based assignees from accessing submit button.


= 2.5.11 =
- Fixed issue with merge tag evaluation that caused a fatal error involving certain conditional logic setup. Update Form Connector add-on will also be required if installed.
- Fixed an issue with second layer confirmation for Approval Step. When the Revert to User Input step option is enabled, the Revert button was not displaying the confirm box.
- Fixed support for latest versions of Gravity View Advanced Filters. Credit: The team at GravityView.
- Fixed an issue with the reports shortcode/page where the "Step Assignees" dropdown was missing when "Category" is set to "Step" and a step is chosen.

= 2.5.10 =
- Added security enhancements.
- Added support for reports in the shortcode [gravityflow page="reports"].
- Added setting to allow the reports shortcode to display reports to display to anonymous users.
- Added support for the Pods Gravity Forms Add-On.
- Added support for the GP eCommerce Fields on the User Input Step.
- Added support for the Gravity Forms EmailOctopus Add-On.
- Added support for Merge Tags on Workflow Step Conditions.
- Added support for Gravity Forms conditional shortcode within Outgoing webhook raw body requests.
- Added filter gravityflow_send_to_step_condition_met_required to allow customization of the API send_to_step when the proposed steps conditions are not met. Default is false that API will always send to the proposed step.
- Added filter gravityflow_send_to_step_condition_not_met to allow customization of the API when the proposed next step has not met its step conditions. Defaults to next step after the proposed step.
- Added workflow note when the Update User step is completed.
- Added support for filter gform_pre_validation on the User Input Step.
- Added a second layer confirmation for Approval Step with a confirm box. This can be enabled in the step settings.
- Added filter gravityflow_approval_confirm_prompt_messages to allow customization of the confirmation prompt messages on the Approval Step.
- Updated the Zapier step to support Gravity Forms Zapier Add-On v4.0-beta-1.1 and greater.
- Updated Outgoing Webhook settings to allow GET requests to include the request body settings as URL parameters.
- Updated gravityflow_print_styles filter to include 2nd parameter of entry IDs.
- Fixed an issue with the User Input Step where editable Total, Coupon, Subtotal, Tax, or Discount fields required all other pricing fields to be editable to function correctly.
- Fixed an issue with the User Input Step where the existing Coupon field value is not restored. Requires Coupons Add-On v2.9.2 or greater.
- Fixed an issue with the User Input Step where the Coupon usage count is not updated.
- Fixed a PHP notice which occurs when the shortcode is processed and WordPress isn't able to provide the current post object.
- Fixed a PHP warning on the inbox page when a step is configured with a delay based due date and the offset setting contains non-numeric characters.
- Fixed notices with PHP 7.4.
- Fixed an issue where the Dropbox step does not complete with Dropbox Add-On 2.4.1+.
- Fixed an issue where the report data for assignees by month is incorrect.
- Fixed an issue where the Discussion Field view more/less effect would not operate on the Form > Entries screen (both view and edit).
- Fixed an issue where the scripts and styles are not enqueued when using the shortcode or block in a reusable block.
- Fixed an issue in the step settings page where duplicate Workflow step icons appear for the Gravity Forms HubSpot Add-on and third-party HubSpot Add-on. IMPORTANT: check that your HubSpot workflow steps are correct after updating to this version.
- Fixed a bug with 'Schedule expiration' on Workflow step. 'Next Step if Expired' option should only appear when 'Status after expiration' is set as 'Expired'.
- Fixed the field filter not appearing on the status page when the form_id constraint is set to an array using the gravityflow_status_filter hook.
- Fixed an issue for the completed URL of Partial Entry Submission step, which was directing to a new form entry. Updated to return a message stating that the entry was already completed.
- Fixed an issue with the Connected Apps comparing authorization value from translated text.


= 2.5.9 =
- Fixed incorrect feedback when user input step confirmation message content is left empty and the step is completed.
- Fixed an issue when attempting to modify step settings for a step type that is not active (plugin deactivated, incorrect permissions).
- Fixed an issue where PHP fatal error is thrown in the filter_gravityview_common_get_entry_check_entry_display() method.
- Fixed an issue where PHP fatal error is thrown when processing expired entries with steps that no longer exist.
- Fixed an issue where a multi-select on User Input step settings pages would shrink to a very small width on smaller window resolutions.
- Fixed a PHP notice which can occur when conditional logic is evaluated and the rule is not based on a form field.
- Fixed an issue where workflow merge tags would not be available in the merge tag dropdown for notification content fields in certain steps.
- Fixed an issue where revert notifications would not include the latest workflow note.
- Fixed an issue where PHP fatal error is thrown from workflow detail link integration with Gravity View 2.5. Credit: the GravityView Team.
- Updated the Members integration to include the missing Status page Admin Actions capability.
- Updated the hook used in the WP E-Signature Step to allow all signatures to be collected before continuing workflow.
- Updated the translations for Catalan and Arabic.

= 2.5.8 =
- Added support for the delayed payment enhancements in Gravity Forms 2.4.13. Workflow processing can now be delayed when using PayPal Standard, Stripe (Checkout), and other payment add-ons which support the Post Payment Action setting.
- Added the filter gravityflow_timeline_note_add to support customizing the potential note to add to timeline.
- Added the filter gravityflow_timeline_notes to support customizing the display of timeline notes.
- Added support for CC: field to all Gravity Flow notification types.
- Fixed the "view more" link for the Discussion field being output when merge tags are processed for posts created by the Advanced Post Creation Add-On.
- Fixed the notification tab for Revert Email on Approval step to have the workflow merge tags options.


= 2.5.7 =
- Fixed issue where status page filters show a search box.


= 2.5.6 =
- Added filter gravityflow_entry_url_inbox_table to allow customization of the link from inbox.
- Added filter gravityflow_approval_revert_step_id to allow customization of the revert step.
- Added Merge Tag selector to Outgoing Webhook step settings for raw request body.
- Added the filter gravityflow_step_is_condition_met to enable complex conditional logic for start (or not) a given step.
- Fixed the Post Creation step creating duplicate posts when the workflow or step is restarted.
- Fixed back link display on entry detail page to use css with class gravityflow-back-link-container instead of line breaks for spacing.
- Fixed PHP 7.3 compatibility warning related to entry detail screen display.
- Fixed number field display on user input steps when the field contained 0 value.
- Fixed delayed Zapier steps send duplicate content from the first entry in queue.
- Fixed an issue when gravityflow_form_ids_status runs to ensure entry counts + pagination counts match filtering form IDs.


= 2.5.5 =
- Added security enhancements.
- Added translations for French, Portuguese, Italian, Swedish, Dutch, Turkish, German and Spanish.
- Fixed an issue with the User Input step where the product order summary table is not displayed unless a product field is included in the display fields.
- Fixed an issue with the workflow entry detail page where all fields are displayed to admins instead of only the display fields.

= 2.5.4 =
- Added the gravityflow_can_render_form filter.
- Updated translations.
- Fixed a rare fatal error on the status page when steps are missing.

= 2.5.3 =
- Added security enhancements.
- Added support for Gravity Flow editor blocks - coming soon!
- Added filter gravityflow_assignee_email_reminder_repeat_days and added deprecation notice for gravityflow_assignee_eamil_reminder_repeat_days.
- Added support for the forms attribute in the submit shortcode so the workflow forms can be filtered. e.g. [gravityflow page="submit" forms="1,2,3"]
- Added support for the back_link back_link_text and back_link_url attributes in the status shortcode.
- Added support for multiple forms in the status shortcode e.g. [gravityflow page="status" form="1,2,3"]
- Added support for displaying the first 2,000 users in assignee settings.
- Updated translations.
- Updated the submit shortcode to display only the published workflows or the forms specified in the forms attribute.
- Fixed an issue with notification step not identifying all users in a multi-user field for notification.
- Fixed an issue when Sliced Invoices status was manually updated to paid, entries weren't released from Sliced Invoices steps.
- Fixed an issue with the Status shortcode where users with the gravityflow_status_view_all capability don't see all entries when the shortcode security settings are set to disallow the display_all attribute.
- Fixed validation issue in Assignee, User and Multi-User fields.
- Fixed an issue with the confirmation page for users with the gravityflow_status_view_all capability when transitioning steps.
- Fixed a PHP notice when loading feed message with AJAX.


= 2.5.2 =
- Added security enhancements.
- Fixed an issue with due date column of inbox shortcode displaying a default value when no step settings had been defined.
- Fixed an issue with the inbox incorrectly highlighting some entries as overdue.
- Fixed the workflow stalling on the Dropbox step when there are no files to process.
- Fixed an issue with the display of schedule date field settings.

= 2.5.1 =
- Fixed an issue where the start step settings are not displayed unless the Partial Entries Add-On is active.

= 2.5 =
- Added security enhancements.
- Added support for the Gravity Forms Partial Entries Add-On.
- Added DB performance improvements.
- Added the Update User step to update a user profile - requires the edit_users capability.
- Added link on entry detail page, shortcode only, to 'Return to list' which links user back to inbox / status page.
- Added shortcode attribute back_link (default: false) to identify if back link should be displayed on entry detail for entries loaded via shortcode.
- Added shortcode attribute back_link_text (default: "Return to list" translatable) to allow customization of text for back link on entry detail page.
- Added shortcode attribute back_link_url (default: null) to allow customization of back link on entry detail page.
- Added filter gravityflow_back_link_url_entry_detail to allow customization of back link on entry detail page.
- Added filter gravityflow_search_criteria_status to allow status page to filter on multiple field criteria.
- Added step setting type for due date with sub-options for delay/date/field selection and row highlighting for inbox.
- Added the due date settings to the approval and user input steps.
- Added inbox and status attribute due_date (false by default) to show due date column.
- Added shortcode security settings to the Workflow->Setting page.
- Added the filter gravityflow_next_step to allow customization of the next step for workflow processing (example - restart a completed user input step for new assignees to action)
- Fixed the workflows for GP Nested Forms child entries starting before some Gravity Perks extensions had updated the entries.
- Fixed an issue that prevented conditional routing from correctly matching date field conditions.
- Fixed an issue that assignee fields with placeholder text would have invalid blank selection options on user input steps.
- Fixed the outgoing webhook for steps with raw request body defined to have field merge tag values properly escaped.
- Fixed an issue with the outgoing webhook step where GET requests using connected apps do not include the authorization headers.
