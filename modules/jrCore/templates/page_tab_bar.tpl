<tr>
    <td colspan="2" class="page_tab_bar_holder">
        <ul class="page_tab_bar">
            {foreach from=$tabs item="tab"}
                {if isset($tab.onclick)}
                    {if isset($tab.active) && $tab.active == '1'}
                        <li id="{$tab.id}" class="{$tab.class} page_tab_active" onclick="{$tab.onclick}">{$tab.label}</li>
                    {else}
                        <li id="{$tab.id}" class="{$tab.class}" onclick="{$tab.onclick}"><a href="">{$tab.label}</a></li>
                    {/if}
                {else}
                    {if isset($tab.active) && $tab.active == '1'}
                        <li id="{$tab.id}" class="{$tab.class} page_tab_active"><a href="{$tab.url}">{$tab.label}</a>
                        </li>
                    {else}
                        <li id="{$tab.id}" class="{$tab.class}"><a href="{$tab.url}">{$tab.label}</a></li>
                    {/if}
                {/if}
            {/foreach}
        </ul>
    </td>
</tr>
<tr>
    <td colspan="2" class="page_tab_bar_spacer"></td>
</tr>
