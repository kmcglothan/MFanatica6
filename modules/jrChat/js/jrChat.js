// Jamroom Chat Module Javascript
// @copyright 2003-2017 by Talldude Networks LLC

var __jrchat_iv;
var __jrchat_cr = '';
var __jrchat_bc = '';
var __jrchat_nr = '';
var __jrchat_ip = false;
var __jrchat_lm = false;
var __jrchat_ls = 5000;

$(window).resize(function()
{
    jrChat_set_chat_height();
});

window.addEventListener("focus", function()
{
    jrChat_set_local_item('focused', 1);
    if (jrChat_is_mobile_view()) {
        var ttl = document.title;
        if (ttl.indexOf('[') === 0) {
            var brk = ttl.indexOf(']');
            document.title = ttl.substr(brk + 2);
        }
    }
});

window.addEventListener("blur", function()
{
    jrChat_set_local_item('focused', 0);
});

$(window).on('beforeunload', function()
{
    if ($('#jrchat-room').length > 0) {
        
        var c = $('#jrchat-messages');
        var h = c.html();
        jrChat_set_local_item('messages', h);

        var n = jrChat_get_notification_number();
        jrChat_set_local_item('new_messages', n);

        var t = $('#jrchat-new-message-input').val();
        jrChat_set_local_item('cm_content', t);

        var l = $('#jrchat-active-title').text();
        jrChat_set_local_item('active_room_title', l);

        var b = $('.jrchat-bubble').text();
        jrChat_set_local_item('active_room_users', b);

    }
    __jrchat_ls = 5000;
    __jrchat_lm = false;
    __jrchat_ip = false;
    
});

$(document).ready(function()
{
    if ($('#jrchat-room').length) {
        jrChat_init();
    }
});

/**
 * Initialize Chat
 */
function jrChat_init(cb)
{
    var r = true;
    
    // Restore what user was typing
    var p = jrChat_get_local_item('cm_content', '');
    if (p.length > 0 && p != "undefined") {
        $('#jrchat-new-message-input').val(p);
    }
    jrChat_set_local_item('cm_content', '');

    var h = jrChat_get_local_item('messages', '');
    if (h !== null && typeof h !== "undefined" && h.length > 10) {

        var b = jrChat_get_local_item('active_room_users', 0);
        $('.jrchat-bubble').text(b);

        // Restore messages from page load
        var chm = $('#jrchat-messages');

        // How many messages are we restoring?
        var num = $(h).filter('.jrchat-msg').length;
        if (num < 200 && num > 0) {
            // Restore - otherwise load
            chm.html(h);
            $('#jrchat-page-limit').remove();
            var lst = $('.jrchat-msg').last().attr('id').replace('m', '');
            if (lst >= jrChat_get_last_id()) {
                r = false;
                jrChat_scroll_to_bottom(0);
                jrChat_init_pager();
                jrChat_init_chat_controls();
            }
        }
    }
    jrChat_set_local_item('messages', '');
    jrChat_set_local_item('loop_timer', __jrchat_ls);
    jrChat_set_local_item('loop_number', 0);
    jrChat_set_chat_height();
    jrChat_store_fixed_element_positions();

    var c = $('#jrchat-room');
    var i = jrChat_get_local_item('new_messages', 0);
    var s = jrChat_get_local_item('state', 'closed');
    if (s == 'open' || c.css('right') == '0px') {
        jrChat_set_local_item('focused', 1);
        jrChat_set_initial_tab_state('open');
        if (c.css('right').indexOf('-') === 0) {
            // We are supposed to be open...
            jrChat_toggle('open');
        }
        var w = jrChat_get_local_item('width', 0);
        if (w == 0) {
            w = Number($('#jrchat-width').text().replace('px', ''));
            if (typeof w == "undefined" || w == null || w > 640 || w < 280) {
                w = 400;
            }
        }
        jrChat_position_fixed_elements(w, 0, w);
        jrChat_set_local_item('state', 'open');
        jrChat_set_new_indicator(i);
    }
    else {
        jrChat_set_local_item('state', 'closed');
        jrChat_set_notification_number(Number(i));
        __jrchat_ls = 30000;
        jrChat_set_local_item('loop_timer', 30000);
    }

    jrChat_set_local_item('new_messages', 0);

    // Get our active room_id and init
    jrChat_get_active_room_id(function(i)
    {
        if (i == 0) {
            jrChat_show_no_chat_rooms();
            if (typeof cb == "function") {
                return cb();
            }
        }
        else {
            if (r) {
                jrChat_get_messages(0, function()
                {
                    jrChat_init_chat_controls();
                    jrChat_watch_loop();
                });
            }
            else {
                jrChat_watch_loop();
            }
            if (jrChat_get_tab_state() == 'open') {
                $('#jrchat-new-message-input').focus().jrChat_is_typing();
            }
            if (typeof cb == "function") {
                return cb();
            }
        }
    });

}

/**
 * Initialize chat controls
 */
function jrChat_init_chat_controls()
{
    var c;
    if (jrChat_is_admin()) {
        c = '.jrchat-msg';
    }
    else {
        c = '.jrchat-msg-from';
    }
    if (c.length > 0) {
        $('#jrchat-messages').on('mouseenter', c, function()
        {
            $(this).append(jrChat_get_chat_controls()).show();

        }).on('mouseleave', c, function()
        {
            $('.jrchat-controls').remove();

        }).on('click', '.jrchat-controls', function()
        {
            var i = Number($(this).parent().attr('id').replace('m', ''));
            if (i > 0) {
                jrChat_delete_message_id(i);
            }
        });
    }
}

/**
 * Delete a chat message
 * @param i int Message ID
 */
function jrChat_delete_message_id(i)
{
    var url = core_system_url + '/' + jrChat_url + '/delete_message/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {id: i},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }
            var id = $('#m' + i);
            if (typeof r.ok !== "undefined") {
                id.remove();
            }
            else if (id.length > 0) {
                alert(r.error);
            }
        }
    });
}

/**
 * Get chat controls
 * @returns {string}
 */
function jrChat_get_chat_controls()
{
    if (__jrchat_cr.length === 0) {
        var c = $('#jrchat-controls-holder');
        __jrchat_cr = c.html();
        c.remove();
    }
    return __jrchat_cr;
}

/**
 * Get "beginning of chat" message
 * @returns {string}
 */
function jrChat_get_beginning_of_chat()
{
    if (__jrchat_bc.length === 0) {
        var c = $('#jrchat-beginning-holder');
        __jrchat_bc = c.html();
        c.remove();
    }
    return __jrchat_bc;
}

/**
 * Show "no chat rooms" message
 * @returns {string}
 */
function jrChat_show_no_chat_rooms()
{
    if (__jrchat_nr.length === 0 && $('.jrchat-msg').length === 0) {
        var c = $('#jrchat-no-room-holder');
        __jrchat_nr = c.html();
        c.remove();
    }
    var chm = $('#jrchat-messages');
    if (chm.find('#jrchat-no-room-notice').length === 0) {
        chm.append(__jrchat_nr);
    }
}

/**
 * Complete file uploads to a chat
 */
function jrChat_complete_file_uploads()
{
    var tkn = $('#jrchat-new-message').find("input[name='upload_token']").val();
    if (typeof tkn == "undefined") {
        alert('error uploading file - unable to determine upload token');
    }
    else {
        var rid = jrChat_get_current_room_id();
        var url = core_system_url + '/' + jrChat_url + '/upload_files/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {upload_token: tkn, room_id: rid},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                }
                jrChat_get_new_messages();
            }
        });
    }
}

/**
 * Get the active chat room id
 * @param cb function
 */
function jrChat_get_active_room_id(cb)
{
    var ri = 0;
    var id = jrChat_get_current_room_id();
    if (id > 0) {
        ri = id;
    }
    cb(ri);
}

/**
 * Set the active room title
 * @param t string
 */
function jrChat_set_active_room_title(t)
{
    $('#jrchat-active-title').text(t);
    jrChat_set_local_item('active_room_title', t);
    return true;
}

/**
 * Initialize user live search
 */
function jrChat_init_live_search(id)
{
    setTimeout(function()
    {
        var i = $('#' + id);
        if (i.length > 0) {
            i.liveSearch({url: core_system_url + '/' + jrChat_url + '/search_users/' + id + '/q=', typeDelay: 400});
        }
    }, 200);
}

/**
 * Initialize the previous page pager
 */
function jrChat_init_pager()
{
    var nxp = $('#jrchat-load-next-page');
    if (nxp.length === 0) {
        $('#jrchat-messages').prepend('<div id="jrchat-load-next-page"></div>');
    }
    nxp.off('inview');
    nxp.on('inview', function(e, iv)
    {
        if (iv) {
            var b = jrChat_get_local_item('before_id');
            jrChat_get_messages(b);
        }
    });
}

