/**
 * Skin initialization
 */
function jrCelebrity_init()
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
        topSpacing: 70,
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
        jrCelebrity_open_div('#shareThis');
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

    setTimeout(jrCelebrity_check_pulse, 1000);
    setInterval(jrCelebrity_check_pulse, 30000);

    var menu = $("ul#horizontal");

    // Get static values here first
    var vw = 0, ctr = menu.children().length;         // number of children will not change
    menu.children().each(function()
    {
        vw += $(this).outerWidth();  // widths will not change, so just a total
    });

    jrCelebrity_collect();  // fire first collection on page load
    $(window).resize(jrCelebrity_collect); // fire collection on window resize

    function jrCelebrity_collect()
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

}

/**
 * Get pulse keys for the viewer
 */
function jrCelebrity_check_pulse()
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
 * Listen for ENTER being pressed in comments
 */
function jrCelebrity_enter_listener()
{
    $('#comment_text').unbind('keyup').keyup(function(e)
    {
        if ($(this).val().length > 0) {
            $('#comment_submit_button').removeAttr('disabled').removeClass('form_button_disabled');
        }
        else {
            $('#comment_submit_button').attr('disabled', 'disabled').addClass('form_button_disabled');
        }
    });
}

/**
 * Open a div (stub)
 * @param div string
 * @param f string focus id
 */
function jrCelebrity_open_div(div, f)
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
function jrCelebrity_reply_to(item_id, user_name, div)
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
function jrCelebrity_modal(id, profile_url)
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
    jrCelebrity_init();
});

/**
 * Watch for new entries on the Timeline
 */
function jrCelebrity_watch_timeline()
{
    setInterval(function()
    {
        jrCelebrity_new_actions();
    }, 30000);
}


/**
 * Load more items on the Timeline
 * @param next_page int
 * @param this_page int
 * @returns {boolean}
 */
function jrCelebrity_load_more_timeline(next_page, this_page)
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
function jrCelebrity_new_actions()
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


















