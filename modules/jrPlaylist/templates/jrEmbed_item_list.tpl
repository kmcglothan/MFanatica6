{jrCore_module_url module="jrPlaylist" assign="murl"}
{if isset($_items) && is_array($_items)}
    <div class="container">
        <table class="page_table">
            {foreach $_items as $key => $item}
            <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                <td class="page_table_cell"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}" target="_blank"><h3>{$item.playlist_title}</h3></a></td>
                <td class="page_table_cell center" style="width:16%"><a onclick="jrEmbed_load_module('jrPlaylist', 1, 'profile_url:{$item.profile_url}');">@{$item.profile_name}</a></td>
                <td class="page_table_cell center" style="width:16%">{$item.playlist_count} {jrCore_lang module="jrPlaylist" id=41}</td>
                <td class="page_table_cell" style="width:10%"><input type="button" class="form_button embed_form_button" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}" onclick="jrPlaylist_insert_playlist({$item._item_id})"></td>
            </tr>
            {/foreach}
        </table>
    </div>

{else}

    <div class="container">
        <table class="page_table">
            <tr class="page_table_row">
                <td class="page_table_cell center">{jrCore_lang module="jrVideo" id="41" default="no videos were found"}</td>
            </tr>
        </table>
    </div>

{/if}


<script type="text/javascript">
function jrPlaylist_insert_playlist(id) {
    parent.tinymce.activeEditor.insertContent('[jrEmbed module="jrPlaylist" id="' + id + '"]');
    parent.tinymce.activeEditor.windowManager.close();
}
</script>