/**
 * Resize chat interface
 */
function jrChat_set_chat_height()
{
    var c = $('#jrchat-messages');
    if (c.is(':visible')) {
        var h = $(window).height();
        $('#jrchat-room').height(h);

        var m = $('#jrchat-new-message').outerHeight();
        var t = $('#jrchat-title').outerHeight();
        c.css('height', (h - m - t) + 'px').css('overflow', 'scroll').scrollTop(c[0].scrollHeight + 30);
    }
}

/**
 * Set the tab state on the chat pane
 * @param s string
 */
function jrChat_set_tab_state(s)
{
    var h = $('#jrchat-hidden-tabs');
    if (s == 'closed') {
        // Chat is closed - hide all tabs
        h.hide();
        $('#jrchat-open-close').find('span[class$="_chat-close"]').each(function() {
            this.className = this.className.replace(new RegExp('_chat-close', 'g'), '_chat-open');
        });
        jrChat_set_local_item('state', 'closed');
        jrChat_save_state('closed');
        $('#jrchat-new-message-input').attr('disabled', 'disabled');
    }
    else {
        $('#jrchat-new-message-input').removeAttr('disabled');
        if (h.not(':visible')) {
            // We are not already showing..
            h.show();
            $('#jrchat-open-close').find('span[class$="_chat-open"]').each(function() {
                this.className = this.className.replace(new RegExp('_chat-open', 'g'), '_chat-close');
            });
        }
        jrChat_set_local_item('state', 'open');
        jrChat_save_state('open');
    }
}

/**
 * Get the current chat tab state
 */
function jrChat_get_tab_state()
{
    return jrChat_get_local_item('state', 'closed');
}

/**
 * Toggle chat view on/off
 */
function jrChat_toggle(state)
{
    var s = 400;
    var c = $('#jrchat-room');
    var t = $('#jrchat-tabs');
    var w = c.width();
    if (typeof state == "undefined") {
        if (c.css('right').indexOf('-') === 0) {
            state = 'open';
        }
        else {
            state = 'closed';
        }
    }
    if (state == 'open') {
        // We are closed - open
        jrChat_set_tab_state('open');
        $('body').stop().animate({'padding-right': w + 'px'}, s);
        c.stop().animate({'right': '0'}, s);
        t.stop().animate({'right': w + 'px'}, s);
        jrChat_position_fixed_elements(w, s, w);
        if (w >= 640) {
            jrChat_disable_tab('#jrchat-expand-tab');
            jrChat_enable_tab('#jrchat-contract-tab');
        }
        else if (w <= 280) {
            jrChat_enable_tab('#jrchat-expand-tab');
            jrChat_disable_tab('#jrchat-contract-tab');
        }
        else {
            jrChat_enable_tab('#jrchat-expand-tab');
            jrChat_enable_tab('#jrchat-contract-tab');
        }
        $('#jrchat-new-message-input').focus().jrChat_is_typing();
        jrChat_scroll_to_bottom(true);
        jrChat_set_notification_number(0);
        clearTimeout(__jrchat_iv);
        jrChat_get_messages(0, function()
        {
            jrChat_init_chat_controls();
            jrChat_set_local_item('loop_timer', 5000);
            jrChat_set_local_item('loop_number', 0);
            __jrchat_ls = 5000;
            __jrchat_ip = false;
            jrChat_watch_loop();
        });
    }
    else {
        // We are open - close
        jrChat_set_tab_state('closed');

        jrChat_close_room_selector();
        jrChat_close_user_selector();
        jrChat_close_user_settings();
        if (typeof jrSmiley_close_drawer == 'function') {
            jrSmiley_close_drawer();
        }

        var m = $('#jrchat-messages');
        var u = $('#jrchat-room-browser');
        if (u.is(':visible')) {
            u.stop().animate({'width': '0'}, s, function()
            {
                m.removeClass('jrchat-overlay');
                u.hide();
            });
        }

        jrChat_position_fixed_elements(0, s, -w);

        $('body').stop().animate({'padding-right': '0'}, s);
        c.stop().animate({'right': '-' + (w + 1) + 'px'}, s);
        t.stop().animate({'right': '0'}, s);
        if (jrChat_get_active_loop_timer() < 30000) {
            jrChat_set_local_item('loop_timer', 30000);
            jrChat_set_local_item('loop_number', 5);
            __jrchat_ls = 30000;
        }

    }
}

/**
 * Disable a chat tab
 * @param id string DOM ID of tab
 */
function jrChat_disable_tab(id)
{
    $(id + ' .jrchat-tab').css('opacity', 0.2);
    $(id).attr('data-state', 'disabled');
}

/**
 * Enable a chat tab
 * @param id string DOM ID of tab
 */
function jrChat_enable_tab(id)
{
    $(id + ' .jrchat-tab').css('opacity', 1);
    $(id).attr('data-state', 'enabled');
}

/**
 * Check if a tab is disabled
 * @param id string
 * @returns {boolean}
 */
function jrChat_tab_is_disabled(id)
{
    return ($(id).attr('data-state') == 'disabled');
}

/**
 * Set initial tab state on init
 * @param s string
 */
function jrChat_set_initial_tab_state(s)
{
    var w = $('#jrchat-room').width();
    if (w >= 640) {
        jrChat_disable_tab('#jrchat-expand-tab');
        jrChat_enable_tab('#jrchat-contract-tab');
    }
    else if (w <= 280) {
        jrChat_enable_tab('#jrchat-expand-tab');
        jrChat_disable_tab('#jrchat-contract-tab');
    }
    jrChat_set_tab_state(s);
}

/**
 * Set the chat pane width
 */
function jrChat_set_width(a, cb)
{
    var s = 200;
    var c = $('#jrchat-room');
    var w = Number(c.width()) + Number(a);

    var b = $('#jrchat-available-rooms');
    if (b.is(':visible')) {
        b.stop().animate({'width': (w - 60) + 'px'}, s);
    }

    jrChat_position_fixed_elements(w, s, a);

    c.stop().animate({'width': w + 'px'}, s);

    var t = $('#jrchat-tabs');
    t.stop().animate({'right': w + 'px'}, s);

    jrChat_set_local_item('width', w);
    jrChat_save_width(w);
    jrChat_scroll_to_bottom(true);

    // Is our user browser open?
    var u = $('#jrchat-user-control');
    if (u.is(':visible')) {
        u.stop().animate({'width': (w - 26) + 'px'}, s);
    }

    u = $('#jrchat-room-browser');
    if (u.is(':visible')) {
        u.stop().animate({'width': (w - 26) + 'px'}, s);
    }

    u = $('#jrchat-user-settings');
    if (u.is(':visible')) {
        u.stop().animate({'width': (w - 26) + 'px'}, s);
    }

    u = $('#jrchat_smiley_drawer');
    if (u.is(':visible')) {
        u.stop().animate({'width': (w - 26) + 'px'}, s);
    }

    $('body').stop().animate({'padding-right': w + 'px'}, s, function()
    {
        cb(w);
    });
}

/**
 * Store the position of fixed elements
 */
function jrChat_store_fixed_element_positions()
{
    $(':fixed').each(function()
    {
        var id = $(this).attr('id');
        if (id !== null && typeof id !== "undefined" && id.indexOf('jrchat') !== 0) {
            var i = $('#' + id);
            var r = i.data('cssright');
            if (r == 'auto') {
                r = 0;
            }
            if (r === null || typeof r === "undefined") {
                r = i.css('right').replace('px', '');
                i.data('cssright', r);
            }
        }
    });
}

/**
 * Position elements that are FIXED on the page
 * @param w int Width in pixels of chat side bar
 * @param s int animation speed
 * @param a int amount being increment/decremented
 */
