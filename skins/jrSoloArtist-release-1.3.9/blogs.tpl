{assign var="selected" value="blogs"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="10" default="blog" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="block">
                {jrCore_module_url module="jrBlog" assign="murl"}
                <div class="block_config">
                    <a href="{$jamroom_url}/{$murl}/feed/{$_conf.jrSoloArtist_main_id}" target="_blank"> {jrCore_image module="jrBlog" image="feed.png" alt="RSS Feed" style="vertical-align: top;" width="22" height="22"} Subscribe</a>
                </div>
                <div class="title">
                    <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="10" default="Blog"} {jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="Categories"}:</h1><br>
                </div>
                <div class="block_content">
                    {*the blog categories menu*}
                    {capture name="row_template" assign="blog_cat_row"}
                    {literal}
                        {if isset($_items)}
                        <div class="item">
                            {foreach from=$_items item="item"}
                                <input type="button" value="{$item.blog_category}" class="form_button" onclick="jrCore_window_location('{$jamroom_url}/blogs/category/{$item.blog_category_url}');">
                            {/foreach}
                        </div>
                        {/if}
                    {/literal}
                    {/capture}
                    {jrCore_list module="jrBlog" group_by="blog_category" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" template=$blog_cat_row}
                </div>
            </div>
        </div>
        <div class="col12 last">
            <div class="block">

                <div class="block_content">

                    <div id="site-blogs" class="left p5">
                        {if isset($_post.option) && $_post.option == 'category'}
                            {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" search2="blog_category_url = `$_1`" template="blogs_row.tpl"}
                        {elseif (isset($_post.option) && strlen($_post.option) > 0) && $_post.option != 'category'}
                            {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" search2="blog_title_url = `$_post.option`" template="blog_row.tpl"}
                        {else}
                            {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" template="blogs_row.tpl" pagebreak="4" page=$_post.p pager=true}
                        {/if}
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

{jrCore_include template="footer.tpl"}

