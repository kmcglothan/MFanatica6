{jrCore_module_url module="jrBlog" assign="murl"}
<a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}" title="{$item.blog_title|jrCore_entity_string}">{$item.blog_title}</a>
<div class="action_item_detail">
    {$item.blog_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:170}
</div>
