{jrCore_module_url module="jrEvent" assign="murl"}
{if isset($_items)}
    <div class="container">

        {foreach from=$_items item="item"}
            <div class="row">

                <div class="col2">
                    <div class="center">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="icon" crop="auto" alt=$item.event_title title=$item.event_title class="iloutline" style="max-width:140px;"}</a><br>
                    </div>
                </div>

                <div class="col8">
                    <div class="p5" style="padding-left:10px;">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a></h3><br>
                        <span class="media_title">{jrCore_lang module="jrEvent" id="13" default="Featuring"}:</span> <span class="normal">{$item.profile_name}</span><br>
                        <span class="media_title">{jrCore_lang module="jrEvent" id="11" default="Event Date"}:</span> <span class="normal">{$item.event_date|jrCore_date_format}</span><br>
                        <span class="media_title">{jrCore_lang module="jrEvent" id="6" default="Event location"}:</span> <span class="normal">{$item.event_location|truncate:60}</span>
                        {*** Only show ratings 120 minutes after event start ***}
                        {math equation="x+7200" x=$item.event_date assign="end_time"}
                        {if $end_time <= $smarty.now}
                            <div style="padding:4px 0 8px 4px;">
                                {jrCore_module_function function="jrRating_form" type="star" module="jrEvent" index="1" item_id=$item._item_id current=$item.event_rating_1_average_count|default:0 votes=$item.event_rating_1_count|default:0 }
                            </div>
                        {/if}
                    </div>
                </div>

                <div class="col2 last">
                    {* <div class="nowrap float-right">
                        {jrCore_item_update_button module="jrEvent" profile_id=$item._profile_id item_id=$item._item_id}
                        {jrCore_item_delete_button module="jrEvent" profile_id=$item._profile_id item_id=$item._item_id}
                    </div> *}
                </div>

            </div>

            <div class="row">
                <div class="col12 last">
                    <div class="divider mb10 mt10"></div>
                </div>
            </div>

        {/foreach}

    </div>

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


