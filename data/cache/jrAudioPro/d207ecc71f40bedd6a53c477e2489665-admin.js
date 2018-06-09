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
// Jamroom Mail Core Javascript
// @copyright 2003-2016 by Talldude Networks LLC

/**
 * Get a User report
 * @param uid int User ID
 * @param cid int Campaign ID
 */
function jrMailer_user_report(uid, cid)
{
    var b = $('#top-users-box');
    $('#top-users-holder').html(b.html());
    var url = core_system_url + '/' + jrMailer_url + '/user_report/' + Number(uid) + '/' + Number(cid) + '/__ajax=1';
    b.load(url);
}

/**
 * Load top users box
 */
function jrMailer_top_users()
{
    $('#top-users-box').html($('#top-users-holder').html());
}

/**
 * Resize the campaign viewer
 */
function jrMailer_cp_resize()
{
    var s = $('#cp-display-area');
    var h = ($('body').outerHeight() - s.offset().top - $('#footer').outerHeight() - $('.form_submit_section').outerHeight());
    s.height(h);
}

/**
 * Init campaign view
 */
function jrMailer_cp_init()
{
    jrMailer_cp_resize();
    window.onresize = function()
    {
        jrMailer_cp_resize();
    }
}

// Jamroom User Module Admin Javascript
// @copyright 2003-2018 by Talldude Networks LLC

/**
 * Show delete user modal
 * @param u number User ID
 * @param p number Profile ID
 */
function jrUser_delete_user(u, p)
{
    $('#user-delete-active-user-id').text(u);
    $('#user-delete-active-profile-id').text(p);
    $('#modal_window').modal();
}

/**
 * Delete a user
 */
function jrUser_delete_user_from_modal()
{
    var i = $('#user-delete-active-user-id').text();
    var u = core_system_url + '/' + jrUser_url + '/delete_save/id=' + Number(i);
    jrCore_set_csrf_cookie(u);
    jrCore_window_location(u);
}

/**
 * Delete a profile
 */
function jrUser_delete_profile_from_modal()
{
    var i = $('#user-delete-active-profile-id').text();
    var u = core_system_url + '/' + jrProfile_url + '/delete_save/id=' + Number(i);
    jrCore_set_csrf_cookie(u);
    jrCore_window_location(u);
}
/**
 * jrSiteBuilder admin Javascript functions
 * @copyright 2015 Talldude Networks, LLC.
 */

/**
 * Show Closing Site Builder message and reload
 */
function jrSiteBuilder_modal_reload()
{
    $('body').append('<div id="rn" class="item p20 center" style="width:400px">Closing Site Builder session and refreshing page...</div>');
    $('#rn').modal();
    window.location.reload();
    window.name = '';
}

/**
 * Make Close button show "View Changes"
 */
function jrSiteBuilder_changes_made()
{
    $('#sb-close-button').addClass('success').text('View Changes');
}

/**
 * Close the menu editor
 * @returns {boolean}
 */
function jrSiteBuilder_close_menu_modal()
{
    // Did we make any changes?
    jrEraseCookie('sb-active-menu-entry');
    $('#sb-edit-cp').fadeOut(100, function()
    {
        $.modal.close();
        if (window.name === 'reload') {
            jrSiteBuilder_modal_reload();
        }
    });
    return true;
}

function jrSiteBuilder_modify_menu_saved(r)
{
    var m = $('#jrSiteBuilder_modify_menu_options_msg');
    var i = $('#jrSiteBuilder_modify_menu_options_submit').prev('#form_submit_indicator');
    m.html('The changes were successfully saved').addClass('success');
    i.hide(300, function()
    {
        m.slideDown(150, function()
        {
            $('.form_submit_section input').removeAttr("disabled").removeClass('form_button_disabled');
        });
        if (typeof r.show_changed !== "undefined") {
            window.name = 'reload';
        }
    });
}

/**
 * Close the edit container modal window
 */
function jrSiteBuilder_close_container_modal(r)
{
    $('#sb-edit-cp').fadeOut(100, function()
    {
        if (typeof r.location !== "undefined") {
            $('#sb-widget-col-' + r.location + ' .sb-mod-container-btn .sprite_icon_20_gear').addClass('success');
            jrSiteBuilder_changes_made();
        }
        $.modal.close();
    });
}

/**
 * Close the edit widget modal window
 */
function jrSiteBuilder_close_widget_modal(r)
{
    $('#sb-edit-cp').fadeOut(100, function()
    {
        if (typeof r.widget_id !== "undefined") {
            if (r.title.length > 0) {
                $('#c' + r.widget_id + ' .title').addClass('success').html('<h2>' + r.title + '</h2>');
            }
            else {
                $('#c' + r.widget_id + ' .title').addClass('success').html('<h2 class="sb-widget-type-info">' + r.name + '</h2>');
            }
            jrSiteBuilder_changes_made();
        }
        if (tinymce.EditorManager.activeEditor !== 'undefined') {
            tinymce.EditorManager.execCommand('mceRemoveEditor', false, 'ehtml_content');
        }
        $.modal.close();
    });
}

/**
 * Close the edit page modal window
 */
function jrSiteBuilder_close_page_modal(r)
{
    $('#sb-edit-cp').fadeOut(100, function()
    {
        if (typeof r.show_changed !== "undefined") {
            jrSiteBuilder_changes_made();
        }
        $.modal.close();
    });
}

/**
 * Create a new Menu Entry in SB
 * @param id object This
 * @returns {boolean}
 */
function jrSiteBuilder_create_menu_entry(id)
{
    var t = $(id).val();
    if (t.length === 0) {
        return true;
    }
    var u = core_system_url + '/' + jrSiteBuilder_url + '/create_menu_option_save/t=' + jrE(t.replace(new RegExp('/', 'g'), '~')) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.get(u, function(r)
    {
        // Reset new entry
        var l = $('#list_new');
        $(l).hide();
        $('#sb-new-entry').val('');

        // Clone and add
        var c = $('#sb-new-entry-template').clone().html();
        c = c.split('MENU_ID').join(r.id).split('MENU_TITLE').join(r.ttl);
        $(l).before(c);

        var s = $('#sb-menu-options-form');
        $(s).fadeOut(100, function()
        {
            u = core_system_url + '/' + jrSiteBuilder_url + '/modify_menu_options/id=' + Number(r.id) + '/__ajax=1';
            jrCore_set_csrf_cookie(u);
            $(s).load(u, function()
            {
                s.fadeIn(100);
                window.name = 'reload';
            });
        });

    });
}

/**
 * Delete an existing menu item
 * @param id
 */
function jrSiteBuilder_delete_menu_entry(id)
{
    var u = core_system_url + '/' + jrSiteBuilder_url + '/delete_menu_entry_save/id=' + Number(id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.get(u, function()
    {
        $('#list_' + id).remove();
        window.name = 'reload';
    });
}

/**
 * Display Menu options for a selected menu item
 * @param id
 * @returns {boolean}
 */
function jrSiteBuilder_get_menu_options(id)
{
    var c = jrReadCookie('sb-active-menu-entry');
    if (c === id) {
        return true;
    }
    jrSetCookie('sb-active-menu-entry', id, 1);
    var s = $('#sb-menu-options-form');
    s.fadeOut(100, function()
    {
        var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_menu_options/id=' + Number(id) + '/__ajax=1';
        s.load(u, function()
        {
            s.fadeIn(100);
        });
    })
}

/**
 * When a user changes the title of a menu item, update to reflect change
 * @returns {boolean}
 */
function jrSiteBuilder_modify_title_sync()
{
    var i = $('#menu_id').val();
    var v = $('#menu_title').val();
    $('#t' + i).html(v);
    return true;
}

/**
 * Edit Site menu
 */
function jrSiteBuilder_edit_menu()
{
    jrEraseCookie('sb-active-menu-entry');
    var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_menu/__ajax=1';
    var s = $('#sb-edit-cp');
    s.load(u, function()
    {
        jrSiteBuilder_set_editor_height();
        s.modal();
    });
}

function jrSiteBuilder_close()
{
    jrEraseCookie('sb-active-menu-entry');
    jrEraseCookie('sb-page-layout-reload');
    jrEraseCookie('sb-active');
    $('.connectedSortable').sbPanelSortable('destroy');
    jrSiteBuilder_modal_reload();
}

/**
 * Set the editor height in pixels
 * @returns {boolean}
 */
function jrSiteBuilder_set_editor_height()
{
    var h = $('html').height();
    if (h > 900) {
        h = 900;
    }
    if (h < 600) {
        h = 600;
    }
    $('#sb-edit-cp').height(h - 100);
    $('#sb-widget-work').height(h - 100);
    $('.sb-accordion').height(h - 160);
    return true;
}

/**
 * Edit Page Layout
 */
function jrSiteBuilder_edit_layout(id)
{
    var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_page/id=' + Number(id) + '/__ajax=1';
    var s = $('#sb-edit-cp');
    s.load(u, function()
    {
        jrSiteBuilder_set_editor_height();
        s.modal();
    });
}

/**
 * Create a new Page THEN start the edit process on a page
 */
function jrSiteBuilder_create_and_edit_page()
{
    var p = window.location.href.toString();
    var u = core_system_url + '/' + jrSiteBuilder_url + '/create_page_save/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.post(u, {page_url: p}, function(r)
    {
        window.location.reload();
    });
}

/**
 * Start the edit process on a page
 * @param id int Page ID
 */
function jrSiteBuilder_edit_page(id)
{
    $('.sb-widget-col').css('position', 'relative');
    $('#sb-close-button').show();
    $('#sb-edit-button').hide();
    $('#sb-empty-notice').hide();
    $('.sb-widget-title').show();
    $('#sb-layout-section').show();
    $('#page_container').addClass('sb-editing_active');
    $('.sb-mod-container-btn').show();
    $('.sb-container-tabs').hide();
    $('.sb-drag-handle').show();
    $('.connectedSortable').removeAttr('style');
    jrSiteBuilder_hilight_widget_containers(id);
}

/**
 * Hilight the Widget containers on a page
 * @param id int Page ID
 */
function jrSiteBuilder_hilight_widget_containers(id)
{
    $('.sb-widget-content').hide();
    $('.sb-widget-col').addClass('sb-widget-hilight');
    $('.sb-widget-block').addClass('sb-widget-block-edit');
    $('.sb-widget-controls').show();

    // add new widget system
    var n = $('.sb-add-widget-btn');
    n.show();
    n.unbind('click').on('click', function()
    {
        var c = this;
        var s = $(c).siblings('.connectedSortable');
        var ct = s.find('li').length;
        var l = $(c).parent().data('location');
        var url = core_system_url + '/' + jrSiteBuilder_url + '/widget_create/page_id=' + id + '/ct=' + ct + '/location=' + l + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {},
            cache: false,
            dataType: 'json',
            success: function(d)
            {
                s.append(d.widget_html);
                $('.connectedSortable').sbPanelSortable('destroy');
                jrSiteBuilder_modify_widget('widget_id-' + d.widget_id);
                jrSiteBuilder_enable_widget_drag(id);
            }
        });
    });
    jrSiteBuilder_enable_widget_drag(id);
}

/**
 * Enable drag and drop for widget layout
 * @param id int Page ID
 */
function jrSiteBuilder_enable_widget_drag(id)
{
    $('.connectedSortable').sbPanelSortable({
        connectWith: ".connectedSortable"
    }).bind('sortupdate', function(event, ui)
    {
        var l = event.target.id;

        // Triggered when the user stopped sorting and the DOM position has changed.
        var o = $('#' + l + ' > li').map(function()
        {
            return $(this).data("id");
        }).get();

        var url = core_system_url + '/' + jrSiteBuilder_url + '/widget_order_update/' + l + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                widget_order: o
            },
            cache: false,
            dataType: 'json',
            success: function()
            {
                jrSiteBuilder_changes_made();
            }
        });
    });
}

/**
 * Enable drag reorder support for page layout
 * @param id int Page ID
 */
function jrSiteBuilder_enable_layout_drag(id)
{
    $('.sortable').sortable().bind('sortupdate', function(e, u)
    {
        var o = $('ul.sortable li').map(function()
        {
            return $(this).data('id');
        }).get();
        var url = core_system_url + '/' + jrSiteBuilder_url + "/panel_order_update/id=" + Number(id) + "/__ajax=1";
        jrCore_set_csrf_cookie(url);
        $.post(url, {panel_order: o}, function()
        {
            $('#save_button').removeClass('form_button_disabled').removeAttr('disabled');
        });
    });
}

/**
 * Delete an existing page
 * @param page_id
 */
