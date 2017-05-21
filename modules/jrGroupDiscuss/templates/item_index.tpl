{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}
<div class="block">
    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrGroupDiscuss" profile_id=$_profile_id action="{$murl}/create/group_id={$_post.group_id}"}
            {if !jrProfile_is_profile_owner($_profile_id) && jrGroup_get_user_config('jrGroupDiscuss', 'allowed', $item, $_user._user_id) == 'on'}
                {jrCore_lang module="jrGroupDiscuss" id=2 default="Create new Discussion" assign="ttl"}
                <a href="{$jamroom_url}/{$murl}/create/group_id={$_post.group_id}" title="{$ttl|jrCore_entity_string}">{jrCore_icon icon="plus"}</a>
            {/if}
        </div>
        {jrCore_lang module="jrGroupDiscuss" id=1 default="Discussions" assign="h1"}
        <h1>{$h1}</h1>
        <div class="breadcrumbs">
            {jrCore_module_url module="jrGroup" assign="gurl"}
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo;
            <a href="{$jamroom_url}/{$profile_url}/{$gurl}">{jrCore_lang module="jrGroup" id="1" default="Groups"}</a> &raquo;
            <a href="{$jamroom_url}/{$profile_url}/{$gurl}/{$_group._item_id}/{$_group.group_title_url}">{$_group.group_title}</a> &raquo;
            {$h1}
        </div>
    </div>
    <div class="block_content">
        {jrCore_list module="jrGroupDiscuss" search="discuss_group_id = `$_group._item_id`" order_by="_item_id desc" pagebreak=8 page=$_post.p pager=true}
    </div>
</div>
