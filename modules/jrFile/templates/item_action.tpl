{jrCore_module_url module="jrFile" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="jrFile" id="18" default="Uploaded a new File"}:
    {else}
        {jrCore_lang module="jrFile" id="23" default="Updated a File"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.file_title_url}" title="{$item.action_data.file_title|jrCore_entity_string}">{$item.action_data.file_title}</a>
    </span>
</div>
