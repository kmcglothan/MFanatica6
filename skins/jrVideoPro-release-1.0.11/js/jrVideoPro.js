
/**
 * Skin initialization
 */
function jrVideoPro_init()
{

    $('section#profile_menu').sticky({
        topSpacing: 51,
        'classes': {
            'element': 'jquery-sticky-element',
            'start': 'jquery-sticky-start',
            'sticky': 'jquery-sticky-sticky',
            'stopped': 'jquery-sticky-stopped',
            'placeholder': 'jquery-sticky-placeholder'
        }
    });
    $(document).on("click", 'a', function(e)
    {
        var aurl = $(this).attr("href") || "";
        aurl = aurl.replace(" ", "-");
        if (aurl.indexOf("javascript") >= 0 || aurl == "" || aurl.indexOf("#") == 0) {
            e.preventDefault();
            return false;
        }
    });

    $('.profile_image').hover(function()
    {
        $(this).find(".profile_hoverimage").fadeIn();

    }, function()
    {
        $(this).find(".profile_hoverimage").fadeOut();
    });

    var menu = $("ul#horizontal");

    // Get static values here first
    var vw = 0, ctr = menu.children().length;         // number of children will not change
    menu.children().each(function()
    {
        vw += $(this).outerWidth();  // widths will not change, so just a total
    });

    jrVideoPro_collect();  // fire first collection on page load
    $(window).resize(jrVideoPro_collect); // fire collection on window resize

    function jrVideoPro_collect()
    {
        menu.css({
            visibility: 'collapse',
            'width': "calc(100% - 112px)"
        });

        // Calculate fitCount on the total width this time
        var fc = Math.floor((menu.width() / vw) * ctr) - 1;

        // Reset display and width on all list-items
        menu.children().css({"display": "block", "width": "auto"});

        $('ul#horizontal .hideshow').remove();

        // Make a set of collected list-items based on fc
        var cs = menu.children(":gt(" + fc + ")");

        menu.append($('#pm-drop-opt').html());

        // Empty the more menu and add the collected items
        $("#submenu").empty().append(cs.clone());

        // Set display to none and width to 0 on collection,
        // because they are not visible anyway.
        cs.css({"display": "none", "width": "0"});

        if (cs.length > 0) {
            $('ul#horizontal li.hideshow').css('display', 'block').click(function()
            {
                $(this).children("ul").toggle();
            });
        }
        menu.css({
            visibility: 'visible',
            'width': "100%"
        });
    }

    var im = $( ".image");

    im.hover(
        function() {
            $( this ).find('.hover').addClass('over');
        }, function() {
            $( this ).find('.hover').removeClass('over');
        }
    );

    var index_list = $('.index_list');
    

    $('a.list_nav.next').click(function(){
        if ($(window).width() > 767) {
            par = $(this).parent().find(index_list);
            jrVideoPro_swipeNext(par);
        }
        else {
            par = $(this).parent().find(index_list);
            jrVideoPro_swipeNextMobile(par);
        }
    });

    $('a.list_nav.previous').click(function(){
        if ($(window).width() > 767) {
            par = $(this).parent().find(index_list);
            jrVideoPro_swipePrev(par);
        }
        else {
            par = $(this).parent().find(index_list);
            jrVideoPro_swipePrevMobile(par);
        }
    });

    var par;

    index_list.on("swipeleft",function(){
        if ($(window).width() > 767) {
            par = $(this).parent().find(index_list);
            jrVideoPro_swipeNext(par);
        }
        else {
            par = $(this).parent().find(index_list);
            jrVideoPro_swipeNextMobile(par);
        }
    }).on("swiperight",function(){
        if ($(window).width() > 767) {
            par = $(this).parent().find(index_list);
            jrVideoPro_swipePrev(par);
        }
        else {
            par = $(this).parent().find(index_list);
            jrVideoPro_swipePrevMobile(par);
        }
    });
}
/**
 * Open a modal window
 * @param id
 * @param profile_url
 */
function jrVideoPro_modal(id, profile_url)
{
    $(id).modal();
    if (profile_url) {
        $('#action_update').text(profile_url + ' ');
    }
}


$(document).ready(function()
{
    jrVideoPro_init();

    // Scroll To Top Function
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollup').addClass('show');
        } else {
            $('.scrollup').removeClass('show');
        }
    });

    $('.scrollup').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
});

function jrVideoPro_initSlide() {

    if($(window).width() > 767) {
        var width = 1440;
        var height = 540;
    }
    else {
        width = 800;
        height = 800;
    }

    $('.slides').slidesjs({
        play: {
            active: false,
            // [boolean] Generate the play and stop buttons.
            // You cannot use your own buttons. Sorry.
            effect: "slide",
            // [string] Can be either "slide" or "fade".
            interval: 8000,
            // [number] Time spent on each slide in milliseconds.
            auto: true,
            // [boolean] Start playing the slideshow on load.
            swap: true,
            // [boolean] show/hide stop and play buttons
            pauseOnHover: true,
            // [boolean] pause a playing slideshow on hover
            restartDelay: 2500
            // [number] restart delay on inactive slideshow
        },
        width : width,
        height : height,
        callback: {
            loaded: function(number) {
                // Do something awesome!
                // Passes start slide number
            },
            start: function(number) {
                // Do something awesome!
                // Passes slide number at start of animation
            },
            complete: function(number) {
                // Do something awesome!
                // Passes slide number at end of animation
            }
        }
    });
}

function jrVideoPro_swipePrev(element) {
    var par = $(element);

    if (par.hasClass('page_3'))
        p = '2';
    else if (par.hasClass('page_2'))
        p = '1';
    else
        return false;

    var c = 'page_' + p;
    par
        .removeClass('page_1 page_2 page_3')
        .addClass(c);
}

