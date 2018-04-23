$(function(){
    $('[name=section_id]').on('change', function(){
        location.href = '/admin/sections/' + $(this).val() + '/' + $('[name=lang]').val();
    });
    $('[name=lang]').on('change', function(){
        location.href = '/admin/sections/' + $('[name=section_id]').val() + '/' + $(this).val();
    });
});