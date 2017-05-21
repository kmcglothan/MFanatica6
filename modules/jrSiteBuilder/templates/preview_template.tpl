{if isset($_items) && is_array($_items)}
    {jrCore_include module=$mod template=$tpl}
{else}
    <div class="page_notice warning">This module does not have any items in the datastore to display, please create some to see a preview.</div>
{/if}
