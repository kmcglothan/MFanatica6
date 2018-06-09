
<div id="list">

    {if isset($tag_module)}
        {* module tag cloud *}
        {jrCore_module_url module=$tag_module assign="murl"}
        {jrTags_cloud search="tag_module = `$tag_module`" height=400 module_url=$murl}
    {else}
        {* sitewide tag cloud *}
        {jrTags_cloud height=400}
    {/if}

</div>
</div>
</div>

{jrCore_include template="footer.tpl"}
