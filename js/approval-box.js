(function(){
      const buttons = document.getElementsByClassName('gravityflow-action-buttons')[0].getElementsByTagName('button');
  
      buttons[0].addEventListener('click', function(e) {
        const approveConfirm = confirm( gravityflow_approval_box_strings.approveMessage );
        if ( !approveConfirm ) {
          e.preventDefault();
        }
      });
  
      buttons[1].addEventListener('click', function(e) {
        const rejectConfirm = confirm( gravityflow_approval_box_strings.rejectMessage );
        if ( !rejectConfirm ) {
          e.preventDefault();
        }
      });
})();