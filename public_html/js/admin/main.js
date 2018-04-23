$(function(){
   $(window).on('resize', showAlert).resize();


   function showAlert(){
      var alert = $('.alert');
      if(alert.length == 0) return;
      var fon = alert.parent();
      fon.show();
      var win = $(window),
         w_win = win.width(),
         h_win = win.height(),
         w_al = alert.width(),
         h_al = alert.height();
      fon.css({width: w_win, height: h_win}).find('img').css({
         top: h_win / 2 - h_al / 2 - 10,
         left: w_win / 2 + w_al / 2 + 20
      });
      alert.css({top: h_win / 2 - h_al / 2, left: w_win / 2 - w_al / 2});

      win.on('click', function(){
         fon.remove();
      });

   }

});