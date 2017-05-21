{if isset($_items)}
    {foreach from=$_items item="item"}
        {jrCore_module_url module=$item.rating_module assign="murl"}
        {jrCore_get_datastore_prefix module=$item.rating_module assign="prefix"}
        {assign var="item_title" value="`$prefix`_title"}
        <a href="{$jamroom_url}/{$item.rating_data.profile_url}/{$murl}/{$item.rating_item_id}/{$item.rating_data.$item_title|jrCore_url_string}">
            {jrCore_module_function function="jrImage_display" module=$item.rating_module type="`$prefix`_image" item_id=$item.rating_item_id size="xsmall" crop="auto" class="img_shadow" style="padding:2px;margin-bottom:4px;" title="`$item['rating_data'][$item_title]` rated a `$item.rating_value`" alt="`$item['rating_data'][$item_title]` rated a `$item.rating_value`" width=false height=false}</a>
    {/foreach}
{/if}
