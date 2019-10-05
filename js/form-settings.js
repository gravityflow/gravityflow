;(function (GravityFlowFeedSettings, $) {

	"use strict";

	$(document).ready(function () {

		$('#editable_fields, .gravityflow-multiselect-ui').multiSelect();

		var multiSelectWithSearch = {
			selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='"+gravityflow_form_settings_js_strings.assigneeSearchPlaceholder+"'>",
			selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='"+gravityflow_form_settings_js_strings.assigneeSearchPlaceholder+"'>",
			afterInit: function(ms){
				var that = this,
					$selectableSearch = that.$selectableUl.prev(),
					$selectionSearch = that.$selectionUl.prev(),
					selectableSearchString = '#'+ms.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
					selectionSearchString = '#'+ms.attr('id')+' .ms-elem-selection.ms-selected';

				if ( $('#'+ms.attr('id')+' .ms-elem-selectable').length > 10 ) {
					$('.ms-container .search-input').show();
				}

				that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
					.on('keydown', function(e){
						if (e.which === 40){
							that.$selectableUl.focus();
							return false;
						}
					});

				that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
					.on('keydown', function(e){
						if (e.which == 40){
							that.$selectionUl.focus();
							return false;
						}
					});
			},
			afterSelect: function(){
				this.qs1.cache();
				this.qs2.cache();
			},
			afterDeselect: function(){
				this.qs1.cache();
				this.qs2.cache();
			}
		};

		$('#assignees, #workflow_notification_users').multiSelect(multiSelectWithSearch);

		var gravityFlowIsDirty = false, gravityFlowSubmitted = false;

		$('form#gform-settings').submit(function () {
			gravityFlowSubmitted = true;
			$('form#gform-settings').find(':input').removeAttr('disabled');
		});

		$(':input').change(function () {
			gravityFlowIsDirty = true;
		});

		window.onbeforeunload = function () {
			if (gravityFlowIsDirty && !gravityFlowSubmitted) {
				return "You have unsaved changes.";
			}
		};

		var $stepType = $('input[name=_gaddon_setting_step_type]:checked');
		var selectedStepType = $stepType.val();

		var $statusExpiration = $('#status_expiration');
		var expiredSelected = $statusExpiration.val() == 'expired';
		$('#expiration_sub_setting_destination_expired').toggle(expiredSelected);
		$statusExpiration.change(function () {
			var show = $(this).val() == 'expired';
			$('#expiration_sub_setting_destination_expired').fadeToggle(show);
		});

		setSubSettings();

		var selectedType = $("input[name=_gaddon_setting_type]:checked");
		toggleType(selectedType.val());

		$('#gaddon-setting-row-type input[type=radio]').change(function () {
			toggleType(this.value);
		});

		GravityFlowFeedSettings.getUsersMarkup = function (propertyName) {
			var i, n, account,
				accounts = gf_routing_setting_strings['accounts'],
				str = '<select class="gform-routing-users ' + propertyName + '_{i}">';

			for (i = 0; i < accounts.length; i++) {
				account = accounts[i];
				if (typeof account.choices != 'undefined') {
					var optgroup = '', choice;
					for (n = 0; n < account.choices.length; n++) {
						choice = account.choices[n];
						optgroup += '<option value="{0}">{1}</option>'.format(choice.value, choice.label);
					}
					str += '<optgroup label="{0}">{1}</option>'.format(account.label, optgroup);

				} else {
					str += '<option value="{0}">{1}</option>'.format(account.value, account.label);
				}

			}

			str += "</select>";
			return str;
		};

		var $routingSetting = $('#gform_routing_setting');

		var json = $('#routing').val();

		var routing_items = json ? $.parseJSON(json) : null;

		var options;
		if ($('#editable_fields').length > 0) {
			if (!routing_items) {
				routing_items = [{
					assignee: gf_routing_setting_strings['accounts'][0]['choices'][0]['value'],
					editable_fields: [gf_routing_setting_strings['input_fields'][0]['key']],
					fieldId: '0',
					operator: 'is',
					value: '',
					type: ''
				}];
				$('#user_input_routing').val($.toJSON(routing_items));
			}

			options = {
				fieldName: $routingSetting.data('field_name'),
				fieldId: $routingSetting.data('field_id'),
				settings: gf_routing_setting_strings['fields'],
				accounts: gf_routing_setting_strings['accounts'],
				imagesURL: gf_vars.baseUrl + "/images",
				items: routing_items,
				callbacks: {
					addNewTarget: function (obj, target) {

						var str = GravityFlowFeedSettings.getUsersMarkup('assignee');

						var $fields = $('#editable_fields').clone();
						$fields.attr('name', 'editable_fields');
						var id = $('#gform-routings tbody tr').length;
						$fields.attr('id', 'editable_fields_routing_{i}');
						$fields.attr('style', '');
						$fields.addClass('gform-routing-input-field editable_fields_{i}');

						str += '</td><td>' + $fields[0].outerHTML;
						return str;
					},
					header: function (obj, header) {
						return '<thead><tr><th>Assign To</th><th>Editable Fields</th><th colspan="3">Condition</th></tr></thead>';
					}
				}
			};
		} else {
			if (!routing_items) {
				routing_items = [{
					assignee: gf_routing_setting_strings['accounts'][0]['choices'][0]['value'],
					fieldId: '0',
					operator: 'is',
					value: '',
					type: ''
				}];
				$('#routing').val($.toJSON(routing_items));
			}

			options = {
				fieldName: $routingSetting.data('field_name'),
				fieldId: $routingSetting.data('field_id'),
				settings: gf_routing_setting_strings['fields'],
				accounts: gf_routing_setting_strings['accounts'],
				imagesURL: gf_vars.baseUrl + "/images",
				items: routing_items,
				callbacks: {
					addNewTarget: function (obj, target) {
						var str = GravityFlowFeedSettings.getUsersMarkup('assignee');
						return str;
					}
				}
			};
		}

		$routingSetting.gfRoutingSetting(options);

		// Workflow Notification

		$('#gaddon-setting-row-workflow_notification_type input[type=radio]').click(function () {
			toggleWorkflowNotificationType(this.value);
		});

		var workflowNotificationEnabled = $('#workflow_notification_enabled').prop('checked');
		toggleWorkflowNotificationSettings(workflowNotificationEnabled);
		$('#workflow_notification_enabled').click(function () {
			toggleWorkflowNotificationSettings(this.checked);
		});

		var $workflowNotificationRoutingSetting = $('#gform_user_routing_setting_workflow_notification_routing');

		var workflowNotificationRoutingJSON = $('#workflow_notification_routing').val();

		var workflow_notification_routing_items = workflowNotificationRoutingJSON ? $.parseJSON(workflowNotificationRoutingJSON) : null;

		if (!workflow_notification_routing_items) {
			workflow_notification_routing_items = [{
				assignee: gf_routing_setting_strings['accounts'][0]['choices'][0]['value'],
				fieldId: '0',
				operator: 'is',
				value: '',
				type: '',
			}];
			$('#workflow_notification_routing').val($.toJSON(workflow_notification_routing_items));
		}

		var workflowNotificationOptions = {
			fieldName: $workflowNotificationRoutingSetting.data('field_name'),
			fieldId: $workflowNotificationRoutingSetting.data('field_id'),
			settings: gf_routing_setting_strings['fields'],
			accounts: gf_routing_setting_strings['accounts'],
			imagesURL: gf_vars.baseUrl + "/images",
			items: workflow_notification_routing_items,
			callbacks: {
				addNewTarget: function (obj, target) {
					var str = GravityFlowFeedSettings.getUsersMarkup('assignee');
					return str;
				}
			}
		};

		$workflowNotificationRoutingSetting.gfRoutingSetting(workflowNotificationOptions);

		// Notification Tabs

		GravityFlowFeedSettings.initNotificationTab = function (type) {
			$('#' + type + '_notification_users').multiSelect(multiSelectWithSearch);

			var $enabledSetting = $('#' + type + '_notification_enabled');

			toggleNotificationTabSettings($enabledSetting.prop('checked'), type);

			$enabledSetting.click(function () {
				toggleNotificationTabSettings(this.checked, type);
			});

			$('#gaddon-setting-tab-field-' + type + '_notification_type input[type=radio]').click(function () {
				toggleNotificationTabSettings(true, type);
			});

			var $routingSetting = $('#gform_user_routing_setting_' + type + '_notification_routing');

			if ($routingSetting.length) {
				var $routingJSONInput = $('#' + type + '_notification_routing'),
					routingJSON = $routingJSONInput.val(),
					routingItems = routingJSON ? $.parseJSON(routingJSON) : null;

				if (!routingItems) {
					routingItems = [{
						assignee: gf_routing_setting_strings['accounts'][0]['choices'][0]['value'],
						fieldId: '0',
						operator: 'is',
						value: '',
						type: ''
					}];
					$routingJSONInput.val($.toJSON(routingItems));
				}

				var routingOptions = {
					fieldName: $routingSetting.data('field_name'),
					fieldId: $routingSetting.data('field_id'),
					settings: gf_routing_setting_strings['fields'],
					accounts: gf_routing_setting_strings['accounts'],
					imagesURL: gf_vars.baseUrl + "/images",
					items: routingItems,
					callbacks: {
						addNewTarget: function (obj, target) {
							return GravityFlowFeedSettings.getUsersMarkup('assignee');
						}
					}
				};

				$routingSetting.gfRoutingSetting(routingOptions);
			}

		};

		var notificationTabs = ['assignee', 'rejection', 'approval', 'in_progress', 'complete', 'revert'];

		for (var i = 0; i < notificationTabs.length; i++) {
			GravityFlowFeedSettings.initNotificationTab(notificationTabs[i]);
		}

		// User Input - Save Progress Option/In Progress Email Tab

		var $saveProgressSetting = $('#default_status');
		if ($saveProgressSetting.val() === 'hidden') {
			$('#tabs-notification_tabs').tabs('disable', 1);
		}

		$saveProgressSetting.change(function () {
			var disabled = $(this).val() === 'hidden',
				$notificationTabs = $('#tabs-notification_tabs');
			if (disabled) {
				var $enabledSetting = $('#in_progress_notification_enabled');

				// Disable the In Progress notification if enabled.
				if ($enabledSetting.prop('checked')) {
					$enabledSetting.click();
				}

				// If the In Progress Email tab is active switch to the Assignee Email tab.
				if ($notificationTabs.tabs('option', 'active') === 1) {
					$notificationTabs.tabs('option', 'active', 0);
				}

				$notificationTabs.tabs('disable', 1);
			} else {
				$notificationTabs.tabs('enable', 1);
			}
		});

		// Approval - Revert Email Tab

		var $revertSetting = $('#revertenable');
		if ( !$revertSetting.prop("checked")) {
			$('#tabs-notification_tabs').tabs('disable', 3);

		}

		$revertSetting.change(function () {
			var disabled = $(this).prop("checked");
			var $notificationTabs = $('#tabs-notification_tabs');
			if (!disabled) {
				var $enabledSetting = $('#revert_notification_enabled');

				// Disable the Revert notification if enabled.
				if ($enabledSetting.prop('checked')) {
					$enabledSetting.click();
				}

				// If the Revert Email tab is active switch to the Assignee Email tab.
				if ($notificationTabs.tabs('option', 'active') === 1) {
					$notificationTabs.tabs('option', 'active', 0);
				}

				$notificationTabs.tabs('disable', 3);
			} else {
				$notificationTabs.tabs('enable', 3);
			}
		});

		//-----

		if (window.gform) {
			gform.addFilter('gform_merge_tags', GravityFlowFeedSettings.gravityflow_add_merge_tags);
		}

		if (window['gformInitDatepicker']) {
			gformInitDatepicker();
		}

		loadMessages();

	});

	function toggleNotificationTabSettings(enabled, notificationType) {
		var $NotificationTypeSetting = $('#gaddon-setting-tab-field-' + notificationType + '_notification_type');
		$NotificationTypeSetting.toggle(enabled);
		if (enabled) {
			var selected = $NotificationTypeSetting.find('input[type=radio]:checked').val();
			toggleNotificationTabFields(selected, notificationType);
			$('#gaddon-setting-tab-tab_' + notificationType + '_notification i.gravityflow-tab-checked').show();
			$('#gaddon-setting-tab-tab_' + notificationType + '_notification i.gravityflow-tab-unchecked').hide();
		} else {
			toggleNotificationTabFields('off', notificationType);
			$('#gaddon-setting-tab-tab_' + notificationType + '_notification i.gravityflow-tab-checked').hide();
			$('#gaddon-setting-tab-tab_' + notificationType + '_notification i.gravityflow-tab-unchecked').show();
		}
	}

	function toggleNotificationTabFields(showType, notificationType) {
		var fields = ['users', 'routing', 'from_name', 'from_email', 'reply_to', 'cc', 'bcc', 'subject', 'message', 'autoformat', 'resend', 'gpdf'],
			prefix = '#gaddon-setting-tab-field-' + notificationType + '_notification_';

		$.each(fields, function (i, field) {
			$(prefix + field).hide();
		});

		if (showType == 'off') {
			return;
		}

		$.each(fields, function (i, field) {
			if (field == 'users' && showType == 'routing' || field == 'routing' && showType == 'select') {
				return true;
			}

			$(prefix + field).fadeToggle('normal');
		});
	}

	function toggleWorkflowNotificationType(showType) {
		var fields = {
			select: ['workflow_notification_users\\[\\]', 'workflow_notification_from_name', 'workflow_notification_from_email', 'workflow_notification_reply_to', 'workflow_notification_cc', 'workflow_notification_bcc', 'workflow_notification_subject', 'workflow_notification_message', 'workflow_notification_autoformat', 'workflow_notification_gpdf'],
			routing: ['workflow_notification_routing', 'workflow_notification_from_name', 'workflow_notification_from_email', 'workflow_notification_reply_to', 'workflow_notification_cc', 'workflow_notification_bcc', 'workflow_notification_subject', 'workflow_notification_message', 'workflow_notification_autoformat', 'workflow_notification_gpdf']
		};
		toggleFields(fields, showType, false);
	}

	function toggleType(showType) {
		var fields = {
			select: ['assignees\\[\\]', 'editable_fields\\[\\]', 'conditional_logic_editable_fields_enabled'],
			routing: ['routing', 'conditional_logic_editable_fields_enabled']
		};

		toggleFields(fields, showType);
	}

	function toggleFields(fields, showType, isTab) {
		var prefix = isTab ? '#gaddon-setting-tab-field-' : '#gaddon-setting-row-';
		$.each(fields, function (type, activeFields) {
			$.each(activeFields, function (i, activeField) {
				$(prefix + activeField).hide();
			});
		});

		$.each(fields, function (type, activeFields) {
			if (showType == type) {
				$.each(activeFields, function (i, activeField) {
					$(prefix + activeField).fadeToggle('normal');
				});
			}
		});
	}

	function toggleWorkflowNotificationSettings(enabled) {
		var $workflowNotificationType = $('#gaddon-setting-row-workflow_notification_type');
		$workflowNotificationType.toggle(enabled);
		if (enabled) {
			var selected = $workflowNotificationType.find('input[type=radio]:checked').val();
			toggleWorkflowNotificationType(selected);
		} else {
			toggleWorkflowNotificationType('off');
		}
	}

	function setSubSettings() {
		var subSettings = [
			'routing',
			'assignees\\[\\]',
			'assignee_notification_from_name',
			'assignee_notification_from_email',
			'assignee_notification_reply_to',
			'assignee_notification_cc',
			'assignee_notification_bcc',
			'assignee_notification_subject',
			'assignee_notification_message',
			'assignee_notification_autoformat',
			'resend_assignee_email',
			'assignee_notification_gpdf',
			'rejection_notification_type',
			'rejection_notification_users\\[\\]',
			'rejection_notification_user_field',
			'rejection_notification_routing',
			'rejection_notification_message',
			'rejection_notification_autoformat',
			'approval_notification_type',
			'approval_notification_users\\[\\]',
			'approval_notification_user_field',
			'approval_notification_routing',
			'approval_notification_message',
			'approval_notification_autoformat',

			'workflow_notification_type',
			'workflow_notification_users\\[\\]',
			'workflow_notification_user_field',
			'workflow_notification_routing',
			'workflow_notification_from_name',
			'workflow_notification_from_email',
			'workflow_notification_reply_to',
			'workflow_notification_cc',
			'workflow_notification_bcc',
			'workflow_notification_subject',
			'workflow_notification_message',
			'workflow_notification_autoformat',

			'assignees\\[\\]',
			'editable_fields\\[\\]',
			'routing',
			'assignee_notification_message',
			'workflow_notification_gpdf'

		];
		for (var i = 0; i < subSettings.length; i++) {
			$('#gaddon-setting-row-' + subSettings[i]).addClass('gravityflow_sub_setting');
		}
	}

	GravityFlowFeedSettings.gravityflow_add_merge_tags = function (mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {
		if (isPrepop) {
			return mergeTags;
		}

		addCommonMergeTags(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option);
		addAprovalMergeTags(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option);

		return mergeTags;
	};

	function addCommonMergeTags(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {

		var supportedElementIds = [
			'_gaddon_setting_workflow_notification_message',
			'_gaddon_setting_assignee_notification_message',
			'_gaddon_setting_approval_notification_message',
			'_gaddon_setting_rejection_notification_message',
			'_gaddon_setting_revert_notification_message',
		];

		if (supportedElementIds.indexOf(elementId) < 0) {
			return mergeTags;
		}

		var labels = gravityflow_form_settings_js_strings.mergeTagLabels,
			tags = [];

		tags.push({tag: '{workflow_entry_link}', label: labels.workflow_entry_link});
		tags.push({tag: '{workflow_entry_url}', label: labels.workflow_entry_url});
		tags.push({tag: '{workflow_inbox_link}', label: labels.workflow_inbox_link});
		tags.push({tag: '{workflow_inbox_url}', label: labels.workflow_inbox_url});
		tags.push({tag: '{workflow_cancel_link}', label: labels.workflow_cancel_link});
		tags.push({tag: '{workflow_cancel_url}', label: labels.workflow_cancel_url});
		tags.push({tag: '{workflow_note}', label: labels.workflow_note});
		tags.push({tag: '{workflow_timeline}', label: labels.workflow_timeline});
		tags.push({tag: '{assignees}', label: labels.assignees});

		mergeTags['gravityflow'] = {
			label: labels.group,
			tags: tags
		};

		return mergeTags;

	}

	function addAprovalMergeTags(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {
		var supportedElementIds = [
			'_gaddon_setting_assignee_notification_message',
		];

		if (supportedElementIds.indexOf(elementId) < 0) {
			return mergeTags;
		}

		var labels = gravityflow_form_settings_js_strings.mergeTagLabels,
			tags = [];

		tags.push({tag: '{workflow_approve_link}', label: labels.workflow_approve_link});
		tags.push({tag: '{workflow_approve_url}', label: labels.workflow_approve_url});
		tags.push({tag: '{workflow_approve_token}', label: labels.workflow_approve_token});
		tags.push({tag: '{workflow_reject_link}', label: labels.workflow_reject_link});
		tags.push({tag: '{workflow_reject_url}', label: labels.workflow_reject_url});
		tags.push({tag: '{workflow_reject_token}', label: labels.workflow_reject_token});

		if (typeof mergeTags['gravityflow'] != 'undefined') {
			mergeTags['gravityflow']['tags'] = $.merge(mergeTags['gravityflow']['tags'], tags);
		} else {
			mergeTags['gravityflow'] = {
				label: labels.group,
				tags: tags
			};
		}

		return mergeTags;
	}

	function loadMessages() {
		var feedId = gravityflow_form_settings_js_strings['feedId'];
		if (feedId > 0) {
			var url = ajaxurl + '?action=gravityflow_feed_message&fid=' + feedId + '&id=' + gravityflow_form_settings_js_strings['formId'];
			$.get(url, function (response) {
				var $heading = $('#save_button');
				$heading.before(response);
			});
		}

	}

}(window.GravityFlowFeedSettings = window.GravityFlowFeedSettings || {}, jQuery));


