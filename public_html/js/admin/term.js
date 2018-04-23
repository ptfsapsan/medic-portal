$(function(){
    $('[name=lang]').on('change', function(){
        location.href = '/admin/term/' + $('[name=term_id]').val() + '/' + $(this).val();
    })
});