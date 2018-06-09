
{if $_conf.kmSuperFans_profile_side == 'left'}
    {jrCore_include template="profile_sidebar.tpl"}
    {$last = 'last'}
{/if}

<div class="col8 {$last}">
    <div class="wrap">
        {$profile_item_detail_content}
    </div>
</div>

{if $_conf.kmSuperFans_profile_side != 'left'}
    {$last = 'last'}
    {jrCore_include template="profile_sidebar.tpl"}
{/if}



