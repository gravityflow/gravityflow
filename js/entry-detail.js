(function (GravityFlowEntryDetail, $) {
    GravityFlowEntryDetail.printPage = function( sURL ) {
        printPage( sURL );
    };

    GravityFlowEntryDetail.displayDiscussionItemToggle = function (formId, fieldId, displayLimit) {
            $toggle = $('.gravityflow-dicussion-item-hidden');
            $toggle.slideToggle( 'fast' );

            var $viewMore = $toggle.siblings( '.gravityflow-dicussion-item-toggle-display' );
            var oldText = $viewMore.attr( 'title' );
            var newText = $viewMore.data( 'title' );

            $viewMore.attr( 'title', newText ).text( newText );
            $viewMore.data( 'title', oldText );    

    }

}(window.GravityFlowEntryDetail = window.GravityFlowEntryDetail || {}, jQuery));

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
    var old_ie = ua.indexOf( 'MSIE ' );
    var new_ie = ua.indexOf( 'Trident/' );

    if ((old_ie > -1) || (new_ie > -1)) {
        ms_ie = true;
    }

    if ( ms_ie ) {
        this.contentWindow.document.execCommand( 'print', false, null );
    } else {
        this.contentWindow.print();
    }

    setTimeout( closePrint, 100 );
}

function printPage (sURL) {
    var oHiddFrame = document.createElement( "iframe" );
    oHiddFrame.classList.add("gravityflow-print-frame");
    oHiddFrame.onload = setPrint;
    oHiddFrame.style.visibility = "hidden";
    oHiddFrame.style.position = "fixed";
    oHiddFrame.style.right = "0";
    oHiddFrame.style.bottom = "0";
    oHiddFrame.src = sURL;
    document.body.appendChild( oHiddFrame );
}
