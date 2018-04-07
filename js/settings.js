;(function (GravityFlowSettings, $) {

	"use strict";

	$(document).ready(function () {
		
		$("form.oauth").submit(function() {
			var inputs = $(this).find("input.required");
			var error = false;
			$.each(inputs, function() {
				if ($(this).val() == "") {
					error = true;
					if ($(this).closest('p').find('p.error').length < 1) {
						$(this).before('<p class="error">' + gravityflow_settings_js_strings.required_fields + '</p>');
					}
				}
				else {
					$(this).closest('p').find('p.error').hide().remove();
				}
			});
			if (error) {
				return false;
			}
		});

		$('#gflow_reauthorize_app').click(function(e) {
			e.preventDefault();
			var secure = gravityflow_settings_js_strings.nonce;
			var app = $(this).data('app');
			$.post(gravityflow_settings_js_strings.ajaxurl, {security: secure, action: 'gravity_flow_reauth_app', app: app}, function(response) {
				if (response.success) {
					window.location.reload();
				}
			})
		});

		$('#new-app').click(function(){
			$(this).hide();
			$('#connected_apps_table_container').hide();
			$('#connected_app_form_container').fadeIn();
		});

		$('#gflow_add_app_cancel').click(function () {

			$('#connected_app_form_container').hide();
			$('#new-app').show();
			$('#connected_apps_table_container').fadeIn();

		});
	});
	
	
}(window.GravityFlowSettings = window.GravityFlowSettings || {}, jQuery));
