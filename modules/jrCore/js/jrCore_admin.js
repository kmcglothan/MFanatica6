// Jamroom Core Javascript - admin functions
// @copyright 2003-2017 by Talldude Networks LLC

var __jrcto = null;

/**
 * Set a page reload timeout on the Dashboard
 * @param {number} seconds
 * @param {number} auto
 * @param {function} f
 */
function jrCore_dashboard_reload_page(seconds, auto, f)
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
            if (typeof f == "function") {
                return f();
            }
            else {
                window.location.reload();
            }
        }
        $(id).val(v);
    }, 1000);
}

/**
 * disable dashboard reload
 * @param {number} s seconds to reload
 * @returns {boolean}
 */
function jrCore_dashboard_disable_reload(s)
{
    jrSetCookie('dash_reload', 'off');
    clearInterval(__jrcto);
    $('#reload').val(s).addClass('form_button_disabled');
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
    $('.form_button').attr('disabled', 'disabled').addClass('form_button_disabled');
    $.get(url, function(r)
    {
        $('.form_button').removeAttr('disabled').removeClass('form_button_disabled');
        if (r.msg == 'ok') {
            $('#d' + id).parent().parent('tr').remove();
        }
        else {
            jrCore_alert(r.msg)
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
    jrCore_confirm('Delete this Item?', 'The item will be permanently deleted from the system', function()
    {
        var url = core_system_url + '/' + jrCore_url + '/recycle_bin_delete/id=' + id + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $('.form_button').attr('disabled', 'disabled').addClass('form_button_disabled');
        $.get(url, function(r)
        {
            $('.form_button').removeAttr('disabled').removeClass('form_button_disabled');
            if (r.msg == 'ok') {
                $('#d' + id).parent().parent('tr').remove();
            }
            else {
                jrCore_alert(r.msg)
            }
        });
    });
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
    $('.form_button').attr('disabled', 'disabled').addClass('form_button_disabled');
    $.get(url, function()
    {
        if (id.indexOf(',') > -1) {
            window.location.reload();
        }
        else {
            $('#d' + id).parent().parent('tr').remove();
            if ($('.tk_checkbox').length === 0) {
                window.location.reload();
            }
            else {
                $('.form_button').removeAttr('disabled').removeClass('form_button_disabled');
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
    $.post(url, function(r)
    {
        if (typeof r.error !== "undefined") {
            jrCore_alert(r.error);
        }
        else {
            $.modal.close();
            window.location.reload();
        }
    });
}

/**
 * Get widget module info
 * @param i
 */
function jrCore_widget_list_get_module_info(i)
{
    var m = $(i).val();
    var a = $('#active_module');
    if (m !== a.val()) {
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