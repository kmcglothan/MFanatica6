{if isset($_items)}
<div class="container">

    {foreach from=$_items item="item"}
        {if $item@first || ($item@iteration % 3) == 1}
            <div class="row">
        {/if}
        <div class="col4{if $item@last || ($item@iteration % 3) == 0} last{/if}">
            <div class="center mb10">
                <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" class="iloutline" alt=$item.profile_name title=$item.profile_name style="max-width:290px;"}</a><br>
                {if isset($_post.option) && $_post.option == 'most_viewed'}
                    <span class="capital">Page Viewed <span class="hilite">{$item.profile_view_count}</span> {if $item.profile_view_count > 1}times{else}time{/if}</span><br>
                {/if}
                <a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}"><span class="capital bold">{if strlen($item.profile_name) > 15}{$item.profile_name|truncate:15:"...":false}{else}{$item.profile_name}{/if}</span></a>
            </div>
        </div>
        {if $item@last || ($item@iteration % 3) == 0}
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
