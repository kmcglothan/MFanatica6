{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="product_title,product_body"}
        <h1>{jrCore_lang module="jrProduct" id=19 default="Products"}</h1>
    </div>

    <div class="block_content">

        {jrCore_list module="jrProduct" profile_id=$_profile_id order_by="_created desc" pagebreak="6" page=$_post.p pager=true}

    </div>

</div>

{jrCore_include template="footer.tpl"}
