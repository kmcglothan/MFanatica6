
{jrCore_module_url module="jrProfile" assign="murl"}


{if strlen($item.action_original_profile_url) > 0}
    {$profile = jrCore_db_get_item('jrProfile', $item.action_original_profile_id)}
{else}
    {$profile = jrCore_db_get_item('jrProfile', $item._profile_id)}
{/if}

<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$profile.profile_url}')">
        {jrCore_module_function
        function="jrImage_display"
        module="jrUser"
        type="user_image"
        item_id=$profile._user_id
        size="icon"
        crop="auto"
        alt=$profile.user_name
        }
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$profile._profile_id item_id=$item.action_data._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$profile.profile_url}" title="{$item.action_data.profile_name|jrCore_entity_string}">{$profile.profile_url}</a></span>
        is now following <a href="{$jamroom_url}/{$item.action_data.profile_url}">{$item.action_data.profile_url|truncate:60}</a>
        <br>
        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>

<div class="item_media">
    <div class="wrap clearfix" style="position: relative">
        {if strlen($item.action_data.profile_image_size) > 0}
            <div class="media_image">
                <a href="{$jamroom_url}/{$item.action_data.profile_url}"
                   title="{$item.action_data.profile_title|jrCore_entity_string}">
                    {jrCore_module_function
                    function="jrImage_display"
                    module="jrProfile"
                    type="profile_image"
                    item_id=$item.action_data._profile_id
                    size="xlarge"
                    class="img_scale"
                    alt=$item.action_data.profile_name
                    crop="auto"
                    }
                </a>
            </div>
        {/if}
        <div class="middle">
            <div>
                <span class="title">{$item.action_data.profile_name|truncate:60}</span>
                <br>
                {jrCore_module_function function="jrFollower_button" class="follow" profile_id=$item.action_data._profile_id title="Follow"}
            </div>
        </div>
    </div>
</div>