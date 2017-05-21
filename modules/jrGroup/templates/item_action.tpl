{jrCore_module_url module="jrGroup" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="jrGroup" id="11" default="Created a new group"}:
    {else}
        {jrCore_lang module="jrGroup" id="12" default="Updated a group"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.group_title_url}" title="{$item.action_data.group_title|jrCore_entity_string}">{$item.action_data.group_title}</a>
    </span>
</div>
