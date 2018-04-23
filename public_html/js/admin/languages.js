$(function(){
    $('input[name=active]').on('change', function(){
        location.href = '?act=ch_active&id=' + $(this).data('id');
    });
});