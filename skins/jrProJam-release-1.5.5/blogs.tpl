{if isset($option) && $option != 'all'}
    {jrCore_list module="jrBlog" order_by="_created desc" limit="5" search1="_user_id = 1" search2="blog_category = `$option`" template="blogs_row.tpl"}
{else}
    {jrCore_list module="jrBlog" order_by="_created desc" limit="5" search1="blog_category not_in about,news,welcome,latest,featured,exclusive" template="blogs_row.tpl"}
{/if}
