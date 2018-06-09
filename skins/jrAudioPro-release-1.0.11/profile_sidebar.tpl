<div class="col4 sidebar">
    <div>
        <div style="padding: 1.5em 1em 0;">
            <div>
                <div class="head">
                    {jrCore_icon icon="info" size="20" color="ff5500"}
                    <span>{jrCore_lang skin="jrAudioPro" id=2 default="About"}</span>
                </div>
                <div class="profile_information">
                    {if jrCore_module_is_active("jrFollower")}
                        <div class="profile_data">
                            {jrAudioPro_stats assign="action_stats" profile_id=$_profile_id}
                            {jrCore_module_url module="jrFollower" assign="furl"}
                            {jrCore_module_url module="jrAction" assign="murl"}
                            {if isset($profile.profile_url) && strlen($profile.profile_url) > 0}
                                {$purl = $profile.profile_url}
                            {else}
                                {$purl = $profile_url}
                            {/if}
                            <ul class="clearfix">
                                <li onclick="jrCore_window_location('{$jamroom_url}/{$purl}/{$furl}')">
                                    <span>{jrCore_lang skin="jrAudioPro" id=35 default="Followers"}</span>
                                    {$action_stats.followers}</li>
                                <li onclick="jrCore_window_location('{$jamroom_url}/{$furl}/following')">
                                    <span>{jrCore_lang skin="jrAudioPro" id=45 default="Following"}</span>
                                    {$action_stats.following}</li>
                                <li onclick="jrCore_window_location('{$jamroom_url}/{$purl}/{$murl}/timeline')">
                                    <span>{jrCore_lang skin="jrAudioPro" id=44 default="Updates"}</span>
                                    {$action_stats.actions}</li>
                            </ul>
                        </div>
                    {/if}

                    {if strlen($profile_location) > 0}
                        <span>{jrCore_icon icon="location" size="16" color="ff5500"} {$profile_location|truncate:40}</span>
                    {/if}
                    {if strlen($profile_website) > 0}
                        <span>{jrCore_icon icon="link" size="16" color="ff5500"} <a href="{$profile_website}"
                                                                                    target="_blank">{$profile_website|replace:"http://":""|replace:"https://":""|truncate:40}</a></span>
                    {/if}
                    <span>{jrCore_icon icon="calendar" size="16" color="ff5500"} {jrCore_lang skin="jrAudioPro" id=36 default="Joined"} {$_created|jrCore_date_format:"%B %d, %Y"}</span>
                    {jrUser_online_status profile_id=$_profile_id}
                </div>
            </div>

            {if strlen($profile_bio) > 0}
                <div class="wrap">
                    <div class="head">
                        {jrCore_icon icon="profile" size="20" color="ff5500"}
                        <span>{jrCore_lang skin="jrAudioPro" id=37 default="Biography"}</span>
                    </div>
                    <div class="bio">
                        {$profile_bio|jrCore_format_string|jrCore_strip_html|truncate:160}
                    </div>
                    <div class="bio-more">
                        {if strlen($profile_bio) > 160}
                            <a class="full_bio"
                               onclick="jrAudioPro_modal('#bio_modal')">{jrCore_lang skin="jrAudioPro" id=38 default="Read Full Biography"}</a>
                        {/if}
                    </div>
                    <div class="modal" id="bio_modal" style="display: none">
                        <div style="padding: 1em 1em 0">
                            {jrCore_lang skin="jrAudioPro" id=37 default="Biography"}
                            <div style="float: right;">
                                {jrCore_icon icon="close" size="22" class='simplemodal-close'}
                            </div>
                        </div>
                        <div class="wrap">
                            <div style="max-height: 400px; overflow: auto">
                                {$profile_bio|jrCore_format_string:$profile_quota_id}
                            </div>
                        </div>
                    </div>
                </div>
            {/if}

            {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`"  search2="follow_active = 1" order_by="_item_id desc" limit=24 assign="followers"}
            {if strlen($followers) > 0}
                <div class="wrap">
                    <div class="head">
                        {jrCore_icon icon="followers" size="20" color="ff5500"}
                        <span>{$action_stats.followers} {jrCore_lang skin="jrAudioPro" id=35 default="followers"}</span>
                    </div>
                    <div class="followers">
                        {$followers}
                    </div>
                </div>
            {/if}

            {if !jrCore_is_mobile_device()}
                {jrCore_list module="jrRating" profile_id=$_profile_id search1="rating_image_size > 0" order_by="_updated desc" limit="24" assign="rated"}
                {if strlen($rated) > 0}
                    <div class="wrap">
                        <div class="head">
                            {jrCore_icon icon="star" size="20" color="ff5500"}
                            <span>{jrCore_lang skin="jrAudioPro" id="47" default="Rating"}</span>
                        </div>
                        <div class="followers">
                            {$rated}
                        </div>
                    </div>
                {/if}
            {/if}

            {if !jrCore_is_mobile_device()}

                {jrTags_cloud profile_id=$_profile_id height="350" assign="tag_cloud"}
                {if strlen($tag_cloud) > 0}
                    <div class="wrap">
                        <div class="head">
                            {jrCore_icon icon="tag" size="20" color="ff5500"}
                            <span>{jrCore_lang skin="jrAudioPro" id="42" default="Tag"}</span>
                        </div>
                        <div class="followers">
                            {$tag_cloud}
                        </div>
                    </div>
                {/if}
            {/if}
        </div>
    </div>
</div>