{jrCore_module_url module="jrEvent" assign="murl"}
{if isset($_items)}
    <div class="container">
        {foreach from=$_items item="item"}
        {if $item@first || ($item@iteration % 4) == 1}
        <div class="row">
        {/if}
            <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                <div class="p10 center">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.event_title title=$item.event_title class="iloutline"}</a><br>
                    <div class="p5 center">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}" title="{$item.event_title}"><h3>{$item.event_title|truncate:20:"...":false}</h3></a><br>
                        <span class="media_title">{jrCore_lang module="jrEvent" id="13" default="Featuring"}:</span> <span class="normal" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</span><br>
                        <span class="media_title">{jrCore_lang module="jrEvent" id="11" default="Event Date"}:</span> <span class="normal">{$item.event_date|jrCore_date_format}</span><br>
                        {if isset($_post.option) && $_post.option == 'by_ratings'}
                            <div class="p5">
                                {jrCore_module_function function="jrRating_form" type="star" module="jrEvent" index="1" item_id=$item._item_id current=$item.event_rating_1_average_count|default:0 votes=$item.event_rating_1_count|default:0 }
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {if $item@last || ($item@iteration % 4) == 0}
        </div>
        {/if}
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


