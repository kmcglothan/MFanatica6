{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        <h1>{jrCore_lang module="jrFile" id="22" default="Files"}</h1>
    </div>

    <div class="block_content">

        {jrCore_list module="jrFile" order_by="_item_id desc" pagebreak=10 page=$_post.p pager=true}

    </div>

</div>

{jrCore_include template="footer.tpl"}