function jrChat_position_fixed_elements(w, s, a)
{
    w = Number(w);
    $(':fixed').each(function()
    {
        var l, r, id = $(this).attr('id'), c = $(this).attr('class');
        if (id !== null && typeof id !== "undefined" && id.indexOf('jrchat') !== 0 && (typeof c == "undefined" || c.indexOf('css-fixed') === -1)) {
            var i = $('#' + id);
            if (id == 'simplemodal-container') {
                r = $('body').width() - a;
                l = (r - i.outerWidth()) / 2;
                if (s > 0) {
                    i.stop().animate({'left': l + 'px'}, s);
                }
                else {
                    i.css('left', l + 'px');
                }
            }
            else if (id.indexOf('modal') === -1) {
                r = i.data('cssright');
                if (r == 'auto') {
                    r = 0;
                }
                if (r === null || typeof r === "undefined") {
                    r = i.css('right').replace('px', '');
                    i.data('cssright', r);
                }
                r = Number(r);
                if (w == 0) {
                    if (s > 0) {
                        i.stop().animate({'right': r}, s);
                    }
                    else {
                        i.css('right', r);
                    }
                }
                else {
                    if (s > 0) {
                        if (r > 0) {
                            i.stop().animate({'right': (w + r) + 'px'}, s);
                        }
                        else {
                            i.stop().animate({'right': (w / 2) + 'px'}, s);
                        }
                    }
                    else {
                        if (r > 0) {
                            i.css('right', (w + r) + 'px');
                        }
                        else {
                            i.css('right', (w / 2) + 'px');
                        }
                    }
                }
            }
        }
    });
}

/**
 * Expand the chat pane
 */
function jrChat_expand()
{
    if (jrChat_tab_is_disabled('#jrchat-expand-tab') === false) {
        jrChat_set_width(40, function(w)
        {
            if (w >= 640) {
                jrChat_disable_tab('#jrchat-expand-tab');
                jrChat_enable_tab('#jrchat-contract-tab');
            }
            else if (w > 280) {
                jrChat_enable_tab('#jrchat-expand-tab');
                jrChat_enable_tab('#jrchat-contract-tab');
            }
        });
    }
}

/**
 * Pop out chat into a separate window
 */
function jrChat_popout()
{
    window.open(core_system_url + '/' + jrChat_url + '/mobile');
    jrChat_toggle();
}

/**
 * Contract the chat pane
 */
function jrChat_contract()
{
    if (jrChat_tab_is_disabled('#jrchat-contract-tab') === false) {
        jrChat_set_width(-40, function(w)
        {
            jrChat_scroll_to_bottom(true);
            if (w <= 280) {
                jrChat_enable_tab('#jrchat-expand-tab');
                jrChat_disable_tab('#jrchat-contract-tab');
            }
            else if (w < 640) {
                jrChat_enable_tab('#jrchat-expand-tab');
                jrChat_enable_tab('#jrchat-contract-tab');
            }
        });
    }
}

/**
 * Save chat width to user preferences
 * @param w int Width in pixels
 */
function jrChat_save_width(w)
{
    if (!jrChat_is_mobile_view()) {
        var url = core_system_url + '/' + jrChat_url + '/set_chat_width/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {width: w},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                }
                return true;
            }
        });
    }
}

/**
 * Save chat state to user preferences
 * @param s string
 */
function jrChat_save_state(s)
{
    if (!jrChat_is_mobile_view()) {
        var url = core_system_url + '/' + jrChat_url + '/set_chat_state/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {state: s},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                }
                return true;
            }
        });
    }
}

/**
 * Return TRUE if we are in the mobile view
 * @returns {boolean}
 */
function jrChat_is_mobile_view()
{
    return (window.location.href.indexOf('/' + jrChat_url + '/mobile') > 0);
}

/**
 * Set an item in temp storage
 * @param key string
 * @param val mixed
 */
function jrChat_set_local_item(key, val)
{
    sessionStorage.setItem(key, val);
}

/**
 * Get a temp storage item
 * @param key string
 * @param def string Default to return if not set
 * @returns {boolean}
 */
function jrChat_get_local_item(key, def)
{
    var v = sessionStorage.getItem(key);
    if (typeof v !== "undefined" && v !== null) {
        return v;
    }
    return def;
}

/**
 * Set an item in temp storage
 * @param key string
 * @param val mixed
 */
function jrChat_set_item(key, val)
{
    localStorage.setItem(key, val);
}

/**
 * Get a temp storage item
 * @param key string
 * @param def string Default to return if not set
 * @returns {boolean}
 */
function jrChat_get_item(key, def)
{
    var v = localStorage.getItem(key);
    if (typeof v !== "undefined" && v !== null) {
        return v;
    }
    return def;
}

/**
 * Close the chat room selector
 * @param cb function callback
 * @returns {*}
 */
function jrChat_close_room_selector(cb)
{
    var i = $('#jrchat-available-rooms');
    if (i.is(':visible')) {
        i.slideUp(100, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-messages').removeClass('jrchat-overlay');
            if (typeof cb == "function") {
                return cb();
            }
        });
    }
    else if (typeof cb == "function") {
        return cb();
    }
}

/**
 * Select a new room from the quick drop down
 */
function jrChat_select_room_id()
{
    var m = $('#jrchat-messages');
    var i = $('#jrchat-available-rooms');
    jrChat_close_user_settings();
    jrChat_close_user_selector();
    if (i.is(':visible')) {
        jrChat_close_room_selector(function()
        {
            m.removeClass('jrchat-overlay');
        });
    }
    else {
        var u = core_system_url + '/' + jrChat_url + '/get_user_rooms/__ajax=1';
        $.ajax({
            type: 'GET',
            url: u,
            cache: false,
            dataType: 'html',
            success: function(r)
            {
                jrChat_check_login(r);
                var c = $('#jrchat-room');
                var w = c.width();
                i.width(w - 60).css('max-height', m.outerHeight() - 5).html(r).slideDown(100, function()
                {
                    m.addClass('jrchat-overlay');
                });
            }
        });
    }
}

/**
 * Disable enter to post for some msg snippets
 * @param msg string
 * @returns {boolean}
 */
function jrChat_disable_post_on_return(msg)
{
    switch (msg.toLowerCase()) {
        case '[code]':
        case '[quote]':
            return true;
    }
    return false;
}

/**
 * Save a new message to a chat room
 */
function jrChat_save_message(msg)
{
    var frm = $('#jrchat-new-message-input');
    var s = jrChat_get_local_item('search_results', 0);
    if (s == 1) {
        jrChat_close_search_selector();
        jrChat_init(function()
        {
            return jrChat_save_message(frm.val().trim());
        });
    }
    if (__jrchat_lm == false) {
        frm.addClass('form_disabled').attr('disabled', 'disabled');
        __jrchat_lm = true;
        clearTimeout(__jrchat_iv);
        var rid = jrChat_get_current_room_id();
        if (typeof msg == "undefined") {
            msg = frm.val().trim();
        }
        if (msg.indexOf('[code]') == -1) {
            msg = jrChat_strip_tags(msg);
        }
        if (msg.length > 0 && jrChat_disable_post_on_return(msg) === false) {
            var c = $('#jrchat-room').find('#chi');
            c.fadeIn(50, function()
            {
                var now = (new Date).getTime();
                rid = Number(rid);
                jrChat_send_save_request(rid, msg, now, function(s, e)
                {
                    if (s === true) {
                        // Success...
                        frm.removeClass('form_disabled').removeAttr('disabled').val('').focus().jrChat_is_typing();
                        jrChat_get_new_messages();
                        c.fadeOut(500, function()
                        {
                            __jrchat_lm = false;
                            __jrchat_ip = false;
                            jrChat_scroll_to_bottom(1);
                            jrChat_reset_loop_timer();
                            return jrChat_watch_loop(__jrchat_ls);
                        });
                    }
                    else {
                        // Error..
                        setTimeout(function()
                        {
                            jrChat_send_save_request(rid, msg, now, function(s, e)
                            {
                                if (s === true) {
                                    frm.removeClass('form_disabled').removeAttr('disabled').val('').focus().jrChat_is_typing();
                                    jrChat_get_new_messages();
                                    c.fadeOut(500, function()
                                    {
                                        __jrchat_lm = false;
                                        __jrchat_ip = false;
                                        jrChat_scroll_to_bottom(1);
                                        jrChat_reset_loop_timer();
                                        return jrChat_watch_loop(__jrchat_ls);
                                    });
                                }
                                else {
                                    if (e == 'timeout') {
                                        alert('the request timed out trying to communicate with the server - please try again');
                                    }
                                    else {
                                        alert('there was an error communicating with the server - please try again');
                                    }
                                    frm.removeClass('form_disabled').removeAttr('disabled').focus().jrChat_is_typing();
                                    jrChat_get_new_messages();
                                    c.fadeOut(500, function()
                                    {
                                        __jrchat_lm = false;
                                        __jrchat_ip = false;
                                        jrChat_scroll_to_bottom(1);
                                        jrChat_reset_loop_timer();
                                        return jrChat_watch_loop(__jrchat_ls);
                                    });
                                }
                            });
                        }, 3000);
                    }
                });
            });
        }
        else {
            // zero length message
            frm.removeClass('form_disabled').removeAttr('disabled');
            __jrchat_lm = false;
            jrChat_reset_loop_timer();
            return jrChat_watch_loop(__jrchat_ls);
        }
    }
    return false;
}

