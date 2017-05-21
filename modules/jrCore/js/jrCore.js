// Jamroom Core Javascript
// @copyright 2003-2013 by Talldude Networks LLC

var __jrcto = null;

/**
 * Set a page reload timeout on the Dashboard
 * @param seconds
 * @param auto
 */
function jrCore_dashboard_reload_page(seconds, auto)
{
    var id = '#reload';
    var ck = jrReadCookie('dash_reload');
    if (auto == '1' && typeof ck !== "undefined" && ck == 'off') {
        // We are disabled
        return true;
    }
    else if (typeof ck !== "undefined" && ck == 'on') {
        // See if we are reloading...
        if (auto != '1') {
            return jrCore_dashboard_disable_reload(seconds);
        }
    }
    else {
        jrSetCookie('dash_reload', 'on', 30);
    }

    $(id).removeClass('form_button_disabled');
    var d = 0;
    var v = seconds;
    __jrcto = setInterval(function()
    {
        d += 1;
        v -= 1;
        if (d >= seconds) {
            clearInterval(__jrcto);
            window.location.reload();
        }
        $(id).val(v);
    }, 1000);
}

function jrCore_dashboard_disable_reload(seconds)
{
    jrSetCookie('dash_reload', 'off');
    clearInterval(__jrcto);
    $('#reload').val(seconds).addClass('form_button_disabled');
    return true;
}

/**
 * Config the Dashboard with a custom panel
 * @param id
 * @returns {boolean}
 */
function jrCore_dashboard_panel(id)
{
    var url = core_system_url + '/' + jrCore_url + '/dashboard_panels/' + id + '/__ajax=1';
    $('#db_modal').modal();
    $.get(url, function(r)
    {
        $('#db_modal').html(r);
    });
    return false;
}

/**
 * Delete an Activity Log Entry
 * @param id
 * @returns {boolean}
 */
function jrCore_delete_activity_log(id)
{
    var url = core_system_url + '/' + jrCore_url + '/activity_log_delete/id=' + id + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.get(url, function(r)
    {
        if (r.msg == 'ok') {
            $('#d' + id).parent().parent('tr').remove();
        }
        else {
            jrAlertMessage(r.msg)
        }
    });
    return false;
}

/**
 * Delete an entry from the Recycle Bin
 * @param id int
 * @returns {boolean}
 */
function jrCore_delete_recyce_bin_entry(id)
{
    if (confirm('Permanently delete this item?')) {
        var url = core_system_url + '/' + jrCore_url + '/recycle_bin_delete/id=' + id + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.get(url, function(r)
        {
            if (r.msg == 'ok') {
                $('#d' + id).parent().parent('tr').remove();
            }
            else {
                jrAlertMessage(r.msg)
            }
        });
    }
    return false;
}

/**
 * Delete an entry from the Queue Browser
 * @param id int
 * @returns {boolean}
 */
function jrCore_delete_queue_entry(id)
{
    var url = core_system_url + '/' + jrCore_url + '/queue_entry_delete/id=' + id + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.get(url, function(r)
    {
        if (id.indexOf(',') > -1) {
            window.location.reload();
        }
        else {
            $('#d' + id).parent().parent('tr').remove();
            if ($('.tk_checkbox').length === 0) {
                window.location.reload();
            }
        }
    });
    return false;
}

/**
 * Set a panel in the dashboard
 * @param row int Row to set
 * @param col int Column to set
 * @param opt string Function to set
 */
function jrCore_set_dashboard_panel(row, col, opt)
{
    var url = core_system_url + '/' + jrCore_url + '/set_dashboard_panel/row=' + Number(row) + '/col=' + Number(col) + '/opt=' + jrE(opt) + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.post(url, function(_msg)
    {
        if (typeof _msg.error !== "undefined") {
            alert(_msg.error);
        }
        else {
            $.modal.close();
            window.location.reload();
        }
    });
}

/**
 * Set number of rows for pagination
 * @param num
 * @param callback
 */
function jrCore_set_pager_rows(num, callback)
{
    jrSetCookie('jrcore_pager_rows', num, 30);
    return callback();
}

/**
 * Set CSRF location cookie
 * @param url
 * @returns {boolean}
 */
function jrCore_set_csrf_cookie(url)
{
    return jrSetCookie('jr_location_url', url, 1);
}

/**
 * Set location CSRF cookie and redirect
 * @param url
 */
function jrCore_window_location(url)
{
    jrCore_set_csrf_cookie(url);
    window.location = url;
}

/**
 * Creates a checkbox in form to prevent spam bots from submitting forms
 * @param {string} name Name of checkbox element to add
 * @param {number} idx Tab Index value for form
 * @return bool
 */
