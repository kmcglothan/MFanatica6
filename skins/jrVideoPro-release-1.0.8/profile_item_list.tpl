{if $profile_disable_header != '1'}
    <div class="col8 {$last}">
        <div class="wrap">
            {$profile_item_list_content}
        </div>
    </div>
    {jrCore_include template="profile_sidebar.tpl"}
{else}
    {$profile_item_list_content}
{/if}





