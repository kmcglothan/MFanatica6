
{if jrCore_checktype($item._profile_id, 'number_nz')}
    {$profile_quota_id = $item.profile_quota_id}
    {$profile_header_image_size = $item.profile_header_image_size}
    {$profile_url = $item.profile_url}
    {$profile_name = $item.profile_name}
    {$_profile_id = $item._profile_id}
{elseif jrCore_checktype($_items[0]['_profile_id'], 'number_nz')}
    {$profile_quota_id = $_items[0]['profile_quota_id']}
    {$profile_header_image_size = $_items[0]['profile_header_image_size']}
    {$profile_url = $_items[0]['profile_url']}
    {$profile_name = $_items[0]['profile_name']}
    {$_profile_id = $_items[0]['_profile_id']}
{elseif jrCore_checktype($_profile['_profile_id'], 'number_nz')}
    {$profile_quota_id = $_profile['profile_quota_id']}
    {$profile_header_image_size = $_profile['profile_header_image_size']}
    {$profile_url = $_profile['profile_url']}
    {$profile_name = $_profile['profile_name']}
    {$_profile_id = $_profile['_profile_id']}
{/if}

{if $_conf.jrMogul_show_header == 'minimal'}
    <div class="profile_minimal_image">
        {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="large" class="img_scale" crop="auto" title=$profile_name alt=$profile_name _v=$profile_image_time}
        <div class="profile_minimal_info">
            <div class="arrow-down"></div>
            <div class="box" style="margin: 0;">
                <div class="media">
                    <div class="profile_images">

                        {jrUser_get_profile_home_data assign="profile"}

                        <a href="{$jamroom_url}/{$profile_url}">
                            {if $profile_header_image_size > 0}
                                {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_header_image" item_id=$_profile_id size="large" class="img_scale" crop="8:3" alt=$profile_name _v=$profile_header_image_time}
                            {else}
                                {jrCore_image image="profile_header_image.jpg" width="1140" class="img_scale" height="auto"}
                            {/if}
                        </a>

                        <div class="profile_image">
                            <a href="{$jamroom_url}/{$profile_url}">
                                {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="large" class="img_scale" crop="auto" alt=$profile_name _v=$profile_image_time}
                            </a>
                        </div>
                        <div class="profile_name">
                            <h1><a href="{$jamroom_url}/{$profile_url}">{$profile_name|truncate:20}</a></h1>
                            <span><a href="{$jamroom_url}/{$profile_url}">@{$profile_url|truncate:20}</a></span>
                        </div>

                    </div>
                    <br>

                    {if jrCore_module_is_active("jrFollower")}
                        <div class="profile_data">
                            {jrMogul_stats assign="action_stats" profile_id=$_profile_id}
                            {jrCore_module_url module="jrFollower" assign="furl"}
                            {jrCore_module_url module="jrAction" assign="murl"}
                            <ul class="clearfix">
                                <li onclick="jrCore_window_location('{$jamroom_url}/{$profile_url}/{$furl}')"><span>{jrCore_lang skin="jrMogul" id="126" default="Followers"}</span>
                                    {$action_stats.followers}</li>
                                <li onclick="jrCore_window_location('{$jamroom_url}/{$furl}/following')"><span>{jrCore_lang skin="jrMogul" id="145" default="Following"}</span>
                                    {$action_stats.following}</li>
                                <li onclick="jrCore_window_location('{$jamroom_url}/{$profile_url}/{$murl}/timeline')"><span>{jrCore_lang skin="jrMogul" id="144" default="Updates"}</span>
                                    {$action_stats.actions}</li>
                            </ul>
                        </div>
                    {/if}
                </div>
            </div>
            {jrProfile_menu
            template="profile_minimal_menu.tpl"
            profile_quota_id=$profile_quota_id
            profile_url=$profile_url
            order="jrAction,jrBlog,jrCombinedAudio,jrAudio,jrCombinedVideo,jrVideo,jrGallery,jrGroup,jrEvent,jrFlickr,jrYouTube,jrVimeo"
            }
        </div>
    </div>
{else}
    <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a>
{/if}