function jrFormSpamBotCheckbox(name, idx)
{
    $('#sb_' + name).html('<input type="checkbox" id="' + name + '" name="' + name + '" tabindex="' + idx + '">');
    return true;
}

/**
 * Handle Stream URL Errors from the Media Player
 * @param error object jPlayer error response object
 * @return bool
 */
function jrCore_stream_url_error(error)
{
    if (error.jPlayer.error.type == 'e_url') {
        // Get module_url from media URL
        var _tm = error.jPlayer.error.context.replace(core_system_url + '/', '').split('/');
        var url = _tm[0];
        $.get(core_system_url + '/' + jrCore_url + '/stream_url_error/' + url + '/__ajax=1', function(res)
        {
            if (typeof res.error != "undefined" && res.error !== null) {
                alert(res.error);
            }
        });
    }
    return true;
}

/**
 * Submits a form handling validation
 * @param {string} form_id Form ID to submit
 * @param {string} vkey MD5 checksum for validation key
 * @param {string} method ajax/modal/post - post form as an AJAX form or normal (post) form
 */
function jrFormSubmit(form_id, vkey, method)
{
    var rv = false;
    var si = $(form_id).find('#form_submit_indicator');
    var sb = $('.form_submit_section input');
    $('.field-hilight').removeClass('field-hilight');
    sb.attr("disabled", "disabled").addClass('form_button_disabled');
    si.show(250, function()
    {
        var to = setTimeout(function()
        {
            // get all the inputs into an array.
            $('.form_editor').each(function(i)
            {
                $('#' + this.name + '_editor_contents').val(tinyMCE.get('e' + this.name).getContent());
            });
            var values = $(form_id).serializeArray();
            // See if we have saved off entries on load
            if (typeof values !== "object" || values.length === 0) {
                si.hide(300, function()
                {
                    sb.removeAttr("disabled").removeClass('form_button_disabled');
                    jrFormSystemError(form_id, "Unable to serialize form elements for submitting!");
                });
                clearTimeout(to);
                return false;
            }
            var action = $(form_id).attr("action");
            if (typeof action === "undefined") {
                si.hide(300, function()
                {
                    sb.removeAttr("disabled").removeClass('form_button_disabled');
                    jrFormSystemError(form_id, "Unable to retrieve form action value for submitting");
                });
                clearTimeout(to);
                return false;
            }

            // Handle form validation
            if (typeof vkey !== "undefined" && vkey !== null) {

                // Submit URL for validation
                $.ajax({
                    type: 'POST',
                    data: values,
                    cache: false,
                    dataType: 'json',
                    url: core_system_url + '/' + jrCore_url + '/form_validate/__ajax=1',
                    success: function(_msg)
                    {
                        // Handle any messages
                        if (typeof _msg === "undefined" || _msg === null) {
                            si.hide(300, function()
                            {
                                sb.removeAttr("disabled").removeClass('form_button_disabled');
                                jrFormSystemError(form_id, 'Empty response received from server - please try again');
                            });
                        }
                        else if (typeof _msg.OK === "undefined" || _msg.OK != '1') {
                            if (typeof _msg.redirect != "undefined") {
                                clearTimeout(to);
                                window.location = _msg.redirect;
                                return true;
                            }
                            else if (typeof _msg.on_close != "undefined") {
                                clearTimeout(to);
                                return window[_msg.on_close](_msg);
                            }
                            jrFormMessages(form_id, _msg);
                        }
                        else {
                            // _msg is "OK" - looks OK to submit now
                            if (typeof method == "undefined" || method == "ajax") {
                                $.ajax({
                                    type: 'POST',
                                    url: action + '/__ajax=1',
                                    data: values,
                                    cache: false,
                                    dataType: 'json',
                                    success: function(_pmsg)
                                    {
                                        // Check for URL redirection
                                        if (typeof _pmsg.redirect != "undefined") {
                                            clearTimeout(to);
                                            window.location = _pmsg.redirect;
                                        }
                                        else if (typeof _pmsg.on_close != "undefined") {
                                            clearTimeout(to);
                                            return window[_pmsg.on_close](_pmsg);
                                        }
                                        else {
                                            jrFormMessages(form_id, _pmsg);
                                        }
                                        rv = true;
                                    },
                                    error: function(x, t, e)
                                    {
                                        si.hide(300, function()
                                        {
                                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                                            // See if we got a message back from the core
                                            var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                                            if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                                msg = 'JSON response error: ' + x.responseText;
                                            }
                                            jrFormSystemError(form_id, msg);
                                        });
                                    }
                                });
                            }

                            // Modal window
                            else if (method == "modal") {

                                si.hide(600, function()
                                {
                                    var k = $('#jr_html_modal_token').val();
                                    var n = 0;
                                    $('#modal_window').modal();
                                    $('#modal_indicator').show();

                                    // Setup our "listener" which will update our work progress
                                    var sid = setInterval(function()
                                    {
                                        sb.removeAttr("disabled").removeClass('form_button_disabled');
                                        $.ajax({
                                            cache: false,
                                            dataType: 'json',
                                            url: core_system_url + '/' + jrCore_url + '/form_modal_status/k=' + k + '/__ajax=1',
                                            success: function(tmp, s, x)
                                            {
                                                n = 0;
                                                var fnc = 'jrFormModalSubmit_update_process';
                                                window[fnc](tmp, sid);
                                            },
                                            error: function(r, t, e)
                                            {
                                                // Track errors - if we get to 10 we error out
                                                n++;
                                                if (n > 10) {
                                                    clearInterval(sid);
                                                    alert('An error was encountered communicating with the server: ' + t + ': ' + e);
                                                }
                                            }
                                        })
                                    }, 1000);

                                    // Submit form
                                    $.ajax({
                                        type: 'POST',
                                        url: action + '/__ajax=1',
                                        data: values,
                                        cache: false,
                                        dataType: 'json',
                                        success: function()
                                        {
                                            clearTimeout(to);
                                            return true;
                                        }
                                    });
                                });
                            }

                            // normal POST submit
                            else {
                                $(form_id).submit();
                                rv = true;
                            }
                        }
                    },
                    error: function(x, t, e)
                    {
                        si.hide(300, function()
                        {
                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                            // See if we got a message back from the core
                            var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                            if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                msg = 'JSON response error: ' + x.responseText;
                            }
                            jrFormSystemError(form_id, msg);
                        });
                    }
                });
            }
            // No validation
            else {

                // AJAX or normal submit?
                if (typeof method == "undefined" || method == "ajax") {
                    $.ajax({
                        type: 'POST',
                        url: action + '/__ajax=1',
                        data: values,
                        cache: false,
                        dataType: 'json',
                        success: function(_msg)
                        {
                            // Check for URL redirection
                            if (typeof _msg.redirect != "undefined") {
                                window.location = _msg.redirect;
                            }
                            else if (typeof _msg.on_close != "undefined") {
                                return window[_msg.on_close](_msg);
                            }
                            else {
                                jrFormMessages(form_id, _msg);
                            }
                            rv = true;
                        },
                        error: function(x, t, e)
                        {
                            si.hide(300, function()
                            {
                                sb.removeAttr("disabled").removeClass('form_button_disabled');
                                // See if we got a message back from the core
                                var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                                if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                    msg = 'JSON response error: ' + x.responseText;
                                }
                                jrFormSystemError(form_id, msg);
                            });
                        }
                    });
                }

                // Modal window
                else if (method == "modal") {

                    si.hide(600, function()
                    {
                        var k = $('#jr_html_modal_token').val();
                        var n = 0;
                        $('#modal_window').modal();
                        $('#modal_indicator').show();
                        // Setup our "listener" which will update our work progress
                        var sid = setInterval(function()
                        {
                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                            $.ajax({
                                cache: false,
                                dataType: 'json',
                                url: core_system_url + '/' + jrCore_url + '/form_modal_status/k=' + k + '/__ajax=1',
                                success: function(tmp, s, x)
                                {
                                    n = 0;
                                    var fnc = 'jrFormModalSubmit_update_process';
                                    window[fnc](tmp, sid);
                                },
                                error: function(r, t, e)
                                {
                                    // Track errors - if we get to 10 we error out
                                    n++;
                                    if (n > 10) {
                                        clearInterval(sid);
                                        alert('An error was encountered communicating with the server: ' + t + ': ' + e);
                                    }
                                }
                            })
                        }, 1000);

                        // Submit form
                        $.ajax({
                            type: 'POST',
                            url: action + '/__ajax=1',
                            data: values,
                            cache: false,
                            dataType: 'json',
                            success: function()
                            {
                                clearTimeout(to);
                                return true;
                            }
                        });
                    });
                }

                else {
                    $(form_id).submit();
                    rv = true;
                }
            }
            clearTimeout(to);
            return rv;
        }, 500);
    });
}

