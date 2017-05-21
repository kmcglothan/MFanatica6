{jrCore_module_url module="jrGroupPage" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}
<div class="block">
    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrGroupPage" profile_id=$_profile_id action="`$murl`/create/group_id=`$_post.group_id`"}
            {if !jrProfile_is_profile_owner($_profile_id) && jrGroup_get_user_config('jrGroupPage', 'allowed', $item, $_user._user_id) == 'on'}
                <a href="{$jamroom_url}/{$murl}/create/group_id={$_post.group_id}" title="create a new page">{jrCore_icon icon="plus"}</a>
            {/if}
        </div>
        <h1>{jrCore_lang module="jrGroupPage" id="1" default="Group Pages"}</h1>
        <div class="breadcrumbs">
            {jrCore_module_url module="jrGroup" assign="gurl"}
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo;
            <a href="{$jamroom_url}/{$profile_url}/{$gurl}">{jrCore_lang module="jrGroup" id=1 default="Groups"}</a> &raquo;
            <a href="{$jamroom_url}/{$profile_url}/{$gurl}/{$_group._item_id}/{$_group.group_title_url}">{$_group.group_title}</a> &raquo;
            {jrCore_lang module="jrGroupPage" id=1 default="Group Pages"}
        </div>
    </div>
    <div class="block_content">
        {jrCore_list module="jrGroupPage" search="npage_group_id = `$_post.group_id`" order_by="_item_id desc" pagebreak=8 page=$_post.p pager=true}
    </div>
</div>
