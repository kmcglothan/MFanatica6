{jrCore_lang module="jrGroupDiscuss" id=1 default="Discussions" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="discuss_title,discuss_description"}
        <h1>{jrCore_lang module="jrGroupDiscuss" id=1 default="Discussions"}</h1>
    </div>

    <div class="block_content">
        {jrCore_list module="jrGroupDiscuss" order_by="blog_publish_date desc" pagebreak=10 page=$_post.p pager=true}
    </div>

</div>

{jrCore_include template="footer.tpl"}