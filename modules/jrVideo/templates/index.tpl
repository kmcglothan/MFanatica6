{jrCore_module_url module="jrVideo" assign="murl"}
{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="video_title,video_description"}
        <h1>{jrCore_lang module="jrVideo" id=39 default="Videos"}</h1>
    </div>

    <div class="block_content">
        {jrCore_list module="jrVideo" order_by="_created numerical_desc" pagebreak=10 page=$_post.p pager=true}
    </div>

</div>
{jrCore_include template="footer.tpl"}
