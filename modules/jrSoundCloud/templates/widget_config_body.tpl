{jrCore_module_url module="jrSoundCloud" assign="murl"}

<div id="jrSoundCloud_holder">
    {* Results *}
    {if isset($_items) && is_array($_items)}
        <div class="container">
            <table class="page_table">
                {foreach $_items as $key => $item}
                    <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                        <td class="page_table_cell center" style="width:5%">
                            {if isset($item.soundcloud_artwork_url) && strlen($item.soundcloud_artwork_url) > 0}
                               <a href="{$item.soundcloud_artwork_url}" target="_blank"><img src="{$item.soundcloud_artwork_url}" alt="{$item.soundcloud_title_url|jrCore_entity_string}" class="iloutline img_scale"></a><br>
                           {/if}
                        </td>
                        <td class="page_table_cell" style="width:43%">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}" target="_blank">
                                <h3>{$item.soundcloud_title}</h3></a></td>
                        <td class="page_table_cell center" style="width:16%">
                            <a onclick="jrSoundCloud_widget_pagination(1, 'profile_url:{$item.profile_url}', false);">@{$item.profile_name}</a>
                        </td>
                        <td class="page_table_cell center" style="width:16%">
                            <a onclick="jrSoundCloud_widget_pagination(1, 'soundcloud_genre_url:{$item.soundcloud_genre_url}', false);">{$item.soundcloud_genre}</a>
                        </td>
                        <td class="page_table_cell" style="width:2%">
                            {if isset($_post.sel) && $_post.sel == $item._item_id}
                            <input type="radio" checked="checked" name="soundcloud_id" class="form_radio" value="{$item._item_id}" title="Select this SoundCloud Video">
                            {else}
                            <input type="radio" name="soundcloud_id" class="form_radio" value="{$item._item_id}" title="Select this SoundCloud Video">
                            {/if}
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
                                <a onclick="jrSoundCloud_widget_pagination({$info.prev_page},'{$_post.sstr}','{$_post.sel}');">{jrCore_icon icon="previous"}</a>
                            {/if}
                        </td>
                        <td style="width:50%;text-align:center">
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrSoundCloud_widget_pagination($(this).val(),'{$_post.sstr}','{$_post.sel}');">
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
                                <a onclick="jrSoundCloud_widget_pagination({$info.next_page},'{$_post.sstr}','{$_post.sel}');">{jrCore_icon icon="next"}</a>
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
                    <td class="page_table_cell center" colspan="8">{jrCore_lang module="jrSoundCloud" id="41" default="No SoundCloud Tracks found"}</td>
                </tr>
            </table>
        </div>
    {/if}
</div>