{jrCore_module_url module="jrAudio" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="container">

            <div class="row">

                <div class="col1">
                    <div class="center">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" class="iloutline img_scale" alt=$item.audio_title style="max-width:196px;"}</a>
                    </div>
                </div>
                <div class="col1">
                    <div class="p10 middle">
                        {if $item.audio_file_extension == 'mp3'}
                            {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="button_player"}
                        {else}
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="108" default="Download" assign="alttitle"}
                            <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                        {/if}
                    </div>
                </div>
                <div class="col8">
                    <div class="p5 middle">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></h3>&nbsp;-&nbsp;<a href="{$jamroom_url}/{$item.profile_url}"><span class="media_title">{$item.profile_name}</span></a><br>
                        <span class="capital">{jrCore_lang module="jrAudio" id="12" default="genre"}</span>: <span class="hilite">{$item.audio_genre}</span>
                        {if isset($item.audio_album) && strlen($item.audio_album) > 0}&nbsp;<span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="64" default="album"}</span>: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a><br>{else}<br>{/if}
                        {if isset($_post.option) && $_post.option == 'by_plays'}
                            <span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="51" default="plays"}:</span> <span class="hilite">{$item.audio_file_stream_count}</span><br>
                        {/if}
                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                    </div>
                </div>
                <div class="col2 last">
                    <div class="nowrap float-right">
                        {if isset($item.audio_file_item_price) && $item.audio_file_item_price > 0}
                            {if jrCore_module_is_active('jrFoxyCart')}
                                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                            {elseif jrCore_module_is_active('jrPayPal')}
                                {jrPayPal_buy_now_button module="jrAudio" item=$item}
                            {/if}
                        {elseif $_conf.jrAudio_block_download != 'on'}
                            <div class="add_to_cart_section" title="Free Download"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}" title="download">{jrCore_icon icon="download" size="16"}</a></div>
                        {else}
                            <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="16"}</div>
                        {/if}
                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrAudio" item_id=$item._item_id}
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
