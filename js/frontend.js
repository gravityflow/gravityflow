(function (GravityFlowFrontEnd, $) {

	$(document).ready(function () {
		var checks, first, last, checked, sliced, mobileEvent, transitionTimeout, focusedRowActions, $firstHeading,
			lastClicked = false;

		// check all checkboxes
		$('tbody').children().children('.check-column').find(':checkbox').click(function (e) {
			if ('undefined' == e.shiftKey) {
				return true;
			}
			if (e.shiftKey) {
				if (!lastClicked) {
					return true;
				}
				checks = $(lastClicked).closest('form').find(':checkbox');
				first = checks.index(lastClicked);
				last = checks.index(this);
				checked = $(this).prop('checked');
				if (0 < first && 0 < last && first != last) {
					sliced = ( last > first ) ? checks.slice(first, last) : checks.slice(last, first);
					sliced.prop('checked', function () {
						if ($(this).closest('tr').is(':visible'))
							return checked;

						return false;
					});
				}
			}
			lastClicked = this;

			// toggle "check all" checkboxes
			var unchecked = $(this).closest('tbody').find(':checkbox').filter(':visible').not(':checked');
			$(this).closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function () {
				return ( 0 === unchecked.length );
			});

			return true;
		});

		$('thead, tfoot').find('.check-column :checkbox').on('click.wp-toggle-checkboxes', function (event) {
			var $this = $(this),
				$table = $this.closest('table'),
				controlChecked = $this.prop('checked'),
				toggle = event.shiftKey || $this.data('wp-toggle');

			$table.children('tbody').filter(':visible')
				.children().children('.check-column').find(':checkbox')
				.prop('checked', function () {
					if ($(this).is(':hidden')) {
						return false;
					}

					if (toggle) {
						return !$(this).prop('checked');
					} else if (controlChecked) {
						return true;
					}

					return false;
				});

			$table.children('thead,  tfoot').filter(':visible')
				.children().children('.check-column').find(':checkbox')
				.prop('checked', function () {
					if (toggle) {
						return false;
					} else if (controlChecked) {
						return true;
					}

					return false;
				});
		});		
	});

	

}(window.GravityFlowFrontEnd = window.GravityFlowFrontEnd || {}, jQuery));
