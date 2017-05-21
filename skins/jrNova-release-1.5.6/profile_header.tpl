{if jrProfile_is_profile_owner($_profile_id)}
    {assign var="from_profile" value="yes"}
{/if}
{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="profile_name_box">
                <a href="{$jamroom_url}/{$profile_url}"><h1 class="profile_name">{$profile_name}</h1></a>
                <div class="profile_actions">

                    {jrCore_module_function function="jrFollower_button" profile_id=$_profile_id title="Follow This Profile"}
                    {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}

                    {if jrUser_is_admin() || jrUser_is_power_user()}
                        {jrCore_item_create_button module="jrProfile" view="create" profile_id=$_profile_id title="Create new Profile"}
                    {/if}

                    {jrProfile_delete_button profile_id=$_profile_id}

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col12 last">
            <div class="profile_menu">
                {if jrCore_is_mobile_device()}
                    {jrProfile_menu template="profile_menu_mobile.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
                {else}
                    {jrProfile_menu template="profile_menu.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
                {/if}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col4">
            <div>
                <div class="block">
                    <div class="profile_image">
                        {if jrProfile_is_profile_owner($_profile_id)}
                            {jrCore_module_url module="jrProfile" assign="purl"}
                            {jrCore_lang  skin=$_conf.jrCore_active_skin id="25" default="Change Image" assign="hover"}
                            <a href="{$_conf.jrCore_base_url}/{$purl}/settings/profile_id={$_profile_id}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="xlarge" class="img_scale" alt=$profile_name title=$hover width=false height=false}</a>
                            <div class="profile_hoverimage">
                                <span class="normal" style="font-weight:bold;color:#FFF;">{$hover}</span>&nbsp;{jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}
                            </div>
                        {else}
                            {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="xxlarge" class="img_scale" alt=$profile_name width=false height=false}
                        {/if}
                    </div>
                </div>

                <div class="block">
                    <div class="block_content mt10">
                        <div style="padding-top:8px;min-height:48px;max-height:288px;overflow:auto;">
                            {jrUser_online_status profile_id=$_profile_id}
                        </div>
                    </div>
                </div>


                {if isset($profile_bio) && strlen($profile_bio) > 0}
                    <div class="block">
                        <h3>{jrCore_lang  skin=$_conf.jrCore_active_skin id="86" default="About"} {$profile_name}</h3>
                        <div style="padding-top:8px;max-height:350px;overflow:auto;">

                            {$profile_bio|jrCore_format_string:$profile_quota_id}

                        </div>
                    </div>
                {/if}

                {if isset($profile_influences) && strlen($profile_influences) > 0}
                    <div class="block">
                        <h3> {jrCore_lang  skin=$_conf.jrCore_active_skin id="94" default="Influences"}</h3>
                        <div style="padding-top:8px;">
                            <span class="highlight-txt bold">{$profile_influences}</span><br>
                        </div>
                    </div>
                {/if}

                {if jrCore_module_is_active('jrFollower')}
                    {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`" search2="follow_active = 1" order_by="_created desc" assign="followers"}
                    {if isset($followers) && strlen($followers) > 0}
                        <div class="block">
                            <h3>{jrCore_lang  skin=$_conf.jrCore_active_skin id="87" default="Followers"}:</h3><br>
                            <div style="padding-top:8px">
                                {$followers}
                            </div>
                        </div>
                    {/if}
                {/if}

                {jrCore_list module="jrRating" profile_id=$_profile_id search1="rating_image_size > 0" order_by="_updated desc" limit="14" assign="rated"}
                {if strlen($rated) > 0}
                    <div class="block">
                        <h3>{jrCore_lang  skin=$_conf.jrCore_active_skin id="95" default="Recently Rated"}:</h3>
                        <div style="padding-top:8px;">
                            {$rated}
                        </div>
                    </div>
                {/if}

                <div class="block mb10">
                    <h3>{jrCore_lang  skin=$_conf.jrCore_active_skin id="42" default="stats"}:</h3>
                    <div class="block_content mt10">

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

                {jrTags_cloud profile_id=$_profile_id height="350" assign="tag_cloud"}
                {if strlen($tag_cloud) > 0}
                    <div class="block mb10">
                        <h3>{jrCore_lang module="jrTags" id="1" default="Profile Tag Cloud"}:</h3>
                        <div class="block_content mt10">
                            <div style="background-color:#949799;border-radius:4px;box-shadow:inset 0 0 5px #111;">
                                {$tag_cloud}
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                {/if}

            </div>
        </div>

        {* next <div> starts in body *}
