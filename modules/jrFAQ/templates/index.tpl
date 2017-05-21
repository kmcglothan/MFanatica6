{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        <h1>{jrCore_lang module="jrFAQ" id="10" default="FAQ"}</h1>
    </div>

    <div class="block_content">

        {jrCore_list module="jrFAQ" order_by="_created numerical_asc" group_by="faq_category" pagebreak="10" page=$_post.p pager=true}

    </div>

</div>

{jrCore_include template="footer.tpl"}
