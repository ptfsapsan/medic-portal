$(function (){
   
   function getTerms(){
      $.post('/ajax/get-terms-by-article', {article_id: vars.article_id},
         function (data){
            var html = '<ul>';
            $.each(eval('(' + data + ')'), function(index, el){
               html += '<li>' + el.ru_term_title +
                  '<div class="del_term" data-id="' + el.id + '">' +
                  '</li>';
            });
            html += '</ul>';
            $('#choose_terms').html(html);

            // удаление
            $('.del_term').on('click', function (){
               $.post('/ajax/del-term-from-article', {
                  a2t_id: $(this).data('id')
               }, getTerms);
            });
         }
      );
   }

   getTerms();

   // добавление
   $('.term').on('click', function (){
      $.post('/ajax/add-term-to-article', {
         article_id: vars.article_id,
         term_id: $(this).data('id')
      }, getTerms);
   });


});