/*function jrSiteLogin() {

    $('#loginform').modal({

        onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.slideDown('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        },

        onClose: function (dialog) {
            dialog.data.fadeOut('fast', function () {
                dialog.container.hide('fast', function () {
                    dialog.overlay.fadeOut('fast', function () {
                        $.modal.close();
                    });
                });
            });
        },

        overlayClose: true

    });

}

function jrSiteSignup() {

    $('#signupform').modal({

        onOpen: function (dialog) {
            dialog.overlay.fadeIn('fast', function () {
                dialog.container.slideDown('fast', function () {
                    dialog.data.fadeIn('fast');
                });
            });
        },

        onClose: function (dialog) {
            dialog.data.fadeOut('fast', function () {
                dialog.container.hide('fast', function () {
                    dialog.overlay.fadeOut('fast', function () {
                        $.modal.close();
                    });
                });
            });
        },

        overlayClose: true

    });

}

function init() {

    $('section#profile_menu')
        .sticky({
            topSpacing: 80,
            'classes': {
                'element': 'jquery-sticky-element',
                'start': 'jquery-sticky-start',
                'sticky': 'jquery-sticky-sticky',
                'stopped': 'jquery-sticky-stopped',
                'placeholder': 'jquery-sticky-placeholder'
            }
        })
        .on('sticky-start', function () {
        })
        .on('sticky-end', function () {
        });

    $('textarea').css('overflow', 'hidden').autogrow();
    $(document).keypress(function () {
        setEnterListener();
    });

    $('nav#menu2 > ul').css('display', 'inline');
    $('nav#menu2').mmenu({
        extensions: ['effect-slide-menu', 'pageshadow'],
        searchfield: true,
        counters: true,
        navbar: {
            title: 'Main Menu'
        },
        navbars: [
            {
                position: 'top',
                content: ['searchfield']
            }, {
                position: 'top',
                content: [
                    'prev',
                    'title',
                    'close'
                ]
            }, {
                position: 'bottom',
                content: [
                    '<a href="#" target="_blank">Get the App</a>'
                ]
            }
        ]
    });

    var next = null;
    $('.down a').click(function() {
        next = $(this).parent().parent().next();
        $.scrollTo( next, 1200);
    });
    $('.down.up a').unbind('click').click(function() {
        $.scrollTo( 0, 2600);
    });


    $('div.detail_section .share').click(function(){
        $('div#shareThis').show();
    });

    $('.profile_image').hover(function () {
        $(this).find(".profile_hoverimage").fadeIn();

    }, function () {
        $(this).find(".profile_hoverimage").fadeOut();
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

    $('.detail_box .description, .detail_box .lyrics, .detail_box .basic-info').click(function () {
        $('.detail_box .item').slideUp();
        var item = $(this).find('.item');
        if (item.css('display') == 'none') {
            item.slideDown();
        }
    });

    var $video = $('#background');
    $video.on('canplaythrough', function() {
        this.play();
    });

    var elemWidth, fitCount, varWidth = 0, ctr, $menu = $("ul#horizontal"), $collectedSet;

    // Get static values here first
    ctr = $menu.children().length;         // number of children will not change
    $menu.children().each(function() {
        varWidth += $(this).outerWidth();  // widths will not change, so just a total
    });

    collect();  // fire first collection on page load
    $(window).resize(collect); // fire collection on window resize

    $("ol li.hideshow").click(function () {
        $(this).children("ul").toggle();
    });

    function collect() {
        elemWidth = $menu.width();  // width of menu

        // Calculate fitCount on the total width this time
        fitCount = Math.floor((elemWidth / varWidth) * ctr) - 1;

        // Reset display and width on all list-items
        $menu.children().css({"display": "block", "width": "auto"});

        // Make a set of collected list-items based on fitCount
        $collectedSet = $menu.children(":gt(" + fitCount + ")");

        // Empty the more menu and add the collected items
        $("#submenu").empty().append($collectedSet.clone());

        // Set display to none and width to 0 on collection,
        // because they are not visible anyway.
        $collectedSet.css({"display": "none", "width": "0"});

        if ( $collectedSet.length > 0) {
            $('li.hideshow').show();
        }

        $('ul#horizontal').css({
            visibility: 'visible'
        });
    }
}

function shareSkinItem(id) {
    var url = core_system_url + '/' + jrAction_url + '/share/' + id + '/__ajax=1';
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        cache: false,
        success: function (e) {
            // loadPage(e.redirect);
        },
        error: function (e) {
            alert(e);
        }
    });
}

function confirmActionShare(id) {
    if (confirm("Share this update with your followers?")) {
        shareSkinItem(id);
    } else {
        return false
    }
}

function showMore(div) {
    $('#truncated_' + div).toggle();
    $('#full_' + div).toggle();
}


function setEnterListener() {

    $('.comment_form').unbind("keypress").keypress(function (e) {
        var value = $(this).find("#comment_text").val();
        if (e.which == 13 && !e.shiftKey && value.length > 1) {
            $(this).submit();
            return false;
        }
    }).unbind('keyup').keyup(function(){
        var button = $(this).parent().parent().parent().find('.comment_submit button');
        var value = $(this).find("#comment_text").val();

        if (value.length > 0) {
            button.prop('disabled', false);
        }
        else {
            button.prop('disabled', true);
        }
    });
}

function openDiv(div) {
    $(div).show();
}

function jrCelebrity_reply_to(item_id, user_name, div)
{

    var form = $(div).find('#comment_form_section');
    //form.hide();
    var comment_text            = $(form).find($('#comment_text'));
    var comment_reply_to_user   = $(form).find($('#comment_reply_to_user'));
    var comment_reply_to        = $(form).find($('#comment_reply_to'));
    var comment_parent_id       = $(form).find($('#comment_parent_id'));

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

            var ct   = $(rid).find('#comment_text');
            var cpi = $(rid).find('#comment_parent_id');

            form.slideUp(100);
            $(rid).slideDown(300);
            ct.focus();
            cpi.val(item_id);
        }
    }
    return false;
}


function n8PostComment(uid, template, limit, editor, mod, id) {
    var usub = $(uid + '_cm_submit');
    var unot = $(uid + '_cm_notice');
    var ufsi = $(uid + '_fsi');

    var div = $('#' + mod + '_' + id + '_comments');
    var form = div.find('#comment_form_section');
    var comment_reply_to_user = div.find('#comment_reply_to_user');
    var comment_reply_to = div.find('#comment_reply_to');
    var qq_upload_list = div.find('.qq-upload-list');
    var comment_parent_id = div.find('#comment_parent_id');

    var button = $(div).find('.comment_submit button');
    button.prop('disabled', true);
    usub.attr("disabled", "disabled").addClass('form_button_disabled');
    ufsi.show(300, function () {
        unot.hide();
        var t = setTimeout(function () {
            var val = $(uid + '_form').serializeArray();
            var url = core_system_url + '/' + jrComment_url + '/comment_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                url: url,
                data: val,
                cache: false,
                dataType: 'json',
                success: function (r) {
                    if (typeof r.error !== "undefined") {
                        unot.text(r.error);
                        ufsi.hide(300, function () {
                            usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                            unot.fadeIn(250);
                        });
                        button.prop('disabled', false);
                    }
                    else {
                        $(uid + '_form textarea').val('');
                        usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                        ufsi.hide(300, function () {
                            var mod = $(uid + '_cm_module').val();
                            var iid = $(uid + '_cm_item_id').val();
                            var ord = $(uid + '_cm_order_by').val();
                            var now = new Date().getTime();
                            $(uid + '_comments').load(core_system_url + '/' + jrComment_url +
                                '/view_comments/item_module=' + jrE(mod) +
                                '/item_id=' + Number(iid) +
                                '/order_by=' + jrE(ord) +
                                '/comment_module=' + mod +
                                '/comment_id=' + id +
                                '/template=' + template +
                                '/limit=' + Number(limit) +
                                '/new=' + Number(r.item_id) +
                                '/__ajax=1/_v=' + now, function () {
                                form.slideDown(300);
                                // Go to our comment
                                var cid = '#cm' + r.item_id;
                                if (r.highlight == 'on') {
                                    $('html, body').animate({scrollTop: $(cid).offset().top - 200}, 300);
                                }
                                if (typeof tinyMCE != "undefined" && tinyMCE.get('comment_text') != "undefined") {
                                    tinyMCE.activeEditor.setContent('');
                                    comment_reply_to_user.text('');
                                    comment_reply_to.hide();
                                }
                                // Hide any file attachments from post
                                qq_upload_list.html('');
                                comment_parent_id.val('0');

                            });
                        });
                    }
                },
                error: function (x, t, e) {
                    ufsi.hide(300, function () {
                        usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                        unot.text('Error communicating with server - please try again').show();
                    });
                    button.prop('disabled', false);
                }
            });
            clearTimeout(t);
        }, 1000);
    });
}

$(document).ready(function () {
    $(document).on("click", 'a', function (e) {
        var aurl = $(this).attr("href") || "";
        aurl = aurl.replace(" ", "-");

        if (aurl.indexOf("javascript") >= 0 || aurl == "" || aurl.indexOf("#") == 0) {
            e.preventDefault();
            return false;
        }

    });
    init();
});*/ // end document ready