function jrSiteBuilder_delete_page(page_id)
{
    var u = core_system_url + '/' + jrSiteBuilder_url + '/delete_page_save/id=' + Number(page_id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.get(u, function()
    {
        window.location = core_system_url;
    });
}

/**
 * Delete an existing page from the '/browse' screen
 * @param page_id
 */
function jrSiteBuilder_browse_delete_page(page_id)
{
    var u = core_system_url + '/' + jrSiteBuilder_url + '/delete_page_save/id=' + Number(page_id) + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.get(u, function()
    {
        $('#d' + page_id).closest('tr').fadeOut()
    });
}

/**
 * Modify page settings
 * @param i
 */
function jrSiteBuilder_modify_page_settings(i)
{
    var w = $('#sb-page-work');
    w.fadeOut(100, function()
    {
        var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_page_settings/id=' + i + '/__ajax=1';
        w.load(u, function()
        {
            w.fadeIn(100);
        });
    });
}

/**
 * Modify Page Layout
 * @param id
 */
function jrSiteBuilder_modify_page_layout(id)
{
    var w = $('#sb-page-work');
    w.fadeOut(100, function()
    {
        var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_page/id=' + Number(id) + '/__ajax=1';
        w.load(u, function()
        {
            w.fadeIn(100);
        });
    });
}

/**
 * Modify widget settings
 * @param i
 */
function jrSiteBuilder_modify_widget_settings(i)
{
    $('#sb-widget-work').fadeOut(100, function()
    {
        var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_widget_settings/id=' + i + '/__ajax=1';
        jrSiteBuilder_set_editor_height();
        $('#sb-widget-settings').load(u).fadeIn(100);
    });
}

/**
 * Close the settings panel
 */
function jrSiteBuilder_close_widget_settings(r)
{
    $('#jrSiteBuilder_modify_widget_form_submit').removeClass('form_button_disabled').removeAttr('disabled');
    var s = $('#sb-widget-settings');
    s.fadeOut(100, function()
    {
        s.html('');
        if (typeof r !== "undefined" && typeof r.widget_id !== "undefined") {
            $('#c' + r.widget_id + ' .title').addClass('success');
            jrSiteBuilder_changes_made();
        }
        $('#sb-widget-work').fadeIn(100);
    });
}

/**
 * Modify widget content
 * @param i
 */
function jrSiteBuilder_modify_widget_content(i)
{
    var w = $('#sb-widget-work');
    w.fadeOut(100, function()
    {
        var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_widget_form/html_id=' + i + '/__ajax=1';
        w.load(u, function()
        {
            w.fadeIn(100);
        });
    });
}

/**
 * Edit a page widget
 */
function jrSiteBuilder_modify_widget(i)
{
    var s = $('#sb-edit-cp');
    var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_widget/html_id=' + i + '/__ajax=1';
    s.load(u, function()
    {
        var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_widget_form/html_id=' + i + '/__ajax=1';
        $('#sb-widget-work').load(u, function()
        {
            jrSiteBuilder_set_editor_height();
            s.modal();
            if (tinymce.EditorManager.activeEditor !== 'undefined') {
                tinymce.settings = ehtml_content;
                tinymce.EditorManager.execCommand('mceAddEditor', false, 'ehtml_content');
            }
            var cm;
            if ($('#code_content').length > 0) {
                cm = CodeMirror.fromTextArea(document.getElementById("code_content"), {
                    lineNumbers: true,
                    matchBrackets: true,
                    mode: 'smarty'
                });
                cm.setSize(850, 500);
                cm.on("blur", function()
                {
                    cm.save()
                });
            }
            else if ($('#list_custom_template').length > 0) {
                cm = CodeMirror.fromTextArea(document.getElementById('list_custom_template'), {
                    lineNumbers: true,
                    matchBrackets: true,
                    mode: 'smarty'
                });
                cm.setSize(600, 120);
                cm.on("blur", function()
                {
                    cm.save()
                });
            }
        });
    });
}

/**
 * Clone a Page Widget
 */
function jrSiteBuilder_clone_widget(i)
{
    var url = core_system_url + '/' + jrSiteBuilder_url + '/widget_clone/widget_id=' + i + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {},
        cache: false,
        dataType: 'json',
        success: function(d)
        {
            if (typeof d.error !== "undefined") {
                jrCore_alert(d.error);
            }
            else {
                $('#w' + i).after(d.widget_html);
                $('.connectedSortable').sbPanelSortable('destroy');
                jrSiteBuilder_modify_widget('widget_id-' + d.widget_id);
                jrSiteBuilder_enable_widget_drag(d.page_id);
            }
        }
    });
}

/**
 * Delete a Page Widget
 */
function jrSiteBuilder_delete_widget(i)
{
    jrCore_confirm('Delete This Widget?', 'Are you sure you want to delete this widget?', function()
    {
        var d = $('#' + i);
        var w = d.data('id');
        var u = core_system_url + '/' + jrSiteBuilder_url + '/delete_widget_save/html_id=' + i + '/__ajax=1';
        jrCore_set_csrf_cookie(u);
        $.get(u, function()
        {
            $('#w' + w).remove();
            jrSiteBuilder_changes_made();
        });
    });
}

/**
 * Show the Modify Widget form for a widget type
 */
function jrSiteBuilder_widget_form(id, m, n)
{
    var c = $('#' + m + '-' + n);
    if (c.hasClass('sb-item-row-active')) {
        return false;
    }
    var t = $('#widget_title').val();

    var a = false;
    var e = false;
    switch (m + '-' + n) {
        case 'jrSiteBuilder-widget_html':
            a = true;
            break;
        default:
            if (tinymce.EditorManager.activeEditor !== 'undefined') {
                e = true;
            }
            break;
    }
    $('.sb-item-row').removeClass('sb-item-row-active');
    c.addClass('sb-item-row-active');
    var l = $('#sb-widget-settings');
    if (l.html().length > 0) {
        l.html('');
    }
    var s = $('#sb-widget-work');
    s.fadeOut(100, function()
    {
        if (e) {
            tinymce.EditorManager.execCommand('mceRemoveEditor', false, 'ehtml_content');
        }
        var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_widget_form/html_id=' + id + '/m=' + m + '/n=' + n + '/__ajax=1';
        s.load(u, function()
        {
            $('#widget_module').val(m);
            $('#widget_name').val(n);
            $('#widget_title').val(t);
            s.fadeIn(100);
            // Are we adding the editor?
            if (a) {
                tinymce.settings = ehtml_content;
                tinymce.EditorManager.execCommand('mceAddEditor', false, 'ehtml_content');
                if (tinymce.EditorManager.activeEditor !== null) {
                    tinymce.EditorManager.activeEditor.theme.resizeTo('100%', 360);
                }
            }
            var cm;
            if ($('#code_content').length > 0) { // add code view.
                cm = CodeMirror.fromTextArea(document.getElementById("code_content"), {
                    lineNumbers: true,
                    matchBrackets: true,
                    mode: 'smarty'
                });
                cm.setSize(850, 500);
                cm.on("blur", function()
                {
                    cm.save()
                });
            }
            else if ($('#list_custom_template').length > 0) {
                cm = CodeMirror.fromTextArea(document.getElementById('list_custom_template'), {
                    lineNumbers: true,
                    matchBrackets: true,
                    mode: 'smarty'
                });
                cm.setSize(600, 120);
                cm.on("blur", function()
                {
                    cm.save()
                });
            }
        });
    });
}

/**
 * Edit a page container
 */
function jrSiteBuilder_modify_container(i)
{
    var s = $('#sb-edit-cp');
    var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_container/html_id=' + i + '/__ajax=1';
    s.load(u, function()
    {
        jrSiteBuilder_set_editor_height();
        s.modal();
    });
}

/**
 * Save a new column layout to the page
 * @returns {boolean}
 */
function jrSiteBuilder_save_layout_row(id)
{
    var l = $('#layout-row');
    var t = $('#row-template');
    var c = $('#page_row_count');
    var v1 = parseInt(l.find('.first-col div').html());
    var v2 = parseInt(l.find('.second-col div').html());
    var v3 = parseInt(l.find('.third-col div').html());
    var i = Number(c.val());
    t.children('li').attr('data-id', i);
    c.val(i + 1);
    var s = t.clone();

    s.find('.first-col').addClass('col' + v1);
    s.find('.first-col div').html(v1);
    s.find('.second-col').addClass('col' + v2);
    s.find('.second-col div').html(v2);
    s.find('.third-col').addClass('col' + v3);
    s.find('.third-col div').html(v3);
    s.find('input').val(v1 + '-' + v2 + '-' + v3);

    $('ul.sortable').append(s.html());
    $('#save_button').removeClass('form_button_disabled').removeAttr('disabled');
    return true;
}

/**
 * Delete a row in the Site Builder panel layout
 * @param row
 * @returns {boolean}
 */
function jrSiteBuilder_delete_layout_row(row)
{
    $(row).closest('.row').remove();
    $('#save_button').removeClass('form_button_disabled').removeAttr('disabled');
    return true;
}

/**
 * Set boxes in Site Builder panel layout
 */
function jrSiteBuilder_set_boxes(t, u)
{
    var l = parseInt(u[0]);
    var r = 12 - parseInt(u[1]);
    var m = 12 - r - l;
    var d = $('#layout-row');
    var b = '<div class="new-cell">';
    var e = '</div>';

    d.find('.first-col').removeClass(function(i, c)
    {
        return (c.match(/\bcol\S+/g) || []).join(' ');
    }).addClass('col' + l).html(b + l + e);

    d.find(".second-col").removeClass(function(i, c)
    {
        return (c.match(/\bcol\S+/g) || []).join(' ');
    }).addClass('col' + m).html(b + m + e);

    d.find(".third-col").removeClass(function(i, c)
    {
        return (c.match(/\bcol\S+/g) || []).join(' ');
    }).addClass('col' + r).html(b + r + e);
}

/**
 * Update and save a new page layout in panel layout
 * @param pid int Page ID
 */
function jrSiteBuilder_save_page_layout(pid)
{
    var id = $('#panel_update_form');
    var s = $('#lfsi');
    s.show(300, function()
    {
        setTimeout(function()
        {
            var d = id.serializeArray();
            $('ul.sortable li').map(function()
            {
                return $(this).data('id');
            }).get();
            var u = core_system_url + '/' + jrSiteBuilder_url + '/modify_page_save/id=' + pid + '/__ajax=1';
            jrCore_set_csrf_cookie(u);
            $.ajax({
                type: 'POST',
                url: u,
                data: d,
                cache: false,
                dataType: 'json',
                success: function(r)
                {
                    if (typeof r.error !== "undefined") {
                        jrCore_alert(r.error);
                        $('.save-row-button').addClass('highlight');
                    }
                    else {
                        $('#sb-edit-cp').fadeOut(100, function()
                        {
                            $.modal.close();
                            jrSiteBuilder_modal_reload();
                        });
                    }
                    return true;
                }
            });
        }, 500);
    });
}

/**
 * Close the modal window.
 */
function jrSiteBuilder_modal_close()
{
    if (tinymce.EditorManager.activeEditor !== 'undefined') {
        tinymce.EditorManager.execCommand('mceRemoveEditor', false, 'ehtml_content');
    }
    $.modal.close();
}

/**
 * Load the default module template code into the custom template editor.
 */
function jrSiteBuilder_load_default_code()
{
    var mod = $('#list_module').val();
    var url = core_system_url + '/' + jrSiteBuilder_url + '/default_tpl/m=' + mod + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.get(url, function(r)
    {
        var cm = $('.CodeMirror')[0].CodeMirror;
        var ln = cm.getValue();
        if (ln.length > 0) {
            jrCore_confirm('Reload Template Content?', 'Reload the template content with the default template?', function()
            {
                cm.setValue(r.code);
            });
        }
        else {
            cm.setValue(r.code);
        }
    });
}
/**
 * Load the default module template code into the custom template editor.
 */
function jrSiteBuilder_activate_editor()
{
    if ($('.CodeMirror').length === 0) {
        var cm = CodeMirror.fromTextArea(document.getElementById("list_custom_template"), {
            mode: "smarty",
            lineNumbers: true,
            smartyVersion: 3
        });
        cm.setSize(600, 120);
        cm.on('change', function()
        {
            var html = cm.getValue();
            $('#list_custom_template').html(html);
        });
        $('#jrSiteBuilder_activate_editor').remove();
    }
}

/**
 * in the TEMPLATE_BUILDER Save the code in the CODE box, then load that into preview box.
 */
function jrSiteBuilder_preview_template()
{
    $('#messages').html('').removeClass('success error');
    var module = $('#module').val();
    // first save the preview template
    var url = core_system_url + '/' + jrSiteBuilder_url + '/save_template/__ajax=1';
    var html = cm.getValue();
    $('#save_form_submit_indicator').show(300, function()
    {
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                html: html,
                filename: 'preview',
                mod: module
            },
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrCore_alert(r.error);
                }
                else {
                    //window.location.reload();
                }
                return true;
            }
        });

        // show preview
        url = core_system_url + '/' + jrSiteBuilder_url + '/preview_template/' + module + '/tpl=preview.tpl/__ajax=1';
        $('#preview').load(url);
        $('#save_form_submit_indicator').hide(300);
    });
}

