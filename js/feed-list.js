(function (GravityFlow, $) {

    $(document).ready(function () {

        if ( $('table.wp-list-table tbody tr').length == 1 ) {
            return;
        }

        $.each( $('.wp-list-table tbody tr'), function() { 
            $( this ).css( 'border-left', '5px solid ' + $(this).find('.step_highlight_color').css( 'background-color' ) );
        });
        $('.wp-list-table .step_highlight').remove();

        var sortHandleMarkup = '<td class="sort-column"><i class="fa fa-bars feed-sort-handle"></i></td>';
        $('.wp-list-table thead tr, .wp-list-table tfoot tr').append('<th class="sort-column"></th>');
        $('.wp-list-table tbody tr').append(sortHandleMarkup);

        $('.wp-list-table tbody').addClass('gravityflow-reorder-mode')
            .sortable({
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