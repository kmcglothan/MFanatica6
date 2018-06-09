{if isset($_items)}

    {foreach from=$_items item="item"}

        {jrCore_module_url module='jrAction' assign="murl"}
        {if strlen($item.action_module) > 0}
            {jrCore_module_url module=$item.action_module assign="aurl"}
        {/if}

        {* Shared Action *}
        {if isset($item.action_shared)}

            <div class="action">
                <div class="wrap">
                    <div class="action_info">
                        <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name}
                        </div>
                        <div class="action_data">
                            <div class="action_delete">
                                {jrCore_item_update_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                                {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                            </div>
                            <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>
                            {jrCore_lang skin="jrMSkin" id="117" default="shared"} <span class="action_user_name"><a>{$item.action_original_data.profile_url}'s</a></span>
                            {if strlen($item.action_original_module) > 0}
                                {jrCore_lang module=$item.action_original_module id="menu"}
                            {else}
                                {jrCore_lang module=$item.action_module id="menu"}
                            {/if}<br>
                            <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>
                        </div>
                    </div>

                    {$pad_style = ''}
                    {if strlen($item.action_text) > 0 }
                        <div style="padding: 0 0 1em">
                            <div class="item_media action_text clearfix">
                                {if isset($item.action_text)}
                                    {$item.action_text|jrCore_format_string:$item.profile_quota_id}
                                {/if}
                            </div>
                        </div>
                        {$pad_style = 'padding-top:0'}
                    {/if}

                    {if strlen($item.action_original_html) > 0}
                        <div style="{$pad_style}">
                            <div class="item_media">
                                <div class="wrap">
                                    {$item.action_original_html|jrUrlScan_replace_urls}
                                </div>
                            </div>
                        </div>
                    {/if}

                    <div class="action_feedback">
                        {jrMSkin_feedback_buttons module="jrAction" item=$item timeline=true disable_share=true}
                    </div>
                </div>
            </div>


            {* Activity Update *}
        {elseif $item.action_module == 'jrAction' && isset($item.action_text)}

            <div class="action">
                <div class="wrap">
                    <div class="action_info">
                        <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name}
                        </div>
                        <div class="action_data">
                            <div class="action_delete">
                                {jrCore_item_update_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                                {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                            </div>
                            <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>
                            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">Posted an update</a></span><br>
                            <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>
                        </div>
                    </div>
                    <div class="action_media">
                        <div class="item_media action_text clearfix">
                            {$item.action_text|jrCore_format_string:$item.profile_quota_id}
                        </div>
                    </div>
                    <div class="action_feedback">
                        {jrMSkin_feedback_buttons module="jrAction" item=$item timeline=true}
                    </div>
                </div>
            </div>


            {* Module Actions *}
        {elseif isset($item.action_html)}

            <div class="action">

                <div class="wrap">
                    {$item.action_html}

                    <div class="action_feedback">
                        {jrMSkin_feedback_buttons module=$item.action_module item_id=$item.action_item_id item=$item timeline=true}
                    </div>
                </div>

            </div>

        {/if}

        {if $item.list_rank == 1}
            <input type="hidden" id="last_item_id" value="{$item._item_id}">
        {/if}


    {/foreach}
{else}
    <div class="box">
        <div class="box_body" style="border-radius: 4px">
            <div class="wrap">
                <div class="item_media no">
                    {jrCore_include template="no_items.tpl"}
                </div>
            </div>
        </div>
    </div>
{/if}
