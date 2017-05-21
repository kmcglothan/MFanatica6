{foreach from=$_items name="loop" item="entry"}
    {if $smarty.foreach.loop.iteration > 1}
        &bull;
    {/if}
    {assign var="oc" value=""}
    {if isset($entry.menu_onclick) && strlen($entry.menu_onclick) > 2}
        {assign var="oc" value=" onclick=\"`$entry.menu_onclick`\" "}
    {/if}
    {if isset($entry.menu_function_result) && strlen($entry.menu_function_result) > 0}
        {if is_numeric($entry.menu_function_result)}
            <a href="{$entry.menu_url}" {$oc}>{$entry.menu_label} [{$entry.menu_function_result}]</a>
        {else}
            <a href="{$entry.menu_url}" {$oc}>{$entry.menu_label} <img src="{$entry.menu_function_result}" alt="{$entry.menu_label}"></a>
        {/if}
    {else}
        <a href="{$entry.menu_url}" {$oc}>{$entry.menu_label}</a>
    {/if}
{/foreach}
