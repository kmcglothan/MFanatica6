{if isset($_items)}
{foreach $_items as $module => $entry}
    {if $entry.active == '1'}
     <a href="{$entry.target}" class="t{$module}"><div class="profile_menu_entry profile_menu_entry_active">{$entry.label}</div></a>
    {else}
     <a href="{$entry.target}" class="t{$module}"><div class="profile_menu_entry">{$entry.label}</div></a>
    {/if}
{/foreach}
{/if}

