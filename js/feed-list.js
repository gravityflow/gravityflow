(function (GravityFlow, $) {

    $(document).ready(function () {

        if ( $('table.wp-list-table tbody tr').length == 1 ) {
            return;
        }

        var hasStartStep = gravityflow_feed_list_strings.hasStartStep,
            hasCompleteStep = gravityflow_feed_list_strings.hasCompleteStep;

        $.each( $('.wp-list-table tbody tr'), function() { 
            $( this ).css( 'border-left', '5px solid ' + $(this).find('.step_highlight_color').css( 'background-color' ) );
        });
        $('.wp-list-table .step_highlight').remove();

        var sortHandleMarkup = '<td class="sort-column"><i class="fa fa-bars feed-sort-handle"></i></td>';
        $('.wp-list-table thead tr, .wp-list-table tfoot tr').append('<th class="sort-column"></th>');

        if ( hasStartStep ) {
            $('.wp-list-table tbody tr:first')
                .addClass('static')
                .append('<td class="sort-column">&nbsp;</td>')
                .find('span.duplicate, span.delete').remove()
                .end()
                .find('.column-is_active').html('&nbsp;');
        }

        if ( hasCompleteStep ) {
            $('.wp-list-table tbody tr:last')
                .addClass('static')
                .append('<td class="sort-column">&nbsp;</td>')
                .find('span.duplicate, span.delete').remove()
                .end()
                .find('.column-is_active').html('&nbsp;');
        }

        $('.wp-list-table tbody tr').not('.static').append(sortHandleMarkup);

        $('.wp-list-table tbody').addClass('gravityflow-reorder-mode')
            .sortable({
                items: 'tr:not(.static)',
                tolerance: "pointer",
                placeholder: "step-drop-zone",
                helper: fixHelperModified,
                handle: '.feed-sort-handle',
                update: function(event, ui){

                    var $feedIds = $(".wp-list-table tbody .check-column input[type=checkbox]");

                    var feedIds = $feedIds.map(function(){return $(this).val();}).get();

                    var data = {
                        action: 'gravityflow_save_feed_order',
                        feed_ids: feedIds,
                        form_id: form.id
                    };

                    $.post( ajaxurl, data)
                        .done( function( response ) {
                            if ( response ) {
                                // OK
                            } else {
                                console.log( 'Error re-ordering feeds');
                                console.log( response);
                            }
                        } )
                        .fail( function( response ) {
                            console.log( 'Error re-ordering feeds');
                            console.log( response);
                        } );
                },
                start: function(){
                    $('.static', this).each(function(){
                        var $this = $(this);
                        $this.data('pos', $this.index());
                    });
                },
                change: function(){
                    var $sortable = $(this);
                    var $statics = $('.static', this).detach();
                    var $helper = $('<tr></tr>').prependTo(this);
                    $statics.each(function(){
                        var $this = $(this);
                        var target = $this.data('pos');

                        $this.insertAfter($('tr', $sortable).eq(target));
                    });
                    $helper.remove();
                }
            });
    });

}(window.GravityFlow = window.GravityFlow || {}, jQuery));


var fixHelperModified = function(e, tr) {
    var $originals = tr.children();
    console.log('originals: ' + $originals.length);
    var $helper = tr.clone();
    $helper.children().each(function(index) {
        jQuery(this).width($originals.eq(index).width());
    });
    return $helper;
};