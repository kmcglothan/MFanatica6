<div class="box" style="margin: 0;">
    <div class="media">
        <div class="profile_images">

            {jrUser_get_profile_home_data assign="profile"}

            <a href="{$jamroom_url}/{$profile.profile_url}">
            {if $profile.profile_header_image_size > 0}
                {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_header_image" item_id=$profile._profile_id size="large" class="img_scale" crop="8:3" alt=$profile.profile_name _v=$profile.profile_header_image_time}
            {else}
                {jrCore_image image="profile_header_image.jpg" width="1140" class="img_scale" height="auto"}
            {/if}
            </a>

            <div class="profile_image">
                <a href="{$jamroom_url}/{$profile.profile_url}">
                    {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$profile._profile_id size="large" class="img_scale" crop="auto" alt=$profile.profile_name _v=$profile.profile_image_time}
                </a>
            </div>
            <div class="profile_name">
                <h1><a href="{$jamroom_url}/{$profile.profile_url}">{$profile.profile_name|truncate:20}</a></h1>
                <span><a href="{$jamroom_url}/{$profile.profile_url}">@{$profile.profile_url|truncate:20}</a></span>
            </div>

        </div>
        <br>

        {if jrCore_module_is_active("jrFollower")}
            <div class="profile_data">
                {jrBeatSlinger_stats assign="action_stats" profile_id=$profile._profile_id}
                {jrCore_module_url module="jrFollower" assign="furl"}
                {jrCore_module_url module="jrAction" assign="murl"}
                <ul class="clearfix">
                    <li onclick="jrCore_window_location('{$jamroom_url}/{$profile.profile_url}/{$furl}')"><span>{jrCore_lang skin="jrBeatSlinger" id="126" default="Followers"}</span>
                        {$action_stats.followers}</li>
                    <li onclick="jrCore_window_location('{$jamroom_url}/{$furl}/following')"><span>{jrCore_lang skin="jrBeatSlinger" id="145" default="Following"}</span>
                        {$action_stats.following}</li>
                    <li onclick="jrCore_window_location('{$jamroom_url}/{$profile.profile_url}/{$murl}/timeline')"><span>{jrCore_lang skin="jrBeatSlinger" id="144" default="Updates"}</span>
                        {$action_stats.actions}</li>
                </ul>
            </div>
        {/if}
    </div>
</div>