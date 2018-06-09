<div class="col4 sidebar">
    {jrCore_include template="profile_info.tpl"}
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="stats" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap">
                <div class="media">
                    <div class="wrap clearfix">
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
                </div>
            </div>
        </div>
    </div>


    {if !jrCore_is_mobile_device() && isset($profile_influences) && strlen($profile_influences) > 0}
        <div class="box">
            {jrCelebrity_sort template="icons.tpl" nav_mode="star" profile_url=$profile_url}
            <div class="box_body">
                <div class="wrap">
                    <div class="media">
                        {$profile_influences}
                    </div>
                </div>
            </div>
        </div>
    {/if}


    {if !jrCore_is_mobile_device() && jrCore_module_is_active('jrFollower')}
        {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`" search2="follow_active = 1" order_by="_item_id desc" limit="16" template="profile_follow.tpl" assign="followers"}
        {if strlen($followers) > 0}
            <div class="box">
                {jrCelebrity_sort template="icons.tpl" nav_mode="jrFollower" profile_url=$profile_url}
                <div class="box_body">
                    <div class="wrap">
                        <div class="media">
                            <div class="wrap clearfix">
                                {$followers}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    {/if}


    {if !jrCore_is_mobile_device()}
        {jrCore_list module="jrRating" profile_id=$_profile_id search1="rating_image_size > 0" order_by="_updated desc" limit="14" assign="rated"}
        {if strlen($rated) > 0}
            <div class="box">
                {jrCelebrity_sort template="icons.tpl" nav_mode="star" profile_url=$profile_url}
                <div class="box_body">
                    <div class="wrap">
                        <div class="media">
                            {$rated}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    {/if}


    {if !jrCore_is_mobile_device()}

        {jrTags_cloud profile_id=$_profile_id height="350" assign="tag_cloud"}
        {if strlen($tag_cloud) > 0}
            <div class="box">
                {jrCelebrity_sort template="icons.tpl" nav_mode="jrTags" profile_url=$profile_url}
                <div class="box_body">
                    <div class="wrap">
                        <div class="media">
                            {$tag_cloud}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    {/if}

</div>