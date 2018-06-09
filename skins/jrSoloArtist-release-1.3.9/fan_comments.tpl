{foreach from=$_items item="item"}
    <div class="item" style="overflow:hidden">
        <div style="float:left;padding-right:12px;">
            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" alt=$item.user_name class="action_item_user_img iloutline"}
        </div>
        <div>
            {jrCore_item_delete_button module="jrComment" profile_id=$item._profile_id item_id=$item._item_id}
        </div>

        <div>
            {if $item.profile_url == $_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}
                <span class="media_title" style="display:inline-block;">{$item._created|jrCore_date_format} <a href="{$jamroom_url}">@{$item.profile_name}</a>&nbsp;Commented On - <a href="{$item.comment_url}" title="{$item.comment_item_title}">{if strlen($item.comment_item_title) > 25}{$item.comment_item_title|truncate:25:"...":false}{else}{$item.comment_item_title}{/if}</a>:</span><br>
            {else}
                <span class="media_title" style="display:inline-block;">{$item._created|jrCore_date_format} <a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_name}</a>&nbsp;Commented On - <a href="{$item.comment_url}" title="{$item.comment_item_title}">{if strlen($item.comment_item_title) > 25}{$item.comment_item_title|truncate:25:"...":false}{else}{$item.comment_item_title}{/if}</a>:</span><br>
            {/if}
            <span class="normal">{if strlen($item.comment_text) > 200}{$item.comment_text|jrCore_format_string|jrCore_convert_at_tags|truncate:200:"...":false}{else}{$item.comment_text|jrCore_format_string|jrCore_convert_at_tags}{/if}</span>
        </div>
    </div>
    <div class="divider">&nbsp;</div>
{/foreach}
