/**
 * Get widget module info
 * @param i
 */
function jrSeamless_widget_list_get_module_info(i)
{
    var modules = $(i).val();
    var url = core_system_url + '/' + jrSeamless_url + '/widget_list_get_module_info/m=' + jrE(modules) + '/__ajax=1';
    $.get(url, function(resp)
    {
        $('#ff-row-list_custom_template').hide();
        $('#list_pagebreak').removeClass('form_element_disabled').removeAttr('disabled');
        $('#list_limit').removeClass('form_element_disabled').removeAttr('disabled');
        $('.list_search_op').removeClass('form_element_disabled').removeAttr('disabled');
        $('.list_search_text').removeClass('form_element_disabled').removeAttr('disabled');
        $('.list_search_key').each(function()
        {
            $(this).empty().removeClass('form_element_disabled').removeAttr('disabled');
            for (i = 0; i < resp.length; ++i) {
                $(this).append($('<option></option>').attr('value', resp[i]).text(' ' + resp[i]));
            }
        });
        $('#list_order_by_dir').removeClass('form_element_disabled').removeAttr('disabled');
        var id = $('#list_order_by_key');
        id.empty().removeClass('form_element_disabled').removeAttr('disabled');
        for (i = 0; i < resp.length; ++i) {
            id.append($('<option></option>').attr('value', resp[i]).text(' ' + resp[i]));
        }
        id = $('#list_template');
        id.removeClass('form_element_disabled').removeAttr('disabled');
    });
}


/**
 * Load the default module template code into the custom template editor.
 */
function jrSeamless_load_default_code()
{
    var url = core_system_url + '/' + jrSeamless_url + '/default_tpl/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.get(url, function(r)
    {
        var cm = $('.CodeMirror')[0].CodeMirror;
        var ln = cm.getValue();
        if (ln.length > 0) {
            if (confirm('Reload the template content with the default template?')) {
                cm.setValue(r.code);
            }
        }
        else {
            cm.setValue(r.code);
        }
    });
}