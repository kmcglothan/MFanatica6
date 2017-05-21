{jrCore_module_url module="jrComment" assign="murl"}

{if $item.action_data.comment_module == 'jrProfile'}

    <span class="action_item_desc">{jrCore_lang module="jrComment" id="12" default="Posted a new Comment on"}</span>
    <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_original_data.profile_url}#cm{$item.action_data._item_id}" title="@{$item.action_original_data.profile_url}">@{$item.action_original_data.profile_url}</a>:</span>
{elseif isset($item.action_data.comment_item_title) && strlen($item.action_data.comment_item_title) > 0}

    <span class="action_item_desc">{jrCore_lang module="jrComment" id="12" default="Posted a new Comment on"}</span>
    {jrCore_module_url module=$item.action_data.comment_module assign="curl"}
    <span class="action_item_title"><a href="{$item.action_original_item_url}#cm{$item.action_item_id}" title="{$item.action_data.comment_item_title|strip_tags|jrCore_entity_encode}">{$item.action_data.comment_item_title|strip_tags}</a>:</span>

{else}

    <span class="action_item_desc">{jrCore_lang module="jrComment" id=30 default="Posted a new Timeline Comment"}:</span>
    {jrCore_module_url module=$item.action_data.comment_module assign="curl"}
    <span class="action_item_title"><a href="{$jamroom_url}/{$item.action_original_data.profile_url}/{$curl}/{$item.action_original_data._item_id}#cm{$item.action_data._item_id}">{$jamroom_url}/{$item.action_original_data.profile_url}/{$curl}/{$item.action_original_data._item_id}</a>:</span>

{/if}

<br>
<div class="action_item_text action_item_comment">
    {* Note: extra jrCore_format_string call allows mentions to be processed *}
    &quot;{$item.action_data.comment_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:160|jrCore_format_string:$item.profile_quota_id:"at_tags"|trim}&quot;
</div>
