{$i = 0}
{foreach $_rows as $_row}
    <div class="row">

        {foreach $_row._cols as $col_num => $_col}
            {if $_col.width == 0}
                {continue}
            {/if}
            {$t = ''}
            <div class="col{$_col.width}">

                <div id="sb-widget-col-{$i}" class="sb-widget-col" data-location="{$i}">

                    {if is_array($config[$i]) && $config[$i].ct_layout == 'tab' && count($layout[$i]) > 1}
                        {$t = ' style="display:none"'}
                        <div class="sb-container-tabs">
                        <table><tr><td class="page_tab_bar_holder">
                        <ul id="c{$i}" class="page_tab_bar">
                        {foreach $layout[$i] as $_widget}
                            {if $_widget@first}
                                <li id="t{$_widget.widget_id}" class="page_tab page_tab_first page_tab_active sb-tab"><a onclick="jrSiteBuilder_load_tab('{$_page.page_id}', '{$i}', '{$_widget.widget_id}')">{$_widget.widget_title|default:$_widget.widget_name}</a></li>
                            {elseif $_widget@last}
                                <li id="t{$_widget.widget_id}" class="page_tab page_tab_last sb-tab"><a onclick="jrSiteBuilder_load_tab('{$_page.page_id}', '{$i}', '{$_widget.widget_id}')">{$_widget.widget_title|default:$_widget.widget_name}</a></li>
                            {else}
                                <li id="t{$_widget.widget_id}" class="page_tab sb-tab"><a onclick="jrSiteBuilder_load_tab('{$_page.page_id}', '{$i}', '{$_widget.widget_id}')">{$_widget.widget_title|default:$_widget.widget_name}</a></li>
                            {/if}
                        {/foreach}
                        </ul></td></tr></table>
                        </div>
                    {/if}

                    {if is_array($config[$i]) && $config[$i].ct_height > 0}
                    <ul id="l{$_page.page_id}-location-{$i}" class="connectedSortable" style="height:{$config[$i].ct_height}px;clear:left">
                    {else}
                    <ul id="l{$_page.page_id}-location-{$i}" class="connectedSortable" style="clear: left;">
                    {/if}

                        {* THE COL IS: {$i} *}
                        {if is_array($layout[$i])}
                            {$pos = 0}
                            {foreach $layout[$i] as $_widget}
                                {if strlen($_widget.content) == 0 && !jrUser_is_master()}
                                    {continue}
                                {/if}
                                {if is_array($config[$i]) && $config[$i].ct_layout == 'tab'}
                                    {if $_widget@first}
                                    <li id="w{$_widget.widget_id}" class="sb-drag-handle sb-content-active" data-id="{$_widget.widget_id}">
                                    {else}
                                    <li id="w{$_widget.widget_id}" class="sb-drag-handle" style="display:none" data-id="{$_widget.widget_id}">
                                    {/if}
                                {else}
                                    <li id="w{$_widget.widget_id}" class="sb-drag-handle" data-id="{$_widget.widget_id}">
                                {/if}
                                    <div id="c{$_widget.widget_id}" class="sb-widget-block box">
                                        {if jrUser_is_master()}
                                        <div class="sb-widget-controls" style="display:none;">
                                            <a title="modify this widget" onclick="jrSiteBuilder_modify_widget('widget_id-{$_widget.widget_id}')">{jrCore_icon icon="gear" size=20}</a>
                                            <a title="clone this widget" onclick="jrSiteBuilder_clone_widget('{$_widget.widget_id}')">{jrCore_icon icon="sb_clone" size=20}</a>
                                            <a title="delete this widget" onclick="jrSiteBuilder_delete_widget('widget_id-{$_widget.widget_id}')">{jrCore_icon icon="trash" size=20}</a>
                                            {if $_col.width > 2 && strlen($_widget.widget_groups) > 0 && $_widget.widget_groups != 'all'}<br><small>{$_widget.widget_groups}</small>{/if}
                                        </div>
                                        {/if}
                                        {if $_col.width == 1}
                                            <div class="title sb-widget-title"{$t}>
                                                <h2>&nbsp;</h2>
                                            </div>
                                        {elseif strlen($_widget.widget_module) > 0}

                                            {if $_widget.widget_module == 'jrCore'}
                                                {$data = $_widget.widget_data|json_decode:1}
                                                {jrBeatSlinger_sort template="icons.tpl" nav_mode=$data.list_module title=$_widget.widget_title} <span>{$_widget.widget_title}</span>

                                            {else}
                                                {jrBeatSlinger_sort template="icons.tpl" nav_mode=$_widget.widget_module title=$_widget.widget_title} <span>{$_widget.widget_title}</span>
                                            {/if}
                                        {/if}
                                        {if jrUser_is_master()}
                                            <div class="title sb-widget-title" style="display:none" id="actions_tab">
                                                {if is_array($_registered_widgets[$_widget.widget_module][$_widget.widget_name])}
                                                    {* some widgets also register 'requires', like jrYouTube, hence this check. *}
                                                    <span id="faq_tab" class="sb-widget-type-info" data-oid="{$_widget.widget_display_number}">{$_widget.widget_display_number}.{$pos} {$_registered_widgets[$_widget.widget_module][$_widget.widget_name]['title']}</span>
                                                {else}
                                                    <span id="faq_tab" class="sb-widget-type-info" data-oid="{$_widget.widget_display_number}">{$_widget.widget_display_number}.{$pos} {$_registered_widgets[$_widget.widget_module][$_widget.widget_name]}</span>
                                                {/if}
                                            </div>
                                            {$pos = $pos + 1}
                                        {/if}
                                        <div id="widget_id-{$_widget.widget_id}" data-id="{$_widget.widget_id}" class="sb-widget-content box_body">
                                            <div class="wrap">
                                                {$_widget.content}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            {/foreach}
                        {/if}
                    </ul>

                    {* container modify and add add widget button *}
                    {if jrUser_is_master()}
                    {$c_tag = '&nbsp;&nbsp;container settings'}
                    {$w_tag = 'add widget&nbsp;&nbsp;'}
                    {if $_col.width < 3}
                        {$c_tag = ''}
                        {if $_col.width == 1}
                            {$w_tag = ''}
                        {/if}
                    {/if}

                    <div class="sb-mod-container-btn" style="display:none" title="modify container settings"><a onclick="jrSiteBuilder_modify_container('{$_page.page_id}-location-{$i}');">{jrCore_icon icon="gear" size=20}</a>{$c_tag}</div>
                    <div class="sb-add-widget-btn" style="display:none" title="add a new widget to this container">{$w_tag}{jrCore_icon icon="plus" size=20}</div>
                    {/if}

                </div>

            </div>

            {* consecutive cols *}
            {if $_col.width > 0}
                {$i = $i + 1}
            {/if}

        {/foreach}

    </div>
{/foreach}
