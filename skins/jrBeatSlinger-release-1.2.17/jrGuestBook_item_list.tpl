{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="item">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" class="action_item_user_img iloutline img_scale"}
                    </div>
                </div>
                <div class="col8">
                    <div class="p10">
                        <span class="info"><a href="{$jamroom_url}/{$item.profile_url}">@{$item.user_name}</a></span>, <span class="info_c">{$item._created|jrCore_date_format}</span><br>
                        <p><span class="normal">{$item.guestbook_text|jrCore_format_string:$item.profile_quota_id}</span></p>
                    </div>
                </div>
                <div class="col2 last">
                    <div class="block_config">
                        {if jrProfile_is_profile_owner($item.guestbook_owner_id)}
                            {jrCore_item_update_button module="jrGuestBook" profile_id=$item.guestbook_owner_id item_id=$item._item_id}
                            {jrCore_item_delete_button module="jrGuestBook" profile_id=$item.guestbook_owner_id item_id=$item._item_id}
                        {elseif jrProfile_is_profile_owner($item._profile_id) || jrUser_can_edit_item($item)}
                            {jrCore_item_update_button module="jrGuestBook" profile_id=$item._profile_id item_id=$item._item_id}
                            {jrCore_item_delete_button module="jrGuestBook" profile_id=$item._profile_id item_id=$item._item_id}
                        {/if}
                    </div>
                </div>
            </div>
        </div>

    </div>
    {/foreach}
{else}
    <div class="item">
        <table>
            <tr>
                <td style="width:100%">
                    <span class="media_title">{jrCore_lang module="jrGuestBook" id="17" default="No entries yet - Be the first"}</span>
                </td>
            </tr>
        </table>
    </div>
{/if}

