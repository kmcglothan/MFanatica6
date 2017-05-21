{foreach $_list as $_l0}
<li {if $_post.module_url == $_l0.menu_url}class="active"{/if}>
    <a href="{if $_l0.menu_url|substr:0:4 === 'http'}{$_l0.menu_url}{elseif $_l0.menu_url|substr:0:1 === '#'}{$_l0.menu_url}{else}{$jamroom_url}/{$_l0.menu_url}{/if}" onclick="{$_l0.menu_onclick}" class="menu_0_link" data-topic="{$_l0.menu_url}">{$_l0.menu_title}<span class="notifications none">0</span></a>
    {if is_array($_l0._children)}
    <ul>
        {foreach $_l0._children as $_l1}
        <li>
            <a href="{if $_l1.menu_url|substr:0:4 === 'http'}{$_l1.menu_url}{elseif $_l1.menu_url|substr:0:1 === '#'}{$_l1.menu_url}{else}{$jamroom_url}/{$_l1.menu_url}{/if}" onclick="{$_l1.menu_onclick}" >{$_l1.menu_title}</a>
            {if is_array($_l1._children)}
            <ul>
            {foreach $_l1._children as $_l2}
                <li><a href="{if $_l2.menu_url|substr:0:4 === 'http'}{$_l2.menu_url}{elseif $_l2.menu_url|substr:0:1 === '#'}{$_l2.menu_url}{else}{$jamroom_url}/{$_l2.menu_url}{/if}" onclick="{$_l2.menu_onclick}" >{$_l2.menu_title}</a></li>
            {/foreach}
            </ul>
            {/if}
        </li>
        {/foreach}
    </ul>
    {/if}
</li>
{/foreach}