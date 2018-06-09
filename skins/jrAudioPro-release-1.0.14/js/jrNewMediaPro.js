
/**
 * Skin initialization
 */
function jrAudioPro_init()
{
    $('section#profile_menu').sticky({
        topSpacing: 50,
        'classes': {
            'element': 'jquery-sticky-element',
            'start': 'jquery-sticky-start',
            'sticky': 'jquery-sticky-sticky',
            'stopped': 'jquery-sticky-stopped',
            'placeholder': 'jquery-sticky-placeholder'
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

    jrAudioPro_collect();  // fire first collection on page load
    $(window).resize(jrAudioPro_collect); // fire collection on window resize

    function jrAudioPro_collect()
    {
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
}
/**
 * Open a modal window
 * @param id
 * @param profile_url
 */
function jrAudioPro_modal(id, profile_url)
{
    $(id).modal();
    if (profile_url) {
        $('#action_update').text(profile_url + ' ');
    }
}


$(document).ready(function()
{
    jrAudioPro_init();
});

function jrAudioPro_chart_days(days) {

    var url = core_system_url + '/index_chart_days/days=' + Number(days) + '/__ajax=1';
    if (url.length > 0) {
        $('#chartLoader').show(50, function()
        {
            setTimeout(function()
            {
                $.get(url, function(res)
                {
                    $('#chartLoader').hide();
                    $('#chart').html(res);
                });
            }, 200);
        });
    }
    return false;


}
