{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
{if isset($_items) && is_array($_items)}
    <div class="container">
        <table class="page_table">
            {foreach $_items as $key => $item}
            <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                <td class="page_table_cell">
                    <div>
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.photoalbum_title_url}">{$item.photoalbum_title}</a>
                    </div>
                    {assign var="limit" value="6"}
                    {if jrCore_is_mobile_device()}
                        {assign var="limit" value="5"}
                    {/if}
                    {$i = 0}
                    {foreach $item.photoalbum_photos as $img_id}
                        {if $i >= $limit}
                            {continue}
                        {/if}
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.photoalbum_title_url}" target="_blank">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$img_id size="small" crop="auto" class="iloutline"}</a>
                        {$i = $i+1}
                    {/foreach}
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.photoalbum_title_url}" target="_blank"><span style="margin-left:6px;">{jrCore_icon icon="next"}</span></a>
                </td>
                <td class="page_table_cell center" style="width:16%"><a onclick="jrEmbed_load_module('jrPhotoAlbum', 1, 'profile_url:{$item.profile_url}');">@{$item.profile_name}</a></td>
                <td class="page_table_cell center" style="width:16%"><span class="info">{jrCore_lang module="jrPhotoAlbum" id="14" default="Photos"}:</span> <span class="info_c">{$item.photoalbum_count}</span></td>
                <td class="page_table_cell" style="width:10%"><input type="button" class="form_button embed_form_button" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}" onclick="jrPhotoAlbum_insert_playlist({$item._item_id})"></td>
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
function jrPhotoAlbum_insert_playlist(id) {
    parent.tinymce.activeEditor.insertContent('[jrEmbed module="jrPhotoAlbum" id="' + id + '"]');
    parent.tinymce.activeEditor.windowManager.close();
}
</script>