/**
 * in the TEMPLATE_BUILDER load the code from the text area into a preview box.
 */
function jrSiteBuilder_save_template()
{
    var filename = $('#description').val();
    var module = $('#module').val();
    $('#messages').html('').removeClass('success error');
    if (typeof filename === "undefined" || filename === null || filename.length <= 1) {
        $('#messages').html('no file name set, or name too short.').addClass('error');
        return false;
    }
    var url = core_system_url + '/' + jrSiteBuilder_url + '/save_template/__ajax=1';
    var html = cm.getValue();
    jrCore_set_csrf_cookie(url);
    $('#save_form_submit_indicator').show(300, function()
    {
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                html: html,
                filename: filename,
                mod: module
            },
            cache: false,
            dataType: 'json',
            success: function(r)
            {
                if (typeof r.error !== "undefined") {
                    jrCore_alert(r.error);
                }
                else {
                    $('#messages').html(r.msg).addClass('success');
                    $('#save_form_submit_indicator').hide(300);
                }
                return true;
            }
        });
    });
}

/**
 * reset the menu to the skins default menu
 */
function jrSiteBuilder_reset_menu()
{
    jrCore_confirm('Reset the Menu?', 'Are you sure you want to reset the menu to the default state?', function()
    {
        var url = core_system_url + '/' + jrSiteBuilder_url + '/reset_menu/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $('#reset_menu_submit_indicator').show(300, function()
        {
            $.ajax({
                type: 'POST',
                url: url,
                cache: false,
                dataType: 'json',
                success: function(r)
                {
                    if (typeof r.error !== "undefined") {
                        jrCore_alert(r.error);
                    }
                    else {
                        window.location.reload();
                    }
                    return true;
                }
            });
        });
    });
}

/**
 * show the .tpl code to build the current menu structure
 */
function jrSiteBuilder_menu_code()
{
    var s = $('#sb-menu-options-form');
    $(s).fadeOut(100, function()
    {
        u = core_system_url + '/' + jrSiteBuilder_url + '/menu_code/__ajax=1';
        jrCore_set_csrf_cookie(u);
        $(s).load(u, function()
        {
            s.fadeIn(100);
            window.name = 'reload';
        });
    });
}

/**
 * Save the current page content to the active skin as the default for this skin
 */
function jrSiteBuilder_save_page_as_json(id)
{
    var url = core_system_url + '/' + jrSiteBuilder_url + '/json_package_page/' + id + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $('#sb-json-spinner').show(300, function()
    {
        setTimeout(function()
        {
            $.ajax({
                type: 'POST',
                url: url,
                data: {},
                cache: false,
                dataType: 'json',
                success: function(r)
                {
                    $('#sb-json-spinner').hide(300);
                    if (typeof r.error !== "undefined") {
                        $('#sb-json-message').html(r.msg).removeClass('success').addClass('error').fadeIn(300);
                    }
                    else {
                        $('#sb-json-message').html(r.msg).addClass('success').fadeIn(300);
                    }
                    return true;
                }
            });
        }, 300);
    });
}


/*
 * ( originally from )
 * HTML5 Sortable jQuery Plugin for the Widgets/Panels system
 * https://github.com/voidberg/html5sortable
 *
 * Original code copyright 2012 Ali Farhadi.
 * Released under the MIT license.
 *
 * CHANGES by michael at jamroom.net
 * name changed from 'sortable' to 'sbPanelSortable' because there are other functions called .sortable
 *
 */
(function($)
{
    'use strict';

    var dragging, draggingHeight, placeholders = $();
    $.fn.sbPanelSortable = function(options)
    {
        var method = String(options);

        options = $.extend({
            connectWith: false,
            placeholder: null
        }, options);

        return this.each(function()
        {
            var $this = $(this),
                citems,
                soptions,
                isHandle,
                index,
                items,
                startParent,
                newParent,
                placeholder;

            if (method === 'reload') {
                $this.children(options.items).off('dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s');
            }
            if (/^enable|disable|destroy$/.test(method)) {
                citems = $this.children($this.data('items')).attr('draggable', method === 'enable');
                if (method === 'destroy') {
                    $this.off('sortupdate');
                    citems.add(this).removeData('connectWith items')
                        .off('dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s').off('sortupdate');
                }
                return;
            }

            soptions = $this.data('opts');

            if (typeof soptions === 'undefined') {
                $this.data('opts', options);
            }
            else {
                options = soptions;
            }

            items = $this.children(options.items);

            placeholder = ( options.placeholder === null ) ? $('<' + (/^ul|ol$/i.test(this.tagName) ? 'li' : 'div') + ' class="sortable-placeholder">') : $(options.placeholder).addClass('sortable-placeholder');
            items.find(options.handle).mousedown(function()
            {
                isHandle = true;
            }).mouseup(function()
            {
                isHandle = false;
            });
            $this.data('items', options.items);
            placeholders = placeholders.add(placeholder);
            if (options.connectWith) {
                $(options.connectWith).add(this).data('connectWith', options.connectWith);
            }
            items.attr('draggable', 'true').on('dragstart.h5s', function(e)
            {
                e.stopImmediatePropagation();
                if (options.handle && !isHandle) {
                    return false;
                }
                isHandle = false;
                var dt = e.originalEvent.dataTransfer;
                dt.effectAllowed = 'move';
                dt.setData('Text', 'dummy');
                index = (dragging = $(this)).addClass('sortable-dragging').index();
                draggingHeight = dragging.outerHeight();
                startParent = dragging.parent();
            }).on('dragend.h5s', function()
            {
                if (!dragging) {
                    return;
                }

                dragging.removeClass('sortable-dragging').show();
                placeholders.detach();
                newParent = $(this).parent();
                // if current index is different from where the item started, change its location
                if (index !== dragging.index() || startParent !== newParent) {
                    dragging.parent().triggerHandler('sortupdate', {
                        item: dragging,
                        oldindex: index,
                        startparent: startParent,
                        endparent: newParent
                    });
                }
                dragging = null;
            }).not('a[href], img').on('selectstart.h5s', function()
            {
                if (options.handle && !isHandle) {
                    return true;
                }

                if (this.dragDrop) {
                    this.dragDrop();
                }
                return false;
            }).end().add([this, placeholder]).on('dragover.h5s dragenter.h5s drop.h5s', function(e)
            {
                // is the item being dragged in the items that are connected to be dragged
                if (!items.is(dragging) && options.connectWith !== dragging.parent().data('connectWith')) {
                    return true;
                }
                if (e.type === 'drop') {
                    // drop the dragging item into this location
                    e.stopPropagation();
                    placeholders.filter(':visible').after(dragging);
                    dragging.trigger('dragend.h5s');
                    return false;
                }
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';

                var $thisEl = $(this),
                    thisHeight,
                    thisIndex,
                    placeholderIndex,
                    deadZone,
                    offsetTop;

                if (items.is(this)) {
                    thisHeight = $thisEl.outerHeight();
                    thisIndex = $thisEl.index();
                    placeholderIndex = placeholder.index();

                    if (options.forcePlaceholderSize) {
                        placeholder.height(draggingHeight);
                    }

                    // Check if $(this) is bigger than the draggable. If it is, we have to define a dead zone to prevent flickering
                    if (thisHeight > draggingHeight) {
                        // Dead zone?
                        deadZone = thisHeight - draggingHeight;
                        offsetTop = $thisEl.offset().top;

                        if (placeholderIndex < thisIndex && e.originalEvent.pageY < offsetTop + deadZone) {
                            return false;
                        }
                        else if (placeholderIndex > thisIndex && e.originalEvent.pageY > offsetTop + thisHeight - deadZone) {
                            return false;
                        }
                    }

                    dragging.hide();
                    $thisEl[placeholderIndex < thisIndex ? 'after' : 'before'](placeholder);
                    placeholders.not(placeholder).detach();
                }
                else if (!placeholders.is(this) && !$thisEl.children(options.items).length) {
                    placeholders.detach();
                    $thisEl.append(placeholder);
                }
                return false;
            });
        });
    };
}(jQuery));

/*! jQuery UI - v1.10.4 - 2014-04-08
* http://jqueryui.com
* Includes: jquery.ui.core.js, jquery.ui.widget.js, jquery.ui.mouse.js, jquery.ui.sortable.js
* Copyright 2014 jQuery Foundation and other contributors; Licensed MIT */

