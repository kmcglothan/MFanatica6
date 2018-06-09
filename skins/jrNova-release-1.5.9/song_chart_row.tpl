{jrCore_module_url module="jrAudio" assign="murl"}
{if isset($_items)}
    <div class="mb8">
        <div class="container">
            {foreach from=$_items item="item"}

                <div class="row">
                    <div class="col1">
                        <div class="p20" style="padding-top:0;">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.audio_title}</a>
                        </div>
                    </div>
                    <div class="col1">
                        <div class="p10">
                            {if $item.audio_file_extension == 'mp3'}
                                {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                            {else}
                                <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt="Download" title="Download"}</a>
                            {/if}
                        </div>
                    </div>
                    <div class="col6">
                        <div class="p5">
                            {if $item.chart_direction == 'up'}
                                {if $item.chart_change > 10}
                                    {assign var="chart_image" value="hot"}
                                {else}
                                    {assign var="chart_image" value="up"}
                                {/if}
                            {elseif $item.chart_direction == 'down'}
                                {if $item.chart_change > 10}
                                    {assign var="chart_image" value="cold"}
                                {else}
                                    {assign var="chart_image" value="down"}
                                {/if}
                            {elseif $item.chart_direction == 'same'}
                                {assign var="chart_image" value="same"}
                            {/if}
                            <div style="display:table;">
                                <div class="rank" style="display:table-cell;text-align:center;vertical-align:top;">
                                    {$item.list_rank}
                                </div>
                                <div style="display:table-cell;text-align:center;vertical-align:top;">
                                    {if $item.chart_direction != 'same'}
                                        {jrCore_lang  skin=$_conf.jrCore_active_skin id="46" default="moved" assign="chart_postition_title1"}
                                        {assign var="cp_title" value="`$chart_postition_title1` `$item.chart_direction`"}
                                    {else}
                                        {jrCore_lang  skin=$_conf.jrCore_active_skin id="47" default="position" assign="chart_postition_title1"}
                                        {assign var="cp_title" value="`$item.chart_direction` `$chart_postition_title1`"}
                                    {/if}
                                    {jrCore_image image="`$chart_image`.png" width="16" height="16" alt=$item.chart_direction title=$cp_title}<br>
                                    {if $item.chart_change > 0}
                                        {$item.chart_change}
                                    {else}
                                        -
                                    {/if}
                                </div>
                                <div style="display:table-cell;text-align:left;vertical-align:middle;padding-left:10px;">
                                    <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}"><span class="capital">{$item.audio_title}</span></a></h3><br>
                                    <span class="capital">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="media_title">{$item.audio_genre}</span>&nbsp;
                                    {if isset($item.audio_album) && strlen($item.audio_album) >0}<span class="capital">album:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a><br>{else}<br>{/if}
                                    <span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="49" default="plays"}:</span> <span class="media_title">{$item.chart_count}</span><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col2">
                        <div class="p5">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                        </div>
                    </div>
                    <div class="col2 last">
                        <div class="nowrap float-right" style="padding-right: 10px;">
                            {if isset($item.audio_file_item_price) && $item.audio_file_item_price > 0}
                                {if jrCore_module_is_active('jrFoxyCart')}
                                    {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                                {elseif jrCore_module_is_active('jrPayPal')}
                                    {jrPayPal_buy_now_button module="jrAudio" item=$item}
                                {/if}
                            {elseif $_conf.jrAudio_block_download != 'on'}
                                <div class="add_to_cart_section" title="Free Download"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}" title="download">{jrCore_icon icon="download" size="24"}</a></div>
                            {else}
                                <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="24"}</div>
                            {/if}
                            {jrCore_module_function function="jrPlaylist_button" playlist_for="jrAudio" item_id=$item._item_id}
                        </div>
                    </div>
                    <br><br>
                </div>

            {/foreach}
        </div>
    </div>
    {if $info.total_pages > 1}
        {if isset($_post.module_url) && $_post.module_url == 'song_chart'}
            {assign var="pb_url" value="song_chart_weekly"}
        {else}
            {assign var="pb_url" value=$_post.module_url}
        {/if}
        <div class="block">
            <table style="width:100%;">
                <tr>

                    <td style="width:25%;">
                        {if isset($info.prev_page) && $info.prev_page > 0}
                            <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrLoad('#sc','{$jamroom_url}/{$pb_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#schart').offset().top });return false;">
                        {/if}
                    </td>

                    <td style="width:50%;text-align:center;">
                        {if $info.total_pages <= 4}
                            {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#sc','{$jamroom_url}/{$pb_url}/p=' +sel);$('html, body').animate({ scrollTop: $('#schart').offset().top });return false;">
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
                            <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrLoad('#sc','{$jamroom_url}/{$pb_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#schart').offset().top });return false;">
                        {/if}
                    </td>

                </tr>
            </table>
        </div>
    {/if}
{/if}
