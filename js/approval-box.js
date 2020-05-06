(function(){
  setTimeout(function() {
    try {
      const buttons = document.getElementsByClassName('gravityflow-action-buttons')[0].getElementsByTagName('button');
  
      buttons[0].addEventListener('click', function(e) {
        const approveConfirm = confirm( "Are you sure you want to approve?" );
        if ( !approveConfirm ) {
          e.preventDefault();
        }
      });
  
      buttons[1].addEventListener('click', function(e) {
        const rejectConfirm = confirm( "Are you sure you want to reject?" );
        if ( !rejectConfirm ) {
          e.preventDefault();
        }
      });        
    } catch (error) {
    }
  }, 2000);
})();