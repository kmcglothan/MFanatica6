{foreach $comments as $item}
    <div class="block">
        <div style="overflow:hidden">
            <div style="float:left;padding-right:12px;">
                <a href="{$jamroom_url}/{$item['user'].profile_url}" title="{$item['user'].profile_name}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item.comment_user_id size="small" alt=$item['user'].profile_url class="action_item_user_img iloutline"}</a>

            </div>
            <div>
                <span class="media_title" style="display:inline-block;"> {$item.comment_created|jrCore_date_format:"relative"} :</span><br>
                <span class="normal">{$item.comment_text|jrCore_format_string:$item.profile_quota_id}</span>
            </div>
        </div>
    </div>
{/foreach}