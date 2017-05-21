{jrCore_module_url module="jrVideo" assign="murl"}

{if isset($_items) && is_array($_items)}
    <div class="container">
        <table class="page_table">
            {foreach $_items as $key => $item}
            <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                <td class="page_table_cell center" style="width:5%">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" crop="auto" class="img_scale" alt=$item.video_title width=false height=false}</td>
                <td class="page_table_cell" style="width:53%"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}" target="_blank"><h3>{$item.video_title}</h3></a></td>
                <td class="page_table_cell center" style="width:16%"><a onclick="jrEmbed_load_module('jrVideo', 1, 'profile_url:{$item.profile_url}');">@{$item.profile_name}</a></td>
                <td class="page_table_cell center" style="width:16%"><a onclick="jrEmbed_load_module('jrVideo', 1, 'video_album_url:{$item.video_album_url}');">{$item.video_album}</a></td>
                <td class="page_table_cell" style="width:10%"><input type="button" class="form_button embed_form_button" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}" onclick="jrVideo_insert_video({$item._item_id})"></td>
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
    function jrVideo_insert_video(video_id) {
        parent.tinymce.activeEditor.insertContent('[jrEmbed module="jrVideo" id="' + video_id + '"]');
        parent.tinymce.activeEditor.windowManager.close();
    }
</script>