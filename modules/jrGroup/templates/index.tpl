{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="group_title,group_description"}
        <h1>{jrCore_lang module="jrGroup" id=1 default="Groups"}</h1>
    </div>

    <div class="block_content">
        {jrCore_list module="jrGroup" order_by="_item_id numerical_desc" pagebreak=10 page=$_post.p pager=true}
    </div>

</div>

{jrCore_include template="footer.tpl"}
