{jrCore_module_url module="jrBlog" assign="murl"}

<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_index_buttons module="jrBlog" profile_id=$_profile_id}

        </div>
        <h1>{jrCore_lang module="jrBlog" id="24" default="Blog"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrBlog" id="24" default="Blog"}</a>
        </div>
    </div>

    {* Show Categories *}
    {jrBlog_categories profile_id=$_profile_id assign="_cats"}
    {if is_array($_cats)}
    <div class="block_content">
        <div class="p10">
            {foreach $_cats as $_c}
                <a href="{$_c.url}"><div class="stat_entry_box"><span class="stat_entry_title">{$_c.title}:</span>&nbsp;<span class="stat_entry_count">{$_c.item_count}</span></div></a>
            {/foreach}
            <div style="clear:both"></div>
        </div>
    </div>
    {/if}

    <div class="block_content">

        {if isset($_post['month']) && isset($_post['year'])}
            {if isset($_post['day'])}
                {$period_start = mktime(-5,0,0,$_post['month'],$_post['day'],$_post['year'])}
                {$period_end = mktime(30,0,0,$_post['month'],$_post['day']+1,$_post['year'])}
            {else}
                {$period_start = mktime(-5,0,0,$_post['month'],0,$_post['year'])}
                {$period_end = mktime(30,0,0,$_post['month']+1,0,$_post['year'])}
            {/if}
            {jrCore_list module="jrBlog" profile_id=$_profile_id search1="blog_publish_date <= $period_end" order_by="_created numerical_desc" pagebreak="8" page=$_post.p pager=true}
        {else}
            {jrCore_list module="jrBlog" profile_id=$_profile_id order_by="blog_display_order numerical_asc" pagebreak="8" page=$_post.p pager=true}
        {/if}

    </div>

</div>
