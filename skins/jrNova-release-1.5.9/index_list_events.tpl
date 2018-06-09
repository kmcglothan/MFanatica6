{jrCore_module_url module="jrEvent" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
    <div style="display:table">
        <div style="display:table-cell">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="small" crop="auto" alt=$item.event_title width=false height=false class="iloutline"}</a><br>
        </div>
        <div class="p5" style="display:table-cell;vertical-align:middle">
            <span class="media_title"><a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a></span><br>
            <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.event_title_url}" title="{$item.event_location}">&#64;&nbsp;{$item.event_location|truncate:20:"...":false}</a></span><br>
            <span class="normal">{$item.event_date|jrCore_date_format}</span>
        </div>
    </div>
    {/foreach}
{/if}