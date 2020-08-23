function SetDefaultValues_workflow_assignee_select(field) {

	field.gravityflowAssigneeFieldShowUsers = true;
	field.gravityflowAssigneeFieldShowRoles = true;
	field.gravityflowAssigneeFieldShowFields = true;

	field.choices = '';


	return field;
}

function SetDefaultValues_workflow_user(field) {

	field.label = gravityflow_form_editor_js_strings.user.defaults.label;
	field.choices = '';

	return field;
}

function SetDefaultValues_workflow_role(field) {

	field.label = gravityflow_form_editor_js_strings.role.defaults.label;
	field.choices = '';

	return field;
}

function SetDefaultValues_workflow_discussion(field) {
	field.label = gravityflow_form_editor_js_strings.discussion.defaults.label;
}

function SetDiscussionTimestampFormat(format) {
	SetFieldProperty('gravityflowDiscussionTimestampFormat', format);
	RefreshSelectedFieldPreview();
}

function SetAssigneeFieldShowUsers() {
	var value = jQuery('#gravityflow-assignee-field-show-users').is(':checked');
	SetFieldProperty('gravityflowAssigneeFieldShowUsers', value);

	var roleFilter = jQuery('li.gravityflow_setting_users_role_filter');

	if (!value) {
		roleFilter.hide('slow', function () {
			jQuery('#gravityflow_users_role_filter').val('');
			SetFieldProperty('gravityflowUsersRoleFilter', '');
		});
	} else {
		roleFilter.show('slow');
	}
}

jQuery(document).bind('gform_load_field_settings', function (event, field, form) {
	var isAssigneeField = field.type == 'workflow_assignee_select';
	var isWorkflowUserField = field.type == 'workflow_user';
	var isWorkflowRoleField = field.type == 'workflow_role';
	var isWorkflowDiscussionField = field.type == 'workflow_discussion';
	var isWorkflowMultiUserField = field.type == 'workflow_multi_user';

	if ( isAssigneeField || isWorkflowUserField || isWorkflowRoleField || isWorkflowMultiUserField ) {
		field.choices = '';
	}

	if (isAssigneeField) {
		var showUsers = field.gravityflowAssigneeFieldShowUsers;

		jQuery('#gravityflow-assignee-field-show-users').prop('checked', !!showUsers);
		jQuery('#gravityflow-assignee-field-show-roles').prop('checked', !!field.gravityflowAssigneeFieldShowRoles);
		jQuery('#gravityflow-assignee-field-show-fields').prop('checked', !!field.gravityflowAssigneeFieldShowFields);

		if (showUsers) {
			jQuery('li.gravityflow_setting_users_role_filter').toggle();
		}
	}

	if ( isAssigneeField || isWorkflowUserField || isWorkflowMultiUserField ) {
		jQuery('#gravityflow_users_role_filter').val(field.gravityflowUsersRoleFilter);
	}

	if ( isWorkflowDiscussionField ) {
		var timestamp_format = field.gravityflowDiscussionTimestampFormat == undefined ? '' : field.gravityflowDiscussionTimestampFormat;
		jQuery('#gravityflow_discussion_timestamp_format').val(timestamp_format);
	}

});

jQuery(document).ready(function () {

	// Allow admin-only field to be used in conditional logic.
	gform.addFilter('gform_is_conditional_logic_field', function (isConditionalLogicField, field) {
		if (field.adminOnly || field.visibility == 'administrative') {
			var inputType = field.inputType ? field.inputType : field.type,
				supported_fields = GetConditionalLogicFields(),
				index = jQuery.inArray(inputType, supported_fields);

			isConditionalLogicField = index >= 0 ? true : false;
		}
		return isConditionalLogicField;
	}, 20 );
});
