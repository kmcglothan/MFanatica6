<div id="widget-container" class="container">
    <div class="row">

        <div class="col3">
            <div style="margin-right:12px">
                <table class="page_banner">
                    <tr>
                        <td class="page_banner_left">
                            Widgets
                        </td>
                    </tr>
                </table>
            </div>
            <dl class="sb-accordion" style="overflow-y:auto">
                {foreach $_widgets as $_w}
                    <dd class="sb-dd" onclick="jrSiteBuilder_widget_form('widget_id-{$_w.widget_id}','{$_w.module}','{$_w.name}');">
                        {if $_w.active == '1'}
                        <div id="{$_w.module}-{$_w.name}" class="sb-item-row sb-item-row-active sb-item-row-default">
                        {else}
                        <div id="{$_w.module}-{$_w.name}" class="sb-item-row">
                        {/if}
                            <div class="sb-item-icon">
                                <img src="{$_w.icon}" width="28" height="28" alt="{$_w.title|jrCore_entity_string}">
                            </div>
                            <div class="sb-item-entry">{$_w.title|jrCore_entity_string}</div>
                        </div>
                    </dd>
                {/foreach}
            </dl>

        </div>

        <div class="col9 last">
            <div id="sb-widget-settings">{* Widget Settings form loads here *}</div>
            <div id="sb-widget-work">{* Widget content form loads here *}</div>
        </div>

    </div>
</div>
