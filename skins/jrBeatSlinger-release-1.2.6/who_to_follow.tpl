{if jrUser_is_logged_in()}
{jrUser_home_profile_key key="_profile_id" assign="id"}
{jrFollower_who_to_follow profile_id=$id limit=4 template="timeline_follow.tpl" assign="profiles_to_follow"}
{if strlen($profiles_to_follow) > 0}
<div class="box">
    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrFollower" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div id="list" class="side_list no-shadow">
                {$profiles_to_follow}
            </div>
        </div>
    </div>
</div>
{/if}
{/if}
