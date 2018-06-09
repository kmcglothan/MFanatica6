{jrCore_module_url module="jrSoundCloud" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="container">

            <div class="row">

                <div class="col1">
                    <div class="center">
                        {if $item.soundcloud_artwork_url != ''}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}"><img src="{$item.soundcloud_artwork_url}" class="iloutline img_scale"></a><br>
                        {/if}
                    </div>
                </div>
                <div class="col1">
                    <div class="p10 middle">
                        {jrSoundCloud_player params=$item}
                    </div>
                </div>
                <div class="col8">
                    <div class="p5 middle">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">{$item.soundcloud_title}</a></h3><br>
                        <span class="capital">{jrCore_lang module="jrSoundCloud" id="26" default="artist"}:</span> <span class="normal">{$item.soundcloud_artist}</span>&nbsp;
                        <span class="capital">{jrCore_lang module="jrSoundCloud" id="27" default="genre"}:</span> <span class="normal">{$item.soundcloud_genre}</span><br>
                        {if isset($_post.option) && $_post.option == 'by_plays'}
                            <span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="51" default="plays"}:</span> <span class="hilite">{$item.soundcloud_stream_count}</span><br>
                        {/if}
                        {jrCore_module_function function="jrRating_form" type="star" module="jrSoundCloud" index="1" item_id=$item._item_id current=$item.soundcloud_rating_1_average_count|default:0 votes=$item.soundcloud_rating_1_count|default:0 text=$text target="alert"}
                    </div>
                </div>
                <div class="col2 last">
                    <div class="nowrap float-right">
                        {if jrCore_module_is_active('jrPlaylist')}
                            {jrCore_module_function function="jrPlaylist_button" image="playlist_add.png" playlist_for="jrSoundCloud" item_id=$item._item_id}
                        {/if}
                        {* )jrCore_item_update_button module="jrSoundCloud" profile_id=$item._profile_id item_id=$item._item_id style="width:100px" *}
                        {* jrCore_item_delete_button module="jrSoundCloud" profile_id=$item._profile_id item_id=$item._item_id prompt="Are you sure you want to delete this item?" style="width:100px;margin:6px 0" *}
                    </div>
                </div>

            </div>

        </div>
        <hr>

    {/foreach}
    {if $info.total_pages > 1}
        <div class="block">
            <table style="width:100%;">
                <tr>

                    <td style="width:25%;">
                        {if isset($info.prev_page) && $info.prev_page > 0}
                            <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrCore_window_location('{$info.page_base_url}/p={$info.prev_page}');">
                        {/if}
                    </td>

                    <td style="width:50%;text-align:center;">
                        {if $info.total_pages <= 5 || $info.total_pages > 500}
                            {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrCore_window_location('{$info.page_base_url}/p=' +sel);">
                                    {for $pages=1 to $info.total_pages}
                                        {if $info.page == $pages}
                                            <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                        {else}
                                            <option value="{$pages}"> {$pages}</option>
                                        {/if}
                                    {/for}
                                </select>&nbsp;/&nbsp;{$info.total_pages}
                            </form>
                        {/if}
                    </td>

                    <td style="width:25%;text-align:right;">
                        {if isset($info.next_page) && $info.next_page > 1}
                            <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrCore_window_location('{$info.page_base_url}/p={$info.next_page}');">
                        {/if}
                    </td>

                </tr>
            </table>
        </div>
    {/if}
{/if}
