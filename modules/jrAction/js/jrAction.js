/**
 * Share a timeline item to your timeline
 * @param mod string
 * @param id int
 */
function jrAction_share(mod, id)
{
    if ($('#share_modal').length === 0) {
        $('body').append('<div id="share_modal"></div>');
    }
    var u = core_system_url + '/' + jrAction_url + '/share_msg/' + mod + '/' + Number(id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.get(u, function(r)
    {
        $('#share_modal').html(r).modal();
        $("#share_update").focus()
    });
    return false;
}

/**
 * Save a new share
 * @returns {boolean}
 */
function jrAction_share_save()
{
    $('#share_submit').attr('disabled', 'disabled').addClass('form_button_disabled');
    $('#share_submit_indicator').show(300, function()
    {
        $('#share_form').submit()
    });
}

/**
 * Load a Quick Share form
 * @param t object This
 * @param m string Module
 * @param f string Function
 */
var __ds_title = '';
function jrAction_quick_share(t, m, f)
{
    $('.quick_action_tab_active').removeClass('quick_action_tab_active').children('span').removeClass('sprite_icon_hilighted');
    $(t).addClass('quick_action_tab_active').children('span').addClass('sprite_icon_hilighted');
    $('#jrAction_function').val(f);
    var d = $('#quick_action_default_form');
    if (f == 'jrAction_quick_share_status_update') {
        if (d.is(':hidden')) {
            var q = $('#quick_action_form');
            q.fadeOut(100, function()
            {
                d.fadeIn(100, function()
                {
                    $('#action_update').focus();
                });
            });
            $('#quick_action_title').text(__ds_title);
        }
    }
    else {
        if (__ds_title.length === 0) {
            __ds_title = $('#quick_action_title').text();
        }
        var u = core_system_url + '/' + jrAction_url + '/quick_share_form/__ajax=1';
        $.ajax({
            type: 'POST',
            data: {m: m, function: f},
            cache: false,
            dataType: 'json',
            url: u,
            success: function(r)
            {
                var a = $('#quick_action_form input');
                var q = $('#quick_action_form');
                if (d.is(':visible')) {
                    d.fadeOut(100, function()
                    {
                        q.html(r.html).fadeIn(100, function()
                        {
                            a.first().focus();
                        });
                    });
                }
                else {
                    q.fadeOut(100, function()
                    {
                        q.html(r.html).fadeIn(100, function()
                        {
                            a.first().focus();
                        });
                    });
                }
                $('#quick_action_title').text(r.title);
            }
        });
    }
}

/**
 * Submit new action from timeline form
 */
function jrAction_submit()
{
    if ($('#quick_action_default_form').is(':visible')) {
        var e = $('#action_text_editor_contents');
        if (e.length) {
            // Editor is enabled
            if (tinyMCE.get('eaction_text').getContent().length < 1) {
                return false;
            }
            e.val(tinyMCE.get('eaction_text').getContent());
        }
        else if ($('#action_update').val().length < 1) {
            return false;
        }
    }
    $('#quick_action_form').find('input').removeClass('field-hilight');
    $('#action_submit').attr('disabled', 'disabled').addClass('form_button_disabled');
    var a = $('#asi');
    a.show(300, function()
    {
        setTimeout(function()
        {
            var f = $('#action_form');
            $.post(f.attr('action'), f.serializeArray(), function(r)
            {
                if (typeof r.field !== "undefined") {
                    a.hide(300, function()
                    {
                        $('#action_submit').removeAttr('disabled').removeClass('form_button_disabled');
                        $('#quick_action_form').find('#' + r.field).addClass('field-hilight');
                    });
                }
                else if (typeof r.error !== "undefined") {
                    a.hide(300, function()
                    {
                        $('#action_submit').removeAttr('disabled').removeClass('form_button_disabled');
                        jrCore_alert(r.error);
                    });
                }
                else {
                    window.location.reload();
                }
            }, 'json');
        }, 200);
    });
}

/**
 * copy and alteration of the character count plugin
 * Character Count Plugin - jQuery plugin
 * Dynamic character count for text areas and input fields
 * written by Alen Grakalic
 * http://cssglobe.com/post/7161/jquery-plugin-simplest-twitterlike-dynamic-character-count-for-textareas
 * Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 * Built for jQuery library
 * http://jquery.com
 * @modified for JR5 by the Jamroom Network.
 */
(function($)
{
    $.fn.shareCharCount = function(options)
    {
        // default configuration properties
        var defaults = {
            allowed: 140,
            warning: 20,
            cssWarning: 'share_warning',
            cssExceeded: 'share_exceeded'
        };
        options = $.extend(defaults, options);

        function calculate(obj)
        {
            var count = $(obj).val().length;
            var available = options.allowed - count;
            if (available <= options.warning && available >= 0) {
                $('#share_text_counter').addClass(options.cssWarning);
            }
            else {
                $('#share_text_counter').removeClass(options.cssWarning);
            }
            if (available < 0) {
                $('#share_text_counter').addClass(options.cssExceeded);
            }
            else {
                $('#share_text_counter').removeClass(options.cssExceeded);
            }
            $('#share_text_num').html(available);
        };
        this.each(function()
        {
            calculate(this);
            $(this).keyup(function()
            {
                calculate(this)
            });
            $(this).change(function()
            {
                calculate(this)
            });
        });
    };
})(jQuery);

