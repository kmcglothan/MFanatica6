{jrCore_module_url module="jrVideo" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="body_5 page" style="margin-right:auto;">

        <div class="container">

            <div class="row">

                <div class="col1">
                    <div class="center">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" crop="auto" class="iloutline img_scale" alt=$item.video_title style="max-width:196px;"}</a>
                    </div>
                </div>
                <div class="col9">
                    <div class="p5" style="padding-left:10px;">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{$item.video_title}</a></h3>&nbsp;-&nbsp;<a href="{$jamroom_url}/{$item.profile_url}"><span class="media_title">{$item.profile_name}</span></a><br>
                        {if isset($item.video_category) && strlen($item.video_category) > 0}<span class="sub_title">{jrCore_lang module="jrVideo" id="12" default="category"}:</span> <span class="hilite">{$item.video_category}</span>&nbsp;{/if}
                        {if isset($item.video_album) && strlen($item.video_album) > 0}<span class="sub_title">{jrCore_lang skin=$_conf.jrCore_active_skin id="64" default="album"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{$item.video_album}</a><br>{else}<br>{/if}
                        {if isset($_post.option) && $_post.option == 'by_plays'}
                            <span class="sub_title">{jrCore_lang skin=$_conf.jrCore_active_skin id="51" default="plays"}:</span> <span class="hilite">{$item.video_file_stream_count}</span><br>
                        {/if}
                        {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$item._item_id current=$item.video_rating_1_average_count|default:0 votes=$item.video_rating_1_count|default:0}
                    </div>
                </div>
                <div class="col2 last">
                    <div class="nowrap float-right">
                        {if isset($item.video_file_item_price) && $item.video_file_item_price > 0}
                            {if jrCore_module_is_active('jrFoxyCart')}
                                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrVideo" field="video_file" item=$item}
                            {elseif jrCore_module_is_active('jrPayPal')}
                                {jrPayPal_buy_now_button module="jrVideo" item=$item}
                            {/if}
                        {elseif $_conf.jrVideo_block_download != 'on'}
                            <div class="add_to_cart_section" title="Free Download"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/video_file/{$item._item_id}">{jrCore_icon icon="download" size="18"}</a></div>
                        {else}
                            <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="18"}</div>
                        {/if}
                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrVideo" item_id=$item._item_id}
                    </div>
                </div>

            </div>

        </div>

    </div>

    {/foreach}
    {if $info.total_pages > 1}
    <div class="block">
        <table style="width:100%;">
            <tr>

                <td class="body_5 page" style="width:25%;text-align:center;">
                    {if isset($info.prev_page) && $info.prev_page > 0}
                        {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                            <a href="{$info.page_base_url}/search_area={$_post.search_area}/search_string={$_post.search_string|urlencode}/p={$info.next_prev}"><span class="button-arrow-previous">&nbsp;</span></a>
                        {else}
                            <a href="{$info.page_base_url}/p={$info.next_prev}"><span class="button-arrow-previous">&nbsp;</span></a>
                        {/if}
                    {else}
                        <span class="button-arrow-previous-off">&nbsp;</span>
                    {/if}
                </td>

                <td class="body_5" style="width:50%;text-align:center;">
                    {if $info.total_pages <= 2 || $info.total_pages > 500}
                        {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                        <form name="form" method="post" action="_self">
                        {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                            <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;window.location='{$info.page_base_url}/search_area={$_post.search_area}/search_string={$_post.search_string|urlencode}/p=' +sel">
                        {else}
                            <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;window.location='{$info.page_base_url}/p=' +sel">
                        {/if}
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

                <td class="body_5 page" style="width:25%;text-align:center;">
                    {if isset($info.next_page) && $info.next_page > 1}
                        {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                            <a href="{$info.page_base_url}/search_area={$_post.search_area}/search_string={$_post.search_string|urlencode}/p={$info.next_page}"><span class="button-arrow-next">&nbsp;</span></a>
                        {else}
                            <a href="{$info.page_base_url}/p={$info.next_page}"><span class="button-arrow-next">&nbsp;</span></a>
                        {/if}
                    {else}
                        <span class="button-arrow-next-off">&nbsp;</span>
                    {/if}
                </td>

            </tr>
        </table>
    </div>
    {/if}
{/if}
