{jrCore_module_url module="jrGroupPage" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}
{if isset($_post.group_id)}
    {jrCore_db_get_item module="jrGroup" item_id=$_post.group_id skip_triggers=true assign="_group"}
{/if}


<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMaestro_breadcrumbs module="jrGroupPage" profile_url=$profile_url profile_name=$profile_name item=$_group page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_module_url module="jrGroupPage" assign="ndurl"}
        {jrCore_item_index_buttons module="jrGroupPage" profile_id=$_profile_id action="`$ndurl`/create/group_id=`$_post.group_id`"}
        {if !jrProfile_is_profile_owner($_profile_id) && jrGroup_get_user_config('jrGroupPage', 'allowed', $item, $_user._user_id) == 'on'}
            <a href="{$jamroom_url}/{$ndurl}/create/group_id={$_post.group_id}" title="create a new page">{jrCore_icon icon="plus"}</a>
        {/if}
    </div>
</div>


<div class="box">
    {jrMaestro_sort template="icons.tpl" nav_mode="jrGroupPage" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div id="list">
                {jrCore_list module="jrGroupPage" search="npage_group_id = `$_post.group_id`" order_by="_created desc" pagebreak="8" page=$_post.p pager=true}
            </div>
        </div>
    </div>
</div>


