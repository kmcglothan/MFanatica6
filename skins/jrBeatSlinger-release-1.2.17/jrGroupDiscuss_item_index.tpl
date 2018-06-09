{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}
{if isset($_post.group_id)}
    {jrCore_db_get_item module="jrGroup" item_id=$_post.group_id skip_triggers=true assign="_group"}
{/if}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrAudio" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrGroupDiscuss" profile_id=$_profile_id action="{$murl}/create/group_id={$_post.group_id}"}
        {if !jrProfile_is_profile_owner($_profile_id) && jrGroup_get_user_config('jrGroupDiscuss', 'allowed', $item, $_user._user_id) == 'on'}
            {jrCore_module_url module="jrGroupDiscuss" assign="ndurl"}
            <a href="{$jamroom_url}/{$ndurl}/create/group_id={$_post.group_id}" title="create a new discussion topic">{jrCore_icon icon="plus"}</a>
        {/if}
    </div>
</div>


<div class="box">
    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrGroupDiscuss" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div id="list">
                {if jrCore_checktype($_post.group_id, 'number_nz')}
                    {jrCore_list module="jrGroupDiscuss" search="discuss_group_id = `$_post.group_id`" order_by="_created desc" pagebreak="8" page=$_post.p pager=true}
                {else}
                    {jrCore_list module="jrGroupDiscuss" profile_id=$_profile_id order_by="_created desc" pagebreak="8" page=$_post.p pager=true}
                {/if}
            </div>
        </div>
    </div>
</div>
