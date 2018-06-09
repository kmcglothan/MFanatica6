{jrCore_module_url module="jrUpimg" assign="murl"}
<script type="text/javascript">
    $(document).ready(function() {
        // add checkmarks to images in the list.
        $.each(list, function(k, v) {
            $('#upimg_id_' + v).prop('checked', 'checked');
        });
    });
</script>

<div id="jrUpimg_holder">
    {* Results *}
    {if isset($_items) && is_array($_items)}

        <div class="container">
            <table class="page_table" id="upimg_results">
                {foreach $_items as $key => $item}
                    <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                        <td class="page_table_cell center" style="width:6%"><a href="{$jamroom_url}/{$murl}/image/upimg_file/{$item._item_id}/xxlarge" data-lightbox="screens">{jrCore_module_function function="jrImage_display" module="jrUpimg" type="upimg_file" item_id=$item._item_id size="small" crop="auto" class="img_scale" alt="img" width=false height=false}</a></td>
                        <td class="page_table_cell" style="width:69%"><h3>{$item.upimg_file_name}</h3></td>
                        <td class="page_table_cell center" style="width:20%"><a onclick="jrUpimg_widget_load_images('1','profile_url:{$item.profile_url}');">@{$item.profile_name}</td>
                        <td class="page_table_cell center" style="width:5%">
                            <input type="checkbox" id="upimg_id_{$item._item_id}" class="form_checkbox" value="" title="Add to Image List" onclick="jrUpimg_include('{$item._item_id}')">
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
                                <a onclick="jrUpimg_widget_load_images({$info.prev_page},'{$_post.sstr}','{$_post.ids}');">{jrCore_icon icon="previous"}</a>
                            {/if}
                        </td>
                        <td style="width:50%;text-align:center">
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrUpimg_widget_load_images($(this).val(),'{$_post.sstr}','{$_post.ids}');">
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
                                <a onclick="jrUpimg_widget_load_images({$info.next_page},'{$_post.sstr}','{$_post.ids}');">{jrCore_icon icon="next"}</a>
                            {/if}
                        </td>
                    </tr>
                </table>
            </div>
        {/if}

    {else}
        <div class="container">
            <table class="page_table" id="upimg_results">
                <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                    <td class="page_table_cell center" style="width:6%">&nbsp;</td>
                    <td class="page_table_cell" style="width:69%">&nbsp;</td>
                    <td class="page_table_cell center" style="width:20%">&nbsp;</td>
                    <td class="page_table_cell center" style="width:5%">&nbsp;</td>
                </tr>
            </table>
        </div>
    {/if}
</div>