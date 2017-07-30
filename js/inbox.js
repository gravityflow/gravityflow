(function (GravityFlowInbox, $) {

	$(document).ready(function () {

		$('.gravityflow-actions-unlock').click( function() {
			var $this = $(this),
				$lock = $this.siblings('.gravityflow-actions-lock'),
				$noteContainer = $this.siblings('.gravityflow-actions-note-field-container'),
				$actionButtons = $this.siblings('.gravityflow-actions');
			$this.hide();
			$lock.show();
			$noteContainer.hide();
			$actionButtons.hide();
			$this.parent('.gravityflow-actions').addClass( 'gravityflow-actions-locked' );
		});

		$('.gravityflow-action').click( function() {
			var $this = $(this),
				$unlock = $this.siblings('.gravityflow-actions-unlock'),
				$lock = $this.siblings('.gravityflow-actions-lock'),
				$noteContainer = $this.siblings('.gravityflow-actions-note-field-container'),
				$noteField = $noteContainer.find('textarea'),
				showNoteField = $this.data('note_field');


			if ( $this.hasClass( 'gravityflow-action-processed' ) ) {
				return;
			}

			if ( $this.parent('.gravityflow-actions').hasClass( 'gravityflow-actions-locked' ) ) {
				$this.parent('.gravityflow-actions').removeClass( 'gravityflow-actions-locked' );
				$lock.hide();
				$unlock.show();

				if ( showNoteField ) {
					$noteContainer.show();
					$noteField.focus();
					$(document).keyup(function(e) {
						var KEYCODE_ESC = 27;
						if (e.keyCode == KEYCODE_ESC) {
							$unlock.click();
						}
					});
				} else {
					setTimeout(function () {
						if ( ! $this.hasClass( 'gravityflow-action-processing' ) && ! $this.hasClass( 'gravityflow-action-processed' ) ) {
							$this.parent('.gravityflow-actions').addClass( 'gravityflow-actions-locked' );
							$lock.show();
							$unlock.hide();
						}
					}, 2000);
				}
				return;
			}

			var entryId = parseInt($this.data('entry_id')),
				restBase = $this.data('rest_base'),
				action = $this.data('action'),
				url = gravityflow_inbox_strings.restUrl,
				nonce = gravityflow_inbox_strings.nonce,
				$spinner = $this.siblings('.gravityflow-actions-spinner');

			$.ajax({
				method: "POST",
				url: url + 'gf/v2/entries/' + entryId + '/workflow/' + restBase,
				data: { 'action' : action, 'gravityflow_note' : $noteField.val() },
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', nonce );
					$this.siblings().andSelf().hide();
					$this.addClass('gravityflow-action-processing');
					$spinner.show();
				},
				success : function( response ) {
					$spinner.hide();
					$this.removeClass('gravityflow-action-processing');
					if ( response.status == 'success' ) {
						$this.addClass('gravityflow-action-processed');
						$this.prop('title', response.feedback);
						$this.show();
						$this.parent('.gravityflow-actions').removeClass( 'gravityflow-actions-locked' );
					} else {
						$this.parent('.gravityflow-actions').addClass( 'gravityflow-actions-locked' );
						$this.siblings('.gravityflow-action').andSelf().show();
						$lock.show();
						alert( response.feedback );
					}
				},
				fail : function( response ) {
					$spinner.hide();
					$unlock.hide();
					$lock.show();
					$this.removeClass('gravityflow-action-processing');
					$this.siblings('.gravityflow-actions').andSelf().show();
					alert( response );
				}

			});
		});
	});

}(window.GravityFlowInbox = window.GravityFlowInbox || {}, jQuery));
