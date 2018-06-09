{jrCore_lang module="jrBlog" id=29 default="Blogs" assign="page_title"}
{jrCore_module_url module="jrBlog" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}


<div class="fs">
    <div class="row">
        <div class="col8">
            <div  style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrMogul_sort template="icons.tpl" nav_mode="jrBlog" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="blog_title,blog_category,blog_text"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrBlog" search1="blog_publish_date <= `$smarty.now`" order_by="blog_publish_date desc" pagebreak=10 page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col4">
            <div class="box">
                {jrMogul_sort template="icons.tpl" nav_mode="jrBlog" profile_url=$profile_url}
                <span>{jrCore_lang skin="jrMogul" id=31 default="Charts"}</span>
                <div class="box_body">
                    <div class="wrap">
                        <div id="list">
                            {jrCore_list module="jrBlog"
                            search1="blog_publish_date <= `$smarty.now`"
                            order_by="blog_like_count numerical_desc"
                            template="chart_blog.tpl"
                            profile_id=$_conf.jrMogul_master_blog|default:1
                            pagebreak=12
                            page=$_post.p
                            pager=true}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}
