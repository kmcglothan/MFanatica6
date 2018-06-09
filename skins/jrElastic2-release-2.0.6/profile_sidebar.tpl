<div class="col4">
    <div style="padding:30px 0 0 1em;">
        <div>
            <div class="head">
                {jrCore_icon icon="info" size="20"}
                {jrCore_lang skin="jrElastic2" id=44 default="About"}
            </div>
            <div class="profile_information">
                {if jrCore_module_is_active("jrFollower")}
                    <div class="profile_data">
                        {jrElastic2_stats assign="action_stats" profile_id=$_profile_id}
                        {jrCore_module_url module="jrFollower" assign="furl"}
                        {jrCore_module_url module="jrAction" assign="murl"}
                        <ul class="clearfix">
                            <li onclick="jrCore_window_location('{$jamroom_url}/{$profile_url}/{$furl}')">
                                <span>{jrCore_lang skin="jrElastic2" id=43 default="Followers"}</span>
                                {$action_stats.followers}</li>
                            <li onclick="jrCore_window_location('{$jamroom_url}/{$profile_url}/{$furl}/profiles_followed')">
                                <span>{jrCore_lang skin="jrElastic2" id=68 default="Following"}</span>
                                {$action_stats.following}</li>
                            <li onclick="jrCore_window_location('{$jamroom_url}/{$profile_url}/{$murl}/timeline')">
                                <span>{jrCore_lang skin="jrElastic2" id=20 default="Updates"}</span>
                                {$action_stats.actions}</li>
                        </ul>
                    </div>
                {/if}

                {if strlen($profile_location) > 0}
                    <span>{jrCore_icon icon="location" size="16"} {$profile_location|truncate:40}</span>
                {/if}
                {if strlen($profile_website) > 0}
                    <span>{jrCore_icon icon="link" size="16"} <a href="{$profile_website}"
                                                                                target="_blank">{$profile_website|replace:"http://":""|replace:"https://":""|truncate:40}</a></span>
                {/if}
                <span>{jrCore_icon icon="calendar" size="16"} {jrCore_lang skin="jrElastic2" id=65 default="Joined"} {$_created|jrCore_date_format:"%B %d, %Y"}</span>
                {jrUser_online_status profile_id=$_profile_id}
            </div>
        </div>



        {if strlen($profile_bio) > 0}
            <div class="block block_profile_left">
                <div class="head">
                    {jrCore_icon icon="profile" size="20"}
                    {jrCore_lang skin="jrElastic2" id=63 default="Bio"}
                </div>
                <div class="block_content mt10">
                    <div style="padding-top:0;max-height:350px;overflow:auto">
                        {$profile_bio|jrCore_format_string:$profile_quota_id}
                    </div>
                </div>
            </div>
        {/if}


        {if !jrCore_is_mobile_device() && isset($profile_influences) && strlen($profile_influences) > 0}
            <div class="block block_profile_left">
                <div class="head">
                    {jrCore_icon icon="audio" size="20"}
                    {jrCore_lang skin="jrElastic2" id="47" default="Influences"}
                </div>
                <div class="block_content mt10">
                    <div style="padding-top:8px;">
                        <span class="highlight-txt bold">{$profile_influences}</span><br>
                    </div>
                </div>
            </div>
        {/if}


        {if !jrCore_is_mobile_device() && jrCore_module_is_active('jrFollower') && $_conf.jrElastic2_follower_count > 0}

            {capture assign="latest_followers_tpl"}
            {literal}
            {if isset($_items)}
            {foreach from=$_items item="item"}
                <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" crop="auto" class="img_shadow" width=58 height=58 style="padding:2px;margin-bottom:4px;" alt="{$item.user_name|jrCore_entity_string}" title="{$item.user_name|jrCore_entity_string}" _v=$item._updated}</a>
            {/foreach}
            {/if}
            {/literal}
            {/capture}

            {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`" search2="follow_active = 1" order_by="_item_id desc" limit=$_conf.jrElastic2_follower_count assign="followers" template=$latest_followers_tpl}
            {if strlen($followers) > 0}
                <div class="block block_profile_left">
                    <div class="head">
                        {jrCore_icon icon="followers" size="20"}
                        {jrCore_lang skin="jrElastic2" id="43" default="Latest Followers"}
                    </div>
                    <div class="block_content mt10">
                        <div style="padding-top:8px">
                            {$followers}
                        </div>
                    </div>
                </div>
            {/if}
        {/if}


        {if !jrCore_is_mobile_device() && jrCore_module_is_active('jrRating') && $_conf.jrElastic2_rated_count > 0}

            {capture assign="recently_rated_tpl"}
            {literal}
            {if isset($_items)}
            {foreach from=$_items item="item"}
                {jrCore_module_url module=$item.rating_module assign="murl"}
                {jrCore_get_datastore_prefix module=$item.rating_module assign="prefix"}
                {assign var="item_title" value="`$prefix`_title"}
                <a href="{$jamroom_url}/{$item.rating_data.profile_url}/{$murl}/{$item.rating_item_id}/{$item.rating_data.$item_title|jrCore_url_string}">
                    {jrCore_module_function function="jrImage_display" module=$item.rating_module type="`$prefix`_image" item_id=$item.rating_item_id size="small" crop="auto" class="img_shadow" style="padding:2px;margin-bottom:4px;" title="`$item['rating_data'][$item_title]` rated a `$item.rating_value`" alt="`$item['rating_data'][$item_title]` rated a `$item.rating_value`" width=58 height=58 _v=$item._updated}</a>
            {/foreach}
            {/if}
            {/literal}
            {/capture}

            {jrCore_list module="jrRating" profile_id=$_profile_id search1="rating_image_size > 0" order_by="_item_id desc" limit=$_conf.jrElastic2_rated_count assign="rated" template=$recently_rated_tpl}
            {if strlen($rated) > 0}
                <div class="block block_profile_left">
                    <div class="head">
                        {jrCore_icon icon="star" size="20"}
                        {jrCore_lang skin="jrElastic2" id="46" default="Recently Rated"}
                    </div>
                    <div class="block_content mt10">
                        <div style="padding-top:8px">
                            {$rated}
                        </div>
                    </div>
                </div>
            {/if}
        {/if}


        {if !jrCore_is_mobile_device() && $_conf.jrElastic2_show_stats == 'on'}

            <div class="block mb10 block_profile_left">
                <div class="head">
                    {jrCore_icon icon="stats" size="20"}
                    {jrCore_lang skin="jrElastic2" id="45" default="Profile Stats"}
                </div>
                <div class="block_content mt10 clearfix">

                    {capture name="template" assign="stats_tpl"}
                    {literal}
                        {foreach $_stats as $title => $_stat}
                        {jrCore_module_url module=$_stat.module assign="murl"}
                        <div class="stat_entry_box">
                            <a href="{$jamroom_url}/{$profile_url}/{$murl}"><span class="stat_entry_title">{$title}:</span> <span class="stat_entry_count">{$_stat.count|default:0}</span></a>
                        </div>
                        {/foreach}
                    {/literal}
                    {/capture}
                    {jrProfile_stats profile_id=$_profile_id template=$stats_tpl}

                </div>
                <div class="clear"></div>
            </div>

            {if jrCore_module_is_active('jrTags') && $_conf.jrElastic2_show_tag_cloud == 'on'}
            {jrTags_cloud profile_id=$_profile_id height="350" assign="tag_cloud"}
            {if strlen($tag_cloud) > 0}
                <div class="block mb10 block_profile_left">
                    <div class="head">
                        {jrCore_icon icon="tag" size="20"}
                        {jrCore_lang module="jrTags" id="1" default="Profile Tag Cloud"}
                    </div>
                    <div class="block_content mt10">
                        {$tag_cloud}
                    </div>
                    <div class="clear"></div>
                </div>
            {/if}
            {/if}

        {/if}


    </div>
</div>