(function(t,e){function n(e,n){var r,s,o,a=e.nodeName.toLowerCase();return"area"===a?(r=e.parentNode,s=r.name,e.href&&s&&"map"===r.nodeName.toLowerCase()?(o=t("img[usemap=#"+s+"]")[0],!!o&&i(o)):!1):(/input|select|textarea|button|object/.test(a)?!e.disabled:"a"===a?e.href||n:n)&&i(e)}function i(e){return t.expr.filters.visible(e)&&!t(e).parents().addBack().filter(function(){return"hidden"===t.css(this,"visibility")}).length}var r=0,s=/^ui-id-\d+$/;t.ui=t.ui||{},t.extend(t.ui,{version:"1.10.4",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),t.fn.extend({focus:function(e){return function(n,i){return"number"==typeof n?this.each(function(){var e=this;setTimeout(function(){t(e).focus(),i&&i.call(e)},n)}):e.apply(this,arguments)}}(t.fn.focus),scrollParent:function(){var e;return e=t.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){return/(relative|absolute|fixed)/.test(t.css(this,"position"))&&/(auto|scroll)/.test(t.css(this,"overflow")+t.css(this,"overflow-y")+t.css(this,"overflow-x"))}).eq(0):this.parents().filter(function(){return/(auto|scroll)/.test(t.css(this,"overflow")+t.css(this,"overflow-y")+t.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!e.length?t(document):e},zIndex:function(n){if(n!==e)return this.css("zIndex",n);if(this.length)for(var i,r,s=t(this[0]);s.length&&s[0]!==document;){if(i=s.css("position"),("absolute"===i||"relative"===i||"fixed"===i)&&(r=parseInt(s.css("zIndex"),10),!isNaN(r)&&0!==r))return r;s=s.parent()}return 0},uniqueId:function(){return this.each(function(){this.id||(this.id="ui-id-"+ (++r))})},removeUniqueId:function(){return this.each(function(){s.test(this.id)&&t(this).removeAttr("id")})}}),t.extend(t.expr[":"],{data:t.expr.createPseudo?t.expr.createPseudo(function(e){return function(n){return!!t.data(n,e)}}):function(e,n,i){return!!t.data(e,i[3])},focusable:function(e){return n(e,!isNaN(t.attr(e,"tabindex")))},tabbable:function(e){var i=t.attr(e,"tabindex"),r=isNaN(i);return(r||i>=0)&&n(e,!r)}}),t("<a>").outerWidth(1).jquery||t.each(["Width","Height"],function(n,i){function r(e,n,i,r){return t.each(s,function(){n-=parseFloat(t.css(e,"padding"+this))||0,i&&(n-=parseFloat(t.css(e,"border"+this+"Width"))||0),r&&(n-=parseFloat(t.css(e,"margin"+this))||0)}),n}var s="Width"===i?["Left","Right"]:["Top","Bottom"],o=i.toLowerCase(),a={innerWidth:t.fn.innerWidth,innerHeight:t.fn.innerHeight,outerWidth:t.fn.outerWidth,outerHeight:t.fn.outerHeight};t.fn["inner"+i]=function(n){return n===e?a["inner"+i].call(this):this.each(function(){t(this).css(o,r(this,n)+"px")})},t.fn["outer"+i]=function(e,n){return"number"!=typeof e?a["outer"+i].call(this,e):this.each(function(){t(this).css(o,r(this,e,!0,n)+"px")})}}),t.fn.addBack||(t.fn.addBack=function(t){return this.add(null==t?this.prevObject:this.prevObject.filter(t))}),t("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(t.fn.removeData=function(e){return function(n){return arguments.length?e.call(this,t.camelCase(n)):e.call(this)}}(t.fn.removeData)),t.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),t.support.selectstart="onselectstart"in document.createElement("div"),t.fn.extend({disableSelection:function(){return this.bind((t.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(t){t.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),t.extend(t.ui,{plugin:{add:function(e,n,i){var r,s=t.ui[e].prototype;for(r in i)s.plugins[r]=s.plugins[r]||[],s.plugins[r].push([n,i[r]])},call:function(t,e,n){var i,r=t.plugins[e];if(r&&t.element[0].parentNode&&11!==t.element[0].parentNode.nodeType)for(i=0;r.length>i;i++)t.options[r[i][0]]&&r[i][1].apply(t.element,n)}},hasScroll:function(e,n){if("hidden"===t(e).css("overflow"))return!1;var i=n&&"left"===n?"scrollLeft":"scrollTop",r=!1;return e[i]>0?!0:(e[i]=1,r=e[i]>0,e[i]=0,r)}})})(jQuery);(function(t,e){var i=0,n=Array.prototype.slice,s=t.cleanData;t.cleanData=function(e){for(var i,n=0;null!=(i=e[n]);n++)try{t(i).triggerHandler("remove")}catch(o){}s(e)},t.widget=function(i,n,s){var o,r,a,l,u={},h=i.split(".")[0];i=i.split(".")[1],o=h+"-"+i,s||(s=n,n=t.Widget),t.expr[":"][o.toLowerCase()]=function(e){return!!t.data(e,o)},t[h]=t[h]||{},r=t[h][i],a=t[h][i]=function(t,i){return this._createWidget?(arguments.length&&this._createWidget(t,i),e):new a(t,i)},t.extend(a,r,{version:s.version,_proto:t.extend({},s),_childConstructors:[]}),l=new n,l.options=t.widget.extend({},l.options),t.each(s,function(i,s){return t.isFunction(s)?(u[i]=function(){var t=function(){return n.prototype[i].apply(this,arguments)},e=function(t){return n.prototype[i].apply(this,t)};return function(){var i,n=this._super,o=this._superApply;return this._super=t,this._superApply=e,i=s.apply(this,arguments),this._super=n,this._superApply=o,i}}(),e):(u[i]=s,e)}),a.prototype=t.widget.extend(l,{widgetEventPrefix:r?l.widgetEventPrefix||i:i},u,{constructor:a,namespace:h,widgetName:i,widgetFullName:o}),r?(t.each(r._childConstructors,function(e,i){var n=i.prototype;t.widget(n.namespace+"."+n.widgetName,a,i._proto)}),delete r._childConstructors):n._childConstructors.push(a),t.widget.bridge(i,a)},t.widget.extend=function(i){for(var s,o,r=n.call(arguments,1),a=0,l=r.length;l>a;a++)for(s in r[a])o=r[a][s],r[a].hasOwnProperty(s)&&o!==e&&(i[s]=t.isPlainObject(o)?t.isPlainObject(i[s])?t.widget.extend({},i[s],o):t.widget.extend({},o):o);return i},t.widget.bridge=function(i,s){var o=s.prototype.widgetFullName||i;t.fn[i]=function(r){var a="string"==typeof r,l=n.call(arguments,1),u=this;return r=!a&&l.length?t.widget.extend.apply(null,[r].concat(l)):r,a?this.each(function(){var n,s=t.data(this,o);return s?t.isFunction(s[r])&&"_"!==r.charAt(0)?(n=s[r].apply(s,l),n!==s&&n!==e?(u=n&&n.jquery?u.pushStack(n.get()):n,!1):e):t.error("no such method '"+r+"' for "+i+" widget instance"):t.error("cannot call methods on "+i+" prior to initialization; "+"attempted to call method '"+r+"'")}):this.each(function(){var e=t.data(this,o);e?e.option(r||{})._init():t.data(this,o,new s(r,this))}),u}},t.Widget=function(){},t.Widget._childConstructors=[],t.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(e,n){n=t(n||this.defaultElement||this)[0],this.element=t(n),this.uuid=i++,this.eventNamespace="."+this.widgetName+this.uuid,this.options=t.widget.extend({},this.options,this._getCreateOptions(),e),this.bindings=t(),this.hoverable=t(),this.focusable=t(),n!==this&&(t.data(n,this.widgetFullName,this),this._on(!0,this.element,{remove:function(t){t.target===n&&this.destroy()}}),this.document=t(n.style?n.ownerDocument:n.document||n),this.window=t(this.document[0].defaultView||this.document[0].parentWindow)),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:t.noop,_getCreateEventData:t.noop,_create:t.noop,_init:t.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetName).removeData(this.widgetFullName).removeData(t.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:t.noop,widget:function(){return this.element},option:function(i,n){var s,o,r,a=i;if(0===arguments.length)return t.widget.extend({},this.options);if("string"==typeof i)if(a={},s=i.split("."),i=s.shift(),s.length){for(o=a[i]=t.widget.extend({},this.options[i]),r=0;s.length-1>r;r++)o[s[r]]=o[s[r]]||{},o=o[s[r]];if(i=s.pop(),1===arguments.length)return o[i]===e?null:o[i];o[i]=n}else{if(1===arguments.length)return this.options[i]===e?null:this.options[i];a[i]=n}return this._setOptions(a),this},_setOptions:function(t){var e;for(e in t)this._setOption(e,t[e]);return this},_setOption:function(t,e){return this.options[t]=e,"disabled"===t&&(this.widget().toggleClass(this.widgetFullName+"-disabled ui-state-disabled",!!e).attr("aria-disabled",e),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")),this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_on:function(i,n,s){var o,r=this;"boolean"!=typeof i&&(s=n,n=i,i=!1),s?(n=o=t(n),this.bindings=this.bindings.add(n)):(s=n,n=this.element,o=this.widget()),t.each(s,function(s,a){function l(){return i||r.options.disabled!==!0&&!t(this).hasClass("ui-state-disabled")?("string"==typeof a?r[a]:a).apply(r,arguments):e}"string"!=typeof a&&(l.guid=a.guid=a.guid||l.guid||t.guid++);var u=s.match(/^(\w+)\s*(.*)$/),h=u[1]+r.eventNamespace,c=u[2];c?o.delegate(c,h,l):n.bind(h,l)})},_off:function(t,e){e=(e||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,t.unbind(e).undelegate(e)},_delay:function(t,e){function i(){return("string"==typeof t?n[t]:t).apply(n,arguments)}var n=this;return setTimeout(i,e||0)},_hoverable:function(e){this.hoverable=this.hoverable.add(e),this._on(e,{mouseenter:function(e){t(e.currentTarget).addClass("ui-state-hover")},mouseleave:function(e){t(e.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(e){this.focusable=this.focusable.add(e),this._on(e,{focusin:function(e){t(e.currentTarget).addClass("ui-state-focus")},focusout:function(e){t(e.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(e,i,n){var s,o,r=this.options[e];if(n=n||{},i=t.Event(i),i.type=(e===this.widgetEventPrefix?e:this.widgetEventPrefix+e).toLowerCase(),i.target=this.element[0],o=i.originalEvent)for(s in o)s in i||(i[s]=o[s]);return this.element.trigger(i,n),!(t.isFunction(r)&&r.apply(this.element[0],[i].concat(n))===!1||i.isDefaultPrevented())}},t.each({show:"fadeIn",hide:"fadeOut"},function(e,i){t.Widget.prototype["_"+e]=function(n,s,o){"string"==typeof s&&(s={effect:s});var r,a=s?s===!0||"number"==typeof s?i:s.effect||i:e;s=s||{},"number"==typeof s&&(s={duration:s}),r=!t.isEmptyObject(s),s.complete=o,s.delay&&n.delay(s.delay),r&&t.effects&&t.effects.effect[a]?n[e](s):a!==e&&n[a]?n[a](s.duration,s.easing,o):n.queue(function(i){t(this)[e](),o&&o.call(n[0]),i()})}})})(jQuery);(function(t){var e=!1;t(document).mouseup(function(){e=!1}),t.widget("ui.mouse",{version:"1.10.4",options:{cancel:"input,textarea,button,select,option",distance:1,delay:0},_mouseInit:function(){var e=this;this.element.bind("mousedown."+this.widgetName,function(t){return e._mouseDown(t)}).bind("click."+this.widgetName,function(i){return!0===t.data(i.target,e.widgetName+".preventClickEvent")?(t.removeData(i.target,e.widgetName+".preventClickEvent"),i.stopImmediatePropagation(),!1):undefined}),this.started=!1},_mouseDestroy:function(){this.element.unbind("."+this.widgetName),this._mouseMoveDelegate&&t(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate)},_mouseDown:function(i){if(!e){this._mouseStarted&&this._mouseUp(i),this._mouseDownEvent=i;var s=this,n=1===i.which,a="string"==typeof this.options.cancel&&i.target.nodeName?t(i.target).closest(this.options.cancel).length:!1;return n&&!a&&this._mouseCapture(i)?(this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){s.mouseDelayMet=!0},this.options.delay)),this._mouseDistanceMet(i)&&this._mouseDelayMet(i)&&(this._mouseStarted=this._mouseStart(i)!==!1,!this._mouseStarted)?(i.preventDefault(),!0):(!0===t.data(i.target,this.widgetName+".preventClickEvent")&&t.removeData(i.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(t){return s._mouseMove(t)},this._mouseUpDelegate=function(t){return s._mouseUp(t)},t(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate),i.preventDefault(),e=!0,!0)):!0}},_mouseMove:function(e){return t.ui.ie&&(!document.documentMode||9>document.documentMode)&&!e.button?this._mouseUp(e):this._mouseStarted?(this._mouseDrag(e),e.preventDefault()):(this._mouseDistanceMet(e)&&this._mouseDelayMet(e)&&(this._mouseStarted=this._mouseStart(this._mouseDownEvent,e)!==!1,this._mouseStarted?this._mouseDrag(e):this._mouseUp(e)),!this._mouseStarted)},_mouseUp:function(e){return t(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,e.target===this._mouseDownEvent.target&&t.data(e.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(e)),!1},_mouseDistanceMet:function(t){return Math.max(Math.abs(this._mouseDownEvent.pageX-t.pageX),Math.abs(this._mouseDownEvent.pageY-t.pageY))>=this.options.distance},_mouseDelayMet:function(){return this.mouseDelayMet},_mouseStart:function(){},_mouseDrag:function(){},_mouseStop:function(){},_mouseCapture:function(){return!0}})})(jQuery);(function(t){function e(t,e,i){return t>e&&e+i>t}function i(t){return/left|right/.test(t.css("float"))||/inline|table-cell/.test(t.css("display"))}t.widget("ui.sortable",t.ui.mouse,{version:"1.10.4",widgetEventPrefix:"sort",ready:!1,options:{appendTo:"parent",axis:!1,connectWith:!1,containment:!1,cursor:"auto",cursorAt:!1,dropOnEmpty:!0,forcePlaceholderSize:!1,forceHelperSize:!1,grid:!1,handle:!1,helper:"original",items:"> *",opacity:!1,placeholder:!1,revert:!1,scroll:!0,scrollSensitivity:20,scrollSpeed:20,scope:"default",tolerance:"intersect",zIndex:1e3,activate:null,beforeStop:null,change:null,deactivate:null,out:null,over:null,receive:null,remove:null,sort:null,start:null,stop:null,update:null},_create:function(){var t=this.options;this.containerCache={},this.element.addClass("ui-sortable"),this.refresh(),this.floating=this.items.length?"x"===t.axis||i(this.items[0].item):!1,this.offset=this.element.offset(),this._mouseInit(),this.ready=!0},_destroy:function(){this.element.removeClass("ui-sortable ui-sortable-disabled"),this._mouseDestroy();for(var t=this.items.length-1;t>=0;t--)this.items[t].item.removeData(this.widgetName+"-item");return this},_setOption:function(e,i){"disabled"===e?(this.options[e]=i,this.widget().toggleClass("ui-sortable-disabled",!!i)):t.Widget.prototype._setOption.apply(this,arguments)},_mouseCapture:function(e,i){var s=null,n=!1,o=this;return this.reverting?!1:this.options.disabled||"static"===this.options.type?!1:(this._refreshItems(e),t(e.target).parents().each(function(){return t.data(this,o.widgetName+"-item")===o?(s=t(this),!1):undefined}),t.data(e.target,o.widgetName+"-item")===o&&(s=t(e.target)),s?!this.options.handle||i||(t(this.options.handle,s).find("*").addBack().each(function(){this===e.target&&(n=!0)}),n)?(this.currentItem=s,this._removeCurrentsFromItems(),!0):!1:!1)},_mouseStart:function(e,i,s){var n,o,a=this.options;if(this.currentContainer=this,this.refreshPositions(),this.helper=this._createHelper(e),this._cacheHelperProportions(),this._cacheMargins(),this.scrollParent=this.helper.scrollParent(),this.offset=this.currentItem.offset(),this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left},t.extend(this.offset,{click:{left:e.pageX-this.offset.left,top:e.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset()}),this.helper.css("position","absolute"),this.cssPosition=this.helper.css("position"),this.originalPosition=this._generatePosition(e),this.originalPageX=e.pageX,this.originalPageY=e.pageY,a.cursorAt&&this._adjustOffsetFromHelper(a.cursorAt),this.domPosition={prev:this.currentItem.prev()[0],parent:this.currentItem.parent()[0]},this.helper[0]!==this.currentItem[0]&&this.currentItem.hide(),this._createPlaceholder(),a.containment&&this._setContainment(),a.cursor&&"auto"!==a.cursor&&(o=this.document.find("body"),this.storedCursor=o.css("cursor"),o.css("cursor",a.cursor),this.storedStylesheet=t("<style>*{ cursor: "+a.cursor+" !important; }</style>").appendTo(o)),a.opacity&&(this.helper.css("opacity")&&(this._storedOpacity=this.helper.css("opacity")),this.helper.css("opacity",a.opacity)),a.zIndex&&(this.helper.css("zIndex")&&(this._storedZIndex=this.helper.css("zIndex")),this.helper.css("zIndex",a.zIndex)),this.scrollParent[0]!==document&&"HTML"!==this.scrollParent[0].tagName&&(this.overflowOffset=this.scrollParent.offset()),this._trigger("start",e,this._uiHash()),this._preserveHelperProportions||this._cacheHelperProportions(),!s)for(n=this.containers.length-1;n>=0;n--)this.containers[n]._trigger("activate",e,this._uiHash(this));return t.ui.ddmanager&&(t.ui.ddmanager.current=this),t.ui.ddmanager&&!a.dropBehaviour&&t.ui.ddmanager.prepareOffsets(this,e),this.dragging=!0,this.helper.addClass("ui-sortable-helper"),this._mouseDrag(e),!0},_mouseDrag:function(e){var i,s,n,o,a=this.options,r=!1;for(this.position=this._generatePosition(e),this.positionAbs=this._convertPositionTo("absolute"),this.lastPositionAbs||(this.lastPositionAbs=this.positionAbs),this.options.scroll&&(this.scrollParent[0]!==document&&"HTML"!==this.scrollParent[0].tagName?(this.overflowOffset.top+this.scrollParent[0].offsetHeight-e.pageY<a.scrollSensitivity?this.scrollParent[0].scrollTop=r=this.scrollParent[0].scrollTop+a.scrollSpeed:e.pageY-this.overflowOffset.top<a.scrollSensitivity&&(this.scrollParent[0].scrollTop=r=this.scrollParent[0].scrollTop-a.scrollSpeed),this.overflowOffset.left+this.scrollParent[0].offsetWidth-e.pageX<a.scrollSensitivity?this.scrollParent[0].scrollLeft=r=this.scrollParent[0].scrollLeft+a.scrollSpeed:e.pageX-this.overflowOffset.left<a.scrollSensitivity&&(this.scrollParent[0].scrollLeft=r=this.scrollParent[0].scrollLeft-a.scrollSpeed)):(e.pageY-t(document).scrollTop()<a.scrollSensitivity?r=t(document).scrollTop(t(document).scrollTop()-a.scrollSpeed):t(window).height()-(e.pageY-t(document).scrollTop())<a.scrollSensitivity&&(r=t(document).scrollTop(t(document).scrollTop()+a.scrollSpeed)),e.pageX-t(document).scrollLeft()<a.scrollSensitivity?r=t(document).scrollLeft(t(document).scrollLeft()-a.scrollSpeed):t(window).width()-(e.pageX-t(document).scrollLeft())<a.scrollSensitivity&&(r=t(document).scrollLeft(t(document).scrollLeft()+a.scrollSpeed))),r!==!1&&t.ui.ddmanager&&!a.dropBehaviour&&t.ui.ddmanager.prepareOffsets(this,e)),this.positionAbs=this._convertPositionTo("absolute"),this.options.axis&&"y"===this.options.axis||(this.helper[0].style.left=this.position.left+"px"),this.options.axis&&"x"===this.options.axis||(this.helper[0].style.top=this.position.top+"px"),i=this.items.length-1;i>=0;i--)if(s=this.items[i],n=s.item[0],o=this._intersectsWithPointer(s),o&&s.instance===this.currentContainer&&n!==this.currentItem[0]&&this.placeholder[1===o?"next":"prev"]()[0]!==n&&!t.contains(this.placeholder[0],n)&&("semi-dynamic"===this.options.type?!t.contains(this.element[0],n):!0)){if(this.direction=1===o?"down":"up","pointer"!==this.options.tolerance&&!this._intersectsWithSides(s))break;this._rearrange(e,s),this._trigger("change",e,this._uiHash());break}return this._contactContainers(e),t.ui.ddmanager&&t.ui.ddmanager.drag(this,e),this._trigger("sort",e,this._uiHash()),this.lastPositionAbs=this.positionAbs,!1},_mouseStop:function(e,i){if(e){if(t.ui.ddmanager&&!this.options.dropBehaviour&&t.ui.ddmanager.drop(this,e),this.options.revert){var s=this,n=this.placeholder.offset(),o=this.options.axis,a={};o&&"x"!==o||(a.left=n.left-this.offset.parent.left-this.margins.left+(this.offsetParent[0]===document.body?0:this.offsetParent[0].scrollLeft)),o&&"y"!==o||(a.top=n.top-this.offset.parent.top-this.margins.top+(this.offsetParent[0]===document.body?0:this.offsetParent[0].scrollTop)),this.reverting=!0,t(this.helper).animate(a,parseInt(this.options.revert,10)||500,function(){s._clear(e)})}else this._clear(e,i);return!1}},cancel:function(){if(this.dragging){this._mouseUp({target:null}),"original"===this.options.helper?this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper"):this.currentItem.show();for(var e=this.containers.length-1;e>=0;e--)this.containers[e]._trigger("deactivate",null,this._uiHash(this)),this.containers[e].containerCache.over&&(this.containers[e]._trigger("out",null,this._uiHash(this)),this.containers[e].containerCache.over=0)}return this.placeholder&&(this.placeholder[0].parentNode&&this.placeholder[0].parentNode.removeChild(this.placeholder[0]),"original"!==this.options.helper&&this.helper&&this.helper[0].parentNode&&this.helper.remove(),t.extend(this,{helper:null,dragging:!1,reverting:!1,_noFinalSort:null}),this.domPosition.prev?t(this.domPosition.prev).after(this.currentItem):t(this.domPosition.parent).prepend(this.currentItem)),this},serialize:function(e){var i=this._getItemsAsjQuery(e&&e.connected),s=[];return e=e||{},t(i).each(function(){var i=(t(e.item||this).attr(e.attribute||"id")||"").match(e.expression||/(.+)[\-=_](.+)/);i&&s.push((e.key||i[1]+"[]")+"="+(e.key&&e.expression?i[1]:i[2]))}),!s.length&&e.key&&s.push(e.key+"="),s.join("&")},toArray:function(e){var i=this._getItemsAsjQuery(e&&e.connected),s=[];return e=e||{},i.each(function(){s.push(t(e.item||this).attr(e.attribute||"id")||"")}),s},_intersectsWith:function(t){var e=this.positionAbs.left,i=e+this.helperProportions.width,s=this.positionAbs.top,n=s+this.helperProportions.height,o=t.left,a=o+t.width,r=t.top,h=r+t.height,l=this.offset.click.top,c=this.offset.click.left,u="x"===this.options.axis||s+l>r&&h>s+l,d="y"===this.options.axis||e+c>o&&a>e+c,p=u&&d;return"pointer"===this.options.tolerance||this.options.forcePointerForContainers||"pointer"!==this.options.tolerance&&this.helperProportions[this.floating?"width":"height"]>t[this.floating?"width":"height"]?p:e+this.helperProportions.width/2>o&&a>i-this.helperProportions.width/2&&s+this.helperProportions.height/2>r&&h>n-this.helperProportions.height/2},_intersectsWithPointer:function(t){var i="x"===this.options.axis||e(this.positionAbs.top+this.offset.click.top,t.top,t.height),s="y"===this.options.axis||e(this.positionAbs.left+this.offset.click.left,t.left,t.width),n=i&&s,o=this._getDragVerticalDirection(),a=this._getDragHorizontalDirection();return n?this.floating?a&&"right"===a||"down"===o?2:1:o&&("down"===o?2:1):!1},_intersectsWithSides:function(t){var i=e(this.positionAbs.top+this.offset.click.top,t.top+t.height/2,t.height),s=e(this.positionAbs.left+this.offset.click.left,t.left+t.width/2,t.width),n=this._getDragVerticalDirection(),o=this._getDragHorizontalDirection();return this.floating&&o?"right"===o&&s||"left"===o&&!s:n&&("down"===n&&i||"up"===n&&!i)},_getDragVerticalDirection:function(){var t=this.positionAbs.top-this.lastPositionAbs.top;return 0!==t&&(t>0?"down":"up")},_getDragHorizontalDirection:function(){var t=this.positionAbs.left-this.lastPositionAbs.left;return 0!==t&&(t>0?"right":"left")},refresh:function(t){return this._refreshItems(t),this.refreshPositions(),this},_connectWith:function(){var t=this.options;return t.connectWith.constructor===String?[t.connectWith]:t.connectWith},_getItemsAsjQuery:function(e){function i(){r.push(this)}var s,n,o,a,r=[],h=[],l=this._connectWith();if(l&&e)for(s=l.length-1;s>=0;s--)for(o=t(l[s]),n=o.length-1;n>=0;n--)a=t.data(o[n],this.widgetFullName),a&&a!==this&&!a.options.disabled&&h.push([t.isFunction(a.options.items)?a.options.items.call(a.element):t(a.options.items,a.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),a]);for(h.push([t.isFunction(this.options.items)?this.options.items.call(this.element,null,{options:this.options,item:this.currentItem}):t(this.options.items,this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),this]),s=h.length-1;s>=0;s--)h[s][0].each(i);return t(r)},_removeCurrentsFromItems:function(){var e=this.currentItem.find(":data("+this.widgetName+"-item)");this.items=t.grep(this.items,function(t){for(var i=0;e.length>i;i++)if(e[i]===t.item[0])return!1;return!0})},_refreshItems:function(e){this.items=[],this.containers=[this];var i,s,n,o,a,r,h,l,c=this.items,u=[[t.isFunction(this.options.items)?this.options.items.call(this.element[0],e,{item:this.currentItem}):t(this.options.items,this.element),this]],d=this._connectWith();if(d&&this.ready)for(i=d.length-1;i>=0;i--)for(n=t(d[i]),s=n.length-1;s>=0;s--)o=t.data(n[s],this.widgetFullName),o&&o!==this&&!o.options.disabled&&(u.push([t.isFunction(o.options.items)?o.options.items.call(o.element[0],e,{item:this.currentItem}):t(o.options.items,o.element),o]),this.containers.push(o));for(i=u.length-1;i>=0;i--)for(a=u[i][1],r=u[i][0],s=0,l=r.length;l>s;s++)h=t(r[s]),h.data(this.widgetName+"-item",a),c.push({item:h,instance:a,width:0,height:0,left:0,top:0})},refreshPositions:function(e){this.offsetParent&&this.helper&&(this.offset.parent=this._getParentOffset());var i,s,n,o;for(i=this.items.length-1;i>=0;i--)s=this.items[i],s.instance!==this.currentContainer&&this.currentContainer&&s.item[0]!==this.currentItem[0]||(n=this.options.toleranceElement?t(this.options.toleranceElement,s.item):s.item,e||(s.width=n.outerWidth(),s.height=n.outerHeight()),o=n.offset(),s.left=o.left,s.top=o.top);if(this.options.custom&&this.options.custom.refreshContainers)this.options.custom.refreshContainers.call(this);else for(i=this.containers.length-1;i>=0;i--)o=this.containers[i].element.offset(),this.containers[i].containerCache.left=o.left,this.containers[i].containerCache.top=o.top,this.containers[i].containerCache.width=this.containers[i].element.outerWidth(),this.containers[i].containerCache.height=this.containers[i].element.outerHeight();return this},_createPlaceholder:function(e){e=e||this;var i,s=e.options;s.placeholder&&s.placeholder.constructor!==String||(i=s.placeholder,s.placeholder={element:function(){var s=e.currentItem[0].nodeName.toLowerCase(),n=t("<"+s+">",e.document[0]).addClass(i||e.currentItem[0].className+" ui-sortable-placeholder").removeClass("ui-sortable-helper");return"tr"===s?e.currentItem.children().each(function(){t("<td>&#160;</td>",e.document[0]).attr("colspan",t(this).attr("colspan")||1).appendTo(n)}):"img"===s&&n.attr("src",e.currentItem.attr("src")),i||n.css("visibility","hidden"),n},update:function(t,n){(!i||s.forcePlaceholderSize)&&(n.height()||n.height(e.currentItem.innerHeight()-parseInt(e.currentItem.css("paddingTop")||0,10)-parseInt(e.currentItem.css("paddingBottom")||0,10)),n.width()||n.width(e.currentItem.innerWidth()-parseInt(e.currentItem.css("paddingLeft")||0,10)-parseInt(e.currentItem.css("paddingRight")||0,10)))}}),e.placeholder=t(s.placeholder.element.call(e.element,e.currentItem)),e.currentItem.after(e.placeholder),s.placeholder.update(e,e.placeholder)},_contactContainers:function(s){var n,o,a,r,h,l,c,u,d,p,f=null,g=null;for(n=this.containers.length-1;n>=0;n--)if(!t.contains(this.currentItem[0],this.containers[n].element[0]))if(this._intersectsWith(this.containers[n].containerCache)){if(f&&t.contains(this.containers[n].element[0],f.element[0]))continue;f=this.containers[n],g=n}else this.containers[n].containerCache.over&&(this.containers[n]._trigger("out",s,this._uiHash(this)),this.containers[n].containerCache.over=0);if(f)if(1===this.containers.length)this.containers[g].containerCache.over||(this.containers[g]._trigger("over",s,this._uiHash(this)),this.containers[g].containerCache.over=1);else{for(a=1e4,r=null,p=f.floating||i(this.currentItem),h=p?"left":"top",l=p?"width":"height",c=this.positionAbs[h]+this.offset.click[h],o=this.items.length-1;o>=0;o--)t.contains(this.containers[g].element[0],this.items[o].item[0])&&this.items[o].item[0]!==this.currentItem[0]&&(!p||e(this.positionAbs.top+this.offset.click.top,this.items[o].top,this.items[o].height))&&(u=this.items[o].item.offset()[h],d=!1,Math.abs(u-c)>Math.abs(u+this.items[o][l]-c)&&(d=!0,u+=this.items[o][l]),a>Math.abs(u-c)&&(a=Math.abs(u-c),r=this.items[o],this.direction=d?"up":"down"));if(!r&&!this.options.dropOnEmpty)return;if(this.currentContainer===this.containers[g])return;r?this._rearrange(s,r,null,!0):this._rearrange(s,null,this.containers[g].element,!0),this._trigger("change",s,this._uiHash()),this.containers[g]._trigger("change",s,this._uiHash(this)),this.currentContainer=this.containers[g],this.options.placeholder.update(this.currentContainer,this.placeholder),this.containers[g]._trigger("over",s,this._uiHash(this)),this.containers[g].containerCache.over=1}},_createHelper:function(e){var i=this.options,s=t.isFunction(i.helper)?t(i.helper.apply(this.element[0],[e,this.currentItem])):"clone"===i.helper?this.currentItem.clone():this.currentItem;return s.parents("body").length||t("parent"!==i.appendTo?i.appendTo:this.currentItem[0].parentNode)[0].appendChild(s[0]),s[0]===this.currentItem[0]&&(this._storedCSS={width:this.currentItem[0].style.width,height:this.currentItem[0].style.height,position:this.currentItem.css("position"),top:this.currentItem.css("top"),left:this.currentItem.css("left")}),(!s[0].style.width||i.forceHelperSize)&&s.width(this.currentItem.width()),(!s[0].style.height||i.forceHelperSize)&&s.height(this.currentItem.height()),s},_adjustOffsetFromHelper:function(e){"string"==typeof e&&(e=e.split(" ")),t.isArray(e)&&(e={left:+e[0],top:+e[1]||0}),"left"in e&&(this.offset.click.left=e.left+this.margins.left),"right"in e&&(this.offset.click.left=this.helperProportions.width-e.right+this.margins.left),"top"in e&&(this.offset.click.top=e.top+this.margins.top),"bottom"in e&&(this.offset.click.top=this.helperProportions.height-e.bottom+this.margins.top)},_getParentOffset:function(){this.offsetParent=this.helper.offsetParent();var e=this.offsetParent.offset();return"absolute"===this.cssPosition&&this.scrollParent[0]!==document&&t.contains(this.scrollParent[0],this.offsetParent[0])&&(e.left+=this.scrollParent.scrollLeft(),e.top+=this.scrollParent.scrollTop()),(this.offsetParent[0]===document.body||this.offsetParent[0].tagName&&"html"===this.offsetParent[0].tagName.toLowerCase()&&t.ui.ie)&&(e={top:0,left:0}),{top:e.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:e.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)}},_getRelativeOffset:function(){if("relative"===this.cssPosition){var t=this.currentItem.position();return{top:t.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:t.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()}}return{top:0,left:0}},_cacheMargins:function(){this.margins={left:parseInt(this.currentItem.css("marginLeft"),10)||0,top:parseInt(this.currentItem.css("marginTop"),10)||0}},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()}},_setContainment:function(){var e,i,s,n=this.options;"parent"===n.containment&&(n.containment=this.helper[0].parentNode),("document"===n.containment||"window"===n.containment)&&(this.containment=[0-this.offset.relative.left-this.offset.parent.left,0-this.offset.relative.top-this.offset.parent.top,t("document"===n.containment?document:window).width()-this.helperProportions.width-this.margins.left,(t("document"===n.containment?document:window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top]),/^(document|window|parent)$/.test(n.containment)||(e=t(n.containment)[0],i=t(n.containment).offset(),s="hidden"!==t(e).css("overflow"),this.containment=[i.left+(parseInt(t(e).css("borderLeftWidth"),10)||0)+(parseInt(t(e).css("paddingLeft"),10)||0)-this.margins.left,i.top+(parseInt(t(e).css("borderTopWidth"),10)||0)+(parseInt(t(e).css("paddingTop"),10)||0)-this.margins.top,i.left+(s?Math.max(e.scrollWidth,e.offsetWidth):e.offsetWidth)-(parseInt(t(e).css("borderLeftWidth"),10)||0)-(parseInt(t(e).css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left,i.top+(s?Math.max(e.scrollHeight,e.offsetHeight):e.offsetHeight)-(parseInt(t(e).css("borderTopWidth"),10)||0)-(parseInt(t(e).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top])},_convertPositionTo:function(e,i){i||(i=this.position);var s="absolute"===e?1:-1,n="absolute"!==this.cssPosition||this.scrollParent[0]!==document&&t.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,o=/(html|body)/i.test(n[0].tagName);return{top:i.top+this.offset.relative.top*s+this.offset.parent.top*s-("fixed"===this.cssPosition?-this.scrollParent.scrollTop():o?0:n.scrollTop())*s,left:i.left+this.offset.relative.left*s+this.offset.parent.left*s-("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():o?0:n.scrollLeft())*s}},_generatePosition:function(e){var i,s,n=this.options,o=e.pageX,a=e.pageY,r="absolute"!==this.cssPosition||this.scrollParent[0]!==document&&t.contains(this.scrollParent[0],this.offsetParent[0])?this.scrollParent:this.offsetParent,h=/(html|body)/i.test(r[0].tagName);return"relative"!==this.cssPosition||this.scrollParent[0]!==document&&this.scrollParent[0]!==this.offsetParent[0]||(this.offset.relative=this._getRelativeOffset()),this.originalPosition&&(this.containment&&(e.pageX-this.offset.click.left<this.containment[0]&&(o=this.containment[0]+this.offset.click.left),e.pageY-this.offset.click.top<this.containment[1]&&(a=this.containment[1]+this.offset.click.top),e.pageX-this.offset.click.left>this.containment[2]&&(o=this.containment[2]+this.offset.click.left),e.pageY-this.offset.click.top>this.containment[3]&&(a=this.containment[3]+this.offset.click.top)),n.grid&&(i=this.originalPageY+Math.round((a-this.originalPageY)/n.grid[1])*n.grid[1],a=this.containment?i-this.offset.click.top>=this.containment[1]&&i-this.offset.click.top<=this.containment[3]?i:i-this.offset.click.top>=this.containment[1]?i-n.grid[1]:i+n.grid[1]:i,s=this.originalPageX+Math.round((o-this.originalPageX)/n.grid[0])*n.grid[0],o=this.containment?s-this.offset.click.left>=this.containment[0]&&s-this.offset.click.left<=this.containment[2]?s:s-this.offset.click.left>=this.containment[0]?s-n.grid[0]:s+n.grid[0]:s)),{top:a-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+("fixed"===this.cssPosition?-this.scrollParent.scrollTop():h?0:r.scrollTop()),left:o-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+("fixed"===this.cssPosition?-this.scrollParent.scrollLeft():h?0:r.scrollLeft())}},_rearrange:function(t,e,i,s){i?i[0].appendChild(this.placeholder[0]):e.item[0].parentNode.insertBefore(this.placeholder[0],"down"===this.direction?e.item[0]:e.item[0].nextSibling),this.counter=this.counter?++this.counter:1;var n=this.counter;this._delay(function(){n===this.counter&&this.refreshPositions(!s)})},_clear:function(t,e){function i(t,e,i){return function(s){i._trigger(t,s,e._uiHash(e))}}this.reverting=!1;var s,n=[];if(!this._noFinalSort&&this.currentItem.parent().length&&this.placeholder.before(this.currentItem),this._noFinalSort=null,this.helper[0]===this.currentItem[0]){for(s in this._storedCSS)("auto"===this._storedCSS[s]||"static"===this._storedCSS[s])&&(this._storedCSS[s]="");this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper")}else this.currentItem.show();for(this.fromOutside&&!e&&n.push(function(t){this._trigger("receive",t,this._uiHash(this.fromOutside))}),!this.fromOutside&&this.domPosition.prev===this.currentItem.prev().not(".ui-sortable-helper")[0]&&this.domPosition.parent===this.currentItem.parent()[0]||e||n.push(function(t){this._trigger("update",t,this._uiHash())}),this!==this.currentContainer&&(e||(n.push(function(t){this._trigger("remove",t,this._uiHash())}),n.push(function(t){return function(e){t._trigger("receive",e,this._uiHash(this))}}.call(this,this.currentContainer)),n.push(function(t){return function(e){t._trigger("update",e,this._uiHash(this))}}.call(this,this.currentContainer)))),s=this.containers.length-1;s>=0;s--)e||n.push(i("deactivate",this,this.containers[s])),this.containers[s].containerCache.over&&(n.push(i("out",this,this.containers[s])),this.containers[s].containerCache.over=0);if(this.storedCursor&&(this.document.find("body").css("cursor",this.storedCursor),this.storedStylesheet.remove()),this._storedOpacity&&this.helper.css("opacity",this._storedOpacity),this._storedZIndex&&this.helper.css("zIndex","auto"===this._storedZIndex?"":this._storedZIndex),this.dragging=!1,this.cancelHelperRemoval){if(!e){for(this._trigger("beforeStop",t,this._uiHash()),s=0;n.length>s;s++)n[s].call(this,t);this._trigger("stop",t,this._uiHash())}return this.fromOutside=!1,!1}if(e||this._trigger("beforeStop",t,this._uiHash()),this.placeholder[0].parentNode.removeChild(this.placeholder[0]),this.helper[0]!==this.currentItem[0]&&this.helper.remove(),this.helper=null,!e){for(s=0;n.length>s;s++)n[s].call(this,t);this._trigger("stop",t,this._uiHash())}return this.fromOutside=!1,!0},_trigger:function(){t.Widget.prototype._trigger.apply(this,arguments)===!1&&this.cancel()},_uiHash:function(e){var i=e||this;return{helper:i.helper,placeholder:i.placeholder||t([]),position:i.position,originalPosition:i.originalPosition,offset:i.positionAbs,item:i.currentItem,sender:e?e.element:null}}})})(jQuery);
/*
 * jQuery UI Nested Sortable
 * v 2.0 / 29 oct 2012
 * http://mjsarfatti.com/sandbox/nestedSortable
 *
 * Depends on:
 *	 jquery.ui.sortable.js 1.10+
 *
 * Copyright (c) 2010-2013 Manuele J Sarfatti
 * Licensed under the MIT License
 * http://www.opensource.org/licenses/mit-license.php
 */

(function($) {

	function isOverAxis( x, reference, size ) {
		return ( x > reference ) && ( x < ( reference + size ) );
	}

	$.widget("mjs.nestedSortable", $.extend({}, $.ui.sortable.prototype, {

		options: {
			doNotClear: false,
			expandOnHover: 700,
			isAllowed: function(placeholder, placeholderParent, originalItem) { return true; },
			isTree: false,
			listType: 'ol',
			maxLevels: 0,
			protectRoot: false,
			rootID: null,
			rtl: false,
			startCollapsed: false,
			tabSize: 20,

			branchClass: 'mjs-nestedSortable-branch',
			collapsedClass: 'mjs-nestedSortable-collapsed',
			disableNestingClass: 'mjs-nestedSortable-no-nesting',
			errorClass: 'mjs-nestedSortable-error',
			expandedClass: 'mjs-nestedSortable-expanded',
			hoveringClass: 'mjs-nestedSortable-hovering',
			leafClass: 'mjs-nestedSortable-leaf'
		},

		_create: function() {
			this.element.data('ui-sortable', this.element.data('mjs-nestedSortable'));

			// mjs - prevent browser from freezing if the HTML is not correct
			if (!this.element.is(this.options.listType))
				throw new Error('nestedSortable: Please check that the listType option is set to your actual list type');

			// mjs - force 'intersect' tolerance method if we have a tree with expanding/collapsing functionality
			if (this.options.isTree && this.options.expandOnHover) {
				this.options.tolerance = 'intersect';
			}

			$.ui.sortable.prototype._create.apply(this, arguments);

			// mjs - prepare the tree by applying the right classes (the CSS is responsible for actual hide/show functionality)
			if (this.options.isTree) {
				var self = this;
				$(this.items).each(function() {
					var $li = this.item;
					if ($li.children(self.options.listType).length) {
						$li.addClass(self.options.branchClass);
						// expand/collapse class only if they have children
						if (self.options.startCollapsed) $li.addClass(self.options.collapsedClass);
						else $li.addClass(self.options.expandedClass);
					} else {
						$li.addClass(self.options.leafClass);
					}
				})
			}
		},

		_destroy: function() {
			this.element
				.removeData("mjs-nestedSortable")
				.removeData("ui-sortable");
			return $.ui.sortable.prototype._destroy.apply(this, arguments);
		},

		_mouseDrag: function(event) {
			var i, item, itemElement, intersection,
				o = this.options,
				scrolled = false;

			//Compute the helpers position
			this.position = this._generatePosition(event);
			this.positionAbs = this._convertPositionTo("absolute");

			if (!this.lastPositionAbs) {
				this.lastPositionAbs = this.positionAbs;
			}

			//Do scrolling
			if(this.options.scroll) {
				if(this.scrollParent[0] != document && this.scrollParent[0].tagName != 'HTML') {

					if((this.overflowOffset.top + this.scrollParent[0].offsetHeight) - event.pageY < o.scrollSensitivity) {
						this.scrollParent[0].scrollTop = scrolled = this.scrollParent[0].scrollTop + o.scrollSpeed;
					} else if(event.pageY - this.overflowOffset.top < o.scrollSensitivity) {
						this.scrollParent[0].scrollTop = scrolled = this.scrollParent[0].scrollTop - o.scrollSpeed;
					}

					if((this.overflowOffset.left + this.scrollParent[0].offsetWidth) - event.pageX < o.scrollSensitivity) {
						this.scrollParent[0].scrollLeft = scrolled = this.scrollParent[0].scrollLeft + o.scrollSpeed;
					} else if(event.pageX - this.overflowOffset.left < o.scrollSensitivity) {
						this.scrollParent[0].scrollLeft = scrolled = this.scrollParent[0].scrollLeft - o.scrollSpeed;
					}

				} else {

					if(event.pageY - $(document).scrollTop() < o.scrollSensitivity) {
						scrolled = $(document).scrollTop($(document).scrollTop() - o.scrollSpeed);
					} else if($(window).height() - (event.pageY - $(document).scrollTop()) < o.scrollSensitivity) {
						scrolled = $(document).scrollTop($(document).scrollTop() + o.scrollSpeed);
					}

					if(event.pageX - $(document).scrollLeft() < o.scrollSensitivity) {
						scrolled = $(document).scrollLeft($(document).scrollLeft() - o.scrollSpeed);
					} else if($(window).width() - (event.pageX - $(document).scrollLeft()) < o.scrollSensitivity) {
						scrolled = $(document).scrollLeft($(document).scrollLeft() + o.scrollSpeed);
					}

				}

				if(scrolled !== false && $.ui.ddmanager && !o.dropBehaviour)
					$.ui.ddmanager.prepareOffsets(this, event);
			}

			//Regenerate the absolute position used for position checks
			this.positionAbs = this._convertPositionTo("absolute");

			// mjs - find the top offset before rearrangement,
			var previousTopOffset = this.placeholder.offset().top;

			//Set the helper position
			if(!this.options.axis || this.options.axis !== "y") {
				this.helper[0].style.left = this.position.left+"px";
			}
			if(!this.options.axis || this.options.axis !== "x") {
				this.helper[0].style.top = this.position.top+"px";
			}

			// mjs - check and reset hovering state at each cycle
			this.hovering = this.hovering ? this.hovering : null;
			this.mouseentered = this.mouseentered ? this.mouseentered : false;

			// mjs - let's start caching some variables
			var parentItem = (this.placeholder[0].parentNode.parentNode &&
							 $(this.placeholder[0].parentNode.parentNode).closest('.ui-sortable').length)
				       			? $(this.placeholder[0].parentNode.parentNode)
				       			: null,
			    level = this._getLevel(this.placeholder),
			    childLevels = this._getChildLevels(this.helper);

			var newList = document.createElement(o.listType);

			//Rearrange
			for (i = this.items.length - 1; i >= 0; i--) {

				//Cache variables and intersection, continue if no intersection
				item = this.items[i];
				itemElement = item.item[0];
				intersection = this._intersectsWithPointer(item);
				if (!intersection) {
					continue;
				}

				// Only put the placeholder inside the current Container, skip all
				// items form other containers. This works because when moving
				// an item from one container to another the
				// currentContainer is switched before the placeholder is moved.
				//
				// Without this moving items in "sub-sortables" can cause the placeholder to jitter
				// beetween the outer and inner container.
				if (item.instance !== this.currentContainer) {
					continue;
				}

				// cannot intersect with itself
				// no useless actions that have been done before
				// no action if the item moved is the parent of the item checked
				if (itemElement !== this.currentItem[0] &&
					this.placeholder[intersection === 1 ? "next" : "prev"]()[0] !== itemElement &&
					!$.contains(this.placeholder[0], itemElement) &&
					(this.options.type === "semi-dynamic" ? !$.contains(this.element[0], itemElement) : true)
				) {

					// mjs - we are intersecting an element: trigger the mouseenter event and store this state
					if (!this.mouseentered) {
						$(itemElement).mouseenter();
						this.mouseentered = true;
					}

					// mjs - if the element has children and they are hidden, show them after a delay (CSS responsible)
					if (o.isTree && $(itemElement).hasClass(o.collapsedClass) && o.expandOnHover) {
						if (!this.hovering) {
							$(itemElement).addClass(o.hoveringClass);
							var self = this;
							this.hovering = window.setTimeout(function() {
								$(itemElement).removeClass(o.collapsedClass).addClass(o.expandedClass);
								self.refreshPositions();
								self._trigger("expand", event, self._uiHash());
							}, o.expandOnHover);
						}
					}

					this.direction = intersection == 1 ? "down" : "up";

					// mjs - rearrange the elements and reset timeouts and hovering state
					if (this.options.tolerance == "pointer" || this._intersectsWithSides(item)) {
						$(itemElement).mouseleave();
						this.mouseentered = false;
						$(itemElement).removeClass(o.hoveringClass);
						this.hovering && window.clearTimeout(this.hovering);
						this.hovering = null;

						// mjs - do not switch container if it's a root item and 'protectRoot' is true
						// or if it's not a root item but we are trying to make it root
						if (o.protectRoot
							&& ! (this.currentItem[0].parentNode == this.element[0] // it's a root item
								  && itemElement.parentNode != this.element[0]) // it's intersecting a non-root item
						) {
							if (this.currentItem[0].parentNode != this.element[0]
							   	&& itemElement.parentNode == this.element[0]
							) {

								if ( ! $(itemElement).children(o.listType).length) {
									itemElement.appendChild(newList);
									o.isTree && $(itemElement).removeClass(o.leafClass).addClass(o.branchClass + ' ' + o.expandedClass);
								}

								var a = this.direction === "down" ? $(itemElement).prev().children(o.listType) : $(itemElement).children(o.listType);
								if (a[0] !== undefined) {
									this._rearrange(event, null, a);
								}

							} else {
								this._rearrange(event, item);
							}
						} else if ( ! o.protectRoot) {
							this._rearrange(event, item);
						}
					} else {
						break;
					}

					// Clear emtpy ul's/ol's
					this._clearEmpty(itemElement);

					this._trigger("change", event, this._uiHash());
					break;
				}
			}

			// mjs - to find the previous sibling in the list, keep backtracking until we hit a valid list item.
			var previousItem = this.placeholder[0].previousSibling ? $(this.placeholder[0].previousSibling) : null;
			if (previousItem != null) {
				while (previousItem[0].nodeName.toLowerCase() != 'li' || previousItem[0] == this.currentItem[0] || previousItem[0] == this.helper[0]) {
					if (previousItem[0].previousSibling) {
						previousItem = $(previousItem[0].previousSibling);
					} else {
						previousItem = null;
						break;
					}
				}
			}

			// mjs - to find the next sibling in the list, keep stepping forward until we hit a valid list item.
			var nextItem = this.placeholder[0].nextSibling ? $(this.placeholder[0].nextSibling) : null;
			if (nextItem != null) {
				while (nextItem[0].nodeName.toLowerCase() != 'li' || nextItem[0] == this.currentItem[0] || nextItem[0] == this.helper[0]) {
					if (nextItem[0].nextSibling) {
						nextItem = $(nextItem[0].nextSibling);
					} else {
						nextItem = null;
						break;
					}
				}
			}

			this.beyondMaxLevels = 0;

			// mjs - if the item is moved to the left, send it one level up but only if it's at the bottom of the list
			if (parentItem != null
				&& nextItem == null
				&& ! (o.protectRoot && parentItem[0].parentNode == this.element[0])
				&&
					(o.rtl && (this.positionAbs.left + this.helper.outerWidth() > parentItem.offset().left + parentItem.outerWidth())
					 || ! o.rtl && (this.positionAbs.left < parentItem.offset().left))
			) {

				parentItem.after(this.placeholder[0]);
				if (o.isTree && parentItem.children(o.listItem).children('li:visible:not(.ui-sortable-helper)').length < 1) {
					parentItem.removeClass(this.options.branchClass + ' ' + this.options.expandedClass)
							  .addClass(this.options.leafClass);
				}
				this._clearEmpty(parentItem[0]);
				this._trigger("change", event, this._uiHash());
			}
			// mjs - if the item is below a sibling and is moved to the right, make it a child of that sibling
			else if (previousItem != null
					 && ! previousItem.hasClass(o.disableNestingClass)
					 &&
						(previousItem.children(o.listType).length && previousItem.children(o.listType).is(':visible')
						 || ! previousItem.children(o.listType).length)
					 && ! (o.protectRoot && this.currentItem[0].parentNode == this.element[0])
					 &&
						(o.rtl && (this.positionAbs.left + this.helper.outerWidth() < previousItem.offset().left + previousItem.outerWidth() - o.tabSize)
						 || ! o.rtl && (this.positionAbs.left > previousItem.offset().left + o.tabSize))
			) {

				this._isAllowed(previousItem, level, level+childLevels+1);

				if (!previousItem.children(o.listType).length) {
					previousItem[0].appendChild(newList);
					o.isTree && previousItem.removeClass(o.leafClass).addClass(o.branchClass + ' ' + o.expandedClass);
				}

		        // mjs - if this item is being moved from the top, add it to the top of the list.
		        if (previousTopOffset && (previousTopOffset <= previousItem.offset().top)) {
		        	previousItem.children(o.listType).prepend(this.placeholder);
		        }
		        // mjs - otherwise, add it to the bottom of the list.
		        else {
					previousItem.children(o.listType)[0].appendChild(this.placeholder[0]);
				}

				this._trigger("change", event, this._uiHash());
			}
			else {
				this._isAllowed(parentItem, level, level+childLevels);
			}

			//Post events to containers
			this._contactContainers(event);

			//Interconnect with droppables
			if($.ui.ddmanager) {
				$.ui.ddmanager.drag(this, event);
			}

			//Call callbacks
			this._trigger('sort', event, this._uiHash());

			this.lastPositionAbs = this.positionAbs;
			return false;

		},

		_mouseStop: function(event, noPropagation) {

			// mjs - if the item is in a position not allowed, send it back
			if (this.beyondMaxLevels) {

				this.placeholder.removeClass(this.options.errorClass);

				if (this.domPosition.prev) {
					$(this.domPosition.prev).after(this.placeholder);
				} else {
					$(this.domPosition.parent).prepend(this.placeholder);
				}

				this._trigger("revert", event, this._uiHash());

			}


			// mjs - clear the hovering timeout, just to be sure
			$('.'+this.options.hoveringClass).mouseleave().removeClass(this.options.hoveringClass);
			this.mouseentered = false;
			this.hovering && window.clearTimeout(this.hovering);
			this.hovering = null;

			$.ui.sortable.prototype._mouseStop.apply(this, arguments);

		},

		// mjs - this function is slightly modified to make it easier to hover over a collapsed element and have it expand
		_intersectsWithSides: function(item) {

			var half = this.options.isTree ? .8 : .5;

			var isOverBottomHalf = isOverAxis(this.positionAbs.top + this.offset.click.top, item.top + (item.height*half), item.height),
				isOverTopHalf = isOverAxis(this.positionAbs.top + this.offset.click.top, item.top - (item.height*half), item.height),
				isOverRightHalf = isOverAxis(this.positionAbs.left + this.offset.click.left, item.left + (item.width/2), item.width),
				verticalDirection = this._getDragVerticalDirection(),
				horizontalDirection = this._getDragHorizontalDirection();

			if (this.floating && horizontalDirection) {
				return ((horizontalDirection == "right" && isOverRightHalf) || (horizontalDirection == "left" && !isOverRightHalf));
			} else {
				return verticalDirection && ((verticalDirection == "down" && isOverBottomHalf) || (verticalDirection == "up" && isOverTopHalf));
			}

		},

		_contactContainers: function(event) {

			if (this.options.protectRoot && this.currentItem[0].parentNode == this.element[0] ) {
				return;
			}

			$.ui.sortable.prototype._contactContainers.apply(this, arguments);

		},

		_clear: function(event, noPropagation) {

			$.ui.sortable.prototype._clear.apply(this, arguments);

			// mjs - clean last empty ul/ol
			for (var i = this.items.length - 1; i >= 0; i--) {
				var item = this.items[i].item[0];
				this._clearEmpty(item);
			}

		},

		serialize: function(options) {

			var o = $.extend({}, this.options, options),
				items = this._getItemsAsjQuery(o && o.connected),
			    str = [];

			$(items).each(function() {
				var res = ($(o.item || this).attr(o.attribute || 'id') || '')
						.match(o.expression || (/(.+)[-=_](.+)/)),
				    pid = ($(o.item || this).parent(o.listType)
						.parent(o.items)
						.attr(o.attribute || 'id') || '')
						.match(o.expression || (/(.+)[-=_](.+)/));

				if (res) {
					str.push(((o.key || res[1]) + '[' + (o.key && o.expression ? res[1] : res[2]) + ']')
						+ '='
						+ (pid ? (o.key && o.expression ? pid[1] : pid[2]) : o.rootID));
				}
			});

			if(!str.length && o.key) {
				str.push(o.key + '=');
			}

			return str.join('&');

		},

		toHierarchy: function(options) {

			var o = $.extend({}, this.options, options),
				sDepth = o.startDepthCount || 0,
			    ret = [];

			$(this.element).children(o.items).each(function () {
				var level = _recursiveItems(this);
				ret.push(level);
			});

			return ret;

			function _recursiveItems(item) {
				var id = ($(item).attr(o.attribute || 'id') || '').match(o.expression || (/(.+)[-=_](.+)/));
				if (id) {
					var currentItem = {"id" : id[2]};
					if ($(item).children(o.listType).children(o.items).length > 0) {
						currentItem.children = [];
						$(item).children(o.listType).children(o.items).each(function() {
							var level = _recursiveItems(this);
							currentItem.children.push(level);
						});
					}
					return currentItem;
				}
			}
		},

		toArray: function(options) {

			var o = $.extend({}, this.options, options),
				sDepth = o.startDepthCount || 0,
			    ret = [],
			    left = 1;

			if (!o.excludeRoot) {
				ret.push({
					"item_id": o.rootID,
					"parent_id": null,
					"depth": sDepth,
					"left": left,
					"right": ($(o.items, this.element).length + 1) * 2
				});
				left++
			}

			$(this.element).children(o.items).each(function () {
				left = _recursiveArray(this, sDepth + 1, left);
			});

			ret = ret.sort(function(a,b){ return (a.left - b.left); });

			return ret;

			function _recursiveArray(item, depth, left) {

				var right = left + 1,
				    id,
				    pid;

				if ($(item).children(o.listType).children(o.items).length > 0) {
					depth ++;
					$(item).children(o.listType).children(o.items).each(function () {
						right = _recursiveArray($(this), depth, right);
					});
					depth --;
				}

				id = ($(item).attr(o.attribute || 'id')).match(o.expression || (/(.+)[-=_](.+)/));

				if (depth === sDepth + 1) {
					pid = o.rootID;
				} else {
					var parentItem = ($(item).parent(o.listType)
											 .parent(o.items)
											 .attr(o.attribute || 'id'))
											 .match(o.expression || (/(.+)[-=_](.+)/));
					pid = parentItem[2];
				}

				if (id) {
						ret.push({"item_id": id[2], "parent_id": pid, "depth": depth, "left": left, "right": right});
				}

				left = right + 1;
				return left;
			}

		},

		_clearEmpty: function(item) {
			var o = this.options;

			var emptyList = $(item).children(o.listType);

			if (emptyList.length && !emptyList.children().length && !o.doNotClear) {
				o.isTree && $(item).removeClass(o.branchClass + ' ' + o.expandedClass).addClass(o.leafClass);
				emptyList.remove();
			} else if (o.isTree && emptyList.length && emptyList.children().length && emptyList.is(':visible')) {
				$(item).removeClass(o.leafClass).addClass(o.branchClass + ' ' + o.expandedClass);
			} else if (o.isTree && emptyList.length && emptyList.children().length && !emptyList.is(':visible')) {
				$(item).removeClass(o.leafClass).addClass(o.branchClass + ' ' + o.collapsedClass);
			}

		},

		_getLevel: function(item) {

			var level = 1;

			if (this.options.listType) {
				var list = item.closest(this.options.listType);
				while (list && list.length > 0 &&
                    	!list.is('.ui-sortable')) {
					level++;
					list = list.parent().closest(this.options.listType);
				}
			}

			return level;
		},

		_getChildLevels: function(parent, depth) {
			var self = this,
			    o = this.options,
			    result = 0;
			depth = depth || 0;

			$(parent).children(o.listType).children(o.items).each(function (index, child) {
					result = Math.max(self._getChildLevels(child, depth + 1), result);
			});

			return depth ? result + 1 : result;
		},

		_isAllowed: function(parentItem, level, levels) {
			var o = this.options,
				maxLevels = this.placeholder.closest('.ui-sortable').nestedSortable('option', 'maxLevels'); // this takes into account the maxLevels set to the recipient list

			// mjs - is the root protected?
			// mjs - are we nesting too deep?
			if ( ! o.isAllowed(this.placeholder, parentItem, this.currentItem)) {
					this.placeholder.addClass(o.errorClass);
					if (maxLevels < levels && maxLevels != 0) {
						this.beyondMaxLevels = levels - maxLevels;
					} else {
						this.beyondMaxLevels = 1;
					}
			} else {
				if (maxLevels < levels && maxLevels != 0) {
					this.placeholder.addClass(o.errorClass);
					this.beyondMaxLevels = levels - maxLevels;
				} else {
					this.placeholder.removeClass(o.errorClass);
					this.beyondMaxLevels = 0;
				}
			}
		}

	}));

	$.mjs.nestedSortable.prototype.options = $.extend({}, $.ui.sortable.prototype.options, $.mjs.nestedSortable.prototype.options);
})(jQuery);

// Jamroom Developer Module Javascript
// @copyright 2003-2015 by Talldude Networks LLC

/**
 * Get license for skin/module
 * @param type string License Type
 * @param dir string Module/Skin directory name
 */
function jrDeveloper_get_license(type, dir)
{
    var url = core_system_url + '/' + jrDeveloper_url + '/get_license/type=' + jrE(type) + '/dir=' + jrE(dir) + '/__ajax=1';
    $.get(url, function(res) {
        if (typeof res.error != "undefined") {
            $('#zip_license_error').hide();
            jrCore_alert(res.error);
        }
        else if (typeof res.empty != "undefined") {
            $('#zip_license_error').show();
        }
        else {
            $('#zip_license_error').hide();
            $('#zip_license').val(res.success);
        }
    });
}
// Jamroom Marketplace Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Update all items that have updates
 */
function jrMarket_update_all_items(token)
{
    $('#modal_window').modal();
    $('#modal_indicator').show();
    var s = setInterval(function() {
        $.ajax({
            cache: false,
            dataType: 'json',
            url: core_system_url + '/' + jrCore_url + '/form_modal_status/k=' + token + '/__ajax=1',
            success: function(t, a, x) {
                var f = 'jrFormModalSubmit_update_process';
                window[f](t, s);
            },
            error: function(r, t, e) {
                clearInterval(s);
                jrCore_alert('An error was encountered communicating with the server: ' + t + ': ' + e);
            }
        })
    }, 1000);
    $.getJSON(core_system_url + '/' + jrMarket_url + '/update_all_items/__ajax=1', function(r, a, x) {
        clearTimeout(s);
        if (r !== null && typeof r.error !== "undefined") {
            jrCore_alert(r.error);
        }
    });
}

/**
 * Submits a Quick Purchase
 * @return bool
 */
function jrMarket_quick_purchase(type, price, market_id, item)
{
    $(this).attr("disabled", "disabled").addClass('form_button_disabled');
    var iid = '#fsi_' + market_id;
    var mid = Number(market_id);
    $(iid).show(300, function() {
        var values = {
            type: type,
            price: price,
            market_id: market_id,
            item: item
        };
        var url = core_system_url + '/' + jrMarket_url + '/purchase/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: values,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                // Check for error
                if (typeof msg.error !== "undefined") {
                    $(iid).hide(300, function() {
                        jrCore_alert(msg.error);
                    });
                }
                else {
                    url = core_system_url + '/' + jrMarket_url + '/install_item/' + type + '/' + msg.name + '/' + mid + '/license=' + msg.license + '/__ajax=1';
                    jrCore_set_csrf_cookie(url);
                    $.getJSON(url, function(res) {
                        // Check for error
                        if (typeof res.error !== "undefined") {
                            $(iid).hide(300, function() {
                                jrCore_alert(res.error);
                            });
                        }
                        else {
                            if (typeof res.redirect !== "undefined") {
                                // We had an error
                                window.location.reload();
                            }
                            else {
                                window.location = res.url;
                            }
                        }
                    });
                }
            },
            error: function(x, t, e) {
                $(iid).hide(300, function() {
                    jrCore_alert('unable to communicate with server - please try again');
                });
            }
        });
    });
    return false;
}

/**
 * Install a Free Item
 * @return bool
 */
function jrMarket_install_item(type, market_id, item) {
    $(this).attr("disabled", "disabled").addClass('form_button_disabled');
    var iid = '#fsi_' + market_id;
    var mid = Number(market_id);
    $(iid).show(300, function() {
        var url = core_system_url + '/' + jrMarket_url + '/license_item/' + type + '/' + item + '/' + mid +'/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.getJSON(url, function(msg) {
            // Check for error
            if (typeof msg.error !== "undefined") {
                $(iid).hide(300, function() {
                    jrCore_alert(msg.error);
                });
            }
            else if (typeof msg.license !== "undefined" && msg.license.length > 0) {
                url = core_system_url + '/' + jrMarket_url + '/install_item/' + type + '/' + item + '/' + mid + '/license=' + msg.license + '/__ajax=1';
                jrCore_set_csrf_cookie(url);
                $.getJSON(url, function(res) {
                    // Check for error
                    if (typeof res.error !== "undefined") {
                        $(iid).hide(300, function() {
                            jrCore_alert(res.error);
                        });
                    }
                    else {
                        if (typeof res.redirect !== "undefined") {
                            // We had an error
                            window.location.reload();
                        }
                        else {
                            window.location = res.url;
                        }
                    }
                });
            }
            else {
                jrCore_alert('Unable to retrieve a license for the item - please try again');
            }
        });
    });
    return false;
}

/**
 * Update/Reload an existing item
 * @return bool
 */
function jrMarket_update_item(type, item, reload, market_id) {
    var bid = '#u' + item;
    var img = $(bid).prev('img');
    $(bid).fadeOut(300, function() {
        $('input').attr("disabled", "disabled").addClass('form_button_disabled');
        $(img).show(300, function() {
            var url = core_system_url + '/' + jrMarket_url + '/update_item/' + type + '/' + item;
            if (typeof reload != "undefined" && reload == 'reload') {
                url = url + '/reload';
            }
            url = url + '/id=' + Number(market_id) + '/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.getJSON(url, function(res) {
                window.location = res.url;
            });
        });
    });
    return false;
}

// Jamroom Support Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Load support options
 */
function jrSupport_view_options(type, name)
{
    if (typeof name !== "undefined" && name.length > 0) {
        $('#' + type + '_submit_indicator').show(200, function()
        {
            $.get(core_system_url + '/' + jrSupport_url + '/options/' + type + '/' + name + '/__ajax=1', function(data)
            {
                $('#' + type + '_submit_indicator').hide();
                $('#' + type + '_info').html(data).fadeTo(150, 1);
            });
        });
    }
}
