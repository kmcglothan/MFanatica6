{jrCore_module_url module="jrFAQ" assign="murl"}
<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="jrFAQ" id="11" default="Posted a new faq"}:
    {else}
        {jrCore_lang module="jrFAQ" id="12" default="Updated a faq"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}#{$item.action_item_id}-{$item.action_data.faq_question_url}" title="{$item.action_data.faq_question|jrCore_entity_string}">{$item.action_data.faq_question}</a>
    </span>
</div>
