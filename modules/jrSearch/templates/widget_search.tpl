{if $widget_data.search_module == '_'}
    {jrSearch_form class="form_text" style="width:60%"}
{else}
    {jrSearch_form class="form_text" style="width:60%" module=$widget_data.search_module value=""}
{/if}
