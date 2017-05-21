// Jamroom 5 jrGroupMailer module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Paul Asher - paul@jamroom.net

/**
 * Save an email
 */
function jrGroupMailer_save(gid)
{
    var s = $('#groupmailer_title');
    if (s.val().length == 0) {
        s.addClass('field-hilight');
    }
    else {
        s.removeClass('field-hilight');
        $('#save_indicator').show(300, function()
        {
            setTimeout(function()
            {
                $('.form_editor').each(function()
                {
                    if (tinymce.EditorManager.activeEditor !== 'undefined') {
                        $('#' + this.name + '_editor_contents').val(tinymce.EditorManager.activeEditor.getContent());
                    }
                });
                var v = $('#jrGroupMailer_compose').serializeArray();
                var u = core_system_url + '/' + jrGroupMailer_url + '/save_draft/__ajax=1';
                jrCore_set_csrf_cookie(u);
                $.ajax({
                    url: u,
                    type: 'POST',
                    cache: false,
                    data: v,
                    dataType: 'json',
                    success: function(r)
                    {
                        $('#save_indicator').hide(150, function()
                        {
                            if (typeof r.error !== "undefined") {
                                alert(r.error);
                            }
                            else {
                                if (window.location.href.indexOf('/draft=') == -1) {
                                    window.location = core_system_url + '/' + jrGroupMailer_url + '/compose' + gid + '/draft=' + Number(r.draft_id);
                                }
                            }
                            return true;
                        });
                    },
                    error: function()
                    {
                        $('#save_indicator').hide(150, function()
                        {
                            alert('unable to communicate with server - please try again');
                            return true;
                        });
                    }
                });
            }, 500);
        });
    }
}

function jrGroupMailer_compose_new()
{
    window.location = core_system_url + '/' + jrGroupMailer_url + '/compose';
}

/**
 * Check that we have a template before saving
 */
function jrGroupMailer_check_template()
{
    $('.form_editor').each(function()
    {
        if (tinymce.EditorManager.activeEditor !== 'undefined') {
            $('#' + this.name + '_editor_contents').val(tinymce.EditorManager.activeEditor.getContent());
        }
    });
    if ($('#groupmailer_message_editor_contents').val().length > 1) {
        $('#save-as-template').modal();
    }
    else {
        alert('There is no Group Email Content to save as a Template');
    }
}

/**
 * Save updates to a Template
 */
function jrGroupMailer_save_template()
{
    $('#save_indicator').show(300, function()
    {
        setTimeout(function()
        {
            $('.form_editor').each(function()
            {
                if (tinymce.EditorManager.activeEditor !== 'undefined') {
                    $('#' + this.name + '_editor_contents').val(tinymce.EditorManager.activeEditor.getContent());
                }
            });
            var v = $('#jrGroupMailer_compose').serializeArray();
            var u = core_system_url + '/' + jrGroupMailer_url + '/save_template_update/__ajax=1';
            jrCore_set_csrf_cookie(u);
            $.ajax({
                url: u,
                type: 'POST',
                cache: false,
                data: v,
                dataType: 'json',
                success: function(r)
                {
                    $('#save_indicator').hide(150, function()
                    {
                        if (typeof r.error !== "undefined") {
                            alert(r.error);
                        }
                        return true;
                    });
                },
                error: function()
                {
                    $('#save_indicator').hide(150, function()
                    {
                        alert('unable to communicate with server - please try again');
                        return true;
                    });
                }
            });
        }, 500);
    });
}

/**
 * Save an email as a template
 */
function jrGroupMailer_save_as_template()
{
    $('#template_title').val($('#template-title').val() );
    $('.form_editor').each(function()
    {
        if (tinymce.EditorManager.activeEditor !== 'undefined') {
            $('#' + this.name + '_editor_contents').val(tinymce.EditorManager.activeEditor.getContent());
        }
    });
    var v = $('#jrGroupMailer_compose').serializeArray();
    var u = core_system_url + '/' + jrGroupMailer_url + '/save_template/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.ajax({
        url: u,
        type: 'POST',
        cache: false,
        data: v,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                $('#template-error').text(r.error).show();
            }
            else {
                window.location = core_system_url + '/' + jrGroupMailer_url + '/edit_email_template/id=' + Number(r.tid);
            }
        },
        error: function()
        {
            alert('unable to communicate with server - please try again');
            return true;
        }
    });
    $('#save-as-template').modal();

}
