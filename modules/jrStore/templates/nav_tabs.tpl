<table class="page_content">
    <tbody>
    <tr>
        <td class="page_tab_bar_holder">
            <ul class="page_tab_bar">
                {if isset ($_profile_id) && is_numeric($_profile_id)}
                    <li class="page_tab {if $active_tab == 'list'}page_tab_active{/if} page_tab_first"><a href="{$jamroom_url}/{$profile_url}/{$murl}/sales">{jrCore_lang module="jrStore" id="57" default="All Sales"}</a></li>
                    {if $active_tab != 'list'}
                        <li class="page_tab {if $active_tab == 'details'}page_tab_active{/if}"><a href="{$jamroom_url}/{$profile_url}/{$murl}/sales/{$txn_id}">{jrCore_lang module="jrStore" id="58" default="Details"}</a></li>
                        <li class="page_tab {if $active_tab == 'communication'}page_tab_active{/if} page_tab_last"><a href="{$jamroom_url}/{$profile_url}/{$murl}/sales/{$txn_id}/communication">{jrCore_lang module="jrStore" id="59" default="Communication"}</a></li>
                    {/if}
                {else}
                    <li class="page_tab {if $active_tab == 'list'}page_tab_active{/if} page_tab_first"><a href="{$jamroom_url}/{$murl}/purchases">{jrCore_lang module="jrStore" id="60" default="All Purchases"}</a></li>
                    {if $active_tab != 'list'}
                        <li class="page_tab {if $active_tab == 'details'}page_tab_active{/if}"><a href="{$jamroom_url}/{$murl}/purchases/{$txn_id}/{$seller['_profile_id']}">{jrCore_lang module="jrStore" id="58" default="Details"}</a></li>
                        <li class="page_tab {if $active_tab == 'communication'}page_tab_active{/if} page_tab_last"><a href="{$jamroom_url}/{$murl}/purchases/{$txn_id}/{$seller['_profile_id']}/communication">{jrCore_lang module="jrStore" id="59" default="Communication"}</a></li>
                    {/if}
                {/if}
            </ul>
        </td>
    </tr>
    <tr>
        <td class="page_tab_bar_spacer"></td>
    </tr>
    </tbody>
</table>
