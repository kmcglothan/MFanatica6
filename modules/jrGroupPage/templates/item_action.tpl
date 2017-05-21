{jrCore_module_url module="jrGroupPage" assign="murl"}
<span class="action_item_title">
{if $item.action_mode == 'create'}
    {jrCore_lang module="jrGroupPage" id=11 default="Posted a new Group Page"}:
{else}
    {jrCore_lang module="jrGroupPage" id=12 default="Updated a Group Page"}:
{/if}
<br>
<a href="{$item.action_item_url}" title="{$item.action_data.npage_title|jrCore_entity_string}">{$item.action_data.npage_title}</a>
</span>
