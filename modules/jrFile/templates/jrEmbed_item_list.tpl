{jrCore_module_url module="jrFile" assign="murl"}

{if isset($_items) && is_array($_items)}
    <div class="container">
        <table class="page_table">
            {foreach $_items as $key => $item}
                <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                    <td class="page_table_cell" style="width:30%"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.file_title_url}" target="_blank"><h3>{$item.file_title}</h3></a></td>
                    <td class="page_table_cell center"><a onclick="jrEmbed_load_module('jrFile', 1, 'profile_url:{$item.profile_url}');">@{$item.profile_name}</a></td>
                    <td class="page_table_cell center">{$item.file_file_name} ({$item.file_file_size|jrCore_format_size})</td>
                    <td class="page_table_cell" style="width:10%"><input type="button" class="form_button embed_form_button" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}" onclick="jrFile_insert_file({$item._item_id})"></td>
                </tr>
            {/foreach}
        </table>
    </div>

{else}

    <div class="container">
        <table class="page_table">
            <tr class="page_table_row">
                <td class="page_table_cell center" colspan="2">{jrCore_lang module="jrFile" id=17 default="no files were found"}</td>
            </tr>
        </table>
    </div>

{/if}


<script type="text/javascript">
    function jrFile_insert_file(item_id) {
        parent.tinymce.activeEditor.insertContent('[jrEmbed module="jrFile" id="' + item_id + '"]');
        parent.tinymce.activeEditor.windowManager.close();
    }
</script>
