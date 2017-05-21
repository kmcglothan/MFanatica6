{if $_post.option == 'search'}
{assign var="selected" value="news"}
{else}
{assign var="selected" value="blogs"}
{/if}
{assign var="no_inner_div" value=" true"}
{jrCore_lang  skin=$_conf.jrCore_active_skin id="93" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

{if isset($_post.option) && strlen($_post.option) > 0}

    {if $_post.option == 'by_rating'}
        {assign var="newclass" value="p_choice"}
        {assign var="ratedclass" value="p_choice_active"}
        {assign var="order_by" value="blog_rating_overall_average_count NUMERICAL_DESC"}
        {assign var="catclass" value="p_choice"}
        {assign var="group_by" value=""}
    {elseif $_post.option == 'categories'}
        {assign var="newclass" value="p_choice"}
        {assign var="ratedclass" value="p_choice"}
        {assign var="catclass" value="p_choice_active"}
        {assign var="order_by" value="blog_category asc"}
        {assign var="group_by" value="yes"}
    {elseif $_post.option == 'category' && isset($_post._1)}
        {assign var="newclass" value="p_choice"}
        {assign var="ratedclass" value="p_choice"}
        {assign var="catclass" value="p_choice_active"}
        {assign var="order_by" value="blog_display_order numerical_asc"}
        {assign var="group_by" value=""}
    {elseif $_post.option == 'search'}
        {assign var="newclass" value="p_choice"}
        {assign var="ratedclass" value="p_choice"}
        {assign var="catclass" value="p_choice"}
        {assign var="order_by" value=""}
        {assign var="group_by" value=""}
    {/if}

{else}

    {assign var="newclass" value="p_choice_active"}
    {assign var="order_by" value="_created desc"}
    {assign var="group_by" value=""}
    {assign var="ratedclass" value="p_choice"}
    {assign var="catclass" value="p_choice"}

{/if}

<div class="menu_tab">
    {if $_post.option == 'search'}
    <div class="{$newclass}" onclick="jrCore_window_location('{$jamroom_url}');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="1" default="home"}</div>
    {else}
    <div class="{$newclass}" onclick="jrCore_window_location('{$jamroom_url}/blogs');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="93" default="blogs"}</div>
    <div class="{$ratedclass}" onclick="jrCore_window_location('{$jamroom_url}/blogs/by_rating');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="93" default="blogs"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="54" default="by ratings"}</div>
    <div class="{$catclass}" onclick="jrCore_window_location('{$jamroom_url}/blogs/categories');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="10" default="blog"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="90" default="categories"}</div>
    {/if}
    <div class="clear"></div>
</div>
<div class="inner">
    {if isset($group_by) && $group_by == 'yes'}
        {jrCore_list module="jrBlog" order_by=$order_by group_by="blog_category" template="blogs_row.tpl"}
    {else}
        {if $_post.option == 'category' && isset($_post._1)}
            {jrCore_list module="jrBlog" order_by="_created asc" search1="blog_category_url = `$_post._1`" template="blogs_row.tpl" pagebreak=$_conf.jrNova_default_pagebreak page=$_post.p pager=true}
        {elseif $_post.option == 'search' && isset($_post._1)}
            {jrCore_list module="jrBlog" search1="_item_id = `$_post._1`" template="blogs_row.tpl" pagebreak=$_conf.jrNova_default_pagebreak page=$_post.p pager=true}
        {else}
            {jrCore_list module="jrBlog" order_by=$order_by template="blogs_row.tpl" pagebreak=$_conf.jrNova_default_pagebreak page=$_post.p pager=true}
        {/if}
    {/if}
</div>

{jrCore_include template="footer.tpl"}
