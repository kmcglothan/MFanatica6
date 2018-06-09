{jrCore_module_url module="jrSiteBuilder" assign="murl"}

<style type="text/css">

    .placeholder {
        outline: 1px dashed #CCCCCC;
    }
    .mjs-nestedSortable-error {
        background: #fbe3e4;
        border-color: transparent;
    }
    ol {
        margin: 0;
        padding: 0;
    }

    ol.sortable, ol.sortable ol {
        margin: 0 0 0 25px;
        padding: 0;
        list-style-type: none;
        cursor:pointer;
    }

    ol.sortable {
        margin: 0;
    }

    .sortable li {
        margin: 5px 0 0 0;
        padding: 0;
    }

    li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {
        border-color: #999;
        background: #fafafa;
    }

    .sortable li.mjs-nestedSortable-collapsed > ol {
        display: none;
    }

    .sortable li.active_menu > div:first-of-type {
        background: #888888 !important;
        color: #ffffff !important;
    }

    #properties_form label {
        display: inline-block;
        width: 20%;
    }

    #menu-entry-holder {
        margin-right: 12px;
        height: 100%;
        /*overflow: scroll;*/
    }
</style>


<div class="container">

    <div class="page_notice_drop">
        <div class="page_notice form_notice success" id="jrSiteBuilder_update_success">The menu was successfully updated</div>
    </div>

    <div class="row" id="menu_editor">

        <div class="col4">

            <div style="margin-right: 12px">
            <table class="page_banner">
                <tr>
                    <td class="page_banner_left">Menu Entries</td>
                    <td class="page_banner_right">
                        {jrCore_module_url module="jrImage" assign="iurl"}
                        <img width="24" height="24" alt="working..." src="{$jamroom_url}/{$iurl}/img/skin/{$_conf.jrCore_active_skin}/submit.gif" id="reset_menu_submit_indicator" style="display: none;">
                        {if count($_list) > 0}
                        <input type="button" value="Reset to Default" class="form_button" onclick="jrSiteBuilder_reset_menu()"></td>
                        {/if}
                 </tr>
            </table>
            </div>

            <div id="menu-entry-holder">

            <ol class="sortable">
            {foreach $_list as $_l0}
                <li id="list_{$_l0.menu_id}" data-id="{$_l0.menu_id}">

                    <div style="position:relative">
                        <div class="sb-menu-entry">
                            {if isset($_l0._children)}
                                <a class="sb-menu-expand-icon">{jrCore_icon icon="plus" size="14"}</a>
                            {else}
                                <div class="sb-menu-noexpand"></div>
                            {/if}
                            <a id="t{$_l0.menu_id}" onclick="jrSiteBuilder_get_menu_options('{$_l0.menu_id}')">{$_l0.menu_title}</a>
                        </div>
                        <div class="sb-menu-delete-icon" onclick="jrSiteBuilder_delete_menu_entry('{$_l0.menu_id}')">{jrCore_icon icon="close" size="14"}</div>
                    </div>

                    {if isset($_l0._children)}
                        <ol>
                        {foreach $_l0._children as $_l1}
                            <li id="list_{$_l1.menu_id}" data-id="{$_l1.menu_id}">

                                <div style="position:relative">
                                    <div class="sb-menu-entry">
                                        {if isset($_l1._children)}
                                            <a class="sb-menu-expand-icon">{jrCore_icon icon="plus" size="14"}</a>
                                        {else}
                                            <div class="sb-menu-noexpand"></div>
                                        {/if}
                                        <a id="t{$_l1.menu_id}" onclick="jrSiteBuilder_get_menu_options('{$_l1.menu_id}')">{$_l1.menu_title}</a>
                                    </div>
                                    <div class="sb-menu-delete-icon" onclick="jrSiteBuilder_delete_menu_entry('{$_l1.menu_id}')">{jrCore_icon icon="close" size="14"}</div>
                                </div>

                                {if isset($_l1._children)}
                                    <ol>
                                    {foreach $_l1._children as $_l2}

                                        <li id="list_{$_l2.menu_id}" data-id="{$_l2.menu_id}">
                                            <div style="position:relative">
                                                <div class="sb-menu-entry">
                                                    <a id="t{$_l2.menu_id}" onclick="jrSiteBuilder_get_menu_options('{$_l2.menu_id}')">{$_l2.menu_title}</a>
                                                </div>
                                                <div class="sb-menu-delete-icon" onclick="jrSiteBuilder_delete_menu_entry('{$_l2.menu_id}')">{jrCore_icon icon="close" size="14"}</div>
                                            </div>
                                        </li>

                                    {/foreach}
                                    </ol>
                                {/if}
                            </li>
                        {/foreach}
                        </ol>
                    {/if}

                </li>
            {/foreach}

                {* New Menu item *}
                <li id="list_new" style="display:none">
                    <div style="position:relative">
                        <div class="sb-menu-entry">
                            <input id="sb-new-entry" type="text" class="form_text sb-menu-text" name="menu_title" onkeypress="if (event && ( event.keyCode == 13 || event.keyCode == 9)) { jrSiteBuilder_create_menu_entry(this) }">
                            <button class="form_button sb-menu-add-button" onclick="jrSiteBuilder_create_menu_entry($('#sb-new-entry'))">Add</button>
                        </div>
                    </div>
                </li>

            </ol>

            </div>

            <div style="text-align:center;padding-top:24px">
                <input type="button" class="form_button" value="add new menu entry" onclick="$('#list_new').show();$('#sb-new-entry').focus();">
                {if jrCore_is_developer_mode()}
                <br><br><input type="button" class="form_button" value="Developer: Show Menu Code" onclick="jrSiteBuilder_menu_code()">
                {/if}
            </div>

        </div>

        <div class="col8 last">

            <table class="page_banner">
                <tr>
                    <td id="sb-menu-options-title" class="page_banner_left">Selected Menu Entry Options</td>
                    <td class="page_banner_right"><input type="button" value="close" class="form_button" onclick="jrSiteBuilder_close_menu_modal()"></td>
                </tr>
            </table>

            <div id="sb-menu-options-form"></div>

        </div>

    </div>


</div>


{* New entry template *}
<div id="sb-new-entry-template" style="display:none">
<li id="list_MENU_ID" data-id="MENU_ID">
    <div style="position:relative">
        <div class="sb-menu-entry">
            <div class="sb-menu-noexpand"></div>
            <a id="tMENU_ID" onclick="jrSiteBuilder_get_menu_options('MENU_ID')">MENU_TITLE</a>
        </div>
        <div class="sb-menu-delete-icon" onclick="jrSiteBuilder_delete_menu_entry('MENU_ID')">{jrCore_icon icon="close" size="14"}</div>
    </div>
</li>
</div>

<script type="text/javascript">

    $(document).ready(function() {

        $('ol.sortable').nestedSortable( {

            forcePlaceholderSize: true,
            handle: 'div',
            helper: 'clone',
            items: 'li',
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            maxLevels: 3,

            isTree: true,
            expandOnHover: 700,
            startCollapsed: true,

            stop: function()
            {
                var p = $('ol.sortable').nestedSortable('serialize');
                var u = core_system_url + '/' + jrSiteBuilder_url + '/menu_order_update?__ajax=1&' + p;
                jrCore_set_csrf_cookie(u);
                $.get(u, function() {
                    window.name = 'reload';
                });
                return true;
            }

        });

        $('.sb-menu-expand-icon').on('click', function () {
            $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
        });

    });

</script>
