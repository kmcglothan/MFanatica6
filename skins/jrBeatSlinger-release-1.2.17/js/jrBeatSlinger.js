
/**
 * Skin initialization
 */
function jrBeatSlinger_init()
{
    var n, o = ['mentions', 'feedback', 'followers', 'timeline'];
    for (var i = 0, len = o.length; i < len; i++) {
        n = sessionStorage.getItem(o[i]);
        if (typeof n !== "undefined" && n > 0) {
            $('#' + o[i]).addClass('active');
            $('.' + o[i] + '_count').text(Number(n)).show();
        }
        else {
            $('#' + o[i]).removeClass('active');
            $('.' + o[i] + '_count').hide().text(0);
        }
    }
    $(document).on("click", 'a', function(e)
    {
        var aurl = $(this).attr("href") || "";
        aurl = aurl.replace(" ", "-");
        if (aurl.indexOf("javascript") >= 0 || aurl == "" || (aurl.indexOf("#") == 0 && aurl.length == 1)) {
            e.preventDefault();
            return false;
        }
    });
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

    $('div.detail_section .share').click(function()
    {
        jrBeatSlinger_open_div('#shareThis');
    });

    $('.profile_image').hover(function()
    {
        $(this).find(".profile_hoverimage").fadeIn();

    }, function()
    {
        $(this).find(".profile_hoverimage").fadeOut();
    });

    var next = null;
    $('.down a').click(function()
    {
        next = $(this).parent().parent().next();
        $.scrollTo(next, 1200);
    });
    $('.down.up a').unbind('click').click(function()
    {
        $.scrollTo(0, 2600);
    });

    $('.detail_box .trigger').click(function()
    {
        $('.detail_box .item').slideUp();
        var item = $(this).parent().find('.item');
        if (item.css('display') == 'none') {
            item.slideDown();
        }
        else {
            item.slideUp();
        }
    });

    var $video = $('#background');
    $video.on('canplaythrough', function() {
        this.play();
    });

    setTimeout(jrBeatSlinger_check_pulse, 1000);
    setInterval(jrBeatSlinger_check_pulse, 30000);

    var menu = $("ul#horizontal");

    // Get static values here first
    var vw = 0, ctr = menu.children().length;         // number of children will not change
    menu.children().each(function()
    {
        vw += $(this).outerWidth();  // widths will not change, so just a total
    });

    jrBeatSlinger_collect();  // fire first collection on page load
    $(window).resize(jrBeatSlinger_collect); // fire collection on window resize

    function jrBeatSlinger_collect()
    {
        menu.css({
            visibility: 'collapse',
            'width': "calc(100% - 112px)"
        });

        $('ul#horizontal .hideshow').remove();

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

    $('.profile_minimal_image > img').click(function()
    {
        $('.profile_minimal_info').toggleClass('open')
    });

    $('body').click(function(evt)
    {
        if ($(evt.target).parents('.profile_minimal_image').length == 0) {
            $('.profile_minimal_info').removeClass('open')
        }
    });

    var index_item = $( ".index_item");

    index_item.hover(
        function() {
            $( this ).find('.hover').addClass('over');
            $( this ).find('.tap_block').fadeOut('slow');
        }, function() {
            $( this ).find('.hover').removeClass('over');
            $( this ).find('.tap_block').show();
        }
    );

    var pc = $('section#profile #profile_tab_content');
    pc.prependTo("#product_tabs");
}

/**
 * Get pulse keys for the viewer
 */
function jrBeatSlinger_check_pulse()
{
    jrProfile_get_pulse_counts(function(n)
    {
        var c = 0;
        c += n['jrComment_comments'];
        c += n['jrLike_likes'];
        c += n['jrLike_dislikes'];
        c += n['jrRating_ratings'];
        if (c > 0) {
            $('#feedback').addClass('active');
            $('.feedback_count').text(c).show();
        }
        else {
            $('#feedback').removeClass('active');
            $('.feedback_count').hide().text(0);
        }

        c = n['jrFollower_followers'];
        if (c > 0) {
            $('#followers').addClass('active');
            $('.followers_count').text(c).show()
        }
        else {
            $('#followers').removeClass('active');
            $('.followers_count').hide().text(0);
        }

        c = n['jrAction_mentions'];
        if (c > 0) {
            $('#mentions').addClass('active');
            $('.mentions_count').text(c).show();
        }
        else {
            $('#mentions').removeClass('active');
            $('.mentions_count').hide().text(0);
        }
    });
}

/**
 * Open a div (stub)
 * @param div string
 * @param f string focus id
 */
function jrBeatSlinger_open_div(div, f)
{
    if (typeof f !== "undefined" && f !== null) {
        $(div).show(200, function()
        {
            $(f).focus();
        });
    }
    else {
        $(div).show();
    }
}

/**
 * Reply to a comment
 * @param item_id int
 * @param user_name string
 * @param div string
 * @returns {boolean}
 */
function jrBeatSlinger_reply_to(item_id, user_name, div)
{
    var form = $(div).find('#comment_form_section');
    //form.hide();
    var comment_text = $(form).find($('#comment_text'));
    var comment_reply_to_user = $(form).find($('#comment_reply_to_user'));
    var comment_reply_to = $(form).find($('#comment_reply_to'));
    var comment_parent_id = $(form).find($('#comment_parent_id'));

    if (typeof tinyMCE != "undefined" && tinyMCE.get('comment_text') != "undefined" && tinyMCE.get('comment_text') != null) {
        comment_reply_to_user.text(user_name);
        comment_reply_to.show();
        comment_parent_id.val(item_id);
        $('html, body').animate({scrollTop: $('#cform').offset().top}, 200);
        tinyMCE.execCommand('mceFocus', true, 'comment_text');
    }
    else {
        var rid = '#r' + item_id;
        if ($(rid).is(':visible')) {
            // Already showing - remove
            $(rid).slideUp(100).empty();
            form.slideDown(300);
        }
        else {
            var ht = form.html();
            $(ht).appendTo(rid);

            var ct = $(rid).find('#comment_text');
            var cpi = $(rid).find('#comment_parent_id');

            form.slideUp(100);
            $(rid).slideDown(300);
            ct.focus();
            cpi.val(item_id);
        }
    }
    return false;
}

/**
 * Open a modal window
 * @param id
 * @param profile_url
 */
function jrBeatSlinger_modal(id, profile_url)
{
    $(id).modal();
    if (profile_url) {
        $('#action_update').text(profile_url + ' ');
    }
}

/**
 * Save pulse counts to local storage for faster load
 */
$(window).on('beforeunload', function()
{
    var n, o = ['mentions', 'feedback', 'followers', 'timeline'];
    for (var i = 0, len = o.length; i < len; i++) {
        n = $('.' + o[i] + '_count').text();
        if (n > 0) {
            sessionStorage.setItem(o[i], n);
        }
        else {
            sessionStorage.removeItem(o[i]);
        }
    }
});

$(document).ready(function()
{
    jrBeatSlinger_init();
});

/**
 * Watch for new entries on the Timeline
 */
function jrBeatSlinger_watch_timeline()
{
    setInterval(function()
    {
        jrBeatSlinger_new_actions();
    }, 30000);
}


/**
 * Load more items on the Timeline
 * @param next_page int
 * @param this_page int
 * @returns {boolean}
 */
function jrBeatSlinger_load_more_timeline(next_page, this_page)
{
    var t = $('#timeline_pagination_url');
    if (t.length > 0) {
        $('#moreLoader').slideDown(300, function()
        {
            setTimeout(function()
            {
                var url = t.text() + '/p=' + Number(next_page) + '/__ajax=1';
                $.get(url, function(res)
                {
                    $('#moreHolder' + this_page).remove();
                    $('#timeline').append(res);
                });
            }, 200);
        });
    }
    return false;
}


/**
 * Get new Timeline entries for a Profile
 * @returns {boolean}
 */
function jrBeatSlinger_new_actions()
{
    var i = $('#last_item_id').val();
    var url = core_system_url + '/' + jrAction_url + '/new_actions_count/__ajax=1';
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        cache: false,
        data: { last_item_id: Number(i) },
        success: function(c)
        {
            if (typeof c.cnt !== "undefined" && c.cnt > 0) {
                $('.timeline_count').text(c.cnt).show();
                $('#timeline_notifications').slideDown(500);
            }
        },
        error: function()
        {
            return false;
        }
    });
    return false;
}