/**
 * jrFormSystemError
 */
function jrFormSystemError(form_id, text)
{
    jrFormMessages(form_id + '_msg', {"notices": [{'type': 'error', 'text': text}]});
}

/**
 * jrFormMessages
 */
function jrFormMessages(form_id, _msg)
{
    var m = $(form_id + '_msg');
    var rv = true;
    $('.page-notice-shown').hide(10);
    // Handle any messages
    if (typeof _msg.notices != "undefined") {
        for (var n in _msg.notices) {
            if (_msg.notices.hasOwnProperty(n)) {
                m.html(_msg.notices[n].text).removeClass("error success warning notice").addClass(_msg.notices[n].type);
                if (_msg.notices[n].type == 'error') {
                    rv = false;
                }
            }
        }
    }
    // Handle any error fields
    if (typeof _msg.error_fields != "undefined") {
        for (var e in _msg.error_fields) {
            if (_msg.error_fields.hasOwnProperty(e)) {
                $(_msg.error_fields[e]).addClass('field-hilight');
            }
        }
    }
    else {
        // Remove any previous errors
        $('.field-hilight').removeClass('field-hilight');
    }
    var si = $(form_id).find('#form_submit_indicator');
    si.hide(300, function()
    {
        m.slideDown(150, function()
        {
            $('.form_submit_section input').removeAttr('disabled').removeClass('form_button_disabled');
            if ($('.simplemodal-close').length == 0 && m.position() && m.position().top < $(window).scrollTop()) {
                $('html,body').animate({scrollTop: (m.position().top - 100)}, 300);
            }
        });
    });
    return rv;
}

