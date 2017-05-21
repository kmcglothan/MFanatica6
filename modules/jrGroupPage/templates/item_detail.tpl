{jrProfile_disable_header}
{jrProfile_disable_sidebar}

{jrCore_module_url module="jrGroupPage" assign="murl"}
<div class="block">
    <div class="title">
        <div class="block_config">
            {if !jrUser_is_admin() && jrUser_is_logged_in() && $item._user_id == $_user._user_id && ($_conf.jrGroupDiscuss_update_always == 'on' || ($_conf.jrGroupDiscuss_update_always == 'off' && $item.discuss_comment_count == 0))}
                <a href="{$jamroom_url}/{$murl}/update/id={$item._item_id}">{jrCore_icon icon="gear"}</a>
            {else}
                {jrCore_item_detail_buttons module="jrGroupPage" item=$item}
            {/if}
        </div>
        <h1>{$item.npage_title}</h1>
        <br>
        <div class="breadcrumbs" style="float:left;">
            {jrCore_module_url module="jrGroup" assign="gurl"}
            <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$gurl}">{jrCore_lang module="jrGroup" id=1 default="Groups"}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$gurl}/{$item.npage_group_id}/{$item.group_title_url}">{$item.group_title}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/group_id={$item.npage_group_id}">{jrCore_lang module="jrGroupPage" id=10 default="Group Page"}</a> &raquo; {$item.npage_title}
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="block_content">
        <div class="item p20">
            {$item.npage_body|jrCore_format_string:$item.profile_quota_id}
        </div>
        {jrCore_item_detail_features module="jrGroupPage" item=$item}
    </div>
</div>
