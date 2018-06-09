{jrCore_module_url module="jrVideo" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}

        <div class="container">
            <div class="row">
                <div class="col1">
                    <div class="p20">
                        {if jrCore_is_mobile_device()}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.video_title}</a>
                        {else}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="small" crop="auto" class="iloutline" alt=$item.video_title}</a>
                        {/if}
                    </div>
                </div>
                <div class="{if jrCore_is_mobile_device()}col8{else}col6{/if}">
                    <div class="p10" style="padding-top: 25px;">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}"><span class="capital">{$item.video_title}</span></a></h3><br>
                        {if isset($item.video_album) && strlen($item.video_album) > 0}
                            <span class="media_title"> album:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}"><span class="capital">{$item.video_album}</span></a><br>
                        {/if}
                        {if isset($item.video_category) && strlen($item.video_category) > 0}
                            <span class="media_title">{jrCore_lang module="jrVideo" id="12" default="category"}:</span> <span class="capital">{$item.video_category}</span><br>
                        {/if}
                        {if isset($_post.option) && $_post.option == 'by_plays'}
                            <span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="49" default="plays"}:</span> <span class="capital">{$item.video_file_stream_count}</span><br>
                        {elseif $_post.option == 'by_newest'}
                            <span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="105" default="Created"}:</span> <span class="capital">{$item._created|jrCore_date_format}</span><br>
                        {/if}
                        {if jrCore_is_mobile_device()}
                            {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$item._item_id current=$item.video_rating_1_average_count|default:0 votes=$item.video_rating_1_count|default:0}
                        {/if}
                    </div>
                </div>
                {if !jrCore_is_mobile_device()}
                <div class="col2">
                    <div class="p5" style="padding-top: 40px;">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$item._item_id current=$item.video_rating_1_average_count|default:0 votes=$item.video_rating_1_count|default:0}
                    </div>
                </div>
                {/if}
                <div class="col3 last">
                    <div class="p10 nowrap float-right" style="padding-top: 40px;">
                        {if isset($item.video_file_item_price) && $item.video_file_item_price > 0}
                            {if jrCore_module_is_active('jrFoxyCart')}
                                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrVideo" field="video_file" item=$item}
                            {elseif jrCore_module_is_active('jrPayPal')}
                                {jrPayPal_buy_now_button module="jrVideo" item=$item}
                            {/if}
                        {elseif $_conf.jrVideo_block_download != 'on'}
                            <div class="add_to_cart_section" title="Free Download"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/video_file/{$item._item_id}" title="download">{jrCore_icon icon="download" size="24"}</a></div>
                        {else}
                            <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="24"}</div>
                        {/if}
                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrVideo" item_id=$item._item_id}
                    </div>
                </div>
                <br><br>
            </div>
        </div>

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
                    {if $info.total_pages <= 5}
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
