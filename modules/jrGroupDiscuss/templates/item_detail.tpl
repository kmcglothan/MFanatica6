{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="block">
    <div class="title">
        <div class="block_config">
            {if !jrUser_is_admin() && jrUser_is_logged_in() && $item._user_id == $_user._user_id && ($_conf.jrGroupDiscuss_update_always == 'on' || ($_conf.jrGroupDiscuss_update_always == 'off' && $item.discuss_comment_count == 0))}
                <a href="{$jamroom_url}/{$murl}/update/id={$item._item_id}">{jrCore_icon icon="gear"}</a>
            {else}
                {jrCore_item_detail_buttons module="jrGroupDiscuss" item=$item}
            {/if}
        </div>
        <h1>{$item.discuss_title}</h1>
        <div class="breadcrumbs">
            {jrCore_module_url module="jrGroup" assign="gurl"}
            <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$gurl}">{jrCore_lang module="jrGroup" id=1 default="Groups"}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$gurl}/{$item.discuss_group_id}/{$item.group_title_url}">{$item.group_title}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/group_id={$item.discuss_group_id}">{jrCore_lang module="jrGroupDiscuss" id=1 default="Discussions"}</a> &raquo; {$item.discuss_title}
        </div>
    </div>
    <div class="item">
        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="p10 center">
                        {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="medium" title="{$item.user_name}" alt="{$item.user_name}" class="img_scale" _v=$item._updated}<br>
                        <small>{$item._created|jrCore_format_time}</small><br>
                        <a href="{$jamroom_url}/{$item.original_profile_url}">@{$item.original_profile_url}</a>
                    </div>
                </div>
                <div class="col10 last">
                    <div class="p10">
                        {$item.discuss_description|jrCore_format_string:$item.profile_quota_id}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {if jrGroup_member_has_access($item)}
        {* bring in the item details *}
        {jrCore_item_detail_features module="jrGroupDiscuss" item=$item}
    {elseif isset($_conf.jrGroup_comment_membership) && $_conf.jrGroup_comment_membership == 'on'}
        {jrComment_form item_id=$item._item_id module="jrGroupDiscuss" profile_id=$item._profile_id}
    {/if}
</div>
