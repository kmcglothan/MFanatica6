{jrCore_module_url module="jrDocs" assign="murl"}
{jrCore_lang module="jrDocs" id="50" default="Select the type of document section you wish to add"}:<br><br>
{if is_numeric($doc_section_order)}
    {assign var="add" value="order=`$doc_section_order`"}
{else}
    {assign var="add" value=""}
{/if}
{foreach $_types as $type => $text}
    <span class="section_type"><a href="{$jamroom_url}/{$murl}/create_section/{$type}/id={$item_id}/profile_id={$profile_id}/{$add}"><h3>{$text}</h3></a></span><br>
{/foreach}

<div style="float:right;margin:3px;">
    <a href="javascript:" onclick="jrDocs_hide();">{jrCore_icon icon="close" size="16"}</a>
</div>
