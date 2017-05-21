<span class="action_item_desc"><a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a> {jrCore_lang module="jrComment" id="12" default="Posted a new Comment on"}</span>
<span class="action_item_title"><a href="{$item.comment_url}">{$item.comment_item_title}</a>:</span><br>
<div class="action_item_detail">
    {$item.comment_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:160}
</div>

