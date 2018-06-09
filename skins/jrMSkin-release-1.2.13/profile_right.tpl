<div class="col3">
    {jrCore_include template="who_to_follow.tpl"}

    {if !jrCore_is_mobile_device() && jrCore_module_is_active('jrFollower') && $_profile_id > 0}
        {jrCore_list module="jrFollower" search1="follow_profile_id = `$_profile_id`" search2="follow_active = 1" order_by="_item_id desc" limit=9 template="profile_follow.tpl" assign="followers"}
        {if strlen($followers) > 0}
            <div class="box">
                {jrMSkin_sort template="icons.tpl" nav_mode="jrFollower" profile_url=$profile_url}
                <div class="box_body">
                    <div class="wrap">
                        <div class="item_media clearfix" style="padding: 1px;">
                            {$followers}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    {/if}
</div>
