{if isset($_items)}
  {foreach from=$_items item="item"}
    <span class="media_title"><a href="{$jamroom_url}/{$_params.module_url}/{$item._item_id}/{$item.page_title|jrCore_url_string}">{$item.page_title}</a></span><br>
  {/foreach}
{/if}