// Jamroom Comment Module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Submits a new Comment
 * @param {string} uid Unique Form ID
 * @param tpl the name of the template to use to format the returning comments.
 * @param limit the number of results returned in the comments
 * @return bool
 */
function jrPostComment(uid, tpl, limit)
{
    var s = 300;
    var usub = $(uid + '_cm_submit');
    var unot = $(uid + '_cm_notice');
    var ufsi = $(uid + '_fsi');
    if (ufsi.length === 0) {
        //noinspection JSJQueryEfficiency
        $('body').append('<div id="' + uid.substr(1) + '_fsi"></div>');
        //noinspection JSJQueryEfficiency
        ufsi = $('body').find(uid + '_fsi');
        s = 0;
    }

    usub.attr("disabled", "disabled").addClass('form_button_disabled');
    ufsi.show(s, function()
    {
        unot.hide();
        var t = setTimeout(function()
        {
            var val = $(uid + '_form').serializeArray();
            var url = core_system_url + '/' + jrComment_url + '/comment_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                url: url,
                data: val,
                cache: false,
                dataType: 'json',
                success: function(r)
                {
                    if (typeof r.error !== "undefined") {
                        unot.text(r.error);
                        ufsi.hide(s, function()
                        {
                            usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                            unot.fadeIn(250);
                        });
                    }
                    else {
                        $(uid + '_form textarea').val('');
                        usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                        ufsi.hide(s, function()
                        {
                            var mod = $(uid + '_cm_module').val();
                            var iid = $(uid + '_cm_item_id').val();
                            var ord = $(uid + '_cm_order_by').val();
                            var now = new Date().getTime();
                            $(uid + '_comments').load(core_system_url + '/' + jrComment_url + '/view_comments/item_module=' + jrE(mod) + '/item_id=' + Number(iid) + '/order_by=' + jrE(ord) + '/template=' + tpl + '/limit=' + Number(limit) + '/new=' + Number(r.item_id) + '/__ajax=1/_v=' + now, function()
                            {
                                $('#comment_form_section').slideDown(300);
                                // Go to our comment
                                var cid = '#cm' + r.item_id;
                                if (r.highlight == 'on') {
                                    $('html, body').animate({scrollTop: $(cid).offset().top - 200}, 300);
                                }
                                if (typeof tinyMCE != "undefined" && tinyMCE.get('comment_text') != "undefined") {
                                    tinyMCE.activeEditor.setContent('');
                                    $('#comment_reply_to_user').text('');
                                    $('#comment_reply_to').hide();
                                }
                                // Hide any file attachments from post
                                $('.qq-upload-list').html('');
                                $('#comment_parent_id').val('0');
                            });
                        });
                    }
                },
                error: function()
                {
                    ufsi.hide(s, function()
                    {
                        usub.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                        unot.text('Error communicating with server - please try again').show();
                    });
                }
            });
            clearTimeout(t);
        }, (s * 3));
    });
}

/**
 * Append new comments
 * @param module {string} Comment module
 * @param item_id {number} Item_ID
 * @param this_page {number} Current Page Number
 * @param next_page {number} Next Page Number
 * @returns {boolean}
 */
function jrComment_load(module, item_id, this_page, next_page)
{
    $('#cploader').slideDown(300, function()
    {
        setTimeout(function()
        {
            var url = core_system_url + '/' + jrComment_url + '/view_comments/item_module=' + jrE(module) + '/item_id=' + Number(item_id) + '/p=' + Number(next_page) + '/__ajax=1';
            $.get(url, function(res)
            {
                $('#cpholder' + this_page).remove();
                $('.comment_page_section').append(res);
            });
        }, 200);
    });
    return false;
}

/**
 * Reply to a comment (threaded)
 * @param id int Item ID
 * @param un string User Name
 */
function jrComment_reply_to(id, un)
{
    if (typeof tinyMCE != "undefined" && tinyMCE.get('comment_text') != "undefined") {
        $('#comment_reply_to_user').text(un);
        $('#comment_reply_to').show();
        $('#comment_parent_id').val(id);
        var h = $('#header').outerHeight();
        $('html, body').animate({scrollTop: $('#cform').offset().top - h}, 200);
        tinyMCE.execCommand('mceFocus', true, 'comment_text');
    }
    else {
        var rid = '#r' + id;
        if ($(rid).is(':visible')) {
            // Already showing - remove
            $(rid).slideUp(100).empty();
            $('#comment_form_section').slideDown(300);
        }
        else {
            var i = '#comment_form_section';
            var ht = $(i).html();
            $(ht).appendTo('#r' + id);
            $(i).slideUp(100);
            $(rid).slideDown(300);
            $('#comment_text').focus();
            $('#comment_parent_id').val(id);
        }
    }
    return false;
}

/**
 * Grab post data for new post
 * @param id int
 */
function jrCommentQuotePost(id)
{
    var u = core_system_url + '/' + jrComment_url + '/quote/' + Number(id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.ajax({
        type: 'GET',
        url: u,
        cache: false,
        dataType: 'html',
        success: function(r)
        {
            var i = $('#comment_text');
            var c = ((r.match(/\n/g) || []).length * 15) + 20;
            var l = i.height() + 30;
            if (c < l) {
                c = l;
            }
            i.css('height', c + 'px').val(r);
            $('html, body').animate({scrollTop: i.offset().top}, 'fast');
            i.selectRange(r.length);
        }
    });
}

/**
 * Grab post data for new post (into editor)
 * @param id
 */
function jrCommentEditorQuotePost(id)
{
    var u = core_system_url + '/' + jrComment_url + '/quote/' + Number(id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.ajax({
        type: 'GET',
        url: u,
        cache: false,
        dataType: 'html',
        success: function(r)
        {
            tinymce.activeEditor.selection.setContent(r);
            tinymce.activeEditor.focus();
            $('html, body').animate({
                scrollTop: $("#cform").offset().top
            }, 100);
        }
    });
    return false;
}
