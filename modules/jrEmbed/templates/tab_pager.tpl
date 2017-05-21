{* prev/next page footer links *}
{if $info.prev_page > 0 || $info.next_page > 0}
    <div class="block">
        <table style="width:100%">
            <tr>
                <td style="width:25%">
                    {if $info.prev_page > 0}
                        <a onclick="jrEmbed_load_module('{$_params.module}',{$info.prev_page},'{$_post.ss|addslashes}')">{jrCore_icon icon="previous"}</a>
                    {/if}
                </td>
                <td style="width:50%;text-align:center">
                    <form name="form" method="post" action="_self">
                        <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrEmbed_load_module('{$_params.module}', $(this).val(), '{$_post.ss|addslashes}');">
                            {for $pages=1 to $info.total_pages}
                                {if $info.page == $pages}
                                    <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                {else}
                                    <option value="{$pages}"> {$pages}</option>
                                {/if}
                            {/for}
                        </select>&nbsp;/&nbsp;{$info.total_pages}
                    </form>
                </td>
                <td style="width:25%;text-align:right">
                    {if $info.next_page > 0}
                        <a onclick="jrEmbed_load_module('{$_params.module}',{$info.next_page},'{$_post.ss|addslashes}')">{jrCore_icon icon="next"}</a>
                    {/if}
                </td>
            </tr>
        </table>
    </div>
{/if}