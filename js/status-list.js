
(function (GravityFlowStatusList, $) {
    var page = 1, filters;
    $(document).ready(function () {
        $('#doaction, #doaction2').click(function(){

            var action = $(this).prev('select').val();

            if ( action == 'print' ) {
				tb_show('Print Entries', '#TB_inline?width=350&amp;height=250&amp;inlineId=print_modal_container', '');
				return false;
            }

		});

		$('#gravityflow-bulk-print-button').click(function(){
			var checkedValues = $('.gravityflow-cb-entry-id:checked').map(function() {
				return this.value;
			}).get();
			var timelinesQS = $('#gravityflow-print-timelines').is(':checked') ? '&timelines=1' : '';
			var pageBreakQS = jQuery('#gravityflow-print-page-break').is(':checked') ? '&page_break=1' : '';
			printPage( gravityflow_status_list_strings.ajaxurl + '?action=gravityflow_print_entries&lid=' + checkedValues.join(',') + timelinesQS + pageBreakQS );
			return false;
		});

        $('.gravityflow-export-status-button').click(function(){
            var $this = $(this);
            $this.addClass('button-disabled');
            filters = $this.data('filter_args');
            var s = $this.next('.spinner');
            $this.next('.gravityflow-spinner').show();
            processExport();
        });

        function processExport(){
            var url;
            url = ajaxurl + '?action=gravityflow_export_status&order=asc&paged=' + page;
            url += filters;
            $.getJSON(url, function(data){
                if ( data.status =='complete' ) {
                    window.location = data.url;
                } else if( data.status =='incomplete' ) {
                    processExport( page++ );
                } else {
                    alert(data.message);
                }
                $('.gravityflow-export-status-button.button-disabled').next('.gravityflow-spinner').hide();
                $('.gravityflow-export-status-button.button-disabled').removeClass('button-disabled');
            });
        }
    });



}(window.GravityFlowStatusList = window.GravityFlowStatusList || {}, jQuery));

function closePrint () {
    var frames = document.getElementsByClassName("gravityflow-print-frame");
    if (frames.length !== 0) {
        frames[0].remove();
    }
}

function setPrint () {
    this.contentWindow.__container__ = this;
    this.contentWindow.focus();
    var ms_ie = false;
    var ua = window.navigator.userAgent;
    var old_ie = ua.indexOf('MSIE ');
    var new_ie = ua.indexOf('Trident/');

    if ((old_ie > -1) || (new_ie > -1)) {
        ms_ie = true;
    }

    if ( ms_ie ) {
        this.contentWindow.document.execCommand('print', false, null);
    } else {
        this.contentWindow.print();
    }
	
    setTimeout( closePrint, 100 );
}

function printPage (sURL) {
    var oHiddFrame = document.createElement("iframe");
    oHiddFrame.classList.add("gravityflow-print-frame");
    oHiddFrame.onload = setPrint;
    oHiddFrame.style.visibility = "hidden";
    oHiddFrame.style.position = "fixed";
    oHiddFrame.style.right = "0";
    oHiddFrame.style.bottom = "0";
    oHiddFrame.src = sURL;
    document.body.appendChild(oHiddFrame);
}
