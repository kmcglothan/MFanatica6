{jrCore_module_url module=$item.action_data.like_module assign="murl"}

<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.action_data.profile_url}')">
        {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="icon" crop="auto" class="img_scale" alt=$item.profile_name}
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>
        {if $item.action_data.like_action == 'dislike'}
            {jrCore_lang module="jrLike" id="16" default="Disliked"}
        {else}
            {jrCore_lang module="jrLike" id="15" default="Liked"}
        {/if}
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.action_original_data.profile_url}">@{$item.action_original_data.profile_url}'s</a></span> {jrCore_lang module=$item.action_data.like_module id="menu"}
        <br>
        <span class="action_time">{$item.action_data.like_created|jrCore_date_format:"relative"}</span>

    </div>
</div>

<div class="item_media">
    <div class="wrap">

        {if $item.action_original_data.action_module == 'jrAction'}

            {$item.action_original_data.action_html}

        {elseif isset($item.action_original_item_url)}

            {if strlen($item.action_original_data.comment_text) > 0}
                <div style="padding: 0 0 1em;">
                    {$item.action_original_data.comment_text}
                </div>
            {/if}

            {jrCore_module_url module=$item.action_original_module assign="url"}
            <span class="action_item_title"><a href="{$item.action_original_item_url}">{$item.action_original_title}</a></span>

        {elseif isset($item.action_original_title_url)}

            {jrCore_module_url module=$item.action_original_module assign="curl"}
            {if jrCore_module_is_active('jrUrlScan') && is_file("{$jamroom_dir}/modules/{$item.action_original_module}/templates/urlscan_player.tpl")}
                {$item_url = "{$jamroom_url}/{$item.action_original_data.profile_url}/{$curl}/{$item.action_original_data._item_id}/{$item.action_original_title_url}"}
                {$item_url|jrUrlScan_replace_urls}
            {else}
                <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_original_data.profile_url}/{$curl}/{$item.action_original_data._item_id}/{$item.action_original_title_url}">{$item.action_original_title}</a></span>
            {/if}

        {elseif strlen($item.action_original_html) > 0}

            {$item.action_original_html}

        {/if}
    </div>
</div>
