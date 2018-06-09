{jrCore_module_url module="jrGroup" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="item">
            <div class="container">
                <div class="row">
                    <div class="col2">
                        {if $item.group_private == 'on'}
                            <div class="p5 center error">
                                <span class="info">{jrCore_lang module="jrGroup" id="20" default="Private Group"}</span>
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.group_title_url}">{jrCore_module_function function="jrImage_display" module="jrGroup" type="group_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.group_title width=false height=false}</a>
                            </div>
                        {else}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.group_title_url}">{jrCore_module_function function="jrImage_display" module="jrGroup" type="group_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.group_title width=false height=false}</a>
                        {/if}
                    </div>
                    <div class="col8">
                        <div style="padding-left:28px">
                            <div style="overflow-wrap:break-word">
                                <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.group_title_url}">{$item.group_title}</a></h2><br>
                                <span class="info">{jrCore_lang module="jrGroup" id="13" default="Members"}:</span>&nbsp;<span class="info_c">{$item.group_member_count|default:0}</span><br>
                                <span class="info">{jrCore_lang module="jrGroup" id="14" default="Description"}:</span>&nbsp;<span class="info_c">{$item.group_description|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:75}</span>
                                {if $item.group_private == 'on'}
                                    <br><span class="info">{jrCore_lang module="jrGroup" id="20" default="Private Group"}</span>
                                {/if}
                            </div>
                        </div>
                    </div>
                    <div class="col2 last">
                        <div class="block_config">
                            {jrCore_item_list_buttons module="jrGroup" item=$item}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
