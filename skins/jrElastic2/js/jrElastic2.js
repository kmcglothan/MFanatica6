// Scroll To Top Function
$(document).ready(function(){
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn(500);
        } else {
            $('.scrollup').fadeOut(500);
        }
    });

    $('.scrollup').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 1500);
        return false;
    });


    $('.profile_image').hover(function()
    {
        $(this).find(".profile_hoverimage").fadeIn(500);

    }, function()
    {
        $(this).find(".profile_hoverimage").fadeOut(500);
    });

    var menu = $("ul#horizontal");

    // Get static values here first
    var vw = 0, ctr = menu.children().length;         // number of children will not change
    menu.children().each(function()
    {
        vw += $(this).outerWidth();  // widths will not change, so just a total
    });

    jrElastic2_collect();  // fire first collection on page load
    $(window).resize(jrElastic2_collect);

    function jrElastic2_collect()
    {
        $('ul#horizontal .hideshow').remove();

        menu.css({
            visibility: 'collapse',
            'width': "calc(100% - 112px)"
        });

        // Calculate fitCount on the total width this time
        var fc = Math.floor((menu.width() / vw) * ctr) - 1;

        // Reset display and width on all list-items
        menu.children().css({"display": "block", "width": "auto"});

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
});
