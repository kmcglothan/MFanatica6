<tr class="nodrag nodrop">
    <td colspan="{$colspan}">
        <table class="page_table_pager">
            <tr>

                <td class="page_table_pager_left">
                    {if isset($prev_page_num) && $prev_page_num > 0}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="window.location='{$prev_page_url}'">
                    {/if}
                </td>

                <td nowrap="nowrap" class="page_table_pager_center">
                    {if $total_pages > 0}
                        {if strlen($page_select) > 0}
                            {$page_jumper} of {$total_pages} &nbsp;&nbsp;&nbsp; {$page_select} per page
                        {else}
                            {$page_jumper} of {$total_pages}
                        {/if}
                    {else}
                        {if strlen($page_select) > 0}
                            {$page_jumper} &nbsp; {$page_select} per page
                        {else}
                            {$page_jumper}
                        {/if}
                    {/if}
                </td>

                <td class="page_table_pager_right">
                    {if isset($next_page_num) && $next_page_num > 1}
                        <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="window.location='{$next_page_url}'">
                    {/if}
                </td>

            </tr>
        </table>
    </td>
</tr>
