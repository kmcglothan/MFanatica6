// Jamroom Core Javascript
// @copyright 2003-2013 by Talldude Networks LLC

/**
 * Set number of rows for pagination
 * @param n Number
 * @param cb {function}
 */
function jrCore_set_pager_rows(n, cb)
{
    jrSetCookie('jrcore_pager_rows', n, 30);
    return cb();
}

/**
 * Set CSRF location cookie
 * Validated on the server site with jrCore_validate_location_url()
 * @param {string} u URL
 */
function jrCore_set_csrf_cookie(u)
{
    return jrSetCookie('jr_location_url', u, 1);
}

/**
 * Set location CSRF cookie and redirect
 * @param {string} u URL
 */
function jrCore_window_location(u)
{
    jrCore_set_csrf_cookie(u);
    window.location = u;
}

/**
 * Creates a checkbox in form to prevent spam bots from submitting forms
 * @param {string} n Name of checkbox element to add
 * @param {number} i Tab Index value for form
 * @return {boolean}
 */
function jrFormSpamBotCheckbox(n, i)
{
    $('#sb_' + n).html('<input type="checkbox" id="' + n + '" name="' + n + '" tabindex="' + i + '">');
    return true;
}

/**
 * Handle Stream URL Errors from the Media Player
 * @param {object} e jPlayer error response object
 * @return {boolean}
 */
function jrCore_stream_url_error(e)
{
    if (e.jPlayer.error.type == 'e_url') {
        // Get module_url from media URL
        var _tm = e.jPlayer.error.context.replace(core_system_url + '/', '').split('/');
        var url = _tm[0];
        $.get(core_system_url + '/' + jrCore_url + '/stream_url_error/' + url + '/__ajax=1', function(r) {
            if (typeof r.error != "undefined" && r.error !== null) {
                jrCore_alert(r.error);
            }
            else if (typeof e.jPlayer.error.message == "undefined" || e.jPlayer.error.message == null) {
                jrCore_alert(e.jPlayer.error.message);
            }
            else {
                jrCore_alert('an unknown error has occured - please try again');
            }
        });
    }
    return true;
}

/**
 * Submits a form handling validation
 * @param {string} fi Form ID to submit
 * @param {string} ck MD5 checksum for validation key
 * @param {string} mt ajax/modal/post - post form as an AJAX form or normal (post) form
 */