/**
 * Send request to save a new message
 * @param rid int Room ID
 * @param msg string Message
 * @param now int epoch time for post
 * @param cb function callback
 */
function jrChat_send_save_request(rid, msg, now, cb)
{
    var url = core_system_url + '/' + jrChat_url + '/new_message/__ajax=1';
    $.ajax({
        type: 'POST',
        url: url,
        data: {room_id: Number(rid), message: msg, unique: now},
        cache: false,
        dataType: 'json',
        timeout: 10000,
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }
            return cb(true, null);
        },
        error: function(x, t, e)
        {
            return cb(false, t);
        }
    });
}


/**
 * Main watch loop for new messages
 */
function jrChat_watch_loop(n)
{
    if (__jrchat_ip === false) {
        __jrchat_ip = true;
        if (typeof n === "undefined" || n === null || n < __jrchat_ls) {
            n = __jrchat_ls;
        }
        jrChat_set_local_item('loop_timer', n);
        __jrchat_iv = setTimeout(function()
        {
            jrChat_get_new_messages(function(s)
            {
                __jrchat_ip = false;
                jrChat_watch_loop(s);
            });
        }, n);
    }
}

/**
 * Scroll to the bottom of the chat window
 * @returns {boolean}
 */
function jrChat_scroll_to_bottom(f)
{
    var c = $('#jrchat-messages');
    if (f == 0) {
        setTimeout(function()
        {
            c.stop().scrollTop(c[0].scrollHeight + 30);
        }, 50);
    }
    else if (f == 1) {
        c.stop().animate({scrollTop: c[0].scrollHeight + 10}, 800);
    }
    else {
        var l = (c.scrollTop() + c.outerHeight());
        if (l >= (c[0].scrollHeight - 120)) {
            c.stop().animate({scrollTop: c[0].scrollHeight + 10}, 800);
        }
    }
    return true;
}

/**
 * Set the last_id flag for getting new messages
 * @param n int Number to set it to
 */
function jrChat_set_last_id(n)
{
    var lid = Number(jrChat_get_last_id());
    if (Number(n) > lid) {
        jrChat_set_local_item('last_id', n);
    }
    return true;
}

/**
 * Get the current last_id value
 * @returns {boolean}
 */
function jrChat_get_last_id()
{
    return jrChat_get_local_item('last_id', 0);
}

/**
 * Get NEW messages in a chat room
 */
