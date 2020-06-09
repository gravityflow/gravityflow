(function(){
      var buttons = document.getElementsByClassName('gravityflow-action-buttons')[0].getElementsByTagName('button');
  
      buttons[0].addEventListener('click', function(e) {
        var approveConfirm = confirm( gravityflow_approval_confirmation_prompts.approveMessage );
        if ( !approveConfirm ) {
          e.preventDefault();
        }
      });
  
      buttons[1].addEventListener('click', function(e) {
        var rejectConfirm = confirm( gravityflow_approval_confirmation_prompts.rejectMessage );
        if ( !rejectConfirm ) {
          e.preventDefault();
        }
      });

      if (buttons.length == 3) {
        buttons[2].addEventListener('click', function(e) {
          var revertConfirm = confirm( gravityflow_approval_confirmation_prompts.revertMessage );
          if ( !revertConfirm ) {
            e.preventDefault();
          }
        });        
      }
})();
