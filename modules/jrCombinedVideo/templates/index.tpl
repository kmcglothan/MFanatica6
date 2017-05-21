{jrCore_module_url module="jrCombinedVideo" assign="murl"}
{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">

        {jrCombinedVideo_search_form}
        <h1>{jrCore_lang module="jrCombinedVideo" id=1 default="Videos"}</h1>

    </div>

    <div class="block_content">
        {jrCombinedVideo_get_active_modules assign="mods"}
        {if strlen($mods) > 0}
            {jrSeamless_list modules=$mods order_by="_created numerical_desc" pagebreak=10 page=$_post.p pager=true}
        {elseif jrUser_is_admin()}
            No active video modules found!
        {/if}
    </div>

</div>
{jrCore_include template="footer.tpl"}