function jrChat_get_new_messages(cb)
{
    var rid = jrChat_get_current_room_id();
    var lid = jrChat_get_last_id();
    var url = core_system_url + '/' + jrChat_url + '/new_messages/room_id=' + rid + '/last_id=' + Number(lid) + '/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            // Check for login
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }

            // Update room count
            jrChat_update_room_count(r);

            // Does the chat room still exist?
            if (typeof r.rid != "undefined" && r.rid == 0) {
                // Chat room we are currently in has been removed...
                return jrChat_show_no_chat_selected();
            }

            // Process messages
            jrChat_set_local_item('user_id', r.uid);
            if (typeof r.lid !== "undefined" && r.lid > 0 && Number(r.lid) > Number(lid)) {
                jrChat_set_last_id(r.lid);
            }

            var n = 0;
            var dn = {};
            var chr = $('#jrchat-room');
            var chm = $('#jrchat-messages');
            if (typeof r.new !== "undefined" && typeof r.new == "object" && r.new !== null) {

                var z = Object.keys(r.new).length;
                if (z > 0) {

                    // Update Title to show new message count if on mobile
                    var foc = jrChat_get_local_item('focused', 0);
                    if (foc == 0) {
                        if (jrChat_is_mobile_view()) {
                            var bnm = z;
                            var ttl = document.title;
                            if (ttl.indexOf('[') === 0) {
                                var brk = ttl.indexOf(']');
                                bnm = Number(ttl.substr(1, (brk - 1)));
                                if (bnm > 0) {
                                    bnm = Number(bnm + z);
                                }
                                ttl = ttl.substr(brk + 2);
                            }
                            document.title = '[' + bnm + '] ' + ttl;
                        }

                    }

                    var s = '';
                    var c = 0;
                    var lm = '';
                    var ld = '';
                    var gt = '';
                    var lu = '';
                    var li = jrChat_get_local_item('last_notification_id', 0);
                    var nw = ((new Date).getTime() - 86400000);
                    var ps = false;
                    for (var m in r.new) {
                        if (r.new.hasOwnProperty(m)) {

                            // Are we playing a message sound?
                            if (r.sound == 'on' && r.uid != r.new[m].u && !ps && foc == 0 && jrChat_get_tab_state() != 'closed') {
                                jrChat_new_message_sound();
                                ps = true;
                            }

                            if (r.new[m].r == rid) {
                                // We match the active room_id - show message if we are not already
                                if (chr.find('#m' + r.new[m].i).length === 0) {
                                    r.new[m].c = jrChat_process_message_action(r, r.new[m].c);
                                    s = s + jrChat_get_message_html(r.uid, r.new[m], nw);
                                    dn[r.new[m].u] = 1;
                                    if (r.new[m].i > lid) {
                                        n++;
                                    }
                                }

                                // If this user was previously typing, remove their entry
                                var uit = chr.find('#t' + r.new[m].u);
                                if (uit.length) {
                                    uit.remove();
                                }
                            }
                            else {
                                if (r.new[m].i > lid) {
                                    n++;
                                }
                            }
                            if (r.uid != r.new[m].u && r.new[m].i > li) {
                                // Message from another user in the chat room - notification
                                c++;
                                lm = r.new[m].c;
                                ld = r.new[m].u;
                                gt = r.new[m].m;
                                lu = r.new[m].n;
                                li = r.new[m].i;
                            }
                        }
                    }

                    // append all NEW messages
                    chm.append(s);

                    // If any of these are NOT from us, create notification
                    if (jrChat_get_tab_state() == 'open') {
                        if (foc == 0 && c > 0 && r.notify == 'on') {
                            // Is API Supported?
                            if ('Notification' in window) {
                                // And have we been granted permissions?
                                if (Notification.permission === "granted") {
                                    if (li != jrChat_get_item('last_notification_id', 0)) {
                                        new Notification("New Chat Message from " + lu, {
                                            icon: jrChat_get_user_image_url(ld, 'icon', gt),
                                            body: jrChat_strip_tags(lm)
                                        });
                                        jrChat_set_item('last_notification_id', li);
                                    }
                                }
                                // Otherwise, we need to ask the user for permission
                                else if (Notification.permission !== 'denied') {
                                    Notification.requestPermission(function(p)
                                    {
                                        // If the user accepts, let's create a notification
                                        if (p === "granted") {
                                            if (li != jrChat_get_item('last_notification_id', 0)) {
                                                new Notification("New Chat Message from " + lu, {
                                                    icon: jrChat_get_user_image_url(ld, 'icon', gt),
                                                    body: jrChat_strip_tags(lm)
                                                });
                                                jrChat_set_item('last_notification_id', li);
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                    else if (n > 0) {
                        var q = jrChat_get_notification_number() + n;
                        jrChat_set_notification_number(q);
                    }
                }
            }

            // Is anyone typing?
            if (jrChat_get_tab_state() == 'open' && typeof r.live !== "undefined") {
                var l = Object.keys(r.live).length;
                if (l > 0) {
                    for (var uid in r.live) {
                        if (r.live.hasOwnProperty(uid) && uid != r.uid && typeof dn[uid] == "undefined") {
                            if (r.live[uid] < 36) {
                                // Show user typing if we did not JUST post a new message from the user
                                if (chr.find('#t' + uid).length === 0) {
                                    var html = jrChat_user_is_typing_html(uid);
                                    chm.append(html);
                                    n++;
                                }
                            }
                            else {
                                var rti = chr.find('#t' + uid);
                                if (rti.length) {
                                    rti.fadeOut(800, function()
                                    {
                                        $(this).remove();
                                    });
                                }
                            }
                        }
                    }
                }
            }

            // If the user is at the bottom, move new message in place
            var lt = __jrchat_ls;
            if (n > 0) {
                // We have new messages
                jrChat_scroll_to_bottom(2);
                var v = jrChat_get_notification_number();
                jrChat_set_new_indicator(v - n);
                jrChat_reset_loop_timer();
            }
            else {
                // No messages - progressive back off
                lt = jrChat_get_active_loop_timer();
            }

            // Do we have any new messages in other chats that are not active?
            if (typeof r.other == "object") {
                var b = 0;
                for (var bn in r.other) {
                    if (r.other.hasOwnProperty(bn) && bn !== rid) {
                        b += r.other[bn];
                    }
                }
                jrChat_set_new_indicator(b);
                if (jrChat_get_tab_state() != 'open') {
                    jrChat_set_notification_number(b);
                }
            }

            if (typeof cb == "function") {
                return cb(lt);
            }
        }
    });
}

/**
 * Update count of users in a chat room
 * @param r object
 * @returns {boolean}
 */
function jrChat_update_room_count(r)
{
    if (typeof r.cnt !== "undefined") {
        $('.jrchat-bubble').text(Number(r.cnt)).fadeIn(100);
    }
    return true;
}

/**
 * Process a message action
 * @param r object Response object
 * @param m string Message
 * @returns {*}
 */
function jrChat_process_message_action(r, m)
{
    // i.e. ~page:user_id~{
    // i.e. ~page:everyone~{
    if (m.indexOf('~') === 0 && m.indexOf('~{') !== -1) {
        var t = m.substr(1).substr(0, m.indexOf('~{') - 1).split(':');
        var f = 'jrChat_action_' + t[0];
        if (typeof window[f] !== "undefined" && typeof window[f] == "function") {
            return jrChat_strip_message_action(window[f](t, r, m));
        }
    }
    return m;
}

/**
 * Strip a message action from the action text
 * @param msg string
 * @returns {string}
 */
function jrChat_strip_message_action(msg)
{
    if (msg.indexOf('~{') !== -1) {
        return msg.substr(msg.indexOf('~{') + 2).trim();
    }
    return msg;
}

/**
 * Action: page
 * @param t array action function params
 * @param r object Response object
 * @param m string Message
 * @returns {string}
 */
function jrChat_action_page(t, r, m)
{
    // We have to get our username from this message - if it is US,
    // then we need to ring the bell - otherwise just remove the action
    if (typeof t[1] !== "undefined" && (t[1] == 'everyone' || Number(t[1]) == Number(r.uid))) {
        // It is us - notify
        var s = new Audio(core_system_url + '/modules/jrChat/contrib/chime.mp3');
        s.play();
        var n = 0;
        var i = setInterval(function()
        {
            var f = Number(jrChat_get_local_item('focused', 0));
            if (f === 0) {
                s.play();
                n++;
                if (n == 2) {
                    clearInterval(i);
                }
            }
            else {
                clearInterval(i);
            }
        }, 3000);
    }
    return m;
}

/**
 * Play a new message sound
 */
function jrChat_new_message_sound()
{
    new Audio(core_system_url + '/modules/jrChat/contrib/message.mp3').play();
}

/**
 * Reset the loop timer
 */
function jrChat_reset_loop_timer()
{
    jrChat_set_local_item('loop_number', 0);
    jrChat_set_local_item('loop_timer', __jrchat_ls);
}

/**
 * Get the active loop timer
 * @returns {number}
 */
function jrChat_get_active_loop_timer()
{
    var t = __jrchat_ls;
    var n = (Number(jrChat_get_local_item('loop_number', 0)) + 1);
    jrChat_set_local_item('loop_number', n);
    if (n > 5) {
        t = Number(jrChat_get_local_item('loop_timer', __jrchat_ls)) + 1000;
        if (t > 30000) {
            if (jrChat_get_tab_state() == 'open') {
                t = 30000;
            }
            else if (t > 60000) {
                t = 60000;
            }
        }
    }
    return t;
}

/**
 * Show notifications for other chat rooms
 * @param rid int Room ID
 * @param r object
 * @param n int Number of new messages in active room
 * @returns {boolean}
 */
function jrChat_show_other_notifications(rid, r, n)
{
    if (typeof r !== "undefined" && typeof r == "object" && r != null) {
        if (Object.keys(r).length > 0) {
            var not = 0;
            rid = Number(rid);
            for (var t in r) {
                if (r.hasOwnProperty(t)) {
                    if (Number(t) != rid && r[t] > 0) {
                        not += r[t];
                    }
                }
            }
            if (not > 0) {
                if (not > 99) {
                    // Show 99 max...
                    not = 99;
                }
                jrChat_set_new_indicator(not);
                jrChat_set_notification_number(not);
                return true;
            }
        }
        else {
            jrChat_set_new_indicator(0);
            jrChat_set_notification_number(0);
        }
    }
    else {
        jrChat_set_new_indicator(0);
        jrChat_set_notification_number(0);
    }
    return true;
}

/**
 * Show new message indicator
 * @param n int Number
 * @returns {boolean}
 */
function jrChat_set_new_indicator(n)
{
    var c = $('#jrchat-select-room').find('.sprite_icon');
    n = Number(n);
    if (n > 0) {
        c.addClass('sprite_icon_hilighted');
    }
    else {
        c.removeClass('sprite_icon_hilighted');
    }
    return true;
}

/**
 * Get the current notification number
 * @returns {number}
 */
function jrChat_get_notification_number()
{
    return Number($('#jrchat-new-bubble').text());
}

/**
 * Set new number notification in bubble
 * @param n {number}
 */
function jrChat_set_notification_number(n)
{
    var c = $('#jrchat-open-close').children('span');
    n = Number(n);
    if (n > 0 && jrChat_get_tab_state() == 'closed') {
        // Highlight that there are new chats
        c.addClass('sprite_icon_hilighted');
        // $('#jrchat-new-bubble').text(n).show();
    }
    else {
        c.removeClass('sprite_icon_hilighted');
        // $('#jrchat-new-bubble').hide().text(0);
    }
}

/**
 * Return true if a user is an admin user
 * @returns {boolean}
 */
function jrChat_is_admin()
{
    var a = jrChat_get_local_item('is_admin', 0);
    return (Number(a) === 1);

}

/**
 * Get the current room id (as displayed on screen)
 * @returns {*}
 */
function jrChat_get_current_room_id()
{
    var i = jrChat_get_local_item('room_id', 0);
    if (i == 0) {
        i = $('#display-room-id').val();
    }
    return i;
}

/**
 * Set the current room id (as displayed on screen)
 * @param id int Room ID
 * @returns {boolean}
 */
function jrChat_set_current_room_id(id)
{
    jrChat_set_local_item('room_id', id); 
    return true;
}

/**
 * Load Messages for a Chat Room
 */
function jrChat_get_messages(bid, cb)
{
    var rid = jrChat_get_current_room_id();
    var url = core_system_url + '/' + jrChat_url + '/messages/room_id=' + rid + '/before_id=' + Number(bid) + '/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            // Check for login
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }

            // Update room count
            jrChat_update_room_count(r);

            jrChat_set_local_item('user_id', r.uid);
            jrChat_set_local_item('is_admin', r.adm);

            // Does the chat room still exist?
            if (typeof r.rid != "undefined" && r.rid == 0) {
                // Chat room we are currently in has been removed...
                return jrChat_show_no_chat_selected();
            }

            rid = Number(r.rid);
            jrChat_set_current_room_id(rid);
            if (typeof r.new !== "undefined" && r.new !== false) {

                var lid = jrChat_get_last_id();
                if (typeof r.lid !== "undefined" && r.lid > 0 && r.lid > lid) {
                    jrChat_set_last_id(r.lid);
                }
                if (typeof r.bid !== "undefined" && r.bid > 0) {
                    jrChat_set_local_item('before_id', r.bid);
                }

                var htm = jrChat_get_beginning_of_chat();
                var chm = $('#jrchat-messages');
                var nxp = $('#jrchat-load-next-page');
                var n = 0;
                var z = Object.keys(r.new).length;
                if (z > 0) {

                    var s = '';
                    var nw = ((new Date).getTime() - 86400000);
                    for (var m in r.new) {
                        if (r.new.hasOwnProperty(m)) {
                            if (r.new[m].r == rid) {
                                // We match the active room_id - show message
                                if ($('#jrchat-room #m' + r.new[m].i).length === 0) {
                                    r.new[m].c = jrChat_strip_message_action(r.new[m].c);
                                    s = s + jrChat_get_message_html(r.uid, r.new[m], nw);
                                    if (r.new[m].i > lid) {
                                        n++;
                                    }
                                }
                            }
                            else if (r.new[m].i > lid) {
                                // We have a new message that is NOT in our active room
                                n++;
                            }
                        }
                    }

                    if (bid == 0) {
                        // all NEW messages
                        chm.append(s);
                        jrChat_scroll_to_bottom(0);
                    }
                    else {
                        // Adding NEW messages above existing messages
                        // Save old height
                        var old_height = chm[0].scrollHeight;
                        var old_scroll = chm.scrollTop();

                        // Add new messages
                        nxp.after(s);

                        // Reposition
                        var new_height = chm[0].scrollHeight;
                        chm.scrollTop((old_scroll + new_height) - old_height);
                    }

                    if (z < 50) {
                        // We have less than 50 total messages in this room
                        chm.prepend(htm);
                        nxp.off('inview').remove();
                    }
                    else {
                        // There are more messages to show..
                        setTimeout(function()
                        {
                            jrChat_init_pager();
                        }, 2000);
                    }
                    
                    if (n > 0 && jrChat_get_tab_state() == 'closed') {
                        var c = jrChat_get_notification_number() + n;
                        jrChat_set_notification_number(c);
                    }
                }
                else {
                    // We have run out of previous pages
                    if (jrChat_get_last_id() > 0) {
                        chm.prepend(htm);
                    }
                    else {
                        // No messages in this chat room yet
                        chm.html(htm);
                    }
                    nxp.off('inview').remove();
                }

                if (typeof cb == "function") {
                    return cb(n);
                }
            }
        }
    });
}

