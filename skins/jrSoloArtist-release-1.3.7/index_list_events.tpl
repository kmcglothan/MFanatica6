{jrCore_module_url module="jrEvent" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="p5 left top">
            <span class="info">{$item.event_date|jrCore_date_format}</span><br>
            <span class="info"><a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a></span><br>
            <br>
            <span class="info">&#64;&nbsp;{$item.event_location}</span><br>
            <span class="normal">{if isset($item.event_description) && strlen($item.event_description) > 150}{$item.event_description|truncate:150:"...":false}{else}{$item.event_description}{/if}</span>
            {* <div class="normal right capital"><a href="{$jamroom_url}">{jrCore_lang skin=$_conf.jrCore_active_skin id="51" default="Buy Tickets"}&nbsp;&raquo;</a></div> *}
            <div class="p10 divider">&nbsp;</div>
        </div>
    {/foreach}
{/if}