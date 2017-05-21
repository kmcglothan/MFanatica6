{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="p5">
            <a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" class="iloutline" item_id=$item._user_id size="xsmall" alt=$item.user_name style="float:right;margin-left:8px;"}</a>
            <h3><a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}">{$item.page_title}</a></h3><br>
            <span class="normal">{$item.page_body|jrCore_format_string:$item.profile_quota_id|nl2br|jrCore_strip_html|truncate:180}</span>
        </div>
        <div class="divider mb10 mt10"></div>
    {/foreach}
    {if $info.total_pages > 1}
        <div class="block">
            <table style="width:100%;">
                <tr>

                    <td style="width:25%;">
                        {if isset($info.prev_page) && $info.prev_page > 0}
                            <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="window.location='{$info.page_base_url}/p={$info.prev_page}'">
                        {/if}
                    </td>

                    <td style="width:50%;text-align:center;">
                        {if $info.total_pages <= 5 || $info.total_pages > 500}
                            {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;window.location='{$info.page_base_url}/p=' +sel">
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
                            <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="window.location='{$info.page_base_url}/p={$info.next_page}'">
                        {/if}
                    </td>

                </tr>
            </table>
        </div>
    {/if}
{/if}
