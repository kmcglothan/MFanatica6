
{if $_conf.jrBeatSlinger_bio_right == 'on'}
    <div class="page_nav">
        <div class="breadcrumbs">
            {jrCore_include template="profile_header_minimal.tpl"}
            {jrBeatSlinger_breadcrumbs module="jrAction" profile_url=$profile_url page="index"}
        </div>
        <div class="action_buttons">
            {jrCore_lang id=25 skin="jrBeatSlinger" default="Follow" assign="follow"}
            {jrCore_module_function function="jrFollower_button" class="follow" profile_id=$_profile_id title=$follow}
            {if jrProfile_is_profile_owner($_profile_id)}
                {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Edit Profile"}
                {if jrUser_is_admin() || jrUser_is_power_user()}
                    {jrCore_item_create_button module="jrProfile" view="create" profile_id=$_profile_id title="Create Profile"}
                {/if}
                {jrProfile_delete_button profile_id=$_profile_id}
            {/if}
        </div>
    </div>
{/if}


<div class="box">
    {jrBeatSlinger_sort template="icons.tpl" nav_mode="bio" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div class="media">
                <div class="wrap">
                    <div style=";max-height:350px;overflow:auto;">
                        {$profile_bio|jrCore_format_string:$profile_quota_id}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>