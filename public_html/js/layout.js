$(function(){
   // в мобильной версии
   var head_height;
   $(window).on('resize', function (){
      head_height = $('#left').height();
      $('#mobil_nav, #terms').css('top', head_height + 20);
   }).resize();

   // if($(width) < 768){
   //    openClose('search', 'close');
   //    openClose('terms', 'close');
   //    openClose('menu', 'close');
   // }

   // меню
   $('#show_menu').on('click', function (){
      openClose('search', 'close');
      openClose('terms', 'close');
      openClose('menu', 'change');
   });

   // поиск
   $('#show_search').on('click', function (){
      openClose('terms', 'close');
      openClose('menu', 'close');
      openClose('search', 'change');
   });

   // термины
   $('#show_terms').on('click', function (){
      openClose('search', 'close');
      openClose('menu', 'close');
      openClose('terms', 'change');
   });

   function openClose(object, status){
      var nav, max, min, val;
      switch(object){
         case 'menu':
            nav = $('#mobil_nav');
            max = '10px';
            min = '-170px';
            status = status != 'change'? status:
               (nav.css('left') == max? 'close': 'open');
            if(status == 'open') nav.show().animate({'left': max});
            else nav.hide().css('left', min);
            break;
         case 'search':
            nav = $('header');
            max = head_height + 'px';
            min = '0px';
            val = status == 'open'? max: (status == 'close'? min:
               (nav.css('top') == max? min: max));
            nav.css('top', val);
            break;
         case 'terms':
            nav = $('#terms');
            max = '10px';
            min = '-250px';
            status = status != 'change'? status:
               (nav.css('right') == max? 'close': 'open');
            if(status == 'open') nav.show().animate({'right': max});
            else nav.hide().css('right', min);
            break;
      }
   }

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

