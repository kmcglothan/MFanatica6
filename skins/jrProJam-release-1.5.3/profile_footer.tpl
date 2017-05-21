    <div class="col3 last">
        <div style="margin-top: 15px; margin-right: 5px;">
            <div class="block mb20" style="border:1px solid #333;">
                <div class="block_content">
                    <div class="body_2">
                        <div class="profile_image">
                            {if jrProfile_is_profile_owner($_profile_id)}
                                {jrCore_module_url module="jrProfile" assign="purl"}
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="25" default="Change Image" assign="hover"}
                                <a href="{$_conf.jrCore_base_url}/{$purl}/settings/profile_id={$_profile_id}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="xlarge" class="img_scale img_shadow" alt=$profile_name title=$hover width=false height=false}</a>
                                <div class="profile_hoverimage">
                                    <span class="normal" style="font-weight:bold;color:#FFF;">{$hover}</span>&nbsp;{jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}
                                </div>
                            {else}
                                {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$_profile_id size="xxlarge" class="img_scale img_shadow" alt=$profile_name width=false height=false}
                            {/if}
                        </div>
                    </div>
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
                <div class="head_2" style="margin-top:5px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"} {$profile_name}</div>
                <div class="block mb20" style="border:1px solid #333;">
                    <div class="block_content">
                        <div class="item" style="max-height:350px;overflow:auto;">
                            {$profile_bio|jrCore_format_string:$profile_quota_id}
                        </div>
                    </div>
                </div>
            {/if}

            {if isset($profile_influences) && strlen($profile_influences) > 0}
                <div class="head_2"> {jrCore_lang skin=$_conf.jrCore_active_skin id="143" default="Influences"}:</div>
                <div class="block mb20" style="border:1px solid #333;">
                    <div class="block_content">
                        <div class="item">
                            <span class="highlight-txt bold">{$profile_influences}</span><br>
                        </div>
                    </div>
                </div>
            {/if}

            {if jrCore_module_is_active('jrFollower')}
                {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`" search2="follow_active = 1" order_by="_created desc" assign="followers"}
                {if isset($followers) && strlen($followers) > 0}
                    <div class="head_2"> {jrCore_lang skin=$_conf.jrCore_active_skin id="131" default="Followers"}:</div>
                    <div class="block mb20" style="border:1px solid #333;">
                        <div class="block_content">
                            <div class="item">
                                {$followers}
                            </div>
                        </div>
                    </div>
                {/if}
            {/if}

            {jrCore_list module="jrRating" profile_id=$_profile_id search1="rating_image_size > 0" order_by="_updated desc" limit="14" assign="rated"}
            {if strlen($rated) > 0}
                <div class="head_2">{jrCore_lang skin=$_conf.jrCore_active_skin id="157" default="Recently Rated"}:</div>
                <div class="block mb20" style="border:1px solid #333;">
                    <div class="block_content">
                        <div class="item">
                            {$rated}
                        </div>
                    </div>
                </div>
            {/if}

            <div class="head_2">{jrCore_lang skin=$_conf.jrCore_active_skin id="36" default="stats"}</div>
            <div class="block mb20" style="border:1px solid #333;">
                <div class="block_content">
                    <div class="item">

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

                        <div class="clear"></div>
                    </div>
                </div>
            </div>

            {jrTags_cloud profile_id=$_profile_id height="350" assign="tag_cloud"}
            {if strlen($tag_cloud) > 0}
                <div class="head_2">{jrCore_lang module="jrTags" id="1" default="Profile Tag Cloud"}:</div>
                <div class="block mb20" style="border:1px solid #333;">
                    <div class="block_content">
                        <div class="item">
                            {$tag_cloud}
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            {/if}

        </div>
    </div>

</div>
</div>

{jrCore_include template="footer.tpl"}

{* setup counter for page view *}
{jrCore_counter module="jrProfile" item_id=$_profile_id name="profile_view"}