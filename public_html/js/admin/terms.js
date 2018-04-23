$(function(){
    $('[name=section_id]').on('change', function(){
        var t = $(this);
        location.href = '?act=ch_section_id&id=' + t.val();
    });
    $('[name=active]').on('change', function(){
        location.href = '?act=ch_active&id=' + $(this).data('id');
    });
});