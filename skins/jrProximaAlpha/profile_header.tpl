{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="profile_name_box">

                <div class="block_config" style="margin-top:3px">
                    {jrCore_module_function function="jrFollower_button" profile_id=$_profile_id title="Follow This Profile"}
                    {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}
                    {if jrUser_is_admin()}
                        {jrCore_item_create_button module="jrProfile" view="create" profile_id=$_profile_id title="Create new Profile"}
                    {/if}
                    {if jrUser_is_master()}
                        {jrCore_item_delete_button module="jrProfile" view="delete_save" profile_id=$_profile_id item_id=$_profile_id title="Delete this Profile" prompt="Are you sure you want to delete this profile?"}
                    {/if}
                </div>

                <a href="{$jamroom_url}/{$profile_url}"><h1 class="profile_name">{$profile_name}</h1></a>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col12 last">
            <div class="profile_menu">
                {jrProfile_menu template="profile_menu.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col3">
            <div>

                <div class="block">
                    {if jrProfile_is_profile_owner($_profile_id)}
                        {jrCore_module_url module="jrProfile" assign="purl"}
                        <div class="profile_image">
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="3" default="Change Image" assign="hover"}
                            <a href="{$_conf.jrCore_base_url}/{$purl}/settings/profile_id={$_profile_id}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="xlarge" class="img_scale img_shadow" alt=$profile_name title=$hover width=false height=false}</a>
                            <div class="profile_hoverimage">
                                <span class="normal" style="font-weight:bold;color:#FFF;">{$hover}</span>&nbsp;{jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}
                            </div>
                        </div>
                    {else}
                        {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="xxlarge" class="img_scale img_shadow" alt=$profile_name width=false height=false}
                    {/if}
                </div>

                <div class="block">
                    <div class="block_content mt10">
                        <div style="padding-top:8px;min-height:48px;max-height:288px;overflow:auto;">
                            {jrUser_online_status profile_id=$_profile_id}
                        </div>
                    </div>
                </div>


                {if !jrCore_is_mobile_device() && strlen($profile_bio) > 0}
                <div class="block">
                    <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="4" default="About"} {$profile_name}</h3>
                    <div class="block_content mt10">
                        <div style="padding-top:8px;max-height:350px;overflow:auto;">
                            {$profile_bio|jrCore_format_string:$profile_quota_id}
                        </div>
                    </div>
                </div>
                {/if}


                {if jrCore_module_is_active('jrFollower')}
                {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`" search2="follow_active = 1" order_by="_created desc" limit="15" assign="followers"}
                {if strlen($followers) > 0}
                <div class="block">
                    <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="5" default="Latest Followers"}:</h3>
                    <div class="block_content mt10">
                            <div style="padding-top:8px">
                                {$followers}
                            </div>
                    </div>
                </div>
                {/if}
                {/if}


                {jrCore_list module="jrRating" search1="_profile_id = `$_profile_id`" search2="rating_image_size = 1" order_by="_updated desc" limit="14" assign="rated"}
                {if strlen($rated) > 0}
                <div class="block">
                    <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="6" default="Recently Rated"}:</h3>
                    <div class="block_content mt10">
                            <div style="padding-top:8px">
                                {$rated}
                            </div>
                    </div>
                </div>
                {/if}


                {if !jrCore_is_mobile_device()}
                    <div class="block mb10">
                        <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="7" default="Profile Stats"}:</h3>
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
                        <div class="block_content mt10" style="border: 1px solid #eee">
                           {$tag_cloud}
                        </div>
                        <div class="clear"></div>
                    </div>
                    {/if}
                {/if}


            </div>
        </div>

        {* next <div> starts in body *}