/**
 * generic popup window
 */
function popwin(page, name, w, h, scr)
{
    var b = $('body');
    var l = (b.width() / 2) - (w / 2);
    var t = (b.height() / 2) - (h / 2);
    var s = 'height=' + h + ',width=' + w + ',top=' + t + ',left=' + l + ',scrollbars=' + scr + ',resizable';
    var o = window.open(page, name, s);
    if (o.opener == null) {
        o.opener = self;
    }
}

/**
 * The jrSetCookie function will set a Javascript cookie
 */
function jrSetCookie(name, value, days)
{
    var expires = '';
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

/**
 * The jrReadCookie Function will return the value of a previously set cookie
 */
function jrReadCookie(name)
{
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        {
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
    }
    return null;
}

/**
 * The jrEraseCookie will remove a cookie set by jrSetCookie()
 */
function jrEraseCookie(name)
{
    jrSetCookie(name, "", -1);
}

/**
 * jrAlertMessage
 */
function jrAlertMessage(msg)
{
    alert(msg);
}

/**
 * Check for module updates
 * @param data Message Object
 * @param sid Update Interval Timer
 * @param skey string Form ID
 * @return bool
 */
function jrFormModalSubmit_update_process(data, sid, skey)
{
    // Check for any error/complete messages
    var k = false;
    for (var u in data) {
        if (data.hasOwnProperty(u)) {
            // When our work is complete on the server we will get a "type"
            // message back (complete,update,error)
            if (typeof data[u].t != "undefined") {
                switch (data[u].t) {
                    case 'complete':
                        clearInterval(sid);
                        $('#modal_error').hide();
                        $('#modal_success').prepend(data[u].m + '<br><br>').show();
                        k = $('#jr_html_modal_token').val();
                        jrFormModalCleanup(k);
                        break;
                    case 'update':
                        $('#modal_updates').prepend(data[u].m + '<br>');
                        break;
                    case 'empty':
                        return true;
                        break;
                    case 'error':
                        $('#modal_updates').prepend(data[u].m + '<br>');
                        $('#modal_success').hide();
                        $('#modal_error').prepend(data[u].m + '<br><br>').show();
                        break;
                    default:
                        clearInterval(sid);
                        k = $('#jr_html_modal_token').val();
                        jrFormModalCleanup(k);
                        break;
                }
            }
            else {
                clearInterval(sid);
                k = $('#jr_html_form_token').val();
                jrFormModalCleanup(k);
            }
        }
    }
    return true;
}

/**
 * jrFormModalCleanup
 * @param skey string Form ID
 * @return bool
 */
function jrFormModalCleanup(skey)
{
    $('#modal_indicator').hide();
    $.ajax({
        cache: false,
        url: core_system_url + '/' + jrCore_url + '/form_modal_cleanup/k=' + skey + '/__ajax=1'
    });
    return true;
}

/**
 * jrE - encodeURIComponent
 * @param t string String to encode
 * @return string
 */
function jrE(t)
{
    return encodeURIComponent(t);
}

/**
 * replacement for jQuery $.browser
 * @param ua
 * @returns {{browser: (*|string), version: (*|string)}}
 */
jQuery.uaMatch = function(ua)
{
    ua = ua.toLowerCase();
    var match = /(chrome)[ \/]([\w.]+)/.exec(ua) || /(webkit)[ \/]([\w.]+)/.exec(ua) || /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) || /(msie) ([\w.]+)/.exec(ua) || ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) || [];
    return {
        browser: match[1] || "",
        version: match[2] || "0"
    };
};

