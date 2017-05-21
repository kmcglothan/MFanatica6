{jrCore_module_url module="jrVideo" assign="murl"}
<script type="text/javascript">
    $(document).ready(function() {
        // add checkmarks to songs in the playlist.
        $.each(playlist, function(k, v) {
            $('#video_id_' + v).prop('checked', 'checked');
        });
    });
</script>

<div id="jrVideo_holder">
    {* Results *}
    {if isset($_items) && is_array($_items)}
        <div class="container">
            <table class="page_table">
                {foreach $_items as $key => $item}
                    <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                        <td class="page_table_cell center" style="width:5%">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" crop="auto" class="img_scale" alt="" width=false height=false}</td>
                        <td class="page_table_cell" style="width:43%">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}" target="_blank">
                                <h3>{$item.video_title}</h3></a></td>
                        <td class="page_table_cell center" style="width:16%">
                            <a onclick="jrVideo_widget_pagination(1, '{$item.profile_url}', false, false, false);">@{$item.profile_name}</a>
                        </td>
                        <td class="page_table_cell center" style="width:16%">
                            <a onclick="jrVideo_widget_pagination(1, false, '{$item.video_album_url}', false, false);">{$item.video_album}</a>
                        </td>
                        <td class="page_table_cell" style="width:2%">
                            <input type="checkbox" id="video_id_{$item._item_id}" class="form_checkbox" value="" title="Add to Video Player" onclick="jrVideo_include('{$item._item_id}')">
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
                                <a onclick="jrVideo_widget_pagination({$info.prev_page},'{$_post.profile_url}','{$_post.genre_url}','{$_post.album_url}','{$_post.ss}', '{$_post.ids}');">{jrCore_icon icon="previous"}</a>
                            {/if}
                        </td>
                        <td style="width:50%;text-align:center">
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrVideo_widget_pagination($(this).val(),'{$_post.profile_url}','{$_post.genre_url}','{$_post.album_url}','{$_post.ss}','{$_post.ids}');">
                                    {for $pages=1 to $info.total_pages}
                                        {if $info.page == $pages}
                                            <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                        {else}
                                            <option value="{$pages}"> {$pages}</option>
                                        {/if}
                                    {/for}
                                </select>&nbsp;/&nbsp;{$info.total_pages}
                            </form>
                        </td>
                        <td style="width:25%;text-align:right">
                            {if $info.next_page > 0}
                                <a onclick="jrVideo_widget_pagination({$info.next_page},'{$_post.profile_url}','{$_post.genre_url}','{$_post.album_url}','{$_post.ss}', '{$_post.ids}');">{jrCore_icon icon="next"}</a>
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
                    <td class="page_table_cell center" colspan="8">{jrCore_lang module="jrVideo" id="41" default="no video files were found"}</td>
                </tr>
            </table>
        </div>
    {/if}
</div>