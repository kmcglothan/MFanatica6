{jrCore_module_url module="jrVideo" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        {if $item.chart_direction == 'up'}
            {if $item.chart_change > 10}
                {assign var="chart_image" value="hot_up"}
                {else}
                {assign var="chart_image" value="up"}
            {/if}
            {elseif $item.chart_direction == 'down'}
            {if $item.chart_change > 10}
                {assign var="chart_image" value="cool_down"}
                {else}
                {assign var="chart_image" value="down"}
            {/if}
            {elseif $item.chart_direction == 'same'}
            {assign var="chart_image" value="same"}
            {elseif $item.chart_direction == 'new'}
            {assign var="chart_image" value="new"}
        {/if}

        <div class="container">

            <div class="row">

                <div class="col1">
                    <div class="p5">
                        <div class="rank">
                            {$item.list_rank}<br>
                            {if $item.chart_direction != 'same'}
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="98" default="moved" assign="chart_position_title1"}
                                {assign var="cp_title" value="`$chart_position_title1` `$item.chart_direction`"}
                            {else}
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="65" default="position" assign="chart_position_title1"}
                                {assign var="cp_title" value="`$item.chart_direction` `$chart_position_title1`"}
                            {/if}
                            {jrCore_image image="chart_`$chart_image`.png" width="16" height="16" alt="{$item.chart_direction}" title=$cp_title}<br>
                            {if $item.chart_change > 0}
                                ({$item.chart_change})
                            {else}
                                (-)
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="col1">
                    <div class="center">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" crop="auto" class="iloutline img_scale" alt=$item.audio_title style="max-width:196px;"}</a>
                    </div>
                </div>
                <div class="col8">
                    <div class="p5" style="padding-left:10px;">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{$item.video_title}</a></h3><br>
                        {if isset($item.video_category) && strlen($item.video_category) > 0}<span class="capital">{jrCore_lang module="jrVideo" id="12" default="category"}:</span> <span class="hilite">{$item.video_category}</span>&nbsp;{/if}
                        {if isset($item.video_album) && strlen($item.video_album) > 0}<span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="64" default="album"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{$item.video_album}</a><br>{else}<br>{/if}
                        <span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="51" default="plays"}:</span> <span class="hilite">{$item.chart_count}</span><br>
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
                            <div class="add_to_cart_section" title="Free Download"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/video_file/{$item._item_id}" title="download">{jrCore_icon icon="download" size="16"}</a></div>
                        {else}
                            <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="16"}</div>
                        {/if}
                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrVideo" item_id=$item._item_id}
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col12 last">
                    <div class="divider mb10 mt10"></div>
                </div>
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
