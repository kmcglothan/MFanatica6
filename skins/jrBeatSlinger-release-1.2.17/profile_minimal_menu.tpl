{if isset($_items)}
    <ul id="basic">
{foreach from=$_items key="module" item="entry"}
    {if $entry.active == '1'}
        <li class="active"><a href="{$entry.target}">{$entry.label}</a></li>
    {else}
        <li><a href="{$entry.target}">{$entry.label}</a></li>
    {/if}
{/foreach}
   </ul>
{/if}

