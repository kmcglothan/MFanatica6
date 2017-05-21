{* default index for profile *}
{jrCore_module_url module="jrBlog" assign="murl"}

{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrISkin_breadcrumbs module="jrBlog" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrBlog" profile_id=$_profile_id}
    </div>
</div>


<div class="col8">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrBlog" profile_url=$profile_url}
        <span>{jrCore_lang module="jrBlog" id="24" default="Blog"} by {$profile_name}</span>
        <input type="hidden" id="murl" value="{$murl}" />
        <input type="hidden" id="target" value="#list" />
        <input type="hidden" id="pagebreak" value="8" />
        <input type="hidden" id="mod" value="jrBlog" />
        <input type="hidden" id="profile_id" value="{$_profile_id}" />
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {jrCore_list module="jrBlog" profile_id=$_profile_id order_by="blog_display_order numerical_asc" pagebreak=15 page=$_post.p pager=true}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrBlog" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrISkin" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrBlog"
                    search="_profile_id != `$_profile_id`"
                    order_by='_created RANDOM'
                    pagebreak=8
                    require_image="blog_image"
                    template="chart_blog.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>