if (!jQuery.browser) {
    matched = jQuery.uaMatch(navigator.userAgent);
    browser = {};
    if (matched.browser) {
        browser[matched.browser] = true;
        browser.version = matched.version;
    }
    // Chrome is Webkit, but Webkit is also Safari.
    if (browser.chrome) {
        browser.webkit = true;
    }
    else if (browser.webkit) {
        browser.safari = true;
    }
    jQuery.browser = browser;
}

/**
 * Load a URL into a DOM element with spinner and fade in/out
 * @param id {string} DOM element
 * @param url {string} URL to load
 * @returns {boolean}
 */
function jrCore_load_into(id, url)
{
    if (typeof url == "undefined") {
        return false;
    }
    var i = $(id);
    i.fadeOut(100, function()
    {
        i.html('<div style="text-align:center;padding:20px;margin:0 auto;"><img src="' + core_system_url + '/' + jrImage_url + '/img/module/jrCore/loading.gif" style="margin:15px"></div>').fadeIn(100, function()
        {
            i.load(url, function(r)
            {
                i.html(r);
            });
        })
    });
    return false;
}

/**
 * Get widget module info
 * @param i
 */
function jrCore_widget_list_get_module_info(i)
{
    var m = $(i).val();
    var a = $('#active_module');
    if (m != a.val()) {
        a.val(m);
        var u = core_system_url + '/' + jrCore_url + '/widget_list_get_module_info/m=' + jrE(m) + '/__ajax=1';
        $.get(u, function(r)
        {
            $('#ff-row-list_custom_template').hide();
            $('#list_pagebreak').removeClass('form_element_disabled').removeAttr('disabled');
            $('#list_limit').removeClass('form_element_disabled').removeAttr('disabled');
            $('.list_search_op').removeClass('form_element_disabled').removeAttr('disabled');
            $('.list_search_text').removeClass('form_element_disabled').removeAttr('disabled');
            $('.list_search_key').each(function()
            {
                $(this).empty().removeClass('form_element_disabled').removeAttr('disabled');
                for (i = 0; i < r[0].length; ++i) {
                    $(this).append($('<option></option>').attr('value', r[0][i]).text(' ' + r[0][i]));
                }
            });
            $('#list_order_by_dir').removeClass('form_element_disabled').removeAttr('disabled');
            var id = $('#list_order_by_key');
            id.empty().removeClass('form_element_disabled').removeAttr('disabled');
            for (i = 0; i < r[0].length; ++i) {
                id.append($('<option></option>').attr('value', r[0][i]).text(' ' + r[0][i]));
            }
            id = $('#list_group_by');
            id.empty().removeClass('form_element_disabled').removeAttr('disabled');
            for (i = 0; i < r[0].length; ++i) {
                id.append($('<option></option>').attr('value', r[0][i]).text(' ' + r[0][i]));
            }
            id = $('#list_template');
            id.empty().removeClass('form_element_disabled').removeAttr('disabled');
            for (i in r[1]) {
                if (r[1].hasOwnProperty(i)) {
                    id.append($('<option></option>').attr('value', i).text(' ' + r[1][i]));
                }
            }
            jrCore_update_template_structure();
        });
    }
}


/**
 * Delete an Attachment
 * @param item_id
 * @param upload_field
 * @param upload_module
 * @param idx
 */
function jrCore_delete_attachment(item_id, upload_field, upload_module, idx)
{
    var action = core_system_url + '/' + jrCore_url + '/attachment_delete';
    jrCore_set_csrf_cookie(action);
    $.ajax({
        type: 'POST',
        url: action + '/__ajax=1',
        data: {
            id: item_id,
            upload_field: upload_field,
            upload_module: upload_module
        },
        cache: false,
        dataType: 'json',
        success: function(_pmsg)
        {
            // Check for URL redirection
            if (typeof _pmsg.success != "undefined") {
                $('#' + upload_module + '_' + item_id + '_' + idx).fadeOut(300, function()
                {
                    $(this).remove();
                    var ab = $('#ab' + item_id + ' .image_delete');
                    if (ab.length === 0) {
                        $('#ab' + item_id).remove();
                    }
                });
            }
        },
        error: function(x, t, e)
        {
            alert('jamroom: transmission error - please try again');
        }
    });
}

/**
 * Show pending notice for pending item
 * @param n string notice
 */
function jrCore_show_pending_notice(n)
{
    alert(n);
}
