{jrCore_module_url module="jrAction" assign="murl"}
{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="action_data"}
        <h1>{jrCore_lang module="jrAction" id=29 default="Activity"}</h1>
    </div>

    <div class="block_content">
        {jrCore_list module="jrAction" order_by="_item_id numerical_desc" pagebreak=10 page=$_post.p pager=true}
    </div>

</div>

{jrCore_include template="footer.tpl"}
