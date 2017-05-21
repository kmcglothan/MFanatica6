{foreach $_list as $_l0}
<li><a href="{if $_l0.menu_url|substr:0:4 === 'http'}{$_l0.menu_url}{else}{$jamroom_url}/{$_l0.menu_url}{/if}">{$_l0.menu_title}</a></li>
{if is_array($_l0._children)}
    {foreach $_l0._children as $_l1}
    <li>&nbsp;&nbsp;<a href="{if $_l1.menu_url|substr:0:4 === 'http'}{$_l1.menu_url}{else}{$jamroom_url}/{$_l1.menu_url}{/if}">{$_l1.menu_title}</a></li>
    {if is_array($_l1._children)}
        {foreach $_l1._children as $_l2}
            <li>&nbsp;&nbsp;&nbsp;&nbsp;<a href="{if $_l2.menu_url|substr:0:4 === 'http'}{$_l2.menu_url}{else}{$jamroom_url}/{$_l2.menu_url}{/if}">{$_l2.menu_title}</a></li>
        {/foreach}
    {/if}
    {/foreach}
{/if}
{/foreach}