function jrFormSubmit(fi, ck, mt)
{
    var rv = false;
    var si = $(fi).find('#form_submit_indicator');
    var sb = $('.form_submit_section input');
    $('.field-hilight').removeClass('field-hilight');
    sb.attr("disabled", "disabled").addClass('form_button_disabled');
    si.show(250, function() {
        var to = setTimeout(function() {
            // get all the inputs into an array.
            $('.form_editor').each(function() {
                $('#' + this.name + '_editor_contents').val(tinyMCE.get('e' + this.name).getContent());
            });
            var values = $(fi).serializeArray();
            // See if we have saved off entries on load
            if (typeof values !== "object" || values.length === 0) {
                si.hide(300, function() {
                    sb.removeAttr("disabled").removeClass('form_button_disabled');
                    jrFormSystemError(fi, "Unable to serialize form elements for submitting!");
                });
                clearTimeout(to);
                return false;
            }
            var action = $(fi).attr("action");
            if (typeof action === "undefined") {
                si.hide(300, function() {
                    sb.removeAttr("disabled").removeClass('form_button_disabled');
                    jrFormSystemError(fi, "Unable to retrieve form action value for submitting");
                });
                clearTimeout(to);
                return false;
            }

            // Handle form validation
            if (typeof ck !== "undefined" && ck !== null) {

                // Submit URL for validation
                $.ajax({
                    type: 'POST',
                    data: values,
                    cache: false,
                    dataType: 'json',
                    url: core_system_url + '/' + jrCore_url + '/form_validate/__ajax=1',
                    success: function(r) {
                        // Handle any messages
                        if (typeof r === "undefined" || r === null) {
                            si.hide(300, function() {
                                sb.removeAttr("disabled").removeClass('form_button_disabled');
                                jrFormSystemError(fi, 'Empty response received from server - please try again');
                            });
                        }
                        else if (typeof r.OK === "undefined" || r.OK != '1') {
                            if (typeof r.redirect != "undefined") {
                                clearTimeout(to);
                                window.location = r.redirect;
                                return true;
                            }
                            else if (typeof r.on_close != "undefined") {
                                clearTimeout(to);
                                return window[r.on_close](r);
                            }
                            jrFormMessages(fi, r);
                        }
                        else {
                            // r is "OK" - looks OK to submit now
                            if (typeof mt == "undefined" || mt == "ajax") {
                                $.ajax({
                                    type: 'POST',
                                    url: action + '/__ajax=1',
                                    data: values,
                                    cache: false,
                                    dataType: 'json',
                                    success: function(_pmsg) {
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
                                            jrFormMessages(fi, _pmsg);
                                        }
                                        rv = true;
                                    },
                                    error: function(x, t, e) {
                                        si.hide(300, function() {
                                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                                            // See if we got a message back from the core
                                            var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                                            if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                                msg = 'JSON response error: ' + x.responseText;
                                            }
                                            jrFormSystemError(fi, msg);
                                        });
                                    }
                                });
                            }

                            // Modal window
                            else if (mt == "modal") {

                                si.hide(600, function() {
                                    var k = $('#jr_html_modal_token').val();
                                    var n = 0;
                                    $('#modal_window').modal();
                                    $('#modal_indicator').show();

                                    // Setup our "listener" which will update our work progress
                                    var sid = setInterval(function() {
                                        sb.removeAttr("disabled").removeClass('form_button_disabled');
                                        $.ajax({
                                            cache: false,
                                            dataType: 'json',
                                            url: core_system_url + '/' + jrCore_url + '/form_modal_status/k=' + k + '/__ajax=1',
                                            success: function(t) {
                                                n = 0;
                                                var fnc = 'jrFormModalSubmit_update_process';
                                                window[fnc](t, sid);
                                            },
                                            error: function(r, t, e) {
                                                // Track errors - if we get to 10 we error out
                                                n++;
                                                if (n > 10) {
                                                    clearInterval(sid);
                                                    jrCore_alert('An error was encountered communicating with the server: ' + t + ': ' + e);
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
                                        success: function() {
                                            clearTimeout(to);
                                            return true;
                                        }
                                    });
                                });
                            }

                            // normal POST submit
                            else {
                                $(fi).submit();
                                rv = true;
                            }
                        }
                    },
                    error: function(x, t, e) {
                        si.hide(300, function() {
                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                            // See if we got a message back from the core
                            var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                            if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                msg = 'JSON response error: ' + x.responseText;
                            }
                            jrFormSystemError(fi, msg);
                        });
                    }
                });
            }
            // No validation
            else {

                // AJAX or normal submit?
                if (typeof mt == "undefined" || mt == "ajax") {
                    $.ajax({
                        type: 'POST',
                        url: action + '/__ajax=1',
                        data: values,
                        cache: false,
                        dataType: 'json',
                        success: function(r) {
                            // Check for URL redirection
                            if (typeof r.redirect != "undefined") {
                                window.location = r.redirect;
                            }
                            else if (typeof r.on_close != "undefined") {
                                return window[r.on_close](r);
                            }
                            else {
                                jrFormMessages(fi, r);
                            }
                            rv = true;
                        },
                        error: function(x, t, e) {
                            si.hide(300, function() {
                                sb.removeAttr("disabled").removeClass('form_button_disabled');
                                // See if we got a message back from the core
                                var msg = 'a system level error was encountered trying to validate the form values: ' + t + ': ' + e;
                                if (typeof x.responseText !== "undefined" && x.responseText.length > 1) {
                                    msg = 'JSON response error: ' + x.responseText;
                                }
                                jrFormSystemError(fi, msg);
                            });
                        }
                    });
                }

                // Modal window
                else if (mt == "modal") {

                    si.hide(600, function() {
                        var k = $('#jr_html_modal_token').val();
                        var n = 0;
                        $('#modal_window').modal();
                        $('#modal_indicator').show();
                        // Setup our "listener" which will update our work progress
                        var sid = setInterval(function() {
                            sb.removeAttr("disabled").removeClass('form_button_disabled');
                            $.ajax({
                                cache: false,
                                dataType: 'json',
                                url: core_system_url + '/' + jrCore_url + '/form_modal_status/k=' + k + '/__ajax=1',
                                success: function(t) {
                                    n = 0;
                                    var fnc = 'jrFormModalSubmit_update_process';
                                    window[fnc](t, sid);
                                },
                                error: function(r, t, e) {
                                    // Track errors - if we get to 10 we error out
                                    n++;
                                    if (n > 10) {
                                        clearInterval(sid);
                                        jrCore_alert('An error was encountered communicating with the server: ' + t + ': ' + e);
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
                            success: function() {
                                clearTimeout(to);
                                return true;
                            }
                        });
                    });
                }

                else {
                    $(fi).submit();
                    rv = true;
                }
            }
            clearTimeout(to);
            return rv;
        }, 500);
    });
}

/**
 * Show a system notice
 */
function jrFormSystemError(fi, t)
{
    jrFormMessages(fi + '_msg', {"notices": [{'type': 'error', 'text': t}]});
}

/**
 * jrFormMessages
 */
function jrFormMessages(fi, _msg)
{
    var m = $(fi + '_msg');
    var rv = true;
    $('.page-notice-shown').hide(10);
    // Handle any messages
    if (typeof _msg.notices !== "undefined") {
        for (var n in _msg.notices) {
            if (_msg.notices.hasOwnProperty(n)) {
                m.html(_msg.notices[n].text).removeClass("error success warning notice").addClass(_msg.notices[n].type);
                if (_msg.notices[n].type === 'error') {
                    rv = false;
                }
            }
        }
    }
    // Handle any error fields
    if (typeof _msg.error_fields !== "undefined") {
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
    var si = $(fi).find('#form_submit_indicator');
    si.hide(300, function() {
        m.slideDown(150, function() {
            $('.form_submit_section input').removeAttr('disabled').removeClass('form_button_disabled');
            if ($('.simplemodal-container').length === 0 && m.position() && m.position().top < $(window).scrollTop()) {
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
    if (o.opener === null) {
        o.opener = self;
    }
}

/**
 * The jrSetCookie function will set a Javascript cookie
 */
function jrSetCookie(n, v, d)
{
    var expires = '';
    if (d) {
        var date = new Date();
        date.setTime(date.getTime() + (d * 86400000));
        expires = "; expires=" + date.toGMTString();
    }
    document.cookie = n + "=" + v + expires + "; path=/";
}

/**
 * The jrReadCookie Function will return the value of a previously set cookie
 */
function jrReadCookie(n)
{
    var ne = n + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        {
            if (c.indexOf(ne) === 0) return c.substring(ne.length, c.length);
        }
    }
    return null;
}

/**
 * The jrEraseCookie will remove a cookie set by jrSetCookie()
 */
function jrEraseCookie(n)
{
    jrSetCookie(n, "", -1);
}

/**
 * Check for module updates
 * @param {object} data Message Object
 * @param {number} sid Update Interval Timer name
 * @return {boolean}
 */
function jrFormModalSubmit_update_process(data, sid)
{
    // Check for any error/complete messages
    for (var u in data) {
        if (data.hasOwnProperty(u)) {
            // When our work is complete on the server we will get a "type"
            // message back (complete,update,error)
            var k = $('#jr_html_modal_token');
            if (typeof data[u].t !== "undefined") {
                var e = $('#modal_error');
                var t = $('#modal_updates');
                var s = $('#modal_success');
                switch (data[u].t) {
                    case 'complete':
                        clearInterval(sid);
                        e.hide();
                        s.prepend(data[u].m + '<br><br>').show();
                        jrFormModalCleanup(k.val());
                        break;
                    case 'update':
                        t.prepend(data[u].m + '<br>');
                        break;
                    case 'empty':
                        return true;
                    case 'error':
                        t.prepend(data[u].m + '<br>');
                        s.hide();
                        e.prepend(data[u].m + '<br><br>').show();
                        break;
                    default:
                        clearInterval(sid);
                        jrFormModalCleanup(k.val());
                        break;
                }
            }
            else {
                clearInterval(sid);
                jrFormModalCleanup(k.val());
            }
        }
    }
    return true;
}

/**
 * jrFormModalCleanup
 * @param {string} k Form ID
 * @return {boolean}
 */
function jrFormModalCleanup(k)
{
    $('#modal_indicator').hide();
    $.ajax({
        cache: false,
        url: core_system_url + '/' + jrCore_url + '/form_modal_cleanup/k=' + k + '/__ajax=1'
    });
    return true;
}

/**
 * jrE - encodeURIComponent
 * @param {string} t String to encode
 * @return {string}
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
jQuery.uaMatch = function(ua) {
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
 * @param {string} id DOM element
 * @param {string} url URL to load
 * @returns {boolean}
 */
function jrCore_load_into(id, url)
{
    if (typeof url === "undefined") {
        return false;
    }
    var i = $(id);
    i.fadeOut(100, function() {
        i.html('<div style="text-align:center;padding:20px;margin:0 auto;"><img src="' + core_system_url + '/' + jrImage_url + '/img/module/jrCore/loading.gif" style="margin:15px"></div>').fadeIn(100, function() {
            i.load(url, function(r) {
                i.html(r);
            });
        })
    });
    return false;
}

/**
 * Delete an Attachment
 * @param {number} id Item ID
 * @param {string} f upload field
 * @param {string} m upload module
 * @param {number} i index
 */
function jrCore_delete_attachment(id, f, m, i)
{
    var action = core_system_url + '/' + jrCore_url + '/attachment_delete';
    jrCore_set_csrf_cookie(action);
    $.ajax({
        type: 'POST',
        url: action + '/__ajax=1',
        data: {id: id, upload_field: f, upload_module: m},
        cache: false,
        dataType: 'json',
        success: function(_pmsg) {
            // Check for URL redirection
            if (typeof _pmsg.success !== "undefined") {
                $('#' + m + '_' + id + '_' + i).fadeOut(300, function() {
                    $(this).remove();
                    var ab = $('#ab' + id + ' .image_delete');
                    if (ab.length === 0) {
                        $('#ab' + id).remove();
                    }
                });
            }
        },
        error: function() {
            jrCore_alert('jamroom: transmission error - please try again');
        }
    });
}

/**
 * Show pending notice for pending item
 * @param {string} n notice
 */
function jrCore_show_pending_notice(n)
{
    jrCore_alert(n);
}

/**
 * Show an alert
 * @param {string} text
 */
function jrCore_alert(text)
{
    swal({
        type: 'warning',
        title: '',
        text: text,
        animation: false,
        confirmButtonText: 'OK',
        closeOnConfirm: true
    });
}

/**
 * Show a confirmation prompt
 * @param {string} title
 * @param {string} text
 * @param {function} conf
 * @return {boolean}
 */
function jrCore_confirm(title, text, conf)
{
    var o = {
        type: 'warning',
        title: title,
        animation: false,
        showCancelButton: true,
        confirmButtonText: 'OK',
        closeOnConfirm: false
    };
    if (typeof text !== "undefined") {
        o.text = text;
    }
    swal(o, function(c) {
        if (c) {
            swal.close();
            return conf();
        }
        else {
            return false;
        }
    });
}



$(document).ready(function (e) {
    if ($(window).width() <= 767) {

        $('.jrform').find("input[type=text], input[type=file], input[type=password], textarea").each(function(ev)
        {
            var text =  $(this).parent().parent().find('.element_left').text().trim();
            if (text.length === 0) {
                text =  $(this).parent().parent().parent().find('.element_left').text().trim();
            }

            var parent = $(this).parent();
            var el = '<span class="form_label">' + text + '</span>';
            parent.prepend(el);
        });

        var cbl = $('.checkbox_left');
        cbl.parent().find('.checkbox_right').append(cbl.text());
    }

});