/**
 * Show a message in the chat that a user is typing
 * @param uid int User ID
 * @returns {string}
 */
function jrChat_user_is_typing_html(uid)
{
    var u = core_system_url + '/' + jrUser_url + '/image/user_image';
    var c = $('#jrchat-icon-color').text();
    var v = $('#jrchat-version').text();
    return '<div id="t' + uid + '" class="jrchat-msg jrchat-msg-to"><div class="jrchat-msg-img"><img src="' + u + '/' + uid + '/small/crop=portrait" height="24" width="24"></div><div class="jrchat-msg-msg"><img src=\"' + core_system_url + '/' + jrImage_url + '/img/module/jrChat/typing-' + c + '.gif?_v=' + v + '" class="jrchat-typing-img"></div></div>';
}

/**
 * Get Message HTML
 * @param uid int User ID
 * @param msg object Msg
 * @param old int Epoch Time
 * @returns {string}
 */
function jrChat_get_message_html(uid, msg, old)
{
    var d = (msg.t * 1000);
    var t = new Date(d);
    var h = t.getHours();
    var p = 'AM';
    if (h == 0) {
        h = 12;
    }
    else if (h >= 12) {
        if (h > 12) {
            h = (h - 12);
        }
        p = 'PM';
    }
    var m = t.getMinutes();
    if (m < 10) {
        m = '0' + m;
    }
    var l;
    if (d < old) {
        l = t.toDateString() + ', ' + h + ':' + m + ' ' + p;
    }
    else {
        l = h + ':' + m + ' ' + p;
    }
    if (uid == msg.u) {
        return '<div id="m' + msg.i + '" class="jrchat-msg jrchat-msg-from">' + msg.c + '<div class="jrchat-msg-byline">' + msg.n + ', ' + l + '</div></div>';
    }
    return '<div id="m' + msg.i + '" class="jrchat-msg jrchat-msg-to"><div class="jrchat-msg-img"><a href="' + core_system_url + '/' + jrChat_url + '/profile/' + Number(msg.u) + '" target="_blank"><img src="' + jrChat_get_user_image_url(msg.u, 'small', msg.m) + '" height="24" width="24" title="' + msg.n + '"></a></div><div class="jrchat-msg-msg">' + msg.c + '<div class="jrchat-msg-byline">' + msg.n + ', ' + l + '</div></div></div>';
}

/**
 * Get a User image URL
 * @param u int User ID
 * @param s string Size
 * @param m string Gravatar URL
 * @returns {string}
 */
function jrChat_get_user_image_url(u, s, m)
{
    if (m == 1) {
        m = core_system_url + '/' + jrUser_url + '/image/user_image/' + u + '/' + s + '/crop=portrait';
    }
    return m;
}

/**
 * Close the user selector
 * @param cb function callback
 * @returns {*}
 */
function jrChat_close_user_selector(cb)
{
    var i = $('#jrchat-user-control');
    if (i.is(':visible')) {
        i.slideUp(100, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-messages').removeClass('jrchat-overlay');
            if (typeof cb == "function") {
                return cb();
            }
        });
    }
    else if (typeof cb == "function") {
        return cb();
    }
}

/**
 * Display chat room user control
 * @param r int se to "1" to reload
 */
function jrChat_get_room_users(r)
{
    var s = 200;
    jrChat_close_room_selector();
    jrChat_close_user_settings();
    jrChat_close_search_selector();
    var i = jrChat_get_current_room_id();
    if (i > 0) {
        var c = $('#jrchat-user-control');
        var m = $('#jrchat-messages');
        var u = core_system_url + '/' + jrChat_url + '/users/room_id=' + Number(i) + '/__ajax=1';
        if (c.is(':visible')) {
            if (typeof r !== "undefined" && r === 1) {
                $.ajax({
                    type: 'GET',
                    url: u,
                    cache: false,
                    dataType: 'html',
                    success: function(r)
                    {
                        if (r !== null && typeof r !== "undefined") {
                            jrChat_check_login(r);
                        }
                        if (typeof r !== "undefined" && r.indexOf('ERROR:') === -1) {
                            c.html(r);
                            jrChat_init_live_search('chat_room_user_id');
                        }
                    }
                });
            }
            else {
                c.slideUp(s, function()
                {
                    m.removeClass('jrchat-overlay');
                });
            }
        }
        else {

            var b = $('#jrchat-room-browser');
            if (b.is(':visible')) {
                b.slideUp(s);
            }

            c.width(m.width() - 12).height(m.outerHeight() - 18);
            $.ajax({
                type: 'GET',
                url: u,
                cache: false,
                dataType: 'html',
                success: function(r)
                {
                    if (r !== null && typeof r !== "undefined") {
                        jrChat_check_login(r);
                    }
                    if (typeof r !== "undefined" && r.indexOf('ERROR:') === -1) {
                        m.addClass('jrchat-overlay');
                        c.html(r).slideDown(s, function()
                        {
                            jrChat_init_live_search('chat_room_user_id');
                        });
                    }
                }
            });
        }
    }
}

/**
 * Create a new chat room
 * @returns {boolean}
 */
function jrChat_create_room()
{
    var ttl = $('#jrchat-new-chat-title').val();
    if (ttl.length > 0) {
        var typ = $('#jrchat-new-chat-type').is(':checked') ? 'on' : 'off';
        var url = core_system_url + '/' + jrChat_url + '/create_room/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {title: ttl, type: typ},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                }
                if (typeof r.rid !== "undefined") {
                    jrChat_load_room_id(r.rid);
                }
                else {
                    alert(r.error);
                }
            }
        });
    }
    else {
        alert('please enter a title for the chat room');
    }
    return true;
}

/**
 * delete a chat room
 * @returns {boolean}
 */
function jrChat_delete_room_id(i, c)
{
    var url = core_system_url + '/' + jrChat_url + '/delete_room/id=' + Number(i) + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
                alert(r.error);
            }
            else {
                if (typeof c !== "undefined" && c !== null) {
                    var v = jrChat_get_notification_number();
                    if (v > 0) {
                        jrChat_set_new_indicator(v - c);
                    }
                }
                $('.room-row' + i).remove();
                if (jrChat_get_current_room_id() === i) {
                    // deleting our active room - switch rooms
                    if (typeof r.rid !== "undefined" && r.rid > 0) {
                        jrChat_load_room_id(r.rid);
                    }
                    else {
                        var f = $('.room-row').first().attr('class');
                        var n = f.replace(/[^0-9]/g, '');
                        jrChat_load_room_id(n);
                    }
                }
            }
        }
    });
    return true;
}

/**
 * Load a new chat room by ID
 * @param id int Room ID
 * @param n int New Count
 */
function jrChat_load_room_id(id, n)
{
    var i = $('#jrchat-available-rooms');
    if (i.is(':visible')) {
        i.slideUp(100);
    }
    // Take care of small notification number on new room load
    if (typeof n !== "undefined" && n !== null && n > 0) {
        if (n == 0) {
            jrChat_set_new_indicator(0);
        }
        else {
            var v = jrChat_get_notification_number();
            if (n > v) {
                jrChat_set_new_indicator(0);
            }
            else {
                jrChat_set_new_indicator(v - n);
            }
        }
    }
    $('#jrchat-empty-chat').remove();
    var url = core_system_url + '/' + jrChat_url + '/get_room_info/id=' + Number(id) + '/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
                alert(r.error);
            }
            else {
                // Reset everything and load new room
                $('#jrchat-messages').empty().removeClass('jrchat-overlay');
                jrChat_set_current_room_id(id);
                jrChat_set_active_room_title(r.room_title);
                if (r.room_public == 0 && r.room_user_count == 1) {
                    $('.jrchat-bubble').text('+').fadeIn(100);
                }
                else {
                    $('.jrchat-bubble').text(r.room_user_count).fadeIn(100);
                }
                jrChat_set_local_item('messages', '');
                jrChat_init_pager();

                var b = $('#jrchat-room-browser');
                if (b.is(':visible')) {
                    b.slideUp(200);
                    jrChat_init();
                }
                else {
                    jrChat_init();
                }
            }
        }
    });
    return true;
}

