{jrCore_module_url module="jrCombinedAudio" assign="murl"}
{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">

        {jrCombinedAudio_search_form}
        <h1>{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</h1>

    </div>

    <div class="block_content">
        {jrCombinedAudio_get_active_modules assign="mods"}
        {if strlen($mods) > 0}
            {jrSeamless_list modules=$mods order_by="_created numerical_desc" pagebreak=10 page=$_post.p pager=true}
        {elseif jrUser_is_admin()}
            No active audio modules found!
        {/if}
    </div>

</div>
{jrCore_include template="footer.tpl"}
