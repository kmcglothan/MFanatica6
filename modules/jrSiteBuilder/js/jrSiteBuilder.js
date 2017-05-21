/**
 * jrSiteBuilder Javascript functions
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
        if (window.name == 'reload') {
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
    $.get(u, function(r)
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
    if (c == id) {
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
    $.post(u, { page_url: p }, function(r)
    {
        jrSetCookie('sb-page-layout-reload', Number(r.pid), 1);
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
        $.post(url, { panel_order: o }, function()
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
            if (typeof d.error != "undefined") {
                alert(d.error);
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
    var d = $('#' + i);
    if (d.html().length > 1 && !confirm('Are you sure you want to delete this widget?')) {
        return false;
    }
    var w = d.data('id');
    var u = core_system_url + '/' + jrSiteBuilder_url + '/delete_widget_save/html_id=' + i + '/__ajax=1';
    jrCore_set_csrf_cookie(u);
    $.get(u, function()
    {
        $('#w' + w).remove();
        jrSiteBuilder_changes_made();
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
 * Load new widget content into a container
 * @param p {int} page_id
 * @param l {int} page location
 * @param i {int} widget_id
 */
function jrSiteBuilder_load_tab(p, l, i)
{
    $('#c' + l + ' li').removeClass('page_tab_active');
    $('#t' + i).addClass('page_tab_active');
    $('#l' + p + '-location-' + l + ' .sb-content-active').removeClass('sb-content-active').fadeOut(100, function()
    {
        $('#w' + i).fadeIn(100).addClass('sb-content-active');
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
                        alert(r.error);
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
            if (confirm('Reload the template content with the default template?')) {
                cm.setValue(r.code);
            }
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
    $('#save_form_submit_indicator').show(300, function() {
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
                    alert(r.error);
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
    if (typeof(filename) == "undefined" || filename == null || filename.length <= 1) {
        $('#messages').html('no file name set, or name too short.').addClass('error');
        return false;
    }
    var url = core_system_url + '/' + jrSiteBuilder_url + '/save_template/__ajax=1';
    var html = cm.getValue();
    jrCore_set_csrf_cookie(url);
    $('#save_form_submit_indicator').show(300, function() {
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
                    alert(r.error);
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
function jrSiteBuilder_reset_menu() {

    if (!confirm('Are you sure you want to reset the menu to its skin default state?')) {
        return false;
    }

    var url = core_system_url + '/' + jrSiteBuilder_url + '/reset_menu/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $('#reset_menu_submit_indicator').show(300, function () {
        $.ajax({
            type: 'POST',
            url: url,
            cache: false,
            dataType: 'json',
            success: function (r)
            {
                if (typeof r.error !== "undefined") {
                    alert(r.error);
                }
                else {
                    window.location.reload();
                }
                return true;
            }
        });
    });

}

/**
 * show the .tpl code to build the current menu structure
 */
function jrSiteBuilder_menu_code() {

    var s = $('#sb-menu-options-form');
    $(s).fadeOut(100, function ()
    {
        u = core_system_url + '/' + jrSiteBuilder_url + '/menu_code/__ajax=1';
        jrCore_set_csrf_cookie(u);
        $(s).load(u, function ()
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
    $('#sb-json-spinner').show(300, function () {
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
