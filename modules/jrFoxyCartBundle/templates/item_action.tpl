{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="jrFoxyCartBundle" id="40" default="Created a new Bundle"}:
    {else}
        {jrCore_lang module="jrFoxyCartBundle" id="41" default="Updated a Bundle"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.bundle_title_url|jrCore_entity_string}">{$item.action_data.bundle_title}</a>
    </span>
</div>