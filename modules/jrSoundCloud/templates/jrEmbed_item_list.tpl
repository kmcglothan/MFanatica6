{jrCore_module_url module="jrSoundCloud" assign="murl"}

{if isset($_items) && is_array($_items)}
    <div class="container">
        <table class="page_table">
            {foreach $_items as $key => $item}
                <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                    <td class="page_table_cell center" style="width:5%"><img src="{$item.soundcloud_artwork_url}" class="img_scale"></td>
                    <td class="page_table_cell"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}" target="_blank"><h3>{$item.soundcloud_title}</h3></a></td>
                    <td class="page_table_cell center" style="width:16%"><a onclick="jrEmbed_load_module('jrSoundCloud', 1, 'profile_url:{$item.profile_url}');">@{$item.profile_name}</a></td>
                    <td class="page_table_cell center" style="width:16%">{if isset($item.soundcloud_genre_url)}<a onclick="jrEmbed_load_module('jrSoundCloud', 1,'soundcloud_genre_url:{$item.soundcloud_genre_url}');">{$item.soundcloud_genre}</a>{/if} </td>
                    <td class="page_table_cell" style="width:10%"><input type="button" class="form_button embed_form_button" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}" onclick="jrSoundCloud_insert_audio({$item._item_id})"></td>
                </tr>
            {/foreach}
        </table>
    </div>

{else}

    <div class="container">
        <table class="page_table">
            <tr class="page_table_row">
                <td class="page_table_cell center" colspan="2">{jrCore_lang module="jrSoundCloud" id="41" default="No SoundCloud Tracks found"}</td>
            </tr>
        </table>
    </div>

{/if}

{jrCore_module_url module="jrSoundCloud" assign="murl"}

<script type="text/javascript">
    function jrSoundCloud_insert_audio(item_id) {
        parent.tinymce.activeEditor.insertContent('[jrEmbed module="jrSoundCloud" id="' + item_id + '"]');
        parent.tinymce.activeEditor.windowManager.close();
    }
</script>
