{jrCore_module_url module="jrCore" assign="curl"}
{jrCore_lang module="jrCore" id=40 default="are you sure you want to delete this item?" assign="prompt"}
{jrCore_lang module="jrCore" id=38 default="delete" assign="title"}

{foreach $_items as $key => $item}
<table class="page_section_header jrcore_file_detail" onmouseover="$('#file-delete-{$item._item_id}-{$key}').show()" onmouseout="$('#file-delete-{$item._item_id}-{$key}').hide()">
    <tr>
        <td class="jrcore_file_detail_left">
            {* If this is an image field, show image in lightbox *}
            {if $item.is_image == 1}
                <a href="{$jamroom_url}/{$item.module_url}/image/{$item.field_name}/{$item._item_id}/xxxlarge/_v={$item.time}" target="_blank" data-lightbox="images">
                    {jrCore_module_function function="jrImage_display" module=$_post.module type=$item.field_name item_id=$item._item_id size="icon" crop="auto" alt=$item.name width=48 height=48 _v=$item.time}
                </a>
            {else}
                <a href="{$jamroom_url}/{$item.module_url}/download/{$item.field_name}/{$item._item_id}">{jrCore_file_type_image extension=$item.extension width=48 height=48 alt=$item.extension}</a>
            {/if}
        </td>
        <td class="jrcore_file_detail_right">

            {if $item.is_image == 1}
                <span class="jrcore_file_title">{jrCore_lang module="jrCore" id="74" default="name"}:&nbsp;</span> <a href="{$jamroom_url}/{$item.module_url}/download/{$item.field_name}/{$item._item_id}">{$item.name}</a><br>
            {else}
                <span class="jrcore_file_title">{jrCore_lang module="jrCore" id="74" default="name"}:&nbsp;</span> {$item.name}<br>
            {/if}

            <span class="jrcore_file_title">{jrCore_lang module="jrCore" id="75" default="size"}:&nbsp;</span> {$item.size|jrCore_format_size}<br>
            <span class="jrcore_file_title">{jrCore_lang module="jrCore" id="76" default="date"}:&nbsp;</span> {$item.time|jrCore_format_time}
            <div id="file-delete-{$item._item_id}-{$key}" class="image_delete">
                {if !empty($item.delete_prompt)}
                    {assign var="delete_prompt" value=$item.delete_prompt}
                {else}
                    {assign var="delete_prompt" value=$prompt}
                {/if}
                {if !empty($item.delete_url)}
                    <a onclick="jrCore_confirm('{$delete_prompt|jrCore_entity_string}', '', function() { jrCore_window_location('{$item.delete_url}') } )" title="{$title|jrCore_entity_string}">{jrCore_icon icon="close" size=16}</a>
                {else}
                    <a onclick="jrCore_confirm('{$delete_prompt|jrCore_entity_string}', '', function() { jrCore_window_location('{$jamroom_url}/{$curl}/delete/{$_post.module}/{$item.field_name}/{$item._item_id}') } )" title="{$title|jrCore_entity_string}">{jrCore_icon icon="close" size=16}</a>
                {/if}
            </div>

        </td>
    </tr>
</table>
{/foreach}

