$(function(){
    $('#menu_info p').hide();
    $('.a-img img, #left_menu li').hover(
        function(){
            activeMenu($(this).data('page-id'));
        },
        function(){
            deActiveMenu($(this).data('page-id'));
        }
    );
});

function activeMenu(page_id){
    var img = $('.a-img').find('img[data-page-id=' + page_id + ']'),
        menu = $('#left_menu').find('li[data-page-id=' + page_id + ']'),
        info = $('#menu_info').find('p[data-page-id=' + page_id + ']');
    menu.css({'border':'1px solid #c00', 'box-shadow': '0 0 4px #000'})
        .find('a').css('color', '#c00');
    img.css({'border':'1px solid #c00', 'box-shadow': '0 0 10px #000'});
    info.show();
}

function deActiveMenu(page_id){
    var img = $('.a-img').find('img[data-page-id=' + page_id + ']'),
        menu = $('#left_menu').find('li[data-page-id=' + page_id + ']'),
        info = $('#menu_info').find('p[data-page-id=' + page_id + ']');
    menu.css({'border':'1px solid #00c', 'box-shadow':'none'})
        .find('a').css('color', '#00c');
    img.css({'border':'1px solid #00c', 'box-shadow': 'none'});
    info.hide();
}