/**
 * Close the user settings window
 * @param cb function callback
 * @returns {*}
 */
function jrChat_close_user_settings(cb)
{
    var i = $('#jrchat-user-settings');
    if (i.is(':visible')) {
        i.slideUp(100, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-messages').removeClass('jrchat-overlay');
            if (typeof cb == "function") {
                return cb();
            }
        });
    }
    else if (typeof cb == "function") {
        return cb();
    }
}

/**
 * User setting
 */
function jrChat_user_settings()
{
    var s = 200;
    var c = $('#jrchat-messages');
    var b = $('#jrchat-user-settings');
    jrChat_close_room_selector();
    jrChat_close_user_selector();
    jrChat_close_search_selector();
    if (b.is(':visible')) {
        jrChat_close_user_settings(function()
        {
            c.removeClass('jrchat-overlay');
        })
    }
    else {
        
        var r = $('#jrchat-room-browser');
        if (r.is(':visible')) {
            r.slideUp(s);
        }
        
        c.addClass('jrchat-overlay');
        b.width(c.width() - 12).height(c.outerHeight() - 18);
        var url = core_system_url + '/' + jrChat_url + '/user_config/__ajax=1';
        $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            dataType: 'html',
            success: function(r)
            {
                jrChat_check_login(r);
                if (typeof r.error !== "undefined") {
                    alert(r.error);
                }
                else {
                    b.html(r).slideDown(s);
                }
            }
        });
    }
}

/**
 * Save User Settings
 */
function jrChat_save_user_settings()
{
    var fsi = $('#jrChat_user_config').find('#form_submit_indicator');
    var nfy = $('#notifications').is(':checked') ? 'on' : 'off';
    var sos = $('input[name=online_status]:checked').val();
    var snd = $('input[name=message_sound]:checked').val();
    fsi.show(250, function()
    {
        setTimeout(function()
        {
            var url = core_system_url + '/' + jrChat_url + '/user_config_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                url: url,
                data: {notifications: nfy, online_status: sos, message_sound: snd},
                cache: false,
                dataType: 'json',
                success: function(r)
                {
                    fsi.hide(300, function()
                    {
                        if (typeof r.error !== "undefined") {
                            jrChat_check_login(r.error);
                            alert(r.error);
                        }
                        else {
                            jrChat_close_user_settings(function()
                            {
                                $('#jrchat-messages').removeClass('jrchat-overlay');
                            });
                        }
                    });
                }
            });
        }, 800);
    });
}

/**
 * Bring up search
 */
function jrChat_search_room()
{
    var s = 200;
    var b = $('#jrchat-room-search');

    jrChat_close_room_selector();
    jrChat_close_user_selector();
    jrChat_close_user_settings();

    if (b.is(':visible')) {
        b.slideUp(s, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-search-input').val('');
        });
    }
    else {
        b.slideDown(s, function()
        {
            $('#jrchat-search-input').focus();
            jrChat_set_chat_height();
        });
    }
}

/**
 * Reset search results
 */
function jrChat_search_reset()
{
    var c = $('#jrchat-search-input');
    c.val('');
    var s = jrChat_get_local_item('search_results', 0);
    if (s == 1) {
        // We were showing results
        jrChat_init(function()
        {
            c.focus();
        });
    }
}

/**
 * Close the search room selector
 * @param cb function callback
 * @returns {*}
 */
function jrChat_close_search_selector(cb)
{
    jrChat_set_local_item('search_results', 0);
    var i = $('#jrchat-room-search');
    if (i.is(':visible')) {
        i.slideUp(100, function()
        {
            jrChat_set_chat_height();
            $('#jrchat-search-input').val('');
            $('#jrchat-messages').removeClass('jrchat-overlay');
            if (typeof cb == "function") {
                return cb();
            }
        });
    }
    else if (typeof cb == "function") {
        return cb();
    }
}

/**
 * Search messages
 */
function jrChat_search_messages(ss)
{
    jrChat_set_local_item('search_results', 1);
    var rid = jrChat_get_current_room_id();
    var url = core_system_url + '/' + jrChat_url + '/messages/room_id=' + rid + '/ss=' + jrE(ss) + '/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            $('#jrchat-search-reset').show();

            // Check for login
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
            }

            // Update room count
            jrChat_update_room_count(r);

            jrChat_set_local_item('user_id', r.uid);
            jrChat_set_local_item('is_admin', r.adm);

            // Does the chat room still exist?
            if (typeof r.rid != "undefined" && r.rid == 0) {
                // Chat room we are currently in has been removed...
                return jrChat_show_no_chat_selected();
            }

            rid = Number(r.rid);
            jrChat_set_current_room_id(rid);
            if (typeof r.new !== "undefined" && r.new !== false) {

                var htm = jrChat_get_beginning_of_chat();
                var chm = $('#jrchat-messages');
                var nxp = $('#jrchat-load-next-page');
                var z = Object.keys(r.new).length;
                if (z > 0) {

                    var s = '';
                    var nw = ((new Date).getTime() - 86400000);
                    for (var m in r.new) {
                        if (r.new.hasOwnProperty(m)) {
                            if (r.new[m].r == rid) {
                                // We match the active room_id - show message
                                r.new[m].c = jrChat_strip_message_action(r.new[m].c);
                                s = s + jrChat_get_message_html(r.uid, r.new[m], nw);
                            }
                        }
                    }

                    // all NEW messages
                    chm.html(s).removeClass('jrchat-overlay');
                    jrChat_scroll_to_bottom(0);
                    if (z < 50) {
                        // We have less than 50 total messages in this room
                        chm.prepend(htm);
                        nxp.off('inview').remove();
                    }
                    else {
                        // There are more messages to show..
                        setTimeout(function()
                        {
                            jrChat_init_pager();
                        }, 2000);
                    }
                }
                else {
                    // We have run out of previous pages
                    if (jrChat_get_last_id() > 0) {
                        chm.prepend(htm);
                    }
                    else {
                        // No messages in this chat room yet
                        chm.html(htm);
                    }
                    nxp.off('inview').remove();
                }
            }
            else {
                return jrChat_show_no_search_results();
            }
        }
    });
}

function jrChat_show_no_search_results()
{
    jrChat_close_search_selector();
    var c = $('#jrchat-messages');
    c.empty().html('<div id="jrchat-empty-chat">No Search Results Found</div>');
}

/**
 * Bring up the chat room browser
 */
function jrChat_room_browser()
{
    var s = 200;
    var c = $('#jrchat-messages');
    var b = $('#jrchat-room-browser');
    jrChat_close_room_selector();
    jrChat_close_user_settings();
    jrChat_close_search_selector();
    if (b.is(':visible')) {
        b.slideUp(s, function()
        {
            c.removeClass('jrchat-overlay');
        });
    }
    else {

        var u = $('#jrchat-user-control');
        if (u.is(':visible')) {
            u.slideUp(s);
        }

        c.addClass('jrchat-overlay');
        b.width(c.width() - 12).height(c.outerHeight() - 18);

        var url = core_system_url + '/' + jrChat_url + '/get_chats/last_id=' + jrChat_get_last_id() + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            dataType: 'html',
            success: function(r)
            {
                jrChat_check_login(r);
                if (typeof r.error !== "undefined") {
                    alert(r.error);
                }
                else {
                    b.html(r).slideDown(s, function()
                    {
                        jrChat_init_live_search('chat_user_id');
                    });
                }
            }
        });
    }
}

/**
 * Add a new user to an existing chat room
 */
function jrChat_add_user_to_chat()
{
    var uid = $('#chat_room_user_id_livesearch_value').val();
    if (typeof uid !== "undefined" && uid > 0) {
        var rid = jrChat_get_current_room_id();
        var url = core_system_url + '/' + jrChat_url + '/add_user_to_chat/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {room_id: rid, user_id: uid},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                    alert(r.error);
                }
                else {

                    clearTimeout(__jrchat_iv);
                    setTimeout(function()
                    {
                        jrChat_get_room_users(1);

                        var b = $('.jrchat-bubble');
                        var v = b.text();
                        if (v == '+') {
                            v = 1;
                        }
                        b.show().text(Number(v) + 1);

                        jrChat_get_new_messages(function()
                        {
                            jrChat_scroll_to_bottom(1);
                            __jrchat_ip = false;
                            jrChat_reset_loop_timer();
                            return jrChat_watch_loop(__jrchat_ls);
                        });
                    }, 300);
                }
            }
        });
    }
}

