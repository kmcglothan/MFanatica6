<div class="doc_toc">
    <h3>{jrCore_lang module="jrDocs" id="54" default="Table of Contents"}:</h3><br>
    {foreach $_items as $_item}
        {if $_item.doc_section_type != 'header' && !empty($_item.doc_title_url)}
            {if $_item.doc_section_type == "function_definition"}
                <span><a href="#{$_item.doc_title_url}">{jrCore_lang module="jrDocs" id="52" default="Function:"} {$_item.doc_title}</a></span><br>
            {else}
                <span><a href="#{$_item.doc_title_url}">{$_item.doc_title}</a></span><br>
            {/if}
        {/if}
    {/foreach}
</div>
