<div id="sb-page-work">
{jrCore_module_url module="jrSiteBuilder" assign="murl"}

<table class="page_banner">
    <tr>
        <td class="page_banner_left">Edit Page Layout</td>
        <td class="page_banner_right">
            <input type="button" value="page settings" class="form_button" onclick="jrSiteBuilder_modify_page_settings('{$page_id}')">
            <input type="button" value="close" class="form_button" onclick="jrSiteBuilder_close_page_modal(this)">
        </td>
    </tr>
</table>

<div class="row">

    <div class="col10">

        <div class="row">
            <div id="eventslider"></div>
        </div>

        <div id="layout-row">
            <div class="row">
                <div class="first-col center col3">
                    <div class="new-cell">3</div>
                </div>
                <div class="second-col center col6">
                    <div class="new-cell">6</div>
                </div>
                <div class="third-col center col3">
                    <div class="new-cell">3</div>
                </div>
            </div>
        </div>

    </div>

    <div class="col2 last">
        <div style="padding:26px 32px 0 0">
            <div class="sb-row-button" onclick="jrSiteBuilder_save_layout_row('{$page_id}');">Add This Row</div>
        </div>
    </div>

</div>

<table class="page_banner">
    <tr>
        <td class="page_banner_left">Existing Layout</td>
    </tr>
</table>

<form action="{$jamroom_url}/{$murl}/modify_page_save/id={$page_id}" id="panel_update_form" class="jrform">
<input id="page_row_count" type="hidden" name="total_rows" value="{$page_row_count}">

    <div id="saved-rows">

        {if count($_existing_layout) > 0}
            <ul class="sortable list">
            {foreach $_existing_layout as $row => $_cols}
                <li data-id="{$row}">
                <div class="row">

                    <div class="col10">
                        <div class="saved-row">
                            <div class="row">
                                <div class="first-col center col{$_cols[0].width}">
                                    <div class="new-cell">{$_cols[0].width}</div>
                                </div>
                                <div class="second-col center col{$_cols[1].width}">
                                    <div class="new-cell">{$_cols[1].width}</div>
                                </div>
                                <div class="third-col center col{$_cols[2].width}">
                                    <div class="new-cell">{$_cols[2].width}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col2 last">
                        <div style="padding:8px 32px 0 0">
                            <div class="sb-row-button" onclick="jrSiteBuilder_delete_layout_row(this)">Delete This Row</div>
                            <input type="hidden" name="new-layout[]" value="{$_cols[0].width}-{$_cols[1].width}-{$_cols[2].width}">
                        </div>
                    </div>

                </div>
                </li>
            {/foreach}
            </ul>
        {/if}

    </div>

    <div class="form_submit_section" style="margin-top:20px">
        <img id="lfsi" width="24" height="24" alt="working..." src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" style="display:none">
        <input id="save_button" class="form_button form_button_disabled" type="button" value="Save Page Layout" disabled="disabled" onclick="jrSiteBuilder_save_page_layout('{$page_id}');">
    </div>

</form>


<style type="text/css">
    .sortable {
        list-style:none outside none;
        margin: auto;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .sortable li {
        list-style: none;
        cursor: move;
    }
    li.sortable-placeholder {
        border: 1px dashed #BBB;
        background: none;
        height: 60px;
        margin: 12px;
    }
</style>

<script type="text/javascript">

    $('#eventslider').noUiSlider({
        start: [3, 9],
        step: 1,
        behaviour: 'drag-tap',
        connect: true,
        range: {
            'min': [0],
            'max': [12]
        }
    }).on({
        set: function(e, u)
        {
            jrSiteBuilder_set_boxes(e, u)
        }
    });

    jrSiteBuilder_enable_layout_drag('{$page_id}');

</script>


{* This is a template - do not remove *}
<div id="row-template" style="display:none">
    <li>
    <div class="row">

        <div class="col10">
            <div class="saved-row">
                <div class="row">
                    <div class="first-col center">
                        <div class="new-cell"></div>
                    </div>
                    <div class="second-col center">
                        <div class="new-cell"></div>
                    </div>
                    <div class="third-col center">
                        <div class="new-cell"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col2 last">
            <div style="padding:8px 32px 0 0">
                <div class="sb-row-button" onclick="jrSiteBuilder_delete_layout_row(this)">Delete This Row</div>
                <input type="hidden" name="new-layout[]" value="">
            </div>
        </div>

    </div>
    </li>
</div>
</div>