/**
 * Remove an existing user from chat
 */
function jrChat_remove_user_from_chat(uid, b)
{
    var rid = jrChat_get_current_room_id();
    var url = core_system_url + '/' + jrChat_url + '/remove_user_from_chat/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {room_id: rid, user_id: uid, block: b},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrChat_check_login(r.error);
                alert(r.error);
            }
            else {

                if (r.self == 1) {
                    // user has left the chat
                    jrChat_show_no_chat_selected();
                    var c = $('#jrchat-user-control');
                    c.slideUp(250, function()
                    {
                        m.removeClass('jrchat-overlay');
                    });
                }
                else {

                    clearTimeout(__jrchat_iv);
                    jrChat_get_room_users(1);

                    var b = $('.jrchat-bubble');
                    var v = b.text();
                    if (v == '+') {
                        v = 1;
                    }
                    b.show().text(Number(v) - 1);

                    jrChat_get_new_messages(function()
                    {
                        jrChat_scroll_to_bottom(1);
                        __jrchat_ip = false;
                        jrChat_reset_loop_timer();
                        return jrChat_watch_loop(__jrchat_ls);
                    });
                }
            }
        }
    });
}

/**
 * Start a private chat with a user
 */
function jrChat_start_chat_with_user()
{
    var uid = $('#chat_user_id_livesearch_value').val();
    if (typeof uid !== "undefined" && uid > 0) {
        var url = core_system_url + '/' + jrChat_url + '/create_private_room/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {user_id: uid},
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrChat_check_login(r.error);
                    alert(r.error);
                }
                else {
                    jrChat_load_room_id(r.rid);
                }
            }
        });
    }
}

/**
 * Show a "no chat selected" message
 */
function jrChat_show_no_chat_selected()
{
    $('.jrchat-bubble').hide();
    jrChat_set_active_room_title('no active chat');
    var c = $('#jrchat-messages');
    c.empty().html('<div id="jrchat-empty-chat">No Chat has been Selected</div>');
}

/**
 * author Christopher Blum
 * - based on the idea of Remy Sharp, http://remysharp.com/2009/01/26/element-in-view-event-plugin/
 * - forked from http://github.com/zuk/jquery.inview/
 */
(function(factory)
{
    if (typeof define == 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    }
    else if (typeof exports === 'object') {
        // Node, CommonJS
        module.exports = factory(require('jquery'));
    }
    else {
        // Browser globals
        factory(jQuery);
    }
}(function($)
{
    var inviewObjects   = [], viewportSize, viewportOffset,
        d               = document,
        w               = window,
        documentElement = d.documentElement, timer;

    $.event.special.inview = {
        add: function(data)
        {
            inviewObjects.push({data: data, $element: $(this), element: this});
            // Use setInterval in order to also make sure this captures elements within
            // "overflow:scroll" elements or elements that appeared in the dom tree due to
            // dom manipulation and reflow
            // old: $(window).scroll(checkInView);
            //
            // By the way, iOS (iPad, iPhone, ...) seems to not execute, or at least delays
            // intervals while the user scrolls. Therefore the inview event might fire a bit late there
            //
            // Don't waste cycles with an interval until we get at least one element that
            // has bound to the inview event.
            if (!timer && inviewObjects.length) {
                timer = setInterval(checkInView, 250);
            }
        },

        remove: function(data)
        {
            for (var i = 0; i < inviewObjects.length; i++) {
                var inviewObject = inviewObjects[i];
                if (inviewObject.element === this && inviewObject.data.guid === data.guid) {
                    inviewObjects.splice(i, 1);
                    break;
                }
            }

            // Clear interval when we no longer have any elements listening
            if (!inviewObjects.length) {
                clearInterval(timer);
                timer = null;
            }
        }
    };

    function getViewportSize()
    {
        var mode, domObject, size = {height: w.innerHeight, width: w.innerWidth};

        // if this is correct then return it. iPad has compat Mode, so will
        // go into check clientHeight/clientWidth (which has the wrong value).
        if (!size.height) {
            mode = d.compatMode;
            if (mode || !$.support.boxModel) { // IE, Gecko
                domObject = mode === 'CSS1Compat' ?
                    documentElement : // Standards
                    d.body; // Quirks
                size = {
                    height: domObject.clientHeight,
                    width: domObject.clientWidth
                };
            }
        }

        return size;
    }

    function getViewportOffset()
    {
        return {
            top: w.pageYOffset || documentElement.scrollTop || d.body.scrollTop,
            left: w.pageXOffset || documentElement.scrollLeft || d.body.scrollLeft
        };
    }

    function checkInView()
    {
        if (!inviewObjects.length) {
            return;
        }

        var i = 0, $elements = $.map(inviewObjects, function(inviewObject)
        {
            var selector = inviewObject.data.selector,
                $element = inviewObject.$element;
            return selector ? $element.find(selector) : $element;
        });

        viewportSize = viewportSize || getViewportSize();
        viewportOffset = viewportOffset || getViewportOffset();

        for (; i < inviewObjects.length; i++) {
            // Ignore elements that are not in the DOM tree
            if (!$.contains(documentElement, $elements[i][0])) {
                continue;
            }

            var $element      = $($elements[i]),
                elementSize   = {height: $element[0].offsetHeight, width: $element[0].offsetWidth},
                elementOffset = $element.offset(),
                inView        = $element.data('inview');

            // Don't ask me why because I haven't figured out yet:
            // viewportOffset and viewportSize are sometimes suddenly null in Firefox 5.
            // Even though it sounds weird:
            // It seems that the execution of this function is interferred by the onresize/onscroll event
            // where viewportOffset and viewportSize are unset
            if (!viewportOffset || !viewportSize) {
                return;
            }

            if (elementOffset.top + elementSize.height > viewportOffset.top &&
                elementOffset.top < viewportOffset.top + viewportSize.height &&
                elementOffset.left + elementSize.width > viewportOffset.left &&
                elementOffset.left < viewportOffset.left + viewportSize.width) {
                if (!inView) {
                    $element.data('inview', true).trigger('inview', [true]);
                }
            }
            else if (inView) {
                $element.data('inview', false).trigger('inview', [false]);
            }
        }
    }

    $(w).on("scroll resize scrollstop", function()
    {
        viewportSize = viewportOffset = null;
    });

    // IE < 9 scrolls to focused elements without firing the "scroll" event
    if (!documentElement.addEventListener && documentElement.attachEvent) {
        documentElement.attachEvent("onfocusin", function()
        {
            viewportOffset = null;
        });
    }
}));

/**
 * Create a small "user is typing" message
 */
(function($)
{
    $.fn.jrChat_is_typing = function()
    {
        function send_message_in_progress(obj)
        {
            var c = $(obj).val().length;
            if (c > 1) {
                var now = (new Date).getTime();
                var old = __jrchat_ls;
                if (now - Number(jrChat_get_local_item('last_send', old)) >= old) {
                    var url = core_system_url + '/' + jrChat_url + '/am_typing/__ajax=1';
                    var rid = jrChat_get_current_room_id();
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {room_id: rid},
                        cache: false,
                        dataType: 'json'
                    });
                    jrChat_set_local_item('last_send', now);
                }
            }
        };
        this.each(function()
        {
            $(this).keyup(function(e)
            {
                if (e.keyCode != 13) {
                    send_message_in_progress(this)
                }
            });
            $(this).change(function(e)
            {
                if (e.keyCode != 13) {
                    send_message_in_progress(this)
                }
            });
        });

    };

})(jQuery);

$.extend($.expr[':'], {
    absolute: function(e)
    {
        return $(e).css('position') === 'absolute';
    },
    fixed: function(e)
    {
        return $(e).css('position') === 'fixed';
    }
});

/**
 * Strip HTML tags from a message
 * @param input
 * @param allowed
 * @returns {string|XML}
 */
function jrChat_strip_tags(input, allowed)
{
    //  discuss at: http://phpjs.org/functions/strip_tags/
    allowed = (((allowed || '') + '')
        .toLowerCase()
        .match(/<[a-z][a-z0-9]*>/g) || [])
        .join('');
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        comm = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(comm, '')
        .replace(tags, function($0, $1)
        {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
        });
}

/**
 * Check for login redirect in error message
 * @param e string result/message
 * @returns {boolean}
 */
function jrChat_check_login(e)
{
    if (e.indexOf('requires you to be logged in') !== -1) {
        window.location = core_system_url + '/' + jrUser_url + '/login';
    }
    return true;
}
