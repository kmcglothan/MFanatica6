{jrCore_module_url module="jrEvent" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="jrEvent" id="32" default="Created a new Event"}:
    {elseif $item.action_mode == 'update'}
        {jrCore_lang module="jrEvent" id="33" default="Updated an Event"}:
    {else}
        {jrCore_lang module="jrEvent" id="143" default="Is attending an event"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.event_title_url}" title="{$item.action_data.event_title|jrCore_entity_string}">{$item.action_data.event_title}</a>
    </span>
</div>