function jrVideoPro_swipeNext(element) {
    var p = '1';
    var par = $(element);

    if (par.hasClass('page_1') && par.find('#page_2').length > 0)
        p = '2';
    else if (par.hasClass('page_2') && par.find('#page_3').length > 0)
        p = '3';
    else
        return false;

    var c = 'page_' + p;

    par
        .removeClass('page_1 page_2 page_3')
        .addClass(c);
}

function jrVideoPro_swipePrevMobile(element) {
    var par = $(element);

    if (par.hasClass('page_27'))
        var p = '26';
    else if (par.hasClass('page_26'))
        p = '25';
    else if (par.hasClass('page_25'))
        p = '24';
    else if (par.hasClass('page_24'))
        p = '23';
    else if (par.hasClass('page_23'))
        p = '22';
    else if (par.hasClass('page_22'))
        p = '21';
    else if (par.hasClass('page_21'))
        p = '20';
    else if (par.hasClass('page_20'))
        p = '19';
    else if (par.hasClass('page_19'))
        p = '18';
    else if (par.hasClass('page_18'))
        p = '17';
    else if (par.hasClass('page_17'))
        p = '16';
    else if (par.hasClass('page_16'))
        p = '15';
    else if (par.hasClass('page_15'))
        p = '14';
    else if (par.hasClass('page_14'))
        p = '13';
    else if (par.hasClass('page_13'))
        p = '12';
    else if (par.hasClass('page_12'))
        p = '11';
    else if (par.hasClass('page_11'))
        p = '10';
    else if (par.hasClass('page_10'))
        p = '9';
    else if (par.hasClass('page_9'))
        p = '8';
    else if (par.hasClass('page_8'))
        p = '7';
    else if (par.hasClass('page_7'))
        p = '6';
    else if (par.hasClass('page_6'))
        p = '5';
    else if (par.hasClass('page_5'))
        p = '4';
    else if (par.hasClass('page_4'))
        p = '3';
    else if (par.hasClass('page_3'))
        p = '2';
    else if (par.hasClass('page_2'))
        p = '1';
    else
        return false;

    var c = 'page_' + p;

    par
        .removeClass('page_1 page_2 page_3 page_4 page_5 page_6 page_7 page_8 page_9 page_10 page_11 page_12 page_13 page_14 page_15 page_16 page_17 page_18 page_19 page_20 page_21 page_22 page_23 page_24 page_25 page_26 page_27')
        .addClass(c);
}

function jrVideoPro_swipeNextMobile(element) {
    var p = '1';
    var par = $(element);

    if (par.hasClass('page_1') && par.find('#item_2').length > 0)
        p = '2';
    else if (par.hasClass('page_2') && par.find('#item_3').length > 0)
        p = '3';
    else if (par.hasClass('page_3') && par.find('#item_4').length > 0)
        p = '4';
    else if (par.hasClass('page_4') && par.find('#item_5').length > 0)
        p = '5';
    else if (par.hasClass('page_5') && par.find('#item_6').length > 0)
        p = '6';
    else if (par.hasClass('page_6') && par.find('#item_7').length > 0)
        p = '7';
    else if (par.hasClass('page_7') && par.find('#item_8').length > 0)
        p = '8';
    else if (par.hasClass('page_8') && par.find('#item_9').length > 0)
        p = '9';
    else if (par.hasClass('page_9') && par.find('#item_10').length > 0)
        p = '10';
    else if (par.hasClass('page_10') && par.find('#item_11').length > 0)
        p = '11';
    else if (par.hasClass('page_11') && par.find('#item_12').length > 0)
        p = '12';
    else if (par.hasClass('page_12') && par.find('#item_13').length > 0)
        p = '13';
    else if (par.hasClass('page_13') && par.find('#item_14').length > 0)
        p = '14';
    else if (par.hasClass('page_14') && par.find('#item_15').length > 0)
        p = '15';
    else if (par.hasClass('page_15') && par.find('#item_16').length > 0)
        p = '16';
    else if (par.hasClass('page_16') && par.find('#item_17').length > 0)
        p = '17';
    else if (par.hasClass('page_17') && par.find('#item_18').length > 0)
        p = '18';
    else if (par.hasClass('page_18') && par.find('#item_19').length > 0)
        p = '19';
    else if (par.hasClass('page_19') && par.find('#item_20').length > 0)
        p = '20';
    else if (par.hasClass('page_20') && par.find('#item_21').length > 0)
        p = '21';
    else if (par.hasClass('page_21') && par.find('#item_22').length > 0)
        p = '22';
    else if (par.hasClass('page_22') && par.find('#item_23').length > 0)
        p = '23';
    else if (par.hasClass('page_23') && par.find('#item_24').length > 0)
        p = '24';
    else if (par.hasClass('page_24') && par.find('#item_25').length > 0)
        p = '25';
    else if (par.hasClass('page_25') && par.find('#item_26').length > 0)
        p = '26';
    else if (par.hasClass('page_26') && par.find('#item_27').length > 0)
        p = '27';
    else
        return false;

    var c = 'page_' + p;

    par
        .removeClass('page_1 page_2 page_3 page_4 page_5 page_6 page_7 page_8 page_9 page_10 page_11 page_12 page_13 page_14 page_15 page_16 page_17 page_18 page_19 page_20 page_21 page_22 page_23 page_24 page_25 page_26')
        .addClass(c);
}

function jrVideoPro_open_div(div, close) {
    $(close).slideUp(500);
    $(div).slideDown(500);
}