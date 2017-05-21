{jrCore_module_url module="jrAudio" assign="murl"}
<script type="text/javascript">
    $(document).ready(function() {
        // add checkmarks to songs in the playlist.
        $.each(playlist, function(k, v) {
            $('#audio_id_' + v).prop('checked', 'checked');
        });
    });
</script>

<div id="jrAudio_holder">
    {* Results *}
    {if isset($_items) && is_array($_items)}
        <div class="container">
            <table class="page_table">
                {foreach $_items as $key => $item}
                    <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                        <td class="page_table_cell center" style="width:5%">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" class="img_scale" alt="" width=false height=false}</td>
                        <td class="page_table_cell center" style="width:2%">{jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}</td>
                        <td class="page_table_cell" style="width:43%">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}" target="_blank">
                                <h3>{$item.audio_title}</h3></a></td>
                        <td class="page_table_cell center" style="width:16%">
                            <a onclick="jrAudio_widget_pagination(1, 'profile_url:{$item.profile_url}', false);">@{$item.profile_name}</a>
                        </td>
                        <td class="page_table_cell center" style="width:16%">
                            <a onclick="jrAudio_widget_pagination(1, 'audio_album_url:{$item.audio_album_url}', false);">{$item.audio_album}</a>
                        </td>
                        <td class="page_table_cell center" style="width:16%">
                            <a onclick="jrAudio_widget_pagination(1, 'audio_genre_url:{$item.audio_genre_url}', false);">{$item.audio_genre}</a>
                        </td>
                        <td class="page_table_cell" style="width:2%">
                            <input type="checkbox" id="audio_id_{$item._item_id}" class="form_checkbox" value="" title="Add to Audio Player" onclick="jrAudio_include('{$item._item_id}')">
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
        {* prev/next page footer links *}
        {if $info.prev_page > 0 || $info.next_page > 0}
            <div class="block">
                <table style="width:100%">
                    <tr>
                        <td style="width:25%">
                            {if $info.prev_page > 0}
                                <a onclick="jrAudio_widget_pagination({$info.prev_page},'{$_post.sstr}','{$_post.ids}');">{jrCore_icon icon="previous"}</a>
                            {/if}
                        </td>
                        <td style="width:50%;text-align:center">
                            {if $info.total_pages > 1 && (!isset($pager_show_jumper) || $pager_show_jumper == '1')}
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrAudio_widget_pagination($(this).val(),'{$_post.sstr}','{$_post.ids}');">
                                    {for $pages=1 to $info.total_pages}
                                        {if $info.page == $pages}
                                            <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                        {else}
                                            <option value="{$pages}"> {$pages}</option>
                                        {/if}
                                    {/for}
                                </select>&nbsp;/&nbsp;{$info.total_pages}
                            </form>
                            {else}
                                {$info.this_page}
                            {/if}
                        </td>
                        <td style="width:25%;text-align:right">
                            {if $info.next_page > 0}
                                <a onclick="jrAudio_widget_pagination({$info.next_page},'{$_post.sstr}','{$_post.ids}');">{jrCore_icon icon="next"}</a>
                            {/if}
                        </td>
                    </tr>
                </table>
            </div>
        {/if}

    {else}
        <div class="container">
            <table class="page_table">
                <tr class="page_table_row">
                    <td class="page_table_cell center" colspan="8">{jrCore_lang module="jrAudio" id="53" default="no audio files were found"}</td>
                </tr>
            </table>
        </div>
    {/if}
</div>