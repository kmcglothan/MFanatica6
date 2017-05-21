{if is_array($item)}
    {$profile_bio = $item.profile_bio}
    {$_profile_id = $item._profile_id}
    {$profile_name = $item.profile_name}
    {$profile_location = $item.profile_location}
    {$profile_website = $item.profile_website}
{/if}

<div class="box">
    {jrMogul_sort template="icons.tpl" nav_mode="about" profile_url=$profile_url}
    <div class="box_body">

        <div class="wrap">
            <div class="item_media">
                <div class="wrap">
                    <div class="profile_information">
                        {if strlen($profile_location) > 0}
                            <span>{jrCore_icon icon="location" size="16" color="444444"} {$profile_location|truncate:40}</span>
                        {/if}
                        {if strlen($profile_website) > 0}
                            <span>{jrCore_icon icon="link" size="16" color="444444"} <a href="{$profile_website}" target="_blank">{$profile_website|replace:"http://":""|replace:"https://":""|truncate:40}</a></span>
                        {/if}
                        <span>{jrCore_icon icon="calendar" size="16" color="444444"} {jrCore_lang skin="jrMogul" id=130 default="Joined"} {$_created|jrCore_date_format:"%B %d, %Y"}</span>
                        {jrUser_online_status profile_id=$_profile_id}
                    </div>

                    {if strlen($profile_bio) > 0}
                        <div class="wrap">
                            <div class="bio">
                                {$profile_bio|jrCore_format_string|jrCore_strip_html|truncate:160}
                            </div>

                            {if strlen($profile_bio) > 160}
                                <br>
                                <a class="full_bio" onclick="jrMogul_modal('#bio_modal')">{jrCore_lang skin="jrMogul" id=132 default="Read Full Biography"}</a>
                            {/if}

                            <div class="modal" id="bio_modal" style="display: none">
                                <div class="box">
                                    <div class="box_body">
                                        <div class="head">
                                            <div class="wrap">
                                                {jrCore_lang skin="jrMogul" id=131 default="Biography"}
                                            </div>
                                        </div>
                                        <div class="wrap">
                                            <div class="media">
                                                <div class="wrap">
                                                    <div style="max-height: 400px; overflow: auto">
                                                        {$profile_bio|jrCore_format_string:$profile_quota_id}
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="text-align: right">
                                                <br>
                                                {jrCore_icon icon="close" size="22" class='simplemodal-close'}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>