$(document).ready(function(){
    if ($('.slider').length > 0){
        $('.slider').owlCarousel({
            loop:true,
            singleItem: true,
            autoPlay: 8000,
            transitionStyle : "fade",
            items:1,
            nav:false
        });
    }
    if ($('.dataBody img').length > 0){
        $('.dataBody img').each(function(){
            if ($(this).attr('src').indexOf('http:') == 0){
                var src = $(this).attr('src').replace('http:','https:');
                $(this).attr('src',src);
            }
        })
    }
    $('#trailer').on('show.bs.modal', function (e) {
        $('#f-video').append('<iframe width="800" height="450"  src="https://www.youtube.com/embed/1dSB0yx13_U?rel=0&amp&autoplay=1;showinfo=0" frameborder="0" allowfullscreen></iframe>')
    });
    $('#trailer').on('hidden.bs.modal', function (e) {
        $('#f-video  iframe').remove();
    });
    if ($('.navtabs').length > 0){
        $(function(){
            $('.navtabs li').first().addClass('active');
            $('.tabs-container .tab-content').first().show();
            var $src = $('.navtabs li').first().children('a').attr('data-src');
            $('.navtabs').children('.readmore-tab').attr('href',$src);

            $('.navtabs li').find('a').on('click',function(){
                if(!$(this).closest('li').hasClass('active')) {
                    $('.navtabs').find('li').removeClass('active');
                    $src = $(this).attr('data-src');
                    var $tab = $(this).attr('data-tab');
                    $('.navtabs ul').children($tab).addClass('active');
                    $('.navtabs').children('.readmore-tab').attr('href',$src);
                    $('.tabs-container').find('.tab-content').hide();
                    var $selected_tab = $(this).attr("href");
                    $($selected_tab).show();
                }
                return false;
            })
        });
    }
    $('.nav-icon').click(function(){
        $(".overlay").fadeToggle(200);
        $(this).toggleClass('open');
        $('body').toggleClass('open')
    });
    $('.overlay').on('click', function(){
        $(".overlay").fadeToggle(200);
        $(".nav-icon").toggleClass('open');
        $('body').toggleClass('open');
        open = false;
    });
    $(window).resize();
});

$(window).resize();
$(window).resize(function () {
    var widthWindow = $(this).width();
    if (widthWindow > 1200) {
        window.state = 'mto';
    } else if (widthWindow <= 1200) {
        window.state = 'mthuong';
    }  else if (widthWindow > 960) {
        window.state = 'mtab';
    }
    fullpageInit();
    slider();
});
function slider(){
    $('.owl-hero').owlCarousel({
        autoPlay: 8000,
        pagination: false,
        nav:true,
        items:1,
        loop:true
    });
}
function fullpageInit () {
    switch (window.state) {
        case 'mto':
            activePanel = $("#accordion .panelx:first");
            $(activePanel).addClass('active');
            $("#accordion").delegate('.panelx', 'click', function(e){
                if( ! $(this).is('.active') ){
                    $(activePanel).animate({width: "119px"}, 500);
                    $(this).animate({width: "664px"}, 500);
                    $('#accordion .panelx').removeClass('active');
                    $(this).addClass('active');
                    activePanel = this;
                };
            });
            break;
        case 'mthuong':
            activePanel = $("#accordion .panelx:first");
            $(activePanel).addClass('active');
            $("#accordion").delegate('.panelx', 'click', function(e){
                if( ! $(this).is('.active') ){
                    $(activePanel).animate({width: "119px"}, 500);
                    $(this).animate({width: "524px"}, 500);
                    $('#accordion .panelx').removeClass('active');
                    $(this).addClass('active');
                    activePanel = this;
                };
            });
            break;
    }
}

$('.ico').click(function () {
    $('.ico').removeClass('active');
    $(this).addClass('active');
    $data_class = $(this).attr('data-class');
    $('.nv').attr('class', 'nv').addClass($data_class);
    $data_cap = $(this).attr('data-cap');
    $('.cap').attr('class', 'cap').addClass($data_cap);
    $data_title = $(this).attr('data-tit');
    $('.tit').attr('class', 'tit').addClass($data_title);
});
$('.toggle').click(function () {
   $(this).toggleClass('active');
   $('.fixed-box').toggleClass('active');
});
