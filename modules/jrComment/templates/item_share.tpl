{jrCore_module_url module="jrComment" assign="murl"}

<span class="action_item_title">{jrCore_lang module="jrComment" id=12 default="Posted a new Comment on"} &quot;<a href="{$jamroom_url}/{$item.profile_url}#cm{$item._item_id}" title="@{$item.profile_url}">@{$item.profile_url}</a>&quot;:</span>
<br>
<div class="action_item_desc">
    &quot;{$item.comment_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:160}&quot;
</div>
