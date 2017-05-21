{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{if $item.action_mode == 'create'}

    <span class="action_item_desc">{jrCore_lang module="jrGroupDiscuss" id=11 default="Created a new Discussion"}</span>
    <span class="action_item_title"><a href="{$item.action_item_url}" title="{$item.action_data.discuss_title|jrCore_entity_string}">&quot;{$item.action_data.discuss_title}&quot;</a></span>:

{else}

    <span class="action_item_desc">{jrCore_lang module="jrGroupDiscuss" id=12 default="Updated a Discussion"}</span>
    <span class="action_item_title"><a href="{$item.action_item_url}" title="{$item.topic.discuss_title|jrCore_entity_string}">&quot;{$item.topic.discuss_title}&quot;</a></span>:

{/if}

<br>
<div class="action_item_text action_item_forum">
    {* Note: extra jrCore_format_string call allows mentions to be processed *}
    &quot;{$item.action_data.discuss_description|replace:"<p>":""|replace:"</p>":""|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:160|jrCore_format_string:$item.profile_quota_id:"at_tags"|trim}&quot;
    <br>
    <small><a href="{$jamroom_url}/{$item.action_data._group_data.profile_url}/{$murl}/{$item.action_data._group_data._item_id}/{$item.action_data._group_data.group_title_url}">{$item.action_data._group_data.group_title}</a></small>